-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `em16_kolumne`
-- 

CREATE TABLE `em16_kolumne` (
  `ID` int(11) NOT NULL auto_increment,
  `Datum` date NOT NULL default '0000-00-00',
  `Titel` varchar(128) collate latin1_german1_ci NOT NULL default '',
  `Text` text collate latin1_german1_ci NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `em16_matches`
-- 

CREATE TABLE `em16_matches` (
  `MatchNr` int(11) NOT NULL default '0',
  `Team1Type` char(3) collate latin1_german1_ci default NULL,
  `Team2Type` char(3) collate latin1_german1_ci default NULL,
  `Team1Short` char(3) collate latin1_german1_ci default NULL,
  `Team2Short` char(3) collate latin1_german1_ci default NULL,
  `MatchDate` date NOT NULL default '0000-00-00',
  `StartTime` time NOT NULL default '00:00:00',
  `MatchType` tinyint(4) NOT NULL default '0',
  `ResTeam1Goals` smallint(6) default NULL,
  `ResTeam2Goals` smallint(6) default NULL,
  `MatchSortId` smallint(6) default NULL,
  PRIMARY KEY  (`MatchNr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci COMMENT='MatchType ist leer f?Gruppe, sonst 16, 8, 4,2';

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `em16_matchtypes`
-- 

CREATE TABLE `em16_matchtypes` (
  `TypeNr` tinyint(4) NOT NULL default '0',
  `Desc` varchar(64) collate latin1_german1_ci NOT NULL default '',
  PRIMARY KEY  (`TypeNr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `em16_teams`
-- 

CREATE TABLE `em16_teams` (
  `ID` int(11) NOT NULL auto_increment,
  `NameLong` varchar(64) collate latin1_german1_ci NOT NULL default '',
  `NameShort` char(3) collate latin1_german1_ci NOT NULL default '',
  `FlagURL` varchar(255) collate latin1_german1_ci NOT NULL default '',
  `GroupNr` char(1) collate latin1_german1_ci NOT NULL default '',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `ID_2` (`ID`,`NameLong`,`NameShort`),
  KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `em16_tips`
-- 

CREATE TABLE `em16_tips` (
  `TipID` int(11) NOT NULL auto_increment,
  `UserID` char(64) collate latin1_german1_ci NOT NULL default '',
  `MatchNr` int(11) NOT NULL default '0',
  `Team1Goals` smallint(6) default NULL,
  `Team2Goals` smallint(6) default NULL,
  `TipTime` datetime default NULL,
  PRIMARY KEY  (`TipID`),
  UNIQUE KEY `TipID_2` (`TipID`),
  KEY `TipID` (`TipID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=2001 ;
        
CREATE TABLE `em16_tiptabellen_cache` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `AnzSpiele` smallint(6) NOT NULL,
  `MaxMatchSortId` smallint(6) NOT NULL,
  `Platz` smallint(6) NOT NULL,
  `BenutzerName` varchar(128) NOT NULL,
  `AnzSpieleGetippt` smallint(6) NOT NULL,
  `AnzTendenz` smallint(6) NOT NULL,
  `AnzDifferenz` smallint(6) NOT NULL,
  `AnzErgebnis` smallint(6) NOT NULL,
  `Punkte` smallint(6) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `MaxMatchSortId` (`MaxMatchSortId`,`BenutzerName`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8501 ;



-- 
-- Dumping data for table `em16_matches`
-- 

INSERT INTO `em16_matches` (`MatchNr`, `Team1Type`, `Team2Type`, `Team1Short`, `Team2Short`, `MatchDate`, `StartTime`, `MatchType`, `ResTeam1Goals`, `ResTeam2Goals`,`MatchSortId`) VALUES 
(1, NULL, NULL,'FRA','ROU','2016-06-10','21:00:00',32,NULL,NULL,1),
(2, NULL, NULL,'ALB','SUI','2016-06-11','15:00:00',32,NULL,NULL,2),
(3, NULL, NULL,'WLS','SVK','2016-06-11','18:00:00',32,NULL,NULL,3),
(4, NULL, NULL,'ENG','RUS','2016-06-11','21:00:00',32,NULL,NULL,4),
(5, NULL, NULL,'TUR','CRO','2016-06-12','15:00:00',32,NULL,NULL,5),
(6, NULL, NULL,'POL','NIR','2016-06-12','18:00:00',32,NULL,NULL,8),
(7, NULL, NULL,'GER','UKR','2016-06-12','21:00:00',32,NULL,NULL,6),
(8, NULL, NULL,'ESP','CZE','2016-06-13','15:00:00',32,NULL,NULL,7),
(9, NULL, NULL,'IRL','SWE','2016-06-13','18:00:00',32,NULL,NULL,9),
(10, NULL, NULL,'BEL','ITA','2016-06-13','21:00:00',32,NULL,NULL,10),
(11, NULL, NULL,'AUT','HUN','2016-06-14','18:00:00',32,NULL,NULL,11),
(12, NULL, NULL,'POR','ISL','2016-06-14','21:00:00',32,NULL,NULL,12),
(13, NULL, NULL,'RUS','SVK','2016-06-15','15:00:00',32,NULL,NULL,13),
(14, NULL, NULL,'ROU','SUI','2016-06-15','18:00:00',32,NULL,NULL,14),
(15, NULL, NULL,'FRA','ALB','2016-06-15','21:00:00',32,NULL,NULL,15),
(16, NULL, NULL,'ENG','WLS','2016-06-16','15:00:00',32,NULL,NULL,16),
(17, NULL, NULL,'UKR','NIR','2016-06-16','18:00:00',32,NULL,NULL,17),
(18, NULL, NULL,'GER','POL','2016-06-16','21:00:00',32,NULL,NULL,18),
(19, NULL, NULL,'ITA','SWE','2016-06-17','15:00:00',32,NULL,NULL,19),
(20, NULL, NULL,'CZE','CRO','2016-06-17','18:00:00',32,NULL,NULL,20),
(21, NULL, NULL,'ESP','TUR','2016-06-17','21:00:00',32,NULL,NULL,21),
(22, NULL, NULL,'BEL','IRL','2016-06-18','15:00:00',32,NULL,NULL,22),
(23, NULL, NULL,'ISL','HUN','2016-06-18','18:00:00',32,NULL,NULL,23),
(24, NULL, NULL,'POR','AUT','2016-06-18','21:00:00',32,NULL,NULL,24),
(25, NULL, NULL,'SUI','FRA','2016-06-19','21:00:00',32,NULL,NULL,25),
(26, NULL, NULL,'ROU','ALB','2016-06-19','21:00:00',32,NULL,NULL,26),
(27, NULL, NULL,'SVK','ENG','2016-06-20','21:00:00',32,NULL,NULL,27),
(28, NULL, NULL,'RUS','WLS','2016-06-20','21:00:00',32,NULL,NULL,28),
(29, NULL, NULL,'UKR','POL','2016-06-21','18:00:00',32,NULL,NULL,29),
(30, NULL, NULL,'NIR','GER','2016-06-21','18:00:00',32,NULL,NULL,30),
(31, NULL, NULL,'CRO','ESP','2016-06-21','21:00:00',32,NULL,NULL,31),
(32, NULL, NULL,'CZE','TUR','2016-06-21','21:00:00',32,NULL,NULL,32),
(33, NULL, NULL,'HUN','POR','2016-06-22','18:00:00',32,NULL,NULL,33),
(34, NULL, NULL,'ISL','AUT','2016-06-22','18:00:00',32,NULL,NULL,34),
(35, NULL, NULL,'ITA','IRL','2016-06-22','21:00:00',32,NULL,NULL,35),
(36, NULL, NULL,'SWE','BEL','2016-06-22','21:00:00',32,NULL,NULL,36),
(37, 'ZA','ZC',NULL, NULL,'2016-06-25','15:00:00',16,NULL,NULL,37),
(38, 'EB','VA',NULL, NULL,'2016-06-25','18:00:00',16,NULL,NULL,38),
(39, 'ED','V',NULL, NULL,'2016-06-25','21:00:00',16,NULL,NULL,39),
(40, 'EA','VC',NULL, NULL,'2016-06-26','15:00:00',16,NULL,NULL,40),
(41, 'EC','VA',NULL, NULL,'2016-06-26','18:00:00',16,NULL,NULL,41),
(42, 'EF','ZE',NULL, NULL,'2016-06-26','21:00:00',16,NULL,NULL,42),
(43, 'EE','ZD',NULL, NULL,'2016-06-27','18:00:00',16,NULL,NULL,43),
(44, 'ZB','ZF',NULL, NULL,'2016-06-27','22:00:00',16,NULL,NULL,44),
(45, 'W37','W39',NULL, NULL,'2016-06-30','21:00:00',8,NULL,NULL,45),
(46, 'W38','W42',NULL, NULL,'2016-07-01','21:00:00',8,NULL,NULL,46),
(47, 'W41','W43',NULL, NULL,'2016-07-02','21:00:00',8,NULL,NULL,47),
(48, 'W40','W44',NULL, NULL,'2016-07-03','21:00:00',8,NULL,NULL,48),
(49, 'W45','W46',NULL, NULL,'2016-07-06','21:00:00',4,NULL,NULL,49),
(50, 'W47','W48',NULL, NULL,'2016-07-07','21:00:00',4,NULL,NULL,50),
(51, 'W49','W50',NULL, NULL,'2016-07-10','21:00:00',2,NULL,NULL,51);



-- 
-- Dumping data for table `em16_matchtypes`
-- 

INSERT INTO `em16_matchtypes` VALUES(32, 'Vorrunden-Spiel');
INSERT INTO `em16_matchtypes` VALUES(16, 'Achtelfinale');
INSERT INTO `em16_matchtypes` VALUES(8, 'Viertelfinale');
INSERT INTO `em16_matchtypes` VALUES(4, 'Halbfinale');
INSERT INTO `em16_matchtypes` VALUES(2, 'Finale');

-- 
-- Dumping data for table `em16_teams`
-- 

INSERT INTO `em16_teams` (`ID`, `NameLong`, `NameShort`, `FlagURL`, `GroupNr`) VALUES 
(1,'Frankreich','FRA','fra.png','A'),
(2,'Rumänien','ROU','rou.png','A'),
(3,'Albanien','ALB','alb.png','A'),
(4,'Schweiz','SUI','sui.png','A'),
(5,'Wales','WLS','wls.png','B'),
(6,'Slowakei','SVK','svk.png','B'),
(7,'England','ENG','eng.png','B'),
(8,'Russland','RUS','ned.png','B'),
(9,'Polen','POL','pol.png','C'),
(10,'Nordirland','NIR','nir.png','C'),
(11,'Deutschland','GER','ger.png','C'),
(12,'Ukraine','UKR','ukr.png','C'),
(13,'Türkei','TUR','tur.png','D'),
(14,'Kroatien','CRO','cro.png','D'),
(15,'Spanien','ESP','esp.png','D'),
(16,'Tschechien','CZE','cze.png','D'),
(17,'Irland','IRL','irl.png','E'),
(18,'Schweden','SWE','swe.png','E'),
(19,'Belgien','BEL','bel.png','E'),
(20,'Italien','ITA','ita.png','E'),
(21,'Österreich','AUT','aut.png','F'),
(22,'Ungarn','HUN','hun.png','F'),
(23,'Portugal','POR','por.png','F'),
(24,'Island','ISL','isl.png','F');

INSERT INTO `Gruppen` VALUES (12, 'em2016', 'Teilnehmer am Tippspiel EM 2016');