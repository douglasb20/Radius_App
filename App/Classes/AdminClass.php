<?php

namespace App\Classes;

use App\Exceptions\CommomException;

class AdminClass extends \Core\Defaults\DefaultClassController
{
  public \App\Model\BancoArquivosDAO $BancoArquivosDAO;

  public function UploadImage($data, $id_emp)
  {
    try {
      extract($data);
      unset($data['inputImage']);
      $ext       = pathinfo($inputImage['name']);
      $ext       = $ext['extension'];
      $name_file = "file_image_" . time() . ".{$ext}";

      if (move_uploaded_file($inputImage['tmp_name'], PATH_IMAGES . "/{$name_file}")) {
        $bindImage = [
          "descricao_arquivo" => $descricao_arquivo,
          "nome_arquivo"      => $name_file,
          "id_tipo_arquivo"   => 1,
          "default_arquivo"   => 0,
          "status"            => 1,
          "url_path"          => URL_IMAGES . "/{$name_file}",
          "id_emp"            => $id_emp,
        ];

        $newId = $this->BancoArquivosDAO->insert($bindImage);

        // $this->setContole("Adicionou uma nova imagem ID: {$newId} - Nome: {$descricao_arquivo}");
      } else {
        throw new CommomException($inputImage['error']);
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function AtualizaConfigs($inputJSON, $id_emp)
  {
    try {

      $dataUser = [
        "id_user" => $inputJSON['id_user'],
        "layout_user" => $inputJSON["layout_user"],
      ];

      (new UsersClass)->AtualizaConfigUsuario($dataUser);
      (new EmpresaClass)->AtualizaConfigs($inputJSON["empresa"], $inputJSON['empresa_config'], $id_emp);
      if(!empty($inputJSON['system_config'])){
        (new EmpresaClass)->AtualizaSystemConfigs($inputJSON['system_config']);
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
