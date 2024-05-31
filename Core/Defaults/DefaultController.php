<?php

namespace Core\Defaults;

use App\Exceptions\CommomException;
use App\Exceptions\UnauthorizedException;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use \App\Services\TwigService;

class DefaultController
{

  public ?\mysqli $masterMysqli = null;
  private TwigService $twig;
  public $id_usuario         = "";
  public $retorno            = [];
  public $preventXss         = false;
  public $view               = [];
  public $data               = [];
  public $render             = array('css' => '', 'body' => '', 'js' => '', 'menu' => '');
  public $titulo_pagina      = "";
  public $classDivContainer  = 'container';
  public string $buttons     = '';
  private $preventRetorna    = false;
  private $showFooter        = true;
  private array $breadcrumb  = [];
  private bool $mostraMenu   = true;

  private static string $typeAuth   = "jwt";
  public \App\Model\LogsDAO $LogsDAO;

  private $post              = [];
  private $put               = [];
  private $get               = [];
  private $request           = [];

  public function __construct(?bool $validate = true)
  {
    try {

      $this->iniciaBd();
      $this->iniciaModels();
      if ($_SERVER['REQUEST_METHOD'] && strtoupper($_SERVER['REQUEST_METHOD'])) {
        $this->_parsePut();
      }
      $this->processaRequest();
      if ($validate) {
        $this->validateAuth();
      }
    } catch (\Exception $e) {
      throw ($e);
    }
  }

  public function __destruct()
  {
    try {
      // if($this->masterMysqli != null){
      //     mysqli_close($this->masterMysqli);
      // }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Conexão com banco de dados
   * 
   * @access private
   * @link construct
   */
  private function iniciaBd()
  {
    try {

      $this->masterMysqli = $GLOBALS['_DB_MYSQLI'];
    } catch (\Exception $e) {
      throw $e;
    }
  }

  private function iniciaModels()
  {
    try {
      // Inicia Models / Daos
      $all_model = array_slice(scandir(ROOT_PATH . '/' . MODEL_PATH), 2);
      foreach ($all_model as $model) {

        $model = ucfirst($model);
        $model = str_replace('.php', '', $model);
        $modelWithNamespace  = MODEL_NAMESPACE . $model;

        $this->$model = new $modelWithNamespace($this->masterMysqli);
      }
      spl_autoload_register(array($this, "pathsToAutoload"));
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Aqui ficam os caminhos para o spl_autoload_register puxar automaticamente os daos
   * 
   * @access protected 
   */
  private function pathsToAutoload()
  {
    try {

      $models = glob(ROOT_PATH . '/' . MODEL_PATH . '/*');
      foreach ($models as  $model) {
        include_once $model;
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  private function _parsePut()
  {

    try {

      global $_PUT;

      /* PUT data comes in on the stdin stream */
      $putdata = fopen("php://input", "r");
      $jsonData = json_decode(file_get_contents("php://input"), true);

      if (!empty($jsonData)) {
        $GLOBALS['_PUT'] = $jsonData;
        return;
      }
      unset($jsonData);

      /* Open a file for writing */
      // $fp = fopen("myputfile.ext", "w");

      $raw_data = '';

      /* Read the data 1 KB at a time
            and write to the file */
      while ($chunk = fread($putdata, 1024))
        $raw_data .= $chunk;

      /* Close the streams */
      fclose($putdata);

      // Fetch content and determine boundary
      $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

      if (empty($boundary)) {
        parse_str($raw_data, $data);
        $GLOBALS['_PUT'] = $data;
        return;
      }

      // Fetch each part
      $parts = array_slice(explode($boundary, $raw_data), 1);
      $data = array();

      foreach ($parts as $part) {
        // If this is the last part, break
        if ($part == "--\r\n") break;

        // Separate content from headers
        $part = ltrim($part, "\r\n");
        list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

        // Parse the headers list
        $raw_headers = explode("\r\n", $raw_headers);
        $headers = array();
        foreach ($raw_headers as $header) {
          list($name, $value) = explode(':', $header);
          $headers[strtolower($name)] = ltrim($value, ' ');
        }

        // Parse the Content-Disposition to get the field name, etc.
        if (isset($headers['content-disposition'])) {
          $filename = null;
          $tmp_name = null;
          preg_match(
            '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
            $headers['content-disposition'],
            $matches
          );
          list(, $type, $name) = $matches;

          //Parse File
          if (isset($matches[4])) {
            //if labeled the same as previous, skip
            if (isset($_FILES[$matches[2]])) {
              continue;
            }

            //get filename
            $filename = $matches[4];

            //get tmp name
            $filename_parts = pathinfo($filename);
            $tmp_name = tempnam(ini_get('upload_tmp_dir'), $filename_parts['filename']);

            //populate $_FILES with information, size may be off in multibyte situation
            $_FILES[$matches[2]] = array(
              'error'    => 0,
              'name'     => $filename,
              'tmp_name' => $tmp_name,
              'size'     => strlen($body),
              'type'     => $value
            );

            //place in temporary directory
            file_put_contents($tmp_name, $body);
          }
          //Parse Field
          else {
            $data[$name] = substr($body, 0, strlen($body) - 2);
          }
        }
      }
      $GLOBALS['_PUT'] = $data;
      return;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Remove variáveis $_post, get, request e salva em variáveis no controller
   * 
   * @access private
   * @link construct
   */
  private function processaRequest()
  {
    try {
      //Elimino SQlLInjection 
      //se vier por parametro na uri

      unset($_REQUEST['url']);
      $inputs = json_decode(file_get_contents('php://input'), true);
      $otherPost = $inputs ? $inputs : [];

      $_POST                    = $_POST ? $_POST : $otherPost;

      $this->get                = $this->eliminaSQLInjection($_GET);
      $this->put                = $this->eliminaSQLInjection($GLOBALS['_PUT']);
      $this->post               = $this->eliminaSQLInjection($_POST);
      $this->request            = $this->eliminaSQLInjection($_REQUEST);

      // unset($_GET);
      // unset($_POST);
      // unset($_REQUEST);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  protected function extractToken()
  {
    try {
      $headers = getallheaders();
      if (isset($headers) && count($headers) && isset($headers['Authorization']) && strlen($headers['Authorization']) > 7) {

        $token = str_replace("Bearer ", "", $headers['Authorization']);

        $decodedToken = JWT::decode($token, new Key($_ENV['KEY_JWT'], 'HS256'));

        return $decodedToken;
      } else {
        throw new UnauthorizedException("Erro de validação", 401);
      }
    } catch (UnauthorizedException $e) {

      switch ($e->getMessage()) {
        case "Signature verification failed":
          throw new UnauthorizedException("Assinatura do token inválida.");
          break;
        case "Expired token":
          throw new UnauthorizedException("Token expirado.");
          break;

        default:
          throw $e;
          break;
      }
    }
  }

  public function validateAuth()
  {
    try {
      switch (self::$typeAuth) {
        case "jwt":
          $dados = (array)$this->extractToken();

          if (empty($dados)) {
            throw new UnauthorizedException("Token recebido incorretamente.");
          }

          $GLOBALS['_ID_USUARIO'] = $dados['id'];

          (new \App\Classes\OperatorsClass)->ValidateUser($dados['id']);
          break;
        case "session":
          $autenticado     = true;
          if (GetSessao("lifetime")) {

            $sessionTime     = GetSessao("lifetime");
            $tempoAtual      = date('Y-m-d H:i:s');

            $sessionLifeTime = strtotime($sessionTime);
            $timestampAtual  = strtotime($tempoAtual);

            if ($sessionLifeTime < $timestampAtual) {
              $autenticado = false;
              clearSessao();
            } else {
              $autenticado = true;
            }
          } else {
            $autenticado = false;
            clearSessao();
          }

          return $autenticado;
          break;
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function SetTypeAuth(string $type)
  {
    try {
      self::$typeAuth = $type;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function setControle($msg = "")
  {
    try {
      $bindControle = [
        "operator_id" => GetSessao('id_usuario'),
        "description" => $msg
      ];

      $this->LogsDAO->insert($bindControle);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * retorna o $_GET que foi convertido em variável
   * 
   * @access public 
   */
  public function getQuery(?string $key = null): string|array|null
  {
    try {
      if ($key) {
        return $this->get[$key];
      }

      return $this->get;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * retorna o $_POST que foi convertido em variável
   * 
   * @access protected 
   */
  protected function getPost(): string|array|null
  {
    try {
      $post = [
        ...$this->post,
        ...$_FILES
      ];


      return $post;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * retorna o $_PUT que foi convertido em variável
   * 
   * @access protected 
   */
  protected function getPut(): string|array|null
  {
    try {
      $put = [
        ...$this->put,
        ...$_FILES
      ];


      return $put;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * retorna o $_REQUEST que foi convertido em variável
   * 
   * @access public 
   */
  public function getRequest(): string|array|null
  {
    try {
      return $this->request;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Função que renderiza a página. No arquivo da view (rota), as partes do corpo está 
   * entre captureStart e captureEnd (body, js, css) são salvos nas respectivas variáveis.
   * 
   * Em seguida chama o layout que organiza como os arquivos saem na tela.
   * converte array em variáveis na view => https://www.php.net/manual/en/function.extract.php
   * 
   * @access protected 
   * @param rota da view
   */
  public function render(?string $rota = null, ?array $data = [])
  {
    try {

      //define titulo pagina e detalhes para breadcrumbs em todos os renders
      $this->twig = new TwigService;

      if (empty($rota)) {
        throw new CommomException("Rota de view não definida");
      }

      $rota               = explode(".", $rota);
      $routeWithSeparator = implode("/", $rota);
      $file_route         = ROOT_PATH . "/App/View/" . $routeWithSeparator . ".html";

      $this->AddFunctionTwig("getShowMenu");
      $this->AddFunctionTwig("getClassDivContainer");
      $this->AddFunctionTwig("getBreadcrumb");
      $this->AddFunctionTwig("getShowFooter");
      $this->AddFunctionTwig("getTituloPagina");

      $this->twig->addGlobal("buttons", $this->buttons);

      if (is_file($file_route)) {

        echo $this->twig->render($routeWithSeparator . ".html", $data);
      } else {
        $maybeDir = str_replace(".html", "", $file_route);

        if (is_dir($maybeDir)) {
          if (is_file($maybeDir . "/index.html")) {
            echo $this->twig->render($routeWithSeparator . "/index.html", $data);
          } else {
            throw new CommomException("Não achou " . $file_route);
          }
        } else {
          throw new CommomException("Não achou " . $file_route);
        }
      }
    } catch (CommomException $e) {
      throw $e;
    }
  }


  private function AddFunctionTwig($name)
  {
    try {
      $this->twig->addFunction($name, [$this, $name]);
    } catch (\Exception $e) {
      throw $e;
    }
  }


  /**
   * ob_start das views conforme partes do corpo
   * 
   * @access protected 
   * @param parte do corpo
   */
  protected function captureStart($name): void
  {
    try {
      ob_start();
    } catch (Exception $e) {
      throw $e;
    }
  }

  /**
   * ob_end, salvando conteúdo na variável conforme parte do corpo
   * 
   * @access protected 
   * @param parte do corpo
   */
  protected function captureEnd($name): void
  {
    try {

      // feito esse replace para adaptar a nova regra de abrir "link" ao clicar no table, 
      // fazendo ter oportunidade de abrir em nova janela ao clicar com o botao do meio do mouse
      $this->render[$name] = ob_get_contents();
      ob_end_clean();
    } catch (Exception $e) {
      throw $e;
    }
  }

  /**
   * se quiser colocar um nome da pagina específica, pode colocar
   * 
   * @access protected 
   * @param string (titulo)
   */
  public function setTituloPagina(string $titulo)
  {
    try {
      if ($titulo == '') {
        //alinhamento
        $titulo = '&nbsp;';
      }
      $this->titulo_pagina = $titulo;

      return $this;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function setClassDivContainer(string $class)
  {
    try {
      $this->classDivContainer = $class;

      return $this;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function getClassDivContainer(): string
  {
    try {
      return $this->classDivContainer;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function getBreadcrumb()
  {
    try {
      return $this->breadcrumb;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function getTituloPagina()
  {
    try {
      return $this->titulo_pagina;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function getShowMenu(): bool
  {
    try {
      return $this->mostraMenu;
    } catch (Exception $e) {
      throw $e;
    }
  }

  protected function setShowFooter(bool $show)
  {
    try {
      $this->showFooter = $show;
      return $this;
    } catch (Exception $e) {
      throw $e;
    }
  }

  protected function getShowFooter(): string
  {
    try {
      return $this->showFooter;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function preventRetorna($state = true)
  {
    $this->preventRetorna = $state;
  }

  public function getPreventRetorna()
  {
    return $this->preventRetorna;
  }

  /**
   * metodo de retorno das actions
   * 
   * @access protected 
   */

  public function retorna($Exception = null)
  {
    try {

      // header("Content-Type: application/json");

      if ($Exception === null) {
        header("Content-Type: application/json");
        header("HTTP/1.1 200 OK");
        http_response_code(200);
        echo json_encode($this->data);
        return;
      }

      $this->retorno['erro']         = true;
      $this->retorno['mensagem']     = 'Ocorreu um erro ao executar a operação , favor informar o setor de TI apresentando a mensagem: ' . $Exception->getMessage();
      // $this->retorno['data']         = [];
      // $this->retorno['userMensagem'] = $Exception->getMessage();

      if (!$Exception->getCode()) {
        http_response_code(500);
      } else {
        http_response_code($Exception->getCode());
      }

      if (in_array($Exception->getCode(), [-1, 400, 401, 500, 405])) {
        $this->retorno['mensagem']     = $Exception->getMessage();
      }

      if ($Exception->getCode() === 404) {
        $this->setClassDivContainer("container-fluid p-0")
          ->setTituloPagina("Página não encontrada")
          ->render("404");

        return;
      }

      header("Content-Type: application/json");
      echo json_encode($this->retorno);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * método de retorno de um arquivo PDF salvo no servidor
   * 
   * @access protected 
   * @param caminho_completo: caminho do arquivo salvo no servidor
   * @param nome_do_arquivo: nome a ser salvo no computador do usuário
   */
  protected function retornaPdf($caminho_completo, $nome_do_arquivo)
  {
    try {
      $content = file_get_contents($caminho_completo);
      if ($content == false) {
        throw new \Exception('Arquivo não encontrado');
      }
      header("Cache-Control: maxage=1");
      header("Pragma: public");
      header("Content-type: src/pdf");
      header("Content-Disposition: inline; filename=" . $nome_do_arquivo);
      header("Content-Description: PHP Generated Data");
      header("Content-Transfer-Encoding: binary");
      if (getenv('src_ENV') == 'local') {
        ob_clean(); // Esse cara é importante , limpa a saída pra não aparecer sujeira no arquivo
      }
      echo $content;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * metodo de retorno de um arquivo JPG salvo no servidor
   * 
   * @access protected 
   * @param caminho_completo: caminho do arquivo salvo no servidor
   * @param nome_do_arquivo: nome a ser salvo no computador do usuário
   */
  protected function retornaJpg($caminho_completo, $nome_do_arquivo)
  {
    try {
      $content = file_get_contents($caminho_completo);
      if ($content == false) {
        throw new \Exception('Arquivo não encontrado');
      }
      header("Cache-Control: maxage=1");
      header("Pragma: public");
      header("Content-type: image/jpg");
      header("Content-Disposition: inline; filename=" . $nome_do_arquivo);
      header("Content-Description: PHP Generated Data");
      header("Content-Transfer-Encoding: binary");
      if (getenv('src_ENV') == 'local') {
        ob_clean(); // Esse cara é importante , limpa a saída pra não aparecer sujeira no arquivo
      }
      echo $content;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * metodo de retorno de um arquivo QUALQUER salvo no servidor para download
   * 
   * @access protected 
   * @param caminho_completo: caminho do arquivo salvo no servidor
   * @param nome_do_arquivo: nome a ser salvo no computador do usuário
   * @param novo_nome_arquivo: nome que sobrescreve nome_do_arquivo para salvamento no computador do usuário
   */
  protected function retornaArquivo($nome_do_arquivo, $caminho_completo, $novo_nome_arquivo =  '')
  {
    try {
      $content = file_get_contents($caminho_completo);
      if ($content == false) {
        throw new \Exception('Arquivo não encontrado');
      }
      if ($novo_nome_arquivo != '') {
        $nome_do_arquivo = $novo_nome_arquivo;
      }
      header('Content-Description: File Transfer');
      header('Content-Type: src/octet-stream');
      header("Cache-Control: no-cache, must-revalidate");
      header("Content-Disposition: attachment; filename={$nome_do_arquivo}");
      header('Pragma: public');
      ob_clean(); // Esse cara é importante , limpa a saída pra não aparecer sujeira no arquivo
      echo $content;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * metodo que retorna um arquivo com texto (parametro)
   * 
   * @access protected 
   * @param texto (que estará dentro do arquivo)
   * @param nome_do_arquivo: nome a ser salvo no computador do usuário
   */
  protected function retornaTextoArquivo($texto, $nome_do_arquivo =  '')
  {
    try {
      header('Content-Description: File Transfer');
      header('Content-Type: src/octet-stream');
      header('Content-Disposition: attachment; filename="' . $nome_do_arquivo . '"');
      ob_clean(); // Esse cara é importante , limpa a saída pra não aparecer sujeira no arquivo
      echo $texto;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * metodo que gera uma string randomica
   * 
   * @access protected 
   * @param quantidade_caracteres (int)
   */
  protected function randomString($quantidade_caracteres = 12)
  {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = '';
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $quantidade_caracteres; $i++) {
      $n = rand(0, $alphaLength);
      $pass .= $alphabet[$n];
    }
    return $pass;
  }


  /**
   * Metodo que elimina qualquer tentativa de SQL injection pelo usuário (em recursão)
   * 
   * @access protected 
   * @param request
   */
  protected function eliminaSQLInjection($request)
  {
    try {
      return $this->percorrePalavras($request);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Metodo recursão para percorrer palavra a palvra para remover sql injection
   * 
   * @access private
   * @param parametro 
   */
  private function percorrePalavras($parametro)
  {
    try {
      //Verifico se o parametro é um array , caso for , faço recurção
      if (empty($parametro)) {
        return $parametro;
      }

      if (is_array($parametro)) {
        foreach ($parametro as $key => $value) {
          $parametro[$key] = $this->percorrePalavras($value);
        }
      } else {
        //Aqui ja é quando o parâmetro não é um array
        //Faço o escape comum do mysqly e anti sql para parametros normais ou json
        if (is_string($parametro)) {
          $temp_param = json_decode($parametro, true);
          if (is_array($temp_param)) {
            if (!empty($temp_param)) {
              foreach ($temp_param as $key => $value) {
                $temp_param[$key] = $this->percorrePalavras($value);
              }
              $parametro = json_encode($temp_param);
            }
          } else {
            $parametro = $this->masterMysqli->real_escape_string($parametro);
            if ($this->preventXss) {
              $parametro = htmlspecialchars($parametro, ENT_QUOTES, 'UTF-8');
            }
          }
        } else {
          $parametro = $this->masterMysqli->real_escape_string($parametro);
          if ($this->preventXss) {
            $parametro = htmlspecialchars($parametro, ENT_QUOTES, 'UTF-8');
          }
        }
        //Dicionário de dados das palavras não desejadas
        $dicionario = array('select', 'insert', 'update', 'delete', 'drop', 'truncate', 'create', 'function', 'view', 'trigger', 'procedure', 'database', 'exists', 'alter', "use");
        $parametro =  explode(' ',  $parametro);
        //Removo todos os textos que for encontrado similares ao dicionário de palavras
        foreach ($parametro as $key => $value) {
          if (in_array(strtolower($value), $dicionario)) {
            unset($parametro[$key]);
          }
        }
        $parametro = implode(" ", $parametro);
      }
      return $parametro;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Metodo recursão fazer o real_escape_string em arrays
   * 
   * @access private
   * @param parametro 
   */
  protected function recursivoEscapeString($bind)
  {
    try {
      if (is_array($bind)) {
        foreach ($bind as $key => $value) {
          $bind[$key] = $this->recursivoEscapeString($value);
        }
      } else {
        $bind = $this->masterMysqli->real_escape_string($bind);
      }
      return $bind;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  /**
   * Metodo para filtrar possiveis problemas no insert do alerta
   * 
   * @access private
   * @param string a ser filtrada
   */
  protected function filtroMaster($string)
  {
    return $this->masterMysqli->real_escape_string(iconv(mb_detect_encoding($string, mb_detect_order(), true), "UTF-8//IGNORE", $string));
  }

  protected function validateToken($token)
  {
    try {
      decrypt($token);
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
