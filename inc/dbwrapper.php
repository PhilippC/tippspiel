<?php



function ConnectToDatabase()
{
	require("dbconstants.php");
	$idCon                         = mysql_connect($DB_Address, $DB_User, $DB_Pwd );


	/************************************
	 *        Datenbank connection pruefen        *
	 ************************************/

	if(!$idCon)
	{
		$dbError = true;
		die("<b>Fatal error!</b><br> mysql_connect failed! <br> \n");
		 
	}
	else
	{
		//echo "<!-- Connection OK!  -->\n\n";
		
			
		mysql_query("SET CHARACTER SET 'utf8'", $idCon);
		mysql_query("SET NAMES utf8");
	  

		//Datenbank auswählen:

		if( !$result = mysql_select_db($database, $idCon))
		{
			$dbError = true;
			echo( "<!-- SQL error [mysql_select_db($database, $idCon)] -->\n" );
			die ("Invalid query");
		}
		else
		{
			//echo "<!-- OK: [mysql_select_db($database, $idCon)] -->\n";
		}

		return $idCon;
	}
}

function ExecuteQuery($strSQL)
{
	if( !$result = mysql_query( $strSQL ))
	{
		$dbError = true;
		echo( "<!-- SQL error [$strSQL] -->\n" );
		echo ("<b>Invalid query</b><br>");
		exit;
	}
	else
	{
		//echo "<!-- OK SQL: [$strSQL] -->\n";
	}
	return $result;

}

?>