<?php

                                             
function sendMailDyn( $strTo, $strBCC, $strFrom_mail, $strBetreff, $strContent, $bIsHTML )
{
        $email_to                         = $strTo;
        $emailBCC                        = $strBCC;
        $email_from_mail         = $strFrom_mail;
        $email_betreff                 = $strBetreff;
        $emailbody                         = $strContent;


        // ---
        // header zusammenbasteln
        // ---
        $header        = 'From: '.$email_from_mail."\n";
        if ($emailBCC != "")
          $header .= "Bcc: " .$emailBCC ."\n";

        $header .= "Reply-To: " .$email_from_mail ."\n";
        //$header .= "X-Mailer: PHP/" . phpversion(). "\n";
        //$header .= "X-Sender-IP: " .$REMOTE_ADDR ."\n";
        if ($bIsHTML)
          $header .= "Content-Type: text/html";
        else
          $header .= "Content-Type: text/plain;
	charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit";

        // ---
        // weg damit
        // ---
        if( !mail( $email_to, $email_betreff, $emailbody, $header ) )
        {
          $result=false;
        }
        else
        {
          $result=true;
        }

        return $result;

}

?>