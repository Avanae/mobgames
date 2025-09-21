<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php 
	include("check_login.php");
	$date = date("Y-m-d H:i:s");
	
	if(isset($_GET["bust"])){
		$id = mysqli_real_escape_string($con,test_input($_GET["bust"]));
		
		if(!is_numeric($id) || empty($id)){
			print_bericht("Gevangenis","Deze speler bestaat niet.");
			exit;
		}
		
		$userQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $id . "' AND status='levend' AND ban='0'");
		if(mysqli_num_rows($userQuery) <= 0){
			print_bericht("Gevangenis","Deze speler bestaat niet.");
			exit;
		}
		
		if($spelerInfo->id == $id){
			print_bericht("Gevangenis","Je kan jezelf niet uitbreken!");
			exit;
		}
		
		$chance = rand(1,100);
		if($chance >= 80){
			$user = mysqli_fetch_object($userQuery);
			print_bericht("Gevangenis","Je hebt " . $user->login . " met succes uit de gevangenis gebroken!");
			mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $spelerInfo->id . "','" . $user->id . "','Bust Out','<a href=\"speler_profiel.php?x=" . $spelerInfo->id . "\">" . $spelerInfo->login . "</a> heeft je met succes uit de gevangenis gebroken!',NOW())");
		} else {
			$time = date("Y-m-d H:i:s",(time()+60));
			mysqli_query($con,"UPDATE leden_timers SET gevangenis='" . $time . "' WHERE speler='" . $spelerInfo->id . "'");
			print_bericht("Gevangenis","Mislukt! Je zit nu zelf in de gevangenis...");
		}
		exit;
	}
	if(isset($_GET["borg"])){
		$id = mysqli_real_escape_string($con,test_input($_GET["borg"]));
		
		if(!is_numeric($id) || empty($id)){
			print_bericht("Gevangenis","Deze speler bestaat niet.");
			exit;
		}
		
		$userQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $id . "' AND status='levend' AND ban='0'");
		if(mysqli_num_rows($userQuery) <= 0){
			print_bericht("Gevangenis","Deze speler bestaat niet.");
			exit;
		}
		$user = mysqli_fetch_object($userQuery);
		
		$userTimeQuery = mysqli_query($con,"SELECT * FROM leden_timers WHERE speler='" . $id . "'");
		$userTime = mysqli_fetch_object($userTimeQuery);
		
		$borg = (strtotime($userTime->gevangenis)-time())*(50*$user->rang);
		
		if($spelerInfo->cash < $borg){
			print_bericht("Gevangenis","Je hebt niet genoeg geld om deze speler zijn borg te betalen.");
			exit;
		}
		
		mysqli_query($con,"UPDATE leden SET cash=cash-'" . $borg . "' WHERE id='" . $spelerInfo->id . "'");
		if($id != $spelerInfo->id){
			mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $spelerInfo->id . "','" . $user->id . "','Bust Out','<a href=\"speler_profiel.php?x=" . $spelerInfo->id . "\">" . $spelerInfo->login . "</a> heeft je borg betaald en je bent nu terug vrij!',NOW())");
		}
		mysqli_query($con,"UPDATE leden_timers SET gevangenis=NOW() WHERE id='" . $id . "'");
		print_bericht("Gevangenis","Je betaalde &euro; " . formatDecimaal($borg) . " en " . $user->login . " is nu terug op vrije voeten!");
		exit;
	}
	
	$usersJailQuery = mysqli_query($con,"SELECT * FROM leden_timers WHERE gevangenis >'" . $date . "'");
?>
	<table width="550px" class="inhoud_table" colspan="4">
		<tr>
			<td class='table_subTitle' colspan='4'>De gevangenis</td>
		</tr>
		<tr>
			<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Speler</td>
			<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Tijd</td>
			<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Borg</td>
			<td class="table_mainTxt outline padding_5" width="20%" colspan="1"></td>
		</tr>
		<?php if(mysqli_num_rows($usersJailQuery) >= 1){
			while($user = mysqli_fetch_object($usersJailQuery)){ 
				$victemQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $user->speler . "'");
				$victem = mysqli_fetch_object($victemQuery);
				$totalInJail = 0;
				if($victem->land == $spelerInfo->land){ 
					$time = GetWaitTime(time(),strtotime($user->gevangenis));
					$borg = (strtotime($user->gevangenis)-time())*(50*$victem->rang);
					echo "<tr>";
						echo "<td class=\"table_mainTxt\" width=\"20%\" colspan=\"1\"><a href=\"speler_profiel.php?x=" . $victem->id . "\">" . $victem->login . "</a></td>";
						echo "<td class=\"table_mainTxt\" width=\"20%\" colspan=\"1\">" . $time . "</td>";
						echo "<td class=\"table_mainTxt\" width=\"20%\" colspan=\"1\">&euro; " . formatDecimaal($borg) . "</td>";
						echo "<td class=\"table_mainTxt\" width=\"40%\" colspan=\"1\"><a href=\"crime_jail.php?bust=" . $victem->id . "\">Bust out</a> | <a href=\"crime_jail.php?borg=" . $victem->id . "\">Betaal Borg</a></td>";
					echo "</tr>";
				
					$totalInJail++;
				} 
			}
			if($totalInJail == 0){
				echo "<tr>";
					echo "<td class=\"table_mainTxt outline padding_5\" width=\"100%\" colspan=\"4\">Er zitten momenteel geen spelers in de gevangenis.</td>";
				echo "</tr>";
			} ?>
		<?php } else { ?>
			<tr>
				<td class="table_mainTxt outline padding_5" width="100%" colspan="4">
					Er zitten momenteel geen spelers in de gevangenis.	
				</td>
			</tr>
		<?php } ?>
	</table>
