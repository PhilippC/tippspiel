<?php

/********************

Mannschafts-Klasse
==================

Schnittstelle:
 - NameLang, NameKurz, FlaguRL und Gruppe werden dem Konstruktor übergeben und können
   nicht mehr geändert werden.
 - Alle Properties können gelesen werden.

Philipp Crocoll, Jan. 06

*********************/

  class CMannschaft
  {
  	protected $m_strNameLang = "";         		//z.B. "Deutschland"
         protected $m_strNameKurz = "";			//z.B. "GER"
         protected $m_strFlagURL = "";			//z.B. "images/ger.gif"
         protected $m_strGruppe = "";      		//z.B. "A"

         public function __construct($NameLang,$NameKurz,$FlagURL,$Gruppe)
         {
           $this->m_strNameLang = $NameLang;
           $this->m_strNameKurz = $NameKurz;
           $this->m_strFlagURL = $FlagURL;
           $this->m_strGruppe = $Gruppe;

         }

         public function __get($Property)
         {
           switch ($Property)
           {
             case "NameLang": return $this->m_strNameLang;
             case "NameKurz": return $this->m_strNameKurz;
             case "FlagURL": return $this->m_strFlagURL;
             case "Gruppe": return $this->m_strGruppe;
             default:
               throw new Exception("Eine Eigenschaft $Property existiert nicht!");

           }

         }



  }

?>

