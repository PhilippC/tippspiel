-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle Benutzer
--

CREATE TABLE Benutzer (
  BenutzerID int(11) NOT NULL auto_increment,
  Name varchar(128) NOT NULL default '',
  Mail varchar(128) default '',
  Pwd varchar(128) NOT NULL default '',
  ConfirmString varchar(255) NOT NULL default '',
  PwdResetKey varchar(255) NOT NULL default '',
  GroupAfterConfirm int(11) NOT NULL default '0',
  FacebookUserID varchar(64) COLLATE latin1_german1_ci DEFAULT NULL,
  PRIMARY KEY  (BenutzerID),
  UNIQUE KEY Name (Name),
  UNIQUE KEY Mail (Mail),
  UNIQUE KEY FacebookUserID (FacebookUserID)  
) TYPE=MyISAM AUTO_INCREMENT=161 ;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle Benutzer_Gruppen
--

CREATE TABLE Benutzer_Gruppen (
  BenutzerID int(11) NOT NULL default '0',
  GruppenID int(11) NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle Gruppen
--

CREATE TABLE Gruppen (
  GruppenID int(11) NOT NULL auto_increment,
  Name varchar(32) NOT NULL default '',
  Titel varchar(255) NOT NULL default '',
  PRIMARY KEY  (GruppenID)
) TYPE=MyISAM AUTO_INCREMENT=6 ;





-- 
-- Daten für Tabelle Benutzer
-- 

INSERT INTO Benutzer VALUES (1,'admin', 'admin@[tippspielurl].de', 'admin', '', '', 0, NULL);

-- 
-- Daten für Tabelle Benutzer_Gruppen
-- 

INSERT INTO Benutzer_Gruppen VALUES (1, 2);

-- 
-- Daten für Tabelle Gruppen
-- 

INSERT INTO Gruppen VALUES (1, 'Benutzer', 'Benutzer');
INSERT INTO Gruppen VALUES (2, 'admin', 'Administratoren');
INSERT INTO Gruppen VALUES (3, 'users_pending', 'Benutzer; E-Mail-Adresse noch nicht verifiziert');
