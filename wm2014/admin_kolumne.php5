<?php
  require("./script/TippspielAdminLogin.php5");

  require_once("./script/UIWeb1Classes.inc.php5");

  require_once("./script/Tipp.db.class.php5");
  require_once("./script/Team.db.class.php5");
  require_once("./script/Match.db.class.php5");
  require_once("./script/TippVerw.class.php5");
  require_once("./script/MatchVerw.class.php5");
  require_once("./script/KolumneEintrag.class.php5");
  require_once("./script/KolumneEintrag.db.class.php5");




 require_once('./script/UIFactory.inc.php5');
 $TippSpielUI = createWebUi1();

  $TippSpielUI->AdditionalHTMLHeader = '
  <style type="text/css" media="all">

	@import "widgEditor/css/widgEditor.css";
</style>
<script type="text/javascript">

function DatenInForm1Laden()
{
  ph_theEdit.updateWidgInput();
  document.getElementById(\'Form1CurrentText\').value = document.getElementById(\'TextFeld\').value;
  document.getElementById(\'Form1CurrentTitel\').value = document.getElementById(\'Titel\').value;

}
</script>

<script type="text/javascript" src="widgEditor/scripts/widgEditor.js"></script>
';
  $TippSpielUI->OutputHeader("Administrationsbereich - Kolumne verfassen");


  $strAktion = "";
  if (isset($_POST["Aktion"]))
    $strAktion = $_POST["Aktion"];
  if (isset($_GET["Aktion"]))
    $strAktion = $_GET["Aktion"];

  $UploadMessages = "";
  if (isset($_REQUEST["UploadMessages"]))
    $UploadMessages = $_REQUEST["UploadMessages"];


  $CurrentText = "";
  if (isset($_REQUEST["CurrentText"]))
    $CurrentText = $_REQUEST["CurrentText"];
  $CurrentTextEdit = str_replace("<","&lt;",
                         str_replace(">","&gt;",$CurrentText));

  $CurrentTitel = "";
  if (isset($_REQUEST["CurrentTitel"]))
    $CurrentTitel = $_REQUEST["CurrentTitel"];


  if ($strAktion=="Speichern")
  {
    $EintragDB = new CKolumneEintragDB();
    $Eintrag = new CKolumneEintrag(NULL,NULL,$CurrentTitel, $CurrentText);
    try
    {
      $EintragDB->SpeichereEintrag($Eintrag);
      echo("Eintrag gespeichert!");
      $CurrentTitel = "";
      $CurrentText = "";
    }
    catch (Exception $e)
    {
      echo("Fehler beim Speichern des Eintrags! Fehlermeldung: ".$e->getMessage());

    }

  }

  if ($strAktion=="Upload")
  {


      if ((isset($_FILES ["Bild"]) && ($_FILES ["Bild"]['name'] != "")))
      {
         $uploaddir = "bilder/kolumne/";

    	if (move_uploaded_file($_FILES ["Bild"]['tmp_name'], $uploaddir . $_FILES ["Bild"]['name']))
         {
            $Dateiname = $_FILES ["Bild"]['name'];
             chmod ($uploaddir . $Dateiname, 0777);
             $UploadMessages .= "Datei $uploaddir$Dateiname hochgeladen!\n";
	} else
         {
             $UploadMessages . "Fehler beim hochladen der Datei $Dateiname!";
	}
      }
  }


?>


<div class="Box">
<div class="BoxTitle">Bild hochladen</div>
<form action="admin_kolumne.php5" method="post" enctype="multipart/form-data"
  onsubmit="DatenInForm1Laden()" >
<input type="hidden" name="Aktion" value="Upload"> </input>
<input type="file" name="Bild"></input><br />

<?php
echo('<input type="hidden" name="UploadMessages" value="'.$UploadMessages.'"></input>');
echo('<input type="hidden" name="CurrentText" id="Form1CurrentText" value="'.$CurrentText.'"></input>');
echo('<input type="hidden" name="CurrentTitel" id="Form1CurrentTitel" value="'.$CurrentTitel.'"></input>');
?>
<br />
<input type="submit" value="Datei hochladen"></input>
<br />
</form>
<?php
echo("<pre>$UploadMessages</pre>");
?>
</div>
<br />
<br />

<div class="Box">
		<form action="admin_kolumne.php5" method="post">
            Titel:      <input type="text" name="CurrentTitel" id="Titel" value="<?php echo($CurrentTitel);?>"></input>
            <input type="hidden" value="Speichern" name="Aktion"></input>
                 <br />
<br />


			<fieldset>
				<textarea id="TextFeld" name="CurrentText" class="widgEditor nothing"><?php
if ($CurrentText == "")
  echo("Hier Text eingeben");
else
  echo("$CurrentTextEdit");
?></textarea>
			</fieldset>
			<fieldset class="submit">
				<input type="submit" value="Eintragen" />
			</fieldset>
		</form>
</div>

</body>


<?php

  $TippSpielUI->OutputFooter();
?>