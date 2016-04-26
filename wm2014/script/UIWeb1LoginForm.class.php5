<?php

/**

Diese Klasse erstellt ein Formular, in dem sich ein User anmelden kann.
Dazu werden Username und Passwort benötigt.

Die Formular-"Action" und die Beschriftung des Submit-Buttons sind
flexibel, ebenso die Frage welche Benutzer angezeigt werden.


**/
  require_once(dirname(__FILE__).'/../tippconfig.php5');
  require_once('UIWeb1Form.class.php5');
  require_once(BASECLASSES_PATH."/BaseClasses/BenutzerVerw.class.php5");

  class CUIWeb1LoginForm extends CUIWeb1Form
  {
    protected $m_Action = "";
    protected $m_Method = "";
    protected $m_SubmitValue = "Einloggen";
    protected $m_bSmall = false;

    public function __construct($Name,$Action="",$Method="POST", $GroupFilter=TIPPSPIEL_USERGROUP,$bSmall=false)
    {
    	$this->m_Action = $Action;
         $this->m_Method = $Method;
         parent::__construct($Name);

         $BenutzerVerw = new CBenutzerVerw();
         
         $showUserDropdown = false;
         
         if ($showUserDropdown)
         {
         
         
         $BenutzerListe = $BenutzerVerw->GetUserList($GroupFilter);

         $counter = 0;
         $arrDD = array();
         foreach($BenutzerListe as $Benutzer)
         {
           $arrDD[$Benutzer->Name] = $Benutzer->Name;
         }


         $UserDropDown = new FormItem($BenutzerVerw->GetUsernameFieldname(), "Benutzer", $arrDD, "dropdown"  );
         }
         else
         $UserDropDown = new FormItem($BenutzerVerw->GetUsernameFieldname(), "Benutzer", ""); 
         $PwdField = new FormItem($BenutzerVerw->GetPasswordFieldname(),"Passwort","","password");


         $this->m_bSmall = $bSmall;
         if ($bSmall)
         {
           $PwdField->SetClass("smallFormElement");
           $UserDropDown->SetClass("smallFormElement");
         }
         else
         {
           $PwdField->SetClass("wideFormElement");
           $UserDropDown->SetClass("wideFormElement");

         }
	parent::addItemObject( $UserDropDown );
      parent::addItemObject($PwdField);


    }

    public function Output()
    {
      echo("<form name=\"".$this->_strFormName."\" action=\"$this->m_Action\" method=\"$this->m_Method\">\n");


      parent::display();

      if ($this->m_bSmall)
        echo('<input type="submit" value="'.$this->m_SubmitValue.'"  class="smallSubmitButton" />');
      else
        echo('<input type="submit" value="'.$this->m_SubmitValue.'"  class="wideSubmitButton" />');


      echo("</form>");
    }

    public function SetButtonText($text)
    {
      $this->m_SubmitValue = $text;
    }

  }
?>