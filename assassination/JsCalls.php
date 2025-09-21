<?php
	include("config.php");
	include("include/functions.php");
	
	if(isset($_GET["PlayerGpsStats"])){
		if(isset($_SESSION["id"])){
			$id = mysqli_real_escape_string($con,$_SESSION["id"]);
			if(is_numeric($id)){
				$usSql = mysqli_query($con,"SELECT cash,bank,land,leven FROM leden WHERE id='" . $id . "'");
				if(mysqli_num_rows($usSql) == 1){
					$user = mysqli_fetch_object($usSql);
					
					$landSql = mysqli_query($con,"SELECT land FROM landen WHERE id='" . $user->land . "'");
					$land = mysqli_fetch_object($landSql);
					
					$res = array("&euro; " . formatDecimaal($user->cash),"&euro; " . formatDecimaal($user->bank),formatDecimaal($user->leven),formatCountry($land->land));
					echo json_encode($res);
				}
			}
			
		}
	}
	if(isset($_GET["PlayerCrimeTimers"])){
		if(isset($_SESSION["id"])){
			$id = mysqli_real_escape_string($con,$_SESSION["id"]);
			if(is_numeric($id)){
				$usSql = mysqli_query($con,"SELECT misdaad,auto,gevangenis,reizen,heist,werken FROM leden_timers WHERE speler='" . $id . "'");
				if(mysqli_num_rows($usSql) == 1){
					$user = mysqli_fetch_object($usSql);
					
					echo json_encode($user);
				}
			}
			
		}
	}
?>

