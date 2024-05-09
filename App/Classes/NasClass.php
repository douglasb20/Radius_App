<?php

namespace App\Classes;

use App\Exceptions\CommomException;

class NasClass extends \Core\Defaults\DefaultClassController
{
  public \App\Model\NasDAO $NasDAO;

  public function AtualizarNas($fields)
  {
    extract($fields);

    $nas = $this->NasDAO->getOne(" id = {$id}");
    $msgLog = "";

    if ($nas['nasname'] !== $nasname) {
      $msgLog .= " nasname de \"{$nas['nasname']}\" para \"{$nasname}\", ";
    }

    if ($nas['description'] !== $description) {
      $msgLog .= " description de \"{$nas['description']}\" para \"{$description}\", ";
    }

    if ($nas['secret'] !== $secret) {
      $msgLog .= " secret de \"{$nas['secret']}\" para \"{$secret}\", ";
    }

    if ($nas['shortname'] !== $shortname) {
      $msgLog .= " shortname de \"{$nas['shortname']}\" para \"{$shortname}\", ";
    }


    $bindNas = [
      "nasname"               => $nasname,
      "shortname"             => $shortname,
      "description"           => $description,
      "secret"                => $secret,
      "ports"                 => $ports,
    ];

    $this->NasDAO->update($bindNas, "id = {$id} ");

    $this->setContole(trim("Atualizou as informações da Nas ID: {$id}, " . $msgLog, ", "));
  }

  public function AtualizarNasStatus($id_nas, $new_status)
  {

    $nas = $this->NasDAO->getOne(" id = {$id_nas} ");

    $bindNas = [
      "status"   => $new_status
    ];

    $this->NasDAO->update($bindNas, "id = {$id_nas} ");

    $this->setContole("Alterou o status da Nas ID: {$id_nas} de {$nas['status']} para {$new_status}");
  }

  public function AdicionarNas($fields)
  {
    extract($fields);
    $nas = $this->NasDAO->getAll(" nasname = '{$nasname}' ");
    if (!empty($nas)) {
      throw new CommomException("Já existe concentradora cadastrada com este IP.");
    }

    $bindNas = [
      "nasname"               => $nasname,
      "shortname"             => $shortname,
      "description"           => $description,
      "secret"                => $secret,
      "ports"                  => $ports,
    ];

    $id = $this->NasDAO->insert($bindNas);
    $this->setContole("Adicionou NAS id: {$id}, Nome: {$description}, IP: {$nasname}");
  }
}
