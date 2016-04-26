<?php
    //---
    //
    // TIPP-SPIEL-EINSTELLUNGEN
    //
    //---

    //Diese Zeilen müssen beim Installieren i.d.R. angepasst werden:
    //Beachten: Auch die baseconfig.php5 in inc/baseclasses/ anpassen!
    
	//Gibt das Verzeichnis an, in dem das Tippspiel liegt (relativ zum Server-Root).
    define ('TIPPSPIEL_ROOT_DIR_RELATIVE',"/wm2014");
    //..dann kann dieses Verzeichnis automatisch gesetzt werden (sollte nicht geändert werden):
    define ('TIPPSPIEL_ROOT_DIR', $_SERVER["DOCUMENT_ROOT"].TIPPSPIEL_ROOT_DIR_RELATIVE);


    //Name der Kirche/Gemeinde, die das Tippspiel anbietet
    define('KIRCHE_NAME', "Kirche XYZ");
	define('KIRCHE_WEBSITE', "");	//leer lassen oder einen Wert im Format "http://www.myhope.de" eingeben
    
    //Name des Tippspiels (wird in der Headline angezeigt)
    define ('TIPPSPIEL_NAME', "Die WM 2014 bei ".KIRCHE_NAME);
    
     //Gibt es eine Kolumne? Diese kann unter /admin_kolumne.php5 von einem Admin eingegeben werden
    //und wird dann auf der Startseite angezeigt. Das kann ein bisschen Leben ins Tippspiel bringen.
    define ('KOLUMNE_ANZEIGEN', false); 
    define ('KOLUMNE_TITEL', "Die Kolumne zur WM");
    
    //Das Tippspiel ist so ausgelegt, dass auf der Startseite und auf der "persönlichen" Tippseite "Werbung"
    //für Events in der Kirche (Gottesdienste, gemeinsames Fußballschauen und andere EM-bezogene Events)
    //eingebunden werden kann. Dazu müssen die .htmlsnippet-Dateien im wm20101/mychurch-Verzeichnis
    //angepasst werden. Soll keine "Werbung" angezeigt werden, kann die folgende Konstante auf false gesetzt werden.
    define ('WERBUNG_ZEIGEN', true);

	//Wenn das Tippspiel vorbei ist, wird den Spielern ihre Platzierung und ein Hinweis auf die Statistiken angezeigt.
	//Der folgende Satz wird angehängt und kann z.B. zum nächsten Tippspiel in zwei Jahren einladen:
	define('BIS_ZUM_NAECHSTEN_MAL','&Uuml;brigens: Die EM 2016 ist nur noch zwei Jahre entfernt. Wir freuen uns, wenn wir dich dann wieder bei unserem Tippspiel begr&uuml;&szlig;en k&ouml;nnen!');

    //Gibt es ein Forum? (muss ein phpBB sein unter rootdir+/forum/) 
    //(unter admin.php5 gibt es eine Funktion, die Teilnehmer des Tippspiels automatisch im Forum anzumelden)
    define ('FORUM_AVAILABLE',false);
    
    //TabellenNamen: (sind so eingestellt, dass mit der Standard-Datenbank-Initialisierung alles passt) 
    define ('MATCH_TABELLE', "wm14_matches");
    define ('TIPP_TABELLE', "wm14_tips");
    define ('TEAM_TABELLE', "wm14_teams");
    define ('KOLUMNE_TABELLE', "wm14_kolumne");
	define ('TIPPTABELLE_CACHE', "wm14_tiptabellen_cache");
        
    //Name der User-Gruppe der Tippspiel-Teilnehmer:
    define ('TIPPSPIEL_USERGROUP', "wm2014"); //wird dieser Wert geändert, muss auch in der Datenbank die entsprechende Gruppe
    										 //angelegt werden!
    
    
    
    //Wenn dieser Wert auf etwas anderes als "" gesetzt ist, wird beim Tippen zu jedem Team ein Link "Weitere Informationen"
    //angezeigt, unter dem weitere Infos abgerufen werden können.
    //### ist der Platzhalter für das Team-Kürzel (z.B. GER)
    define ('TEAM_INFO_LINK', "http://de.fifa.com/associations/association=###/ranking/gender=m/index.html");

	//kann auf false gesetzt werden, um überhaupt keine grafischen Statistiken anzuzugeigen. Dies ist bei großen Spielerzahlen ggf. sinnvoll.
	define ('ENABLE_STATIMAGES',false);
    //Sollen die Statstik-Grafiken bei jedem Aufruf der Statistiken erzeugt werden? (Sonst: nach dem Eingeben der Ergebnisse
    //erzeugt und als PNGs abspeichern). Nachteil: Langsamer. Vorteil: Belegt weniger Platz auf Server
    define ('USE_DYNAMIC_STATIMAGES',false);
    //Wieviele Spieler sollen in den gefilterten Grafiken angezeigt werden. (Bei Problemen mit Timeout beim Speichern der
    //Ergebnisse und dem Erzeugen der Grafiken in admin.php5 sollte dieser Wert erhöht werden.
    //Nach Änderung dieses Wertes sollten sofort die Grafiken neu erzeugt werden, indem in admin.php5 auf Speichern
    //geklickt wird.
    define ('STAT_FILTER_STEPS', 10);
    define ('STATIC_STATIMAGES_DIR',"bilder/statistiken/");  //wird nur benutzt, wenn USE_DYNAMIC_STATIMAGES==false
    
    //kann auf true gesetzt werden, um den Tipp-Ergebnis-Datei mittels Output-Buffering zu cachen. Diest ist nur bei sehr vielen Nutzern sinnvoll.
	define ('ENABLE_CACHE_TIPPERGVERGLEICH',false);
	define ('CACHETIME_TIPPERGVERGLEICH',60);	//Zeit in Sekunden, wie lange der Vergleich gecachet werden soll
	


    //Gibt das Verzeichnis an, unter dem die Mitgliederverwaltung liegt (standardmäßig gleich dem Tippspielverzeichnis):
    define ('MEMBER_ROOT_DIR',TIPPSPIEL_ROOT_DIR_RELATIVE);
    

    //Verzeichnis zu base.db.class, Benutzerverwaltungsklassen u.ä.:
    define ('BASECLASSES_PATH',$_SERVER["DOCUMENT_ROOT"]."/inc");


    //Tipp-Spiel-Daten
    define ('PUNKTE_FUER_ERGEBNIS', 4);
    define ('PUNKTE_FUER_DIFFERENZ', 3);
    define ('PUNKTE_FUER_TENDENZ', 2);
	
		
	//kann auf true gesetzt werden, um die Facebook-Unterstützung zu aktivieren. 
	define ('ENABLE_FACEBOOK_SUPPORT', false);
	//Wenn Sie Facebook-Support aktivieren, sollten Sie eine Facebook-Anwendung für das Tippspiel erstellen und 
	//deren Daten (Id und Secret) im folgenden eintragen.
	define ('FACEBOOK_APP_ID', ""); //ca. 15-stellige Nummer
	define ('FACEBOOK_APP_SECRET', ""); //32-stellige hex.-Zahl (bestehend aus 0-9,a-f)
	
	//ACHTUNG! Tragen Sie diese Werte auch unter inc/facebooktools.php ein!
	
	//Facebook-Support besteht dann aus zwei Teilen:
	// - auf der Tippspiel-Seite werden kleine Links angezeigt, mit denen Spieler z.B. Freunde einladen können oder ihre Tipps an ihre Pinnwand posten können
	// - das Tippspiel ist als "Facebook-App" über die Facebook-Seite erreichbar. Spieler müssen sich dann nicht mehr aufwändig anmelden, sondern nutzen ihren Facebook
	//   Account zum einloggen.
	
	//Wenn Spieler sich über Facebook anmelden, kann man von Facebook ihre Mail-Adressen bekommen. Dem muss der Spieler aber explizit zustimmen (und wenn er es nicht macht,
	//schlägt die Anmeldung fehl). Facebook empfiehlt, nur so wenig Daten wie möglich abzufragen, weil sonst deutlich weniger Leute zustimmen, daher ist dieser Wert
	//standardmäßig false:
	define ('FACEBOOK_COLLECT_MAILADDRESS', false);
	
	//Link unter der die Facebook-App erreichbar ist:
	define ('FACEBOOK_LINK', "http://apps.facebook.com/DEINE_APP/");	//"DEINE_APP" ersetzen!
	
	//Wenn Spieler etwas an ihre Pinnwand posten, dann wird das Tippspiel-Logo angezeigt und ein bisschen Text, was das Tippspiel ist.
	//Diese Werte können im folgenden konfiguriert werden:	
	define ('FACEBOOK_IMAGE_URL', "http://www.deinedomain.de/wm2014/mychurch/logoklein.jpg");
	define ('FACEBOOK_APP_NAME', "Tippspiel zur WM 2014");
	define ('FACEBOOK_APP_CAPTION', "Kostenloses Tippspiel zur WM 2014 - angeboten von ".KIRCHE_NAME);
	define ('FACEBOOK_APP_INFOTEXT', "Du bist herzlich willkommen! Einfach registrieren und mittippen!");
	define ('FACEBOOK_POST_DEFAULT', "Ich mache beim Tippspiel zur WM 2014 mit. Wer tritt gegen mich an?");

?>
