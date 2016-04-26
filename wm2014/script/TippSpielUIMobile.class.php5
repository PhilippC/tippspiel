<?php
  /**
  UI-Klasse des Tippspiels für Darstellung auf mobilen Geräten

  Feb. 11, Philipp Crocoll

  **/

  require_once("TippSpielUIBasis.class.php5");
  require_once("UIMobileClasses.inc.php5");
  
  

  class CTippSpielUIMobile extends CTippSpielUIBase
  {
     public $IndexMenuText = "Startseite";
     protected $ConstructTime=0;

     protected $MenuCounter=0;


     private function microtime_float()
     {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
     }
	 
	 public function __construct()
	 {
		$this->ConstructTime = microtime_float();
	 }


     public function OutputHeader($Headline = '', $EnableCountdown=false)
     {

         echo'<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>'.TIPPSPIEL_NAME.' - Mobilversion</title>
  <meta name="HandheldFriendly" content="true" />
  <meta name="viewport" content="width=device-width" /> 
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
';
  if ($EnableCountdown) echo '
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <script src="js/countdown.js" type="text/javascript" language="javascript"></script>
  ';
  echo($this->AdditionalHTMLHeader);
  echo'
  <link rel="STYLESHEET" type="text/css" href="style_mobile.css" />



</head>

   ';
   if ($EnableCountdown) echo '
<body onload="countdown()">
  '; else echo '
  <body>
  
  ';

			if (KIRCHE_WEBSITE != "")
				echo('<a href="'.KIRCHE_WEBSITE.'">');
			echo('<img src="bilder/head-breit.jpg" alt="Kopf" style="border:none;width:100%" />');
			if (KIRCHE_WEBSITE != "")
				echo('</a>');
				
            
         $this->OutputTippMenu();
         if ($Headline != "")
         echo("<h1>$Headline</h1>");

     }

     public function OutputFooter()
     {


         
	echo'
</body>
</html>';

         $time_end = $this->microtime_float();
         $TimeDiff = ($time_end-$this->ConstructTime);

         CLogClass::log("Mobile-Seite ".$_SERVER["PHP_SELF"]." in ".$TimeDiff."s ausgegeben.");

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
         case "TippErgVergleich": $newComponent =  new CUIMobileTippErgVergleich();break;
         case "TippErgVergleichGrafik": $newComponent =  new CUIWeb1TippErgVergleichGrafik();break;
         case "TippListe": $newComponent =  new CUIWeb1TippListe();break;
         case "TippTabelle": $newComponent =  new CUIWeb1TippTabelle($ConstrParam1,$ConstrParam2);break;
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
     	echo('
     <div class="MessageBox Box" >
<div class="BoxTitle">'.$Title.'</div>
<br />
<div class="BoxContent">'.$Text.'</div>
       </div><br />');
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
     $this->OutputMenuItem("tabelle.php5","Tabelle");
     $this->OutputMenuItem("statistiken.php5","Statistiken");
     $this->OutputMenuItem("regeln.php5","Regeln");
     $this->OutputMenuItem("spielplan.php5","Spielplan");
     echo'
     </tr>
     </table>
     ';
     }
     
   public function StartColumn1()
     {
     }
     
    
 	 public function StartColumn2()
     {
     }
     
     public function EndColumn2()
     {
     	
     }
     
     public function GetUIName()
     {
     	return "Mobile";
     }

  }



?>