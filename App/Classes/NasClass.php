<?php

namespace App\Classes;

class NasClass extends \Core\Defaults\DefaultClassController
{
  public \App\Model\NasDAO $NasDAO;

  private function salvaFile($file): string
  {
      $text = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
      $fileExist = true;

      do {
        $extensao     = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nome_arquivo = pathinfo($file['name'], PATHINFO_FILENAME);

        $nome_arquivo  = preg_replace('/\s/', "_", str_replace(".", "", $nome_arquivo));
        $nome_arquivo  = strtolower(substr($nome_arquivo, 0, 6)) . substr(str_shuffle($text), 0, 6);
        $nome_arquivo .= "_" . strtotime("now");
        $nome_arquivo .= "." . $extensao;

        $caminho_completo = PATH_DOCUMENTS . "/{$nome_arquivo}";

        if (!file_exists($caminho_completo)) {
          $fileExist = false;

          $fileOld = file_get_contents(realpath($file['tmp_name']));
          $arqFile = fopen($caminho_completo, "w+");

          fwrite($arqFile, $fileOld);
          fclose($arqFile);

          return $nome_arquivo;
        }
      } while ($fileExist);
  }

  public function AtualizarNas($data, $id_emp)
  {
    extract($data);

    $nas = $this->NasDAO->getOne(" id = {$id}");

    $nas_politica_filename = ($nas_politica_filename === "null" ||  $nas_politica_filename === "") ? null : $nas_politica_filename;
    $nas_termos_filename   = ($nas_termos_filename === "null" || $nas_termos_filename === "") ? null : $nas_termos_filename;

    if ($nas_politica_status === "1") {
      if (gettype($nas_politica_filename) !== "string" && !empty($nas_politica_filename)) {
        $nas_politica_filename = $this->salvaFile($nas_politica_filename);
      } else if (!empty($nas_politica_filename)) {
        $nas_politica_filename = pathinfo(parse_url($nas_politica_filename, PHP_URL_PATH), PATHINFO_BASENAME);
      }
    } else {
      $nas_politica_filename = null;
    }

    if (gettype($nas_termos_filename) !== "string"  && !empty($nas_termos_filename)) {
      $nas_termos_filename = $this->salvaFile($nas_termos_filename);
    } else if (!empty($nas_termos_filename)) {
      $nas_termos_filename = pathinfo(parse_url($nas_termos_filename, PHP_URL_PATH), PATHINFO_BASENAME);
    }

    if (!empty($nas['nas_termos_filename']) && $nas['nas_termos_filename'] !== $nas_termos_filename) {
      $filename = pathinfo(parse_url($nas['nas_termos_filename'], PHP_URL_PATH), PATHINFO_BASENAME);
      unlink(PATH_DOCUMENTS . "/{$filename}");
    }

    if (!empty($nas['nas_politica_filename']) && $nas['nas_politica_filename'] !== $nas_politica_filename) {
      $filename = pathinfo(parse_url($nas['nas_politica_filename'], PHP_URL_PATH), PATHINFO_BASENAME);
      unlink(PATH_DOCUMENTS . "/{$filename}");
    }

    $bindNas = [
      "nasname"               => $nasname,
      "shortname"             => $shortname,
      "description"           => $description,
      "secret"                => $secret,
      "endereco"              => $endereco,
      "port"                  => $port,
      "nas_logo"              => $nas_logo,
      "nas_politica_status"   => $nas_politica_status,
      "nas_politica_filename" => $nas_politica_filename,
      "nas_termos_filename"   => $nas_termos_filename
    ];

    $this->NasDAO->update($bindNas, "id = {$id} AND id_emp = {$id_emp}");

    // $this->setContole("Atualizou as informações da Nas ID: {$id}");
  }

  public function AtualizarNasStatus($id_nas, $new_status, $id_emp)
  {

      $bindNas = [
        "status"   => $new_status
      ];

      $this->NasDAO->update($bindNas, "id = {$id_nas} AND id_emp = {$id_emp}");

      // $this->setContole("Atualizou as informações da Nas ID: {$id}");

  }

  public function AdicionarNas($data, $id_emp)
  {
      extract($data);

      $nas_politica_filename = ($nas_politica_filename === "null" ||  $nas_politica_filename === "") ? null : $nas_politica_filename;
      $nas_termos_filename   = ($nas_termos_filename === "null" || $nas_termos_filename === "") ? null : $nas_termos_filename;

      if ($nas_politica_status === "1") {
        if (gettype($nas_politica_filename) !== "string" && !empty($nas_politica_filename)) {
          $nas_politica_filename = $this->salvaFile($nas_politica_filename);
        } else if (!empty($nas_politica_filename)) {
          $nas_politica_filename = pathinfo(parse_url($nas_politica_filename, PHP_URL_PATH), PATHINFO_BASENAME);
        }
      } else {
        $nas_politica_filename = null;
      }

      if (gettype($nas_termos_filename) !== "string"  && !empty($nas_termos_filename)) {
        $nas_termos_filename = $this->salvaFile($nas_termos_filename);
      } else if (!empty($nas_termos_filename)) {
        $nas_termos_filename = pathinfo(parse_url($nas_termos_filename, PHP_URL_PATH), PATHINFO_BASENAME);
      }

      if (!empty($nas['nas_termos_filename']) && $nas['nas_termos_filename'] !== $nas_termos_filename) {
        $filename = pathinfo(parse_url($nas['nas_termos_filename'], PHP_URL_PATH), PATHINFO_BASENAME);
        unlink(PATH_DOCUMENTS . "/{$filename}");
      }

      if (!empty($nas['nas_politica_filename']) && $nas['nas_politica_filename'] !== $nas_politica_filename) {
        $filename = pathinfo(parse_url($nas['nas_politica_filename'], PHP_URL_PATH), PATHINFO_BASENAME);
        unlink(PATH_DOCUMENTS . "/{$filename}");
      }

      $bindNas = [
        "nasname"               => $nasname,
        "shortname"             => $shortname,
        "description"           => $description,
        "secret"                => $secret,
        "endereco"              => $endereco,
        "port"                  => $port,
        "nas_logo"              => $nas_logo,
        "nas_politica_status"   => $nas_politica_status,
        "nas_politica_filename" => $nas_politica_filename,
        "nas_termos_filename"   => $nas_termos_filename,
        "id_emp"                => $id_emp
      ];

      $this->NasDAO->insert($bindNas);
  }
}
