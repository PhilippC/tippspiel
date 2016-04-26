<?php
/////////////////////////////////////////////////////

function getCheckMailSubject()
{
	return "Dein Account beim Tippspiel: Anmeldung";
}
function getCheckMailBody($Name, $ActivationLink)
{
 return "Hallo $Name,
herzlich willkommen beim Tippspiel!

Deine Anmeldung ist nahezu abgeschlossen, du musst nur noch auf folgenden Link klicken, um dein Benutzerkonto zu aktivieren:
$ActivationLink

Sollte der Link in deinem Mail-Programm nicht als Link erscheinen oder umgebrochen sein, dann kopiere ihn als eine Zeile in die Adressleiste deines Browsers und druecke die ENTER-Taste.

Viel Spass,

der Administrator";
}

/////////////////////////////////////////////////////

function getPasswordMailSubject()
{
	return "Dein Tippspiel-Passwort";
}
function getPasswordMailResetBody($Name, $resetLink)
{
return "Hallo $Name,
du erhaeltst diese Mail, da du auf der Tippspiel-Seite angegeben hast, dein Passwort nicht mehr zu wissen. Du kannst ein neues Passwort setzen, wenn du auf folgende Seite gehst:

$resetLink

Viel Spass beim Tippen!
";
}

function getPasswordMailPlainBody($Name, $pwd)
{

return "Hallo $Name,
du erhaeltst diese Mail, da du auf der Tippspiel-Seite angegeben hast, dein Passwort nicht mehr zu wissen - kein Problem, hier ist es:

$pwd

Viel Spass beim Tippen!
";
}
?>