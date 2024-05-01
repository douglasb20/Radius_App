<?php

namespace Core\Defaults;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


global $_ID_USUARIO;

$_ID_USUARIO = 2;


abstract class DefaultCronController{

    public $flag_salva_log = 1;
    public $array_ignorar_log =  [
    ];

    function __construct(){
        try{

            $this->masterMysqli = $GLOBALS['_DB_MYSQLI'];
            $this->id_usuario   = $GLOBALS['_ID_USUARIO'];

            $this->iniciaModels();
            // $this->salvaLogController();
        
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
    * Inicia todos os models. Define spl_autoload nas classes e api's
    * 
    * @access private
    * @link construct
    */
    private function iniciaModels(){
        try{
            
            // Inicia Models / Daos
            $all_model = array_slice(scandir(ROOT_PATH.'/'. MODEL_PATH),2);
            foreach($all_model as $model) {
                $model              = ucfirst($model);
                $model              = str_replace('.php','',$model);
                $modelWithNamespace = 'App\\Model\\' . $model;
                
                $this->$model = new $modelWithNamespace($this->masterMysqli);
            }
            spl_autoload_register( array($this,"pathsToAutoload") );
        }catch(\Exception $e){
            throw $e;
        }
    }


        /**
    * Metodo para salvar log com objetivo de medir desempenho do sistema. Tem que chamar antes de processaRequest pois usa GET/POST.
    * 
    * @access private 
    */
    private function salvaLogController(){
        try {
            /*
            Temos um log de controller , precisava externalizar em algo fora do default controller e chamar aqui
            */
            if($this->flag_salva_log){
                $bind_log_controller = ['controller' => ''];
                if(!isset($_SERVER['REQUEST_URI'])){
                    $bind_log_controller['server'] = json_encode($_SERVER);
                    if(isset($_SERVER['PHP_SELF'])){
                        $bind_log_controller['controller'] = $_SERVER['PHP_SELF'];
                    }
                    elseif(isset($_SERVER['SCRIPT_NAME'])){
                        $bind_log_controller['controller'] = $_SERVER['SCRIPT_NAME'];
                    }
                }
                else{
                    $bind_log_controller['controller'] = $_SERVER['REQUEST_URI'];
                }
                if(!in_array($bind_log_controller['controller'],$this->array_ignorar_log)){                             
                    $this->id_log_controller = $this->logControllerDAO->insert($bind_log_controller);
                }
            }
        } catch (Exception $e) {
            throw $e;
            $this->salvaLogArquivo('log_controller_erro.log',$e->getMessage());
        }
    }

    /**
    * Salvar log em arquivo
    * 
    * @access public 
    * @param nome_arquivo (de log)
    * @param texto_log (a ser salvo no log)
    */
    public function salvaLogArquivo($nome_arquivo,$texto_log){
        try {
            if(!empty($nome_arquivo) && !empty($texto_log)){
                if(getenv('APPLICATION_ENV') === 'local'){
                    $caminho_logs = $_SERVER['DOCUMENT_ROOT']."/logs/";
                }else{
                    $caminho_logs = '/var/log/webhook_portal/';
                }
                $arquivo = fopen($caminho_logs.$nome_arquivo, "a");
                fwrite($arquivo, date('Y-m-d H:i:s').' - '.$texto_log.PHP_EOL);
                fclose($arquivo);
            }
        } catch (Exception $e) {
        //silence
        }
    }
    /**
    * Aqui ficam os caminhos para o spl_autoload_register puxar automaticamente os daos e classes e apis (e subdiretorios).
    * 
    * @access protected 
    * @param qualquerClass (string): classe/api a ser importada
    */
    protected function pathsToAutoload () {
        try{
            $models = glob(ROOT_PATH.'/'. MODEL_PATH.'/*');
            foreach ($models as  $model) {
                include_once $model;
            }
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
    * Metodo para gerar um alerta no portal de uma Exception
    * 
    * @access protected
    * @param id_alerta_tipo: id do tipo de alerta
    * @param array_replace: array com chaves e valores que serão substituídos no alerta
    */
    public function geraAlertaException($exception){
        try{
            $mensagem  = $this->filtroMaster(substr($exception->__toString(),0,999));
            $alerta_tipo = $this->usersAlertaTipoDAO->getAll('id_alerta_tipo = 15')[0];
            $permissoesAreas = [];
            $result = $this->usersAlertaTipoXAreasDAO->getAll('id_alerta_tipo = '.$alerta_tipo['id_alerta_tipo']);
            foreach($result as $value ){
                $permissoesAreas[] = $value['id_permissao'];
            }
            $whereUsuarios = 'id in (SELECT sub.id_usuario
                                    FROM users_alerta_tipo_x_usuario as sub
                                    WHERE sub.id_alerta_tipo = 15)';
            if(!empty($permissoesAreas)){
                $whereUsuarios .= ' OR id_area_cedet IN (' . implode(',', $permissoesAreas) . ')';
                // $whereUsuarios .= ' OR id in (SELECT s.id_usuario
                //                             FROM users_permissao_x_usuario as s
                //                             WHERE s.receber_alertas = 1 AND 
                //                                     s.id_permissao in ('.implode(',',$permissoesAlerta).'))';
            }
            $usuarios = $this->usersDAO->getAll($whereUsuarios);
            foreach($usuarios as $value){
                $alertaRepetido = false;
                if($alerta_tipo['unico'] == 1){ // Envia apenas 1 alerta por dia desse tipo
                    $whereAlertaPendente = "id_alerta_tipo = 15 AND  
                                            id_usuario = ".$value['id']." AND 
                                            visualizado = 0 AND
                                            texto = '".$mensagem."'";
                    $whereAlertaPendente = str_replace('\\\\','',$whereAlertaPendente);
                    $whereAlertaPendente = str_replace('\\n','n',$whereAlertaPendente);
                    $alertaPendente = $this->usersAlertaDAO->getAll($whereAlertaPendente);
                    if(!empty($alertaPendente)){
                        $alertaRepetido = true;
                    }
                }
                if(!$alertaRepetido){
                    $bindAlerta = [ 'id_alerta_tipo'    => 15,
                                    'id_usuario'        => $value['id'],
                                    'texto'             => $mensagem];
                    $this->usersAlertaDAO->insert($bindAlerta);
                }
            
            }
        }catch(Exception $e){
            throw $e;
        }    
    }

    /**
    * Metodo para filtrar possiveis problemas no insert do alerta
    * 
    * @access private
    * @param string a ser filtrada
    */  
    protected function filtroMaster($string){
        return $this->masterMysqli->real_escape_string(iconv(mb_detect_encoding($string, mb_detect_order(), true), "UTF-8//IGNORE", $string));
    }

    
}
?>