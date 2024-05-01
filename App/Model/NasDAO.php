<?php

namespace App\Model;

class NasDAO extends \Core\Defaults\DefaultModel{
    public $tabela = 'nas';

    public function qtdNasAtivos($id_emp){
        try{
            $query = "SELECT count(*) as qtdConcentradoraAtiva FROM {$this->tabela} WHERE status=1 AND id_emp = {$id_emp} ";

            return $this->executeQuery($query);
        }catch(\Exception $e){
            throw $e;
        }
    }
}

?>