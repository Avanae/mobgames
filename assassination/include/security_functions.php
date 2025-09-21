<?php
//---------------------------------------------------------------
//* Security functie Framework
//*
//*
//*
//* Nick Meeusen
//---------------------------------------------------------------
//------ INCLUDES
//---------------------------------------------------------------

//---------------------------------------------------------------
//------ DECODE INPUT FUNCTIES
//---------------------------------------------------------------
function decodeText($text)
{
	include("../config.php");
	// Functie die verboden tags omzet naar HTML formaat
    $text = str_replace("<", "&lt;", $text);
    $text = str_replace(">", "&gt;", $text);
    $text = str_replace("\"", "&quot;", $text);
    $text = str_replace("'", "&#039;", $text);
    $text = mysqli_real_escape_string($con,$text);
    return $text;
} 

function encodeUrl($url){
	return urlencode($url);
}
function encodeHtml($text){
	include("../config.php");
	if (get_magic_quotes_gpc()){
		$var = stripslashes($text);
		$varencode = mysqli_real_escape_string($con,$var);
		return $varencode;
	} else {
		$varencode = mysqli_real_escape_string($con,$text);
		return $varencode;
	}
}
function decodeHtml($text){
	return htmlentities($text);
}
//---------------------------------------------------------------
//------ QUERY FUNCTIES
//---------------------------------------------------------------
function emptyObject($query){
	return (mysqli_num_rows($query) <= 0 ? true : false);
}
?>
