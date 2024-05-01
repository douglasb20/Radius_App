<?php

namespace App\Model;

class UsersDAO extends \Core\Defaults\DefaultModel{
    public $tabela = 'users';

    public function ValidateUser($id){
        try{
            
            $query = "SELECT * FROM {$this->tabela} WHERE";
            $query .= " id = {$id} ";

            return $this->executeQuery($query);
            
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function UserCustom($id=null){
        try{
            
            $query  = "SELECT user_fullname, user_funcao FROM {$this->tabela}";
            $query .= isset($id) ? " WHERE id = {$id}" : "";
    
            return $this->executeQuery($query);
            
        }catch(\Exception $e){
            throw $e;
        }
    }

}