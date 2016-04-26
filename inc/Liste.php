<?php

require("dbwrapper.php");

function EchoListe($SelectSQL, $Vorher,$Nachher)
{
	require("dbconstants.php");

	/*** DB Deklarationen ***/
	$dbError                = false;
	$strSQL                        = "";
	$idCon                         = ConnectToDatabase();

	if( !$result = ExecuteQuery( $SelectSQL ))
	exit;


	$numTmp = -1;
	while( $numTmp++ < mysql_num_rows( $result) - 1 )
	{
		mysql_data_seek( $result, $numTmp );
		$row = mysql_fetch_row( $result );

		echo "$Vorher$row[0]$Nachher";
	}
	mysql_close( $idCon );

}



function EchoMatrix($SelectSQL, $FormatString, $MaxColIndex)
{


	$strSQL= "";
	$idCon = ConnectToDatabase();


	$strSQL = $SelectSQL;

	if( !$result = ExecuteQuery( $strSQL ))
		exit;

	$numTmp = -1;
	while( $numTmp++ < mysql_num_rows( $result) - 1 )
	{
		mysql_data_seek( $result, $numTmp );
		$row = mysql_fetch_row( $result );

		$EchoString = $FormatString;

		for ($PlaceHolderCount=$MaxColIndex;$PlaceHolderCount>=0;$PlaceHolderCount--)
		{
			$EchoString = str_replace("%".($PlaceHolderCount),$row[$PlaceHolderCount],$EchoString);
		}


		for ($PlaceHolderCount=$MaxColIndex;$PlaceHolderCount>=0;$PlaceHolderCount--)
		{
			$EchoString = str_replace("%q".($PlaceHolderCount),
			str_replace("\n","",str_replace("\r","",str_replace("'","\\'",$row[$PlaceHolderCount]))),
			$EchoString);
		}



		echo "$EchoString";
	}
	mysql_close( $idCon );

}



function ExeqSimpleSQL($strSQL)

{
	require("dbconstants.php");

	$idCon = ConnectToDatabase();

	if( !$result = ExecuteQuery( $strSQL ))
	{
		mysql_close( $idCon );
		return false;
	}
	else
	{
		mysql_close( $idCon );
		return true;
	}
	

}


?>