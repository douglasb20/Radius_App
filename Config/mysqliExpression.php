<?php

class MysqliExpression
{
    public $sql;

    function __construct($sql)
    {
        try {

            $this->sql = $sql;

        } catch (Exception $e) {
            throw $e;
        }
    }

    function getSql(){
        try {
            return $this->sql;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>
