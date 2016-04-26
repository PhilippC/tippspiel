<?php
  /**
  Abstrakte Basis-Klasse für UI-Klassen des Tippspiels

  Jan. 06, Philipp Crocoll

  **/

  abstract class CTippSpielUIBase
  {
     public $AdditionalHTMLHeader;
    //gibt den Seitenanfang aus:
     abstract public function OutputHeader($Headline = '');
    //gibt das Seitenende aus:
     abstract public function OutputFooter();
    //Erzeugt eine Instanz einer UI-Klasse (z.B. LoginForm-Klasse)
     abstract public function CreateUIComponent($ComponentName, $ConstrParam1=NULL, $ConstrParam2=NULL, $ConstrParam3=NULL, $ConstrParam3=NULL);

     static public function CreateLink($UrlWithoutServerAddr)
     {
       $LinkURL = "http://".$_SERVER["SERVER_NAME"];
       if ($_SERVER["SERVER_PORT"] != 80)
         $LinkUrl .= (":".$_SERVER["SERVER_PORT"]);

       $LinkURL .= $UrlWithoutServerAddr;

       return $LinkURL;

     }
  }

?>