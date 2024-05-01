<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

class PhpMailerService extends PHPMailer
{

  public function __construct($smtp = null, $show_exception = true)
  {
    try {
      parent::__construct($show_exception);

      $this->IsSMTP(); // set mailer to use SMTP
      $this->CharSet    = 'UTF-8';
      $this->Host       = $_ENV['SMTP_HOST']; // specify main and backup server
      $this->Port       = $_ENV['SMTP_PORT'];
      $this->SMTPAuth   = true; // turn on SMTP authentication
      $this->SMTPSecure = 'tls';
      $this->Username   = $_ENV['SMTP_USER']; // SMTP username
      $this->Password   = $_ENV['SMTP_PASSWD']; // SMTP password
      $this->setFrom($_ENV['SMTP_FROMMAIL'], $_ENV['SMTP_FROMNAME']);
      $this->addAddress($smtp['tomail'], $smtp['toname']);

      if (!empty($smtp['replytomail'])) {
        $this->AddReplyTo($smtp['replytomail'], $smtp['replytoname']);
      } else {
        $this->AddReplyTo($_ENV['SMTP_FROMMAIL'], $_ENV['SMTP_FROMNAME']);
      }
      $this->IsHTML(true);

      // $this->Subject = 'Here is the subject';
      // $this->Body    = 'This is the HTML message body <b>in bold!</b>';

      $this->SMTPOptions = array(
        'ssl' => array(
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
        )
      );
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function AddAddressGrupo($grupo, $assunto)
  {
    try {

      foreach ($grupo as $key => $value) {

        $this->AddBCC($value['email'], $assunto);
      }
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function trocaVars($msg, $arrayEnv)
  {
    foreach ($arrayEnv as $id => $var) {
      $msg = str_replace($id, $var, $msg);
    }
    return $msg;
  }
}
