<?php
function ersetzeNonAscii($str)
{

	$was = array("�", "�", "�", "�", "�", "�", "�");
	$wie = array("ae", "oe", "ue", "Ae", "Oe", "Ue", "ss");
	$res = str_replace($was, $wie, $str); 
	//echo($str."->".$res."<br>");
	return $res;
}
?>
