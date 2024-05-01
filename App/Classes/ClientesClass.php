<?php

namespace App\Classes;

use App\Exceptions\CommomException;

class ClientesClass extends \Core\Defaults\DefaultClassController
{

  public \App\Model\RadgroupreplyDAO $RadgroupreplyDAO;
  public \App\Model\RadusergroupDAO $RadusergroupDAO;
  public \App\Model\RadacctDAO $RadacctDAO;
  public \App\Model\CliusuDAO $CliusuDAO;
  public \App\Model\GruposDAO $GruposDAO;

  public function ChangeClientStatus($data, $id_client, $id_emp)
  {
    extract($data);

    $client = $this->CliusuDAO->getAll("id_emp = {$id_emp} AND id = {$id_client}")[0];

    $bindStatus = [
      "usu_status" => $status,
      "usu_motbck" => $motivo,
      "usu_dtbck"  => $status == "3" ? date("Y-m-d H:i:s") : "0000-00-00 00:00:00"
    ];

    $this->CliusuDAO->update($bindStatus, "id_emp = {$id_emp} AND id = " . $id_client);

    // $this->setContole(($status === "3" ? "Bloqueio" : "Desbloqueio") . " do cliente ID: {$id_client} - Nome: {$client['usu_nome']}");
  }

  /**
   * Função para salvar um cliente
   * @author Douglas A. Silva
   * @return return
   */
  public function NewClient($data, $id_emp)
  {
    extract($data);

    $this->ValidaEmailUser($usu_email, $id_emp);
    $this->ValidaCPFlUser($usu_cpf, $id_emp);
    $this->ValidaUsername($usu_username, $id_emp);

    if(!validaData($usu_birth, 'Y-m-d')){
      throw new CommomException("Data com formato inválida" );
    }

    $grupo     = $this->GruposDAO->getOne(" id = {$id_grupo} AND id_emp = {$id_emp} and grp_status = 1");
    
    if(count($grupo) === 0){
      $grupo     = $this->GruposDAO->getOne(" grp_pdr = 1 AND id_emp = {$id_emp}");
    }

    $bindClient = [
      "usu_nome"     => mb_strtoupper($usu_nome),
      "usu_cpf"      => $usu_cpf,
      "usu_birth"    => $usu_birth,
      "usu_cell"     => $usu_cell,
      "usu_email"    => $usu_email,
      "usu_username" => $usu_username,
      "usu_senha"    => md5($usu_senha),
      "usu_gender"   => $usu_gender,
      "id_grupo"     => $id_grupo,
      "usu_obs"      => $usu_obs,
      "id_emp"       => $id_emp,
      "usu_depend"   => $usu_depend,
      "usu_tipcad"   => $usu_tipcad
    ];

    $id_client = $this->CliusuDAO->insert($bindClient);
    $bindGroup = [
      "id_client" => $id_client,
      "id_group"  => $id_grupo,
      "username"  => $usu_username,
      "groupname" => $grupo['grp_nome'],
      "priority"  => "1",
      "id_emp"    => $id_emp
    ];

    $this->RadusergroupDAO->insert($bindGroup);
    $newClient = $this->CliusuDAO->getOne("id = {$id_client} AND id_emp = {$id_emp}");
    return $newClient;
  }
  /**
   * Função para salvar um cliente
   * @author Douglas A. Silva
   * @return return
   */
  public function UpdateClient($data, $id_client, $id_emp)
  {
    extract($data);
    $this->ValidaEmailUser($usu_email, $id_emp, $id_client);
    $this->ValidaCPFlUser($usu_cpf, $id_emp, $id_client);
    $this->ValidaUsername($usu_username, $id_emp, $id_client);

    if(!validaData($usu_birth, 'Y-m-d')){
      throw new CommomException("Data com formato inválida" );
    }

    $grupo     = $this->GruposDAO->getOne(" id = {$id_grupo} AND id_emp = {$id_emp} and grp_status = 1");
    
    if(count($grupo) === 0){
      $grupo     = $this->GruposDAO->getOne(" grp_pdr = 1 AND id_emp = {$id_emp}");
    }

    $bindClient = [
      "usu_nome"     => mb_strtoupper($usu_nome),
      "usu_cpf"      => $usu_cpf,
      "usu_birth"    => $usu_birth,
      "usu_cell"     => $usu_cell,
      "usu_email"    => $usu_email,
      "usu_username" => $usu_username,
      "usu_gender"   => $usu_gender,
      "id_grupo"     => $id_grupo,
      "usu_obs"      => $usu_obs,
      "usu_depend"   => $usu_depend,
    ];

    if (!empty($usu_senha)) {
      $bindClient['usu_senha'] = md5($usu_senha);
    }

    $this->CliusuDAO->update($bindClient, "id = {$id_client} AND id_emp = {$id_emp}");

    $bindGroup = [
      "id_group"  => $id_grupo,
      "username"  => $usu_username,
      "groupname" => $grupo['grp_nome'],
    ];

    $this->RadusergroupDAO->update($bindGroup, "id_client = {$id_client} AND id_emp = {$id_emp}");
    $updatedClient = $this->CliusuDAO->getOne("id = {$id_client} AND id_emp = {$id_emp}");
    return $updatedClient;
  }

  public function ValidaEmailUser($email, $id_emp, $id_client = null)
  {
    $user = $this->CliusuDAO->getAll(" usu_email = '{$email}' AND id_emp={$id_emp}");
    if (count($user) > 0) {
      if (!empty($id_client)) {
        if ($id_client !== $user[0]["id"]) {
          throw new CommomException("Já existe um cliente cadastrado com esse email, por favor, tente outro email.");
        }
      } else {
        throw new CommomException("Já existe um cliente cadastrado com esse email, por favor, tente outro email.");
      }
    }
  }

  public function ValidaUsername($username, $id_emp, $id_client = null)
  {
    $user = $this->CliusuDAO->getAll(" usu_username = '{$username}' AND id_emp={$id_emp}");
    if (count($user) > 0) {
      if (!empty($id_client)) {
        if ($id_client !== $user[0]["id"]) {
          throw new CommomException("Já existe um usuário cadastrado com esse username, por favor, tente outro username.");
        }
      } else {
        throw new CommomException("Já existe um usuário cadastrado com esse username, por favor, tente outro username.");
      }
    }
  }

  public function ValidaCPFlUser($cpf, $id_emp, $id_client = null)
  {
    $user = $this->CliusuDAO->getAll(" usu_cpf = '{$cpf}' AND id_emp={$id_emp}");
    if (count($user) > 0) {
      if (isset($id_client)) {
        if ($id_client !== $user[0]["id"]) {
          throw new CommomException("Já existe um usuário cadastrado com esse CPF, por favor, tente outro CPF.");
        }
      } else {
        throw new CommomException("Já existe um usuário cadastrado com esse CPF, por favor, tente outro CPF.");
      }
    }

    if(!validaCPF($cpf)){
      throw new CommomException("CPF inválido.");
    }
  }

  public function ListarClientes($data, $id_emp){

    $where = "id_emp = {$id_emp}";
    extract($data);


    if ($status !== "-1") {
      $where .= " AND usu_status={$status}";
    }

    $clientes = $this->CliusuDAO->getView($where);

    return $clientes;
  }

  public function PegarClientesPorID($id_client, $id_emp){

    $where = "id_emp = {$id_emp} AND id = {$id_client}";
    $clientes = $this->CliusuDAO->getView($where);

    if(count($clientes) === 0){
      $clientes = [];
    }else{
      $clientes = $clientes[0];
    }

    return $clientes;
  }

    
  public function ListaConsumoCliente($id, $id_emp)
  {
    $clientes   = $this->RadacctDAO->relacaoUso($id, $id_emp);

    return $clientes;
  }
}
