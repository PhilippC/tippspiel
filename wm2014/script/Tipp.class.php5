<?php

/********************

Tipp-Klasse
===============


Philipp Crocoll, Jan. 06

*********************/

  class CTipp
  {
  	protected $m_strBenutzerName = "";
         protected $m_iSpielNr;
         protected $m_iTeam1Tore;
         protected $m_iTeam2Tore;
         protected $m_bErgBekannt = false;
         protected $m_iTeam1ToreErg;
         protected $m_iTeam2ToreErg;
         protected $m_TippDateTime;

         public function __construct($BenutzerName,$SpielNr)
         {
  	  $this->m_strBenutzerName = $BenutzerName;
           $this->m_iSpielNr = $SpielNr;
         }


         public function __get($Property)
         {
           switch ($Property)
           {
             case "BenutzerName": return $this->m_strBenutzerName;
             case "Team1Tore": return $this->m_iTeam1Tore;
             case "Team2Tore": return $this->m_iTeam2Tore;
             case "Team1ToreErg": return $this->m_iTeam1ToreErg;
             case "Team2ToreErg": return $this->m_iTeam2ToreErg;
             case "ErgebnisBekannt": return $this->m_bErgBekannt;
             case "TippDateTime": return $this->m_TippDateTime;
             case "iSpielNr":return $this->m_iSpielNr;

             default:
               throw new Exception("Eine Eigenschaft $Property existiert nicht!");

           }

         }

         public function __set($FieldName,$Value)
         {
           switch($FieldName)
           {
             case "Team1Tore": { $this->m_iTeam1Tore = $Value; break; }
             case "Team2Tore": { $this->m_iTeam2Tore = $Value; break; }
             case "TippDateTime": { $this->m_TippDateTime = $Value; break; }
             default: throw new Exception("Eine Eigenschaft $FieldName existiert nicht!");
           }
         }

         public function SetzeErgebnis($Team1Tore,$Team2Tore)
         {
            $this->m_iTeam1ToreErg = $Team1Tore;
            $this->m_iTeam2ToreErg = $Team2Tore;
            $this->m_bErgBekannt = true;
         }

         public function SetzeTipp($Team1Tore,$Team2Tore,$TippZeit = 0)
         {
            $this->m_iTeam1Tore = $Team1Tore;
            $this->m_iTeam2Tore = $Team2Tore;
            $TippZeit = 0 ? $this->m_TippDateTime = time() : $this->m_TippDateTime = $TippZeit;
         }


  }

?>