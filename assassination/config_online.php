<?php
	include("SYSTEM_CONFIGURATION.php");
	$con = mysqli_connect("localhost","assterror637_fritZakske","w00Q0cTUvS","assterror637_assassingame");
	if(!$con) {
		echo '
		<html>
		<head>
		<title>Assassination - Fout</title>
		<link rel="stylesheet" type="text/css" href="stijl' . $SYSTEM_STYLE . '.css">
		</head>
		<table align="center" width=100%>
		  <tr><td class="subTitel" colspan="2"><b>Fout</b></td></tr>
		  <tr>
		  <td class="mainTxt" colspan="1">
			<center>We zijn nu de site aan het overzetten op een andere host! Dit neemt ongeveer 5 minuten in beslag!	</center>
		  </td></tr>
		  </table>
		</body>
		</html>';
		exit;
	}
	
	include_once("sessie.php");
  
  
	// controleren of sessie nog bestaan en speler ingelogt is of niet
	if(isset($_SESSION['id']) && isset($_SESSION['pass'])){
		mysqli_query($con,"UPDATE leden SET online='" . date("Y-m-d H:i:s") . "' WHERE id='" . $_SESSION['id'] . "'");
		// Deze pagina dient om te kijken of je 5 minuten niet actief bent dan moet je opnieuw nloggen
		//include("check_login.php");
		$query1 = "SELECT * FROM leden WHERE id='" . $_SESSION['id'] . "'";
		$spelerInfo2 = mysqli_query($con,$query1);
		if(mysqli_num_rows($spelerInfo2) <= 0){
			exit;
		}
		$spelerInfo = mysqli_fetch_object($spelerInfo2);		
	}
?>
