<?php

/********************

Benutzer-Klasse
===============

Schnittstelle:
 - Name, Mail, Pwd werden dem Konstruktor übergeben und können
   nicht mehr geändert werden.

Philipp Crocoll, Jan. 06

*********************/

  class CBenutzer
  {
    	protected $m_iUserID = 0;			//Datenbank-ID
		protected $m_strName = "";         		//Anzeigename des Benutzer
         protected $m_strMail = "";			//E-Mail-Adresse
         protected $m_strPwd = "";			//Passwort. Wird beim Anlegen neuer Benutzer benötigt.

         public function __construct($ID, $Name,$Mail,$Pwd)
         {
           $this->m_strName = $Name;
           $this->m_strMail = $Mail;
           $this->m_strPwd = $Pwd;
           $this->m_iUserID = $ID;
         }

         public function GetName()
         {
           return $this->m_strName;
         }
         public function GetMail()
         {
           return $this->m_strMail;
         }
         public function GetUserID()
         {
           return $this->m_iUserID;
         }

         public function __get($Property)
         {
           switch ($Property)
           {
             case "Name": return $this->m_strName;
             case "Mail": return $this->m_strMail;
             case "Pwd": return $this->m_strPwd;
             case "ID": return $this->GetUserID();
             case "Groups": return $this->m_arGroups;
			 case "IsFacebookUser": return ($this->m_strPwd == null) || ( $this->m_strPwd == "");
             default:
               throw new Exception("Eine Eigenschaft $Property existiert nicht!");

           }

         }
  }

?>