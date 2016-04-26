# tippspiel
PHP-Implementierung eines Tippspiels für Fußball-WMs und Fußball-EMs. Ursprünglich entwickelt für die WM 2006, angepasst bis zur WM 2014. Bisher leider noch nicht für die EM 2016 aktualisiert.

Dieses Tippspiel wurde für den Einsatz in einer Jugendkirche entwickelt, in der ich 2006 aktiv war. Es wurde seither weiterentwickelt und bei jedem Turnier auch von einigen anderen christlichen Gemeinden und Organisationen genutzt.

Alle zwei Jahre müssen folgende Schritte durchgeführt werden (bisher hatte ich das übernommen, aber zur EM 2016 finde ich leider nicht mehr die Zeit):

Anlegen eines neuen Tippspiels
==============================

(Beispielhaft für 2010 -> 2012)

* Ordner wm2010 kopieren nach em2012
* Tippspiel-Dateien nach "wm2010" und "wm10" und "WM 2010" durchsuchen und entsprechend ersetzen durch "em2012", "em12" und "EM 2012".
* dbinit-Files anpassen und neuen Spielplan eintragen
* baseconfig.php5 anpassen:     define ('ACCOUNT_AKTIVIERUNGS_DATEI','/em2008/accountaktivieren.php5');
* define('BIS_ZUM_NAECHSTEN_MAL' und
  define ('TIPPSPIEL_NAME', "Die EM 2012 bei ".KIRCHE_NAME);
  anpassen
* Prüfen ob define ('TEAM_INFO_LINK', "http://de.fifa.com/associations/association=###/ranking/gender=m/index.html");
  noch gültig (gender prüfen!)
* Countdown.js und index.php5: Datum Eröffnungsspiel eintragen
* Header-Grafik anpassen (media\head-breit.xcf)

(Für spezielle Anpassungen: inc/BaseClasses/BenutzerVerw.class.php5)


