<?php

namespace App\Classes;

use \App\Services\PhpMailerService;

class MailClass extends \Core\Defaults\DefaultClassController
{

  public function SendRecoverPass($link, $name, $toMail)
  {
    try{
      $corpoEmail = "Olá,<br /><br />
            Recebemos uma solicitação para redefinir a sua senha. Clique no link abaixo para criar uma nova senha.<br />
            Este link é válido por 30 minutos a partir do recebimento deste email:
            <br /><br />
            {$link}
            <br /><br />
            Se você não solicitou essa redefinição, por favor, ignore este email.
            <br /><br />
            Atenciosamente,<br/>
            Equipe de suporte";
  
      $m = [
        "tomail"   => $toMail,
        "toname"   => ucwords(mb_strtolower($name)),
      ];
      
      $mail = new PhpMailerService($m);
      $mail->Subject = "Redefinição de senha";
  
      $mail->Body = $corpoEmail;
      $mail->send();
    }catch(\Exception $e){
      if (isset($mail->ErrorInfo)) {
        throw new \Exception($mail->ErrorInfo);
      } else {
        throw $e;
      }
    }
  }
}
