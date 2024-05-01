<?php

namespace Core\Defaults;
use Exception;

abstract class DefaultModel{
    
    public $tabela;
    public $id;
    public $db;
    private $masterMysqli;

    /*
        Métodos de Banco de Dados - construtor conectando ao MySQL, escape, getLastIdInserted, executeQuery, prepareItemToSql
    */ 
    
    function __construct($masterMysqli){
        try {

            $this->masterMysqli = $masterMysqli;
            
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function escapeString($string)
    {
        try{
            return $this->masterMysqli->real_escape_string($string);
        } catch (Exception $e) {
            throw $e;
        }
    
    }
    public function getLastIdInserted()
    {
        try{
            return $this->masterMysqli->insert_id;
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function affectedRows()
    {
        try{
            return $this->masterMysqli->affected_rows;
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function executeQuery($query){
        try{

            //jogo no profiler a query
            // array_push($GLOBALS['_DB_PROFILER'],$query);

            $response = [];

            $result = $this->masterMysqli->query($query);

            if(gettype($result) =='object'){
                while ($data = $result->fetch_assoc()){
                    array_push($response, $data);
                }
            }

            return $response;

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getAll( $where = '', $order = '', $limit = '', $offset = '',$force_index = ''){

        try{

            $query = "SELECT * FROM ".$this->tabela;

            if(!empty($force_index)){
                $query .= " FORCE INDEX($force_index)";
            }

            $where != '' ? $query .= ' WHERE '. $where : $query ;

            $order != '' ? $query .= ' ORDER BY '.$order : $query ;

            $limit != '' ? $query .= ' LIMIT '.$limit : $query ;

            $offset != '' ? $query .= ' OFFSET '.$offset : $query ;

            $response = $this->executeQuery($query);

            return $response;
        
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getOne( $where = '', $order = '', $limit = '', $offset = '',$force_index = ''){

        try{

            $query = "SELECT * FROM ".$this->tabela;

            if(!empty($force_index)){
                $query .= " FORCE INDEX($force_index)";
            }

            $where != '' ? $query .= ' WHERE '. $where : $query ;

            $order != '' ? $query .= ' ORDER BY '.$order : $query ;

            $limit != '' ? $query .= ' LIMIT '.$limit : $query ;

            $offset != '' ? $query .= ' OFFSET '.$offset : $query ;

            $response = $this->executeQuery($query);
            
            if(empty($response)){
                return false;
            }

            return $response[0];
        
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getView( $where = '', $order = '', $limit = '', $offset = '',$force_index = ''){

        try{

            $query = "SELECT * FROM vw".$this->tabela;

            if(!empty($force_index)){
                $query .= " FORCE INDEX($force_index)";
            }

            $where != '' ? $query .= ' WHERE '. $where : $query ;

            $order != '' ? $query .= ' ORDER BY '.$order : $query ;

            $limit != '' ? $query .= ' LIMIT '.$limit : $query ;

            $offset != '' ? $query .= ' OFFSET '.$offset : $query ;

            $response = $this->executeQuery($query);

            return $response;
        
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getAllIdConcat ($column, $where = ''){

        try{

            $this->executeQuery('SET SESSION group_concat_max_len = 4294000000;');
            
            $query = " SELECT GROUP_CONCAT($column) as '$column'
                    FROM ".$this->tabela;

            $where != '' ? $query .= ' WHERE '. $where : $query ;

            $response = $this->executeQuery($query);

            return $response;
        
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function insert($params){
        
        try{

            $query = $this->prepareInsert($params);

            $response = $this->executeQuery($query);
            
            return $this->getLastIdInserted();
    
        } catch (Exception $e) {
            throw $e;
        }
    
    }

    /*
        Atenção , não usar em tabelas com mais de um indice único ou chave primaria
    */

    public function insertUpdate($params){
        
        try{

            $query = $this->prepareInsertUpdate($params);

            $response = $this->executeQuery($query);
            
            return $this->getLastIdInserted();
    
        } catch (Exception $e) {
            throw $e;
        }
    
    }

    public function insertIgnore($params){
        
        try{

            $query = $this->prepareInsertIgnore($params);

            $response = $this->executeQuery($query);

            if($this->affectedRows()>0){
                return $this->getLastIdInserted();
            }
            else return 0;
    
        } catch (Exception $e) {
            throw $e;
        }
    
    }

    public function insertMultiploIdentico($params, $quantidade_de_vezes){
        
        try{

            $query = $this->prepareInsertMultiploIdentico($params, $quantidade_de_vezes);
            $response = $this->executeQuery($query);

            if($this->affectedRows()>0){
                return $this->getLastIdInserted();
            }
            else return 0;
    
        } catch (Exception $e) {
            throw $e;
        }
    
    }

    public function insertMultiplo($params, $ignore=false){
        
        try{

            $query = $this->prepareInsertMultiplo($params, $ignore);
            $response = $this->executeQuery($query);

            if($this->affectedRows()>0){
                return $this->getLastIdInserted();
            }
            else return 0;
    
        } catch (Exception $e) {
            throw $e;
        }
    
    }

    public function insertUpdateMultiplo($params){
        
        try{

            $query = $this->prepareInsertUpdateMultiplo($params);
            $response = $this->executeQuery($query);

            if($this->affectedRows()>0){
                $last_id = $this->getLastIdInserted();
            }else{
                $last_id = 0;
            }

            return $last_id;
    
        } catch (Exception $e) {
            throw $e;
        }
    
    }

    public function update($params, $where = ''){
        try{

            if(empty($where)){
                throw new Exception('UPDATE sem WHERE no banco de dados',-1);
            }
            
            $query =  $this->prepareUpdate($params, $where);

            $response = $this->executeQuery($query);
            
            return [];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateForceIndex($params, $where = ''){
        try{

            if(empty($where)){
                throw new Exception('UPDATE sem WHERE no banco de dados',-1);
            }
            
            $query =  $this->prepareUpdateForceIndex($params, $where);

            $response = $this->executeQuery($query);
            
            return [];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete($where = ''){

        try{

            if(empty($where)){
                throw new Exception('DELETE sem WHERE no banco de dados',-1);
            }

            $query =  "DELETE FROM ".$this->tabela." WHERE ".$where;

            $response = $this->executeQuery($query);
            
            return [];
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function count($where = ''){

        try{
            $query =  "SELECT COUNT(*) as 'count' 
                    FROM ".$this->tabela;

            $where != '' ? $query .= ' WHERE '. $where : $query ;

            $response = $this->executeQuery($query);
            
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function sum($column,$where = ''){

        try{
            $query =  "SELECT SUM(".$column.") as 'sum' 
                    FROM ".$this->tabela;

            $where != '' ? $query .= ' WHERE '. $where : $query ;

            $response = $this->executeQuery($query);
            
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function max($column,$where = ''){

        try{
            $query =  "SELECT MAX(".$column.") as 'max' 
                    FROM ".$this->tabela;

            $where != '' ? $query .= ' WHERE '. $where : $query ;

            $response = $this->executeQuery($query);
            
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function min($column,$where = ''){

        try{
            $query =  "SELECT MIN(".$column.") as 'min' 
                    FROM ".$this->tabela;

            $where != '' ? $query .= ' WHERE '. $where : $query ;

            $response = $this->executeQuery($query);
            
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getAllYear($coluna_tabela){

        try{

            $query =  " SELECT DISTINCT(EXTRACT(YEAR FROM ".$coluna_tabela.")) as ano
                        FROM ".$this->tabela."
                        ORDER BY ".$coluna_tabela." ASC;";
            
            return $this->executeQuery($query);

        } catch (Exception $e) {
            throw $e;
        }
    
    }

    public function lock($tabelas){

        try{

            $query = "LOCK TABLE ".$tabelas.";";

            $response = $this->executeQuery($query);

            return $response;
        
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function unlock(){

        try{

            $query = "UNLOCK TABLES";

            $response = $this->executeQuery($query);

            return $response;
        
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    function prepareInsert($params){

        try{
            $params =  $this->prepareParams($params);
            
            $campos = implode('`,`',array_keys($params));
            $valores = implode(',',$params);

            return "INSERT INTO ".$this->tabela." (`".$campos."`)"." VALUES (".$valores.")";
        } catch (Exception $e) {
            throw $e;
        }
    }

    function prepareInsertUpdate($params){

        try{
            $params =  $this->prepareParams($params);

            $arrayParams = [];
            foreach($params as $key=>$value){
                array_push($arrayParams,'`'.$key."` = ".$value);
            }
            
            $campos = implode('`,`',array_keys($params));
            $valores = implode(',',$params);
            
            return "INSERT INTO ".$this->tabela." (`".$campos."`)"." VALUES (".$valores.") ON DUPLICATE KEY UPDATE ".implode(',',$arrayParams);
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    
    function prepareInsertIgnore($params){

        try{
            $params =  $this->prepareParams($params);
            
            $campos = implode('`,`',array_keys($params));
            $valores = implode(',',$params);

            return "INSERT IGNORE INTO ".$this->tabela." (`".$campos."`)"." VALUES (".$valores.")";
        } catch (Exception $e) {
            throw $e;
        }
    }

    function prepareInsertMultiploIdentico($params, $quantidade_de_vezes){

        try{
            $params =  $this->prepareParams($params);
            
            $campos = implode('`,`',array_keys($params));
            $valores = '('.implode(',',$params).')';

            $query = "INSERT INTO ".$this->tabela." (`".$campos."`)"." VALUES ".$valores;
            for ($i=1; $i < $quantidade_de_vezes ; $i++) { 
                $query .= ', '.$valores;
            }
            return $query;

        } catch (Exception $e) {
            throw $e;
        }
    }


    //params vem array de binds
    function prepareInsertMultiplo($params, $ignore){
        try{
            $dados = [];
            foreach ($params as $kp => $param) {
                $dados[] =  $this->prepareParams($param);
                unset($params[$kp]);
            }
            $campos = array_keys($dados[0]);
            $valores = [];
            //validacao
            foreach ($dados as $item_insert){
                foreach ($campos as $c) {
                    if(!isset($item_insert[$c])){
                        throw new Exception("Array de inserts inválido. Um dos itens não tem campo ".$c, 1);
                    }
                }
                $valores[] = '('.implode(',',$item_insert).')';               
            }

            if($ignore){
                $query = "INSERT IGNORE INTO ";
            }
            else{
                $query = "INSERT INTO ";
            }

            $query .= $this->tabela." (`".implode('`,`',$campos)."`)"." VALUES ".implode(',',$valores);
            return $query;

        } catch (Exception $e) {
            throw $e;
        }
    }

    function prepareInsertUpdateMultiplo($params){
        try{
            $dados = [];

            foreach ($params as $kp => $param) {
                $dados[] =  $this->prepareParams($param);
                unset($params[$kp]);
            }

            $campos = array_keys($dados[0]);
            $valores = [];

            //validacao
            foreach ($dados as $item_insert){
                foreach ($campos as $c) {
                    if(!isset($item_insert[$c])){
                        throw new Exception("Array de inserts inválido. Um dos itens não tem campo ".$c, 1);
                    }
                }
                
                $valores[] = '('.implode(',',$item_insert).')';               
            }

            // update_statement
            
            $update_statement = [];

            foreach ($campos as $c) {
                $update_statement[] =  "$c = VALUES($c)";
            }

            $query = "INSERT INTO ".$this->tabela." (`".implode('`,`',$campos)."`)"." VALUES ".implode(',',$valores)." ON DUPLICATE KEY UPDATE ".implode(',',$update_statement).';';
            
            return $query;

        } catch (Exception $e) {
            throw $e;
        }
    }

    function prepareUpdate($params,$where = ''){
        try{
            $params =  $this->prepareParams($params);

            $arrayParams = [];
            foreach($params as $key=>$value){
                array_push($arrayParams,'`'.$key."` = ".$value);
            }

            if($where !=''){
                $where = " WHERE ".$where;
            }

            return "UPDATE ".$this->tabela." SET ".implode(',',$arrayParams).$where;
        } catch (Exception $e) {
            throw $e;
        }
    }

    function prepareUpdateForceIndex($params,$where = ''){
        try{
            $params =  $this->prepareParams($params);

            $arrayParams = [];
            foreach($params as $key=>$value){
                array_push($arrayParams,'`'.$key."` = ".$value);
            }

            if($where !=''){
                $where = " WHERE ".$where;
            }

            return "UPDATE ".$this->tabela.", (SELECT 1) AS dummy SET ".implode(',',$arrayParams).$where;
        } catch (Exception $e) {
            throw $e;
        }
    }

    function prepareParams($params){
        try{
            //$params = array_filter($params, function($value) { return $value !== ''; });
            
            foreach($params as $key=>$value){

                if(is_object($value)){
                    //MysqliExpression object
                    $params[$key] = $value->getSql();
                }else{
                    if(is_null($value)){
                        $params[$key] = "null";
                    }else{
                        if (!is_numeric($value)){

                            $value = str_replace(['\r','\n'],PHP_EOL,$value); // Correção textarea breakline
                            $value = str_replace(['\\r','\\n'],PHP_EOL,$value); // Correção textarea breakline
                            $value = str_replace(['\\\r','\\\n'],PHP_EOL,$value); // Correção textarea breakline

                            $value = str_replace('\\','',$value); // Retira escape para evitar double escape

                            $params[$key] = "'".$this->converteData($this->masterMysqli->real_escape_string($value))."'"; 
                            
                        }else{
                            $params[$key] = "'".$value."'"; 
                        }
                    }     
                }
            
            }
            
            return $params;
        } catch (Exception $e) { 
            throw $e;
        }
    }

    function converteData($valor){
        try{
            $dateParser = \DateTime::createFromFormat("d/m/Y", $valor);
        
            if($dateParser){
                $valor = $dateParser->format('Y-m-d'); 
            }

            return $valor;
        } catch (Exception $e) {
            throw $e;
        }
    }

    function whereGenerator($params){
        
        $retorno = ['1=1'];

        if(!empty($params)){
            foreach($params as $key=>$value){

                if ($value!=""){
                    $where = '';

                    $filtro = explode(':', $key);
    
                    $coluna = $filtro[0];
                    $tipo_campo = $filtro[1];

                    

                    if(is_array($value)){
                        $value = implode(',',$value);
                    }
                    
                    switch ($tipo_campo) {
                        case 'int':
                        case 'float':
    
                        $where .=  $coluna." = ".$value;
                            break;
                        case 'varchar':
                        $where .=  $coluna." like('%".$value."%')";
                            break;
                        case 'date':
                            $where .=  $coluna." ='".$this->converteData($value)."'";
                            break;

                        case 'intin':
                            $where .=  $coluna."  IN (".$value.")";
                            break;
                    }
    
                    $retorno[] =  $where;
                } 
            }
        }

        return implode(' AND ',$retorno);
    }


}
?>
