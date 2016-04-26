<?PHP

/*****************************
*
* sendMailDyn(...
*
* flexibler; mit allen moeglichen Kopfdaten (incl. BCC)
*
*****************************/
function sendMailDyn( $strTo, $strBCC, $strFrom_mail, $strBetreff, $strContent )
{
        $email_to                         = $strTo;
        $emailBCC                        = $strBCC;
        $email_from_mail         = $strFrom_mail;
        $email_from_name         = $strFrom_mail;
        $email_betreff                 = $strBetreff;
        $emailbody                         = $strContent;


        // ---
        // header zusammenbasteln
        // ---
        $header        = "From: " .$email_from_name ."<" .$email_from_mail .">\n";
        $header .= "Bcc: " .$emailBCC ."\n";
        $header .= "Reply-To: " .$email_from_mail ."\n";
        //$header .= "X-Mailer: PHP/" . phpversion(). "\n";
        //$header .= "X-Sender-IP: " .$REMOTE_ADDR ."\n";
        $header .= "Content-Type: text/html";

        // ---
        // weg damit
        // ---
        if( !mail( $email_to, $email_betreff, $emailbody, $header ) )
        {
                $result="Fehler beim Versenden der Mail.";
        }
        else
        {
          $result="";
        }

        return $result;

}

?>