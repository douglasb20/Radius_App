<?php

use Core\Router;

function printar($variavel)
{
  try {
    print_r('<pre>');
    print_r($variavel);
    print_r('</pre>');
  } catch (Exception $e) {
    throw $e;
  }
}

function vardump($variavel)
{
  try {
    print_r('<pre>');
    var_dump($variavel);
    print_r('</pre>');
  } catch (Exception $e) {
    throw $e;
  }
}

/*  Por padrão , alguns erros o php não sobe exception. 
*  Esse callbackExceptionError faz com que todos os erros virem exceptions
*/
function callbackExceptionError($errno, $errstr, $errfile, $errline)
{
  $array = [
    "code" => $errno,
    "message" => $errstr,
    "file" => $errfile,
    "line" => $errline,
  ];
  echo json_encode($array);
  die();
  // throw new Exception($errno." - ".$errstr." in ".$errfile." line ". $errline);

}

function toMysqlDate($date, $mostra_segundos = true)
{
  try {
    $retorno = $date;

    if (strlen($date) == 10) {
      $retorno = implode('-', array_reverse(explode('/', $date)));
    } else {

      if ($mostra_segundos == true) {
        $retorno = implode('-', array_reverse(explode('/', substr($date, 0, 10)))) . substr($date, 10, 9);
      } else {
        $retorno = implode('-', array_reverse(explode('/', substr($date, 0, 10)))) . substr($date, 10, 6);
      }
    }

    return $retorno;
  } catch (Exception $e) {
    throw $e;
  }
}

function toBrDate($date, $mostra_segundos = true)
{
  try {
    $retorno = $date;

    if (strlen($date) == 10) {
      $retorno = implode('/', array_reverse(explode('-', $date)));
    } else {

      if ($mostra_segundos == true) {
        $retorno = implode('/', array_reverse(explode('-', substr($date, 0, 10)))) . substr($date, 10, 9);
      } else {
        $retorno = implode('/', array_reverse(explode('-', substr($date, 0, 10)))) . substr($date, 10, 6);
      }
    }

    return $retorno;
  } catch (Exception $e) {
    throw $e;
  }
}

function encrypt($data)
{
  try {
    //Modelo encriptação
    $method = "AES-256-CBC";

    //Chave e IV secretos , não mudam
    $secret_key = '9rv7rg9p3ox26vtr1i91p4j57gp5d6ja';
    $secret_iv  = 'l0mticdy4rm9l472jstnfolseud929j4';

    //Faço um hash dos dois para gerar a chave e o IV
    $key = hash('sha256', $secret_key);

    //Pego apenas 16 bytes do iv senão vai gerar um warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    //Transformo texto em claro para texto cifrado
    $data = openssl_encrypt($data, $method, $key, 0, $iv);
    $data = base64url_encode($data);

    return $data;
  } catch (Exception $e) {
    throw $e;
  }
}

function decrypt($data)
{
  try {
    $data = base64url_decode($data);

    //Modelo encriptação
    $method = "AES-256-CBC";

    //Chave e IV secretos , não mudam
    $secret_key = '9rv7rg9p3ox26vtr1i91p4j57gp5d6ja';
    $secret_iv  = 'l0mticdy4rm9l472jstnfolseud929j4';

    //Faço um hash dos dois para gerar a chave e o IV
    $key = hash('sha256', $secret_key);

    //Pego apenas 16 bytes do iv senão vai gerar um warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    //Transformo cifrado para texto em claro

    $data = openssl_decrypt($data, $method, $key, 0, $iv);

    return $data;
  } catch (Exception $e) {
    throw $e;
  }
}

function encrypt_string($data)
{
  try {
    //Modelo encriptação
    $method = "AES-256-CBC";

    //Chave e IV secretos , muda a cada dia

    $secret_key = gmdate('y') . 'c09275cbd16a911a00c3a077e36f379b';
    $secret_iv = gmdate('y') . '549d0aad4f261463b179c94c2ea3c736';

    //Faço um hash dos dois para gerar a chave e o IV
    $key = hash('sha256', $secret_key);

    //Pego apenas 16 bytes do iv senão vai gerar um warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    //Transformo texto em claro para texto cifrado
    $data = openssl_encrypt($data, $method, $key, 0, $iv);
    $data = base64url_encode($data);

    return $data;
  } catch (Exception $e) {
    throw $e;
  }
}

function decrypt_string($data)
{
  try {
    $data = base64url_decode($data);

    //Modelo encriptação
    $method = "AES-256-CBC";

    //Chave e IV secretos
    $secret_key = gmdate('y') . 'c09275cbd16a911a00c3a077e36f379b';
    $secret_iv = gmdate('y') . '549d0aad4f261463b179c94c2ea3c736';

    //Faço um hash dos dois para gerar a chave e o IV
    $key = hash('sha256', $secret_key);

    //Pego apenas 16 bytes do iv senão vai gerar um warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    //Transformo cifrado para texto em claro

    $data = openssl_decrypt($data, $method, $key, 0, $iv);

    return $data;
  } catch (Exception $e) {
    throw $e;
  }
}

function base64url_encode($data)
{
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data)
{
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function removerCaracteresEspeciais($a, $espaco = "s")
{
  try {
    $retirar = array('-', '.', "\\", "/", "'", "`", "'", "(", ")", "|", '–', '´');
    if ($espaco == "s") $retirar[] = " ";
    return str_replace($retirar, "", $a);
  } catch (Exception $e) {
    throw $e;
  }
}

// FORMATO ENTRADA: ANO[4]-MES[2]-ANO[2]
function somadata($dias, $dataref, $uteis = true)
{ // funcao que soma N $dias na $dataref
  try {
    preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $dataref, $sep);
    if ($sep && is_array($sep)) {
      $dia = $sep[3];
      $mes = $sep[2];
      $ano = $sep[1];
    } else {
      return "N/A";
    }

    $i = $dias;

    for ($i = 0; $i < $dias; $i++) {

      if ($mes == "01" || $mes == "03" || $mes == "05" || $mes == "07" || $mes == "08" || $mes == "10" || $mes == "12") {
        if ($mes == 12 && $dia == 31) {
          $mes = 01;
          $ano++;
          $dia = 00;
        }
        if ($dia == 31 && $mes != 12) {
          $mes++;
          $dia = 00;
        }
      } // fecha if geral

      if ($mes == "04" || $mes == "06" || $mes == "09" || $mes == "11") {
        if ($dia == 30) {
          $dia = 00;
          $mes++;
        }
      } // fecha if geral

      if ($mes == "02") {
        if ($ano % 4 == 0 && $ano % 100 != 0) { // ano bissexto
          if ($dia == 29) {
            $dia = 00;
            $mes++;
          }
        } else {
          if ($dia == 28) {
            $dia = 00;
            $mes++;
          }
        }
      } // fecha if fevereiro
      $dia++;

      if ($uteis) {
        if (date('l', strtotime($dia . "-" . $mes . "-" . $ano)) == "Saturday") $dias++;
        if (date('l', strtotime($dia . "-" . $mes . "-" . $ano)) == "Sunday") $dias++;
      }
    } // fecha o for()

    if (strlen($dia) == 1) {
      $dia = "0" . $dia;
    }; // Coloca um zero antes
    if (strlen($mes) == 1) {
      $mes = "0" . $mes;
    };

    $nova_data = $ano . "-" . $mes . "-" . $dia;

    if (strlen($dataref) == 19) {
      $nova_data = $nova_data . substr($dataref, 10, 9);
    }
    return $nova_data;
  } catch (Exception $e) {
    throw $e;
  }
}

function removeAcento($string, $espaco = true)
{
  //converting any accent of string
  $a = array(
    "[Â]" => "A",
    "[À]" => "A",
    "[Á]" => "A",
    "[Ã]" => "A",
    "[Ä]" => "A",
    "[â]" => "a",
    "[à]" => "a",
    "[á]" => "a",
    "[ã]" => "a",
    "[ä]" => "a",
    "[Ê]" => "E",
    "[È]" => "E",
    "[É]" => "E",
    "[Ë]" => "E",
    "[ê]" => "e",
    "[è]" => "e",
    "[é]" => "e",
    "[ë]" => "e",
    "[Î]" => "I",
    "[Ì]" => "I",
    "[Í]" => "I",
    "[Ï]" => "I",
    "[î]" => "i",
    "[ì]" => "i",
    "[í]" => "i",
    "[ï]" => "i",

    "[Ô]" => "O",
    "[Õ]" => "O",
    "[Ò]" => "O",
    "[Ó]" => "O",
    "[Ö]" => "O",
    "[ô]" => "o",
    "[õ]" => "o",
    "[ò]" => "o",
    "[ó]" => "o",
    "[ö]" => "o",
    "[Û]" => "U",
    "[Ù]" => "U",
    "[Ú]" => "U",
    "[Ü]" => "U",
    "[û]" => "u",
    "[ú]" => "u",
    "[ù]" => "u",
    "[ü]" => "u",
    "[š]" => "s",
    "[ç]" => "c",
    "[Ç]" => "C"
  );
  if ($espaco) {
    $a['[ ]'] = "_";
  }

  return preg_replace(array_keys($a), array_values($a), $string);
}


function validaData($date, $format = 'd/m/Y')
{
  $d = DateTime::createFromFormat($format, $date);
  // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
  return $d && $d->format($format) === $date;
}

// Função que retorna o último digito válido pela regra do EAN.
function retornaEANvalido($digits)
{
  if ($digits == 'SEM GTIN') {
    return '';
  }
  $digits = (string)$digits;
  if (strlen($digits) == 13) {
    $digits = substr($digits, 0, 12);
  }
  if (strlen($digits) != 12) {
    return "EAN Inválido";
  }

  $even_sum = $digits[1] + $digits[3] + $digits[5] + $digits[7] + $digits[9] + $digits[11];
  // 2. Multiply this result by 3.
  $even_sum_three = $even_sum * 3;
  // 3. Add the values of the digits in the odd-numbered positions: 1, 3, 5, etc.
  $odd_sum = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8] + $digits[10];
  // 4. Sum the results of steps 2 and 3.
  $total_sum = $even_sum_three + $odd_sum;
  // 5. The check character is the smallest number which, when added to the result in step 4,  produces a multiple of 10.
  $next_ten = (ceil($total_sum / 10)) * 10;
  $check_digit = $next_ten - $total_sum;

  return $digits . $check_digit;
}

/**
 * Função de tratamento de erros do sistema
 */
function handler($errNo, $errMsg, $errFile, $errLine)
{
  // header('HTTP/1.1 200 Ok');
  // header('Content-type: application/json');

  // $message = "Erro: " . $errMsg . ". arquivo: " . $errFile ." linha ". $errLine;

  // // $dados = [
  // //     'arquivo'       => $errFile,
  // //     'linha'         => $errLine,
  // //     'mensagem'      => $errMsg,
  // //     'erro_text'     => $message,
  // //     'code_http'     => 500,
  // //     'data_hora'     => Date('Y-m-d H:i:s'),
  // //     'id_usuario'    => $_SESSION['userId'],
  // //     'code'          => $errNo,
  // //     'id_log'        => empty($_SESSION['id_log']) ? null : $_SESSION['id_log']
  // // ];

  // // $GLOBALS['_DB_PROFILER'] = preg_replace('/\s\s+/', ' ', $GLOBALS['_DB_PROFILER']);
  // // $retorno['lastSql'] = $GLOBALS['_DB_PROFILER'];

  // //$retorno['id_log_user']     = empty($_SESSION['id_log']) ? null : $_SESSION['id_log'];
  // $retorno['erro']            = true;
  // $retorno['erroMensagem']    = $message;
  // $retorno['mensagem']        = $message;
  // // $retorno['dados_erro']      = $dados;
  // $retorno['codeResponse']    = 500;
  // http_response_code(500);

  // echo json_encode($retorno);
  // die();
}

function convertDateToScreen($data, $separator = "/")
{
  //convert date to screen with separator in day/month/year format
  $data = substr($data, 0, 10);

  if (strstr($data, "/")) {
    $d = explode("/", $data);
  } elseif (strstr($data, "-")) {
    $d = explode("-", $data);
  } else
    return FALSE;

  if (strlen($d[2]) == 4) { // data is day/month/year format
    return  $d[0] . $separator . $d[1] . $separator . $d[2];
  } elseif (strlen($d[0]) == 4) { // data is year/month/day format
    return $d[2] . $separator . $d[1] . $separator . $d[0];
  } else
    return FALSE;
}

function mask($val, $mask)
{
  $maskared = '';
  $val = (string) $val;
  $k = 0;
  for ($i = 0; $i <= strlen($mask) - 1; $i++) {
    if ($mask[$i] == '#') {
      if (isset($val[$k]))
        $maskared .= $val[$k++];
    } else {
      if (isset($mask[$i]))
        $maskared .= $mask[$i];
    }
  }
  return $maskared;
}

function array_find($array, $search, $column)
{
  $a   = array_column($array, $column);
  $k   = array_search($search, $a);

  $ret = $array[$k];

  return $ret;
}

function base64UrlEncode($text)
{
  return str_replace(
    ['+', '/', '='],
    ['-', '_', ''],
    base64_encode($text)
  );
}

function AllSessao()
{
  return $_SESSION;
}

function SetSessao(string $key, string|null $value)
{
  $_SESSION[$key] = $value;
}

function GetSessao(string $key)
{
  return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
}

function removeSessao(string $key)
{
  unset($_SESSION[$key]);
}

function clearSessao()
{
  session_destroy();
}

function route()
{
  return new Router;
}

function render(?string $rota = null, ?array $data = [])
{
  return (new \Core\Defaults\DefaultController(false))->render($rota, $data);
}

function setTitle(?string $titulo)
{
  return (new \Core\Defaults\DefaultController(false))->setTituloPagina($titulo);
}

function getRequest()
{
  return (new \Core\Defaults\DefaultController(false))->getRequest();
}

function getQuery(?string $key = null)
{
  return (new \Core\Defaults\DefaultController(false))->getQuery($key);
}

function bitconverter($valor, $csdecimal = 2)
{
  $byte = 1024;
  $divisao = 1;
  $sigla = ' bytes';
  if ($valor >= $byte ** 1 && $valor <= $byte ** 2) {
    $sigla = " KB";
    $divisao = $byte ** 1;
  } else if ($valor >= $byte ** 2 && $valor <= $byte ** 3) {
    $sigla = " MB";
    $divisao = $byte ** 2;
  } else if ($valor >= $byte ** 3) {
    $sigla = " GB";
    $divisao = $byte ** 3;
  }

  if (!isset($valor) || $valor === "0") {
    $sigla = " bps";
    return number_format($valor, $csdecimal, ".", "") . $sigla;
  } else {
    return number_format(($valor / $divisao), $csdecimal, ".", "") . $sigla;
  }
}

function fileTime($file)
{
  return filemtime(ROOT_PATH . $file);
}

function getTimeExecution(string | DateTime $start): string
{
  $fim        = date('Y-m-d H:i:s');

  $timeInicio = strtotime($start);
  $timeFim    = strtotime($fim);

  return gmdate("H:i:s", $timeFim - $timeInicio);
}

function validaCPF($cpf)
{

  // Extrai somente os números
  $cpf = preg_replace('/[^0-9]/is', '', $cpf);

  // Verifica se foi informado todos os digitos corretamente
  if (strlen($cpf) != 11) {
    return false;
  }

  // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
  if (preg_match('/(\d)\1{10}/', $cpf)) {
    return false;
  }

  // Faz o calculo para validar o CPF
  for ($t = 9; $t < 11; $t++) {
    for ($d = 0, $c = 0; $c < $t; $c++) {
      $d += $cpf[$c] * (($t + 1) - $c);
    }
    $d = ((10 * $d) % 11) % 10;
    if ($cpf[$c] != $d) {
      return false;
    }
  }
  return true;
}
