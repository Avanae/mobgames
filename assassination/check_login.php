
<?php
	include("config.php");
	if(isset($_SESSION['id']) == null){
		print "
			<table width='550px'>
				<tr>
					<td class='table_subTitle'>FOUT</td>
				</tr>
				<tr>
					<td class='table_mainTxt limegreen bold center outline'>U hebt geen bevoegdheid tot deze pagina.</td>
				</tr>
			</table>
		";
		exit;
	}
	if(isset($_SESSION['id']) && isset($_SESSION['pass'])){
		mysqli_query($con,"UPDATE leden SET online=NOW() WHERE id='" . $_SESSION['id'] . "' AND wachtwoord='" . $_SESSION['pass'] . "'");
		// Deze pagina dient om te kijken of je 5 minuten niet actief bent dan moet je opnieuw nloggen
		//include("check_login.php");
		$query1 = "SELECT * FROM leden WHERE id='" . $_SESSION['id'] . "' AND wachtwoord='" . $_SESSION['pass'] . "'";
		$spelerInfo2 = mysqli_query($con,$query1);
		$spelerTTGGHHYY = mysqli_num_rows($spelerInfo2);
		if($spelerTTGGHHYY <= 0){
			exit;
		}
		$spelerInfo = mysqli_fetch_object($spelerInfo2);		
	}
?>