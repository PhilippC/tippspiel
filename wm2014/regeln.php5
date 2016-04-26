<?php

  require_once("./script/UIWeb1Classes.inc.php5");

  require_once("./script/Tippspiel.db.class.php5");



  require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();

  $TippSpielUI->OutputHeader("Spielregeln des Tippspiels");

  $Tippspiel = new CTippSpielDB();



?>
<div style="width:100%;padding:auto" >

<div class="RulesBox Box">
<div class="BoxTitle">Benutzeranmeldung</div>
<div class="BoxContent">
<ul>
<li>Mitspielen darf jeder - aber nur als ein Benutzer pro Person. Eine gültige Mail-Adresse muss angegeben werden.</li>
</ul>
</div>
</div>  <br />
<br />
<div class="RulesBox Box">
<div class="BoxTitle">Punktevergabe und Platzierung</div>
<div class="BoxContent">
<ul>
<li>Ergebnis exakt getroffen: 4 Punkte</li>
<li>Tordifferenz korrekt: 3 Punkte</li>
<li>"Tendenz", also Sieger bzw. Unentschieden richtig: 2 Punkte</li>
<li>Die Plätze sind sortiert nach den Punkten der Spieler. Bei gleicher Punktzahl, ist der Spieler besser platziert,
der bei mehr Spielen 2 oder mehr Punkte erhalten hat. Ist auch diese Zahl gleich, ist der Spieler besser platziert, der
insgesamt weniger Tipps abgegeben hat.</li>
</ul>
</div>
</div> <br />
<br />
<div class="RulesBox Box">
<div class="BoxTitle">Sonstiges</div>
<div class="BoxContent">
<ul>
<li>Die Tipps müssen mindestens 45 Minuten vor Spielbeginn abgegeben werden.</li>
<li>Haftung für technische Fehler im Tippspiel ausgeschlossen!</li>
</ul>
</div>
</div>
</div>

<?php

  $TippSpielUI->OutputFooter();

 ?>