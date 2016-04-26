<?php
  /**
  UI-Klasse des Tippspiels, die (geringe) AJAX ("Web 2.0") Funktionalität bereitstellt

  Feb. 06, Philipp Crocoll

  **/

  require_once("TippSpielUIBasis.class.php5");
  require_once("UIWeb2Classes.inc.php5");
  require_once(dirname(__FILE__)."/../tippconfig.php5");
  require_once(BASECLASSES_PATH."/sajax/Sajax.php");

  class CTippSpielUIWeb2 extends CTippSpielUIBase
  {
     public $IndexMenuText = "Startseite";
     protected $ConstructTime=0;

     protected $MenuCounter=0;




     public function OutputHeader($Headline = '', $EnableCountdown=false)
     {

         echo'<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>'.TIPPSPIEL_NAME.'</title>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
';
  if ($EnableCountdown) echo '
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <script src="js/countdown.js" type="text/javascript" language="javascript"></script>
  ';
  //Wichtig für Sajax:
  echo('<script language="javascript">');
  sajax_show_javascript();
  echo('</script>');
  //Hier werden i.d.R. die Seiten-abhängigen Javascript-Funktionen eingebettet:
  echo($this->AdditionalHTMLHeader);

  echo'
  <link rel="STYLESHEET" type="text/css" href="style.css" />
    ';
  if (isFacebookApp())
	echo('<link rel="STYLESHEET" type="text/css" href="style_facebook.css" />');
  echo'


</head>

   ';
   if ($EnableCountdown) echo '
<body onload="countdown()">
  '; else echo '
  <body>
  ';

  echo'
  

  <table width="730" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff">
 <tr>
      <td colspan="3" class="PageHeader">
    </tr>
    <tr>
      <td class="PageContentMain">

      
         <table width="100%" cellspacing="0">
            <tr>
              <td valign="top" height="500">

              ';
			if (KIRCHE_WEBSITE != "")
				echo('<a href="'.KIRCHE_WEBSITE.'">');
			echo('<img src="bilder/head-breit.jpg" alt="Kopf" style="border:none" />');
			if (KIRCHE_WEBSITE != "")
				echo('</a>');
         $this->OutputTippMenu();
         if ($Headline != "")
         echo("<h1>$Headline</h1>");

     }

     public function OutputFooter()
     {


	echo' </td>
           </tr>
	</table>
      </td>
   </tr>
   <tr>

     <td colspan="3" class="PageFoot">
        <table><tr><td></td></tr></table>
     </td>
   </tr>
   
</table>


</body>
</html>';

         $time_end = explode(" ",microtime());
	$time_end = $time_end[1] + $time_end[0];
         $TimeDiff = ($time_end-$this->ConstructTime);
         $TimeDiff = substr($TimeDiff,0,8);

         CLogClass::log("Seite ".$_SERVER["PHP_SELF"]." in ".$TimeDiff."s ausgegeben.");

     }

     public function CreateUIComponent($ComponentName, $ConstrParam1=NULL,
      						$ConstrParam2=NULL,
                                                 $ConstrParam3=NULL,
                                                 $ConstrParam4=NULL,
                            			$ConstrParam5=NULL)
     {
       $newComponent = NULL;
       switch ($ComponentName)
       {
         case "Advertisement": $newComponent = new CUIWeb1Advertisement();break;
         case "Countdown": $newComponent =  new CUIWeb1Countdown();break;
         case "Form": $newComponent =  new CUIWeb1Form();break;
         case "FormItem": $newComponent =  new CUIWeb1FormItem();break;
         case "Kolumne": $newComponent =  new CUIWeb1Kolumne();break;
         case "LoginForm": $newComponent =  new CUIWeb1LoginForm($ConstrParam1, $ConstrParam2, $ConstrParam3, $ConstrParam4, $ConstrParam5);break;
         case "SpielListe": $newComponent =  new CUIWeb1Spielliste();break;
         case "TippErgVergleich": $newComponent =  new CUIWeb1TippErgVergleich();break;
         case "TippErgVergleichGrafik": $newComponent =  new CUIWeb1TippErgVergleichGrafik();break;
         case "TippListe": $newComponent =  new CUIWeb2TippListe();break;
         case "TippTabelle": $newComponent =  new CUIWeb1TippTabelle($ConstrParam1);break;
         default: throw new Exception("Eine Komponente $ComponentName ist der Klasse CTippSpielUIWeb1 nicht bekannt!");
       }
       $newComponent->parent = $this;
       return $newComponent;
     }

     public function OutputSubMenu($Link,$Text, $isActive=false)
     {
            $this->MenuCounter++;
            $MenuID ="TippMenu".$this->MenuCounter;
            if ($isActive) $Text = "&gt;$Text&lt;";

            echo("<td id=\"$MenuID\" class=\"SubMenu\"><a href=\"$Link\" onmouseover='document.getElementById(\"$MenuID\").className=\"SubMenuHover\";' onmouseout='document.getElementById(\"$MenuID\").className=\"SubMenu\";'>$Text</a></td>");
     }

     public function OutputMessageBox($Title,$Text)
     {
       echo('<table width="100%"><tr><td align="center">
<br />
<div class="Box" style="width:350px;height:150px">
<div class="BoxTitle">'.$Title.'</div>
<br />
<div class="BoxContent">'.$Text.'</div>
</td></tr></table>
       ');
     }

     protected function OutputMenuItem($Link,$Text)
     {
       $this->MenuCounter++;
       $MenuID ="TippMenu".$this->MenuCounter;
       echo("<td id=\"$MenuID\" class=\"Menu\"><a href=\"$Link\" onmouseover='document.getElementById(\"$MenuID\").className=\"MenuHover\";' onmouseout='document.getElementById(\"$MenuID\").className=\"Menu\";'>$Text</a></td>");
     }

     protected function OutputTippMenu()
     {
     echo'
     <table border="0" width="100%">
     <tr>';
     $this->OutputMenuItem("index.php5",$this->IndexMenuText);
     $this->OutputMenuItem("tippspiel.php5","Mein Tippspiel");
     $this->OutputMenuItem("tabelle.php5","Tipp-Tabelle");
     $this->OutputMenuItem("statistiken.php5","Tipp-Statistiken");
     $this->OutputMenuItem("regeln.php5","Tipp-Regeln");
     $this->OutputMenuItem("spielplan.php5","Spielplan");
     echo'
     </tr>
     </table>
     ';
     }
     
  
     public function StartColumn1()
     {
     	echo'<table border="0" width="714" cellspacing="0" cellpadding="0">
	<tr valign="top">
	 <td width="484">';
     }
     
    
 	 public function StartColumn2()
     {
     	echo'
</td>
<td width="8">&nbsp;</td>

<td width="222">';
     }
     
     public function EndColumn2()
     {
     	echo'
</td>
</tr>

</table>';
     }
     
  public function GetUIName()
     {
     	return "Web2";
     }

  }



?>