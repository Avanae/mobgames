<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");
	if($spelerInfo->mini_banner == 1){ 
		echo "<img src='images/headers/wereldkaart.gif' width='550px' height='120px' alt='wapenwinkel pic' />";
	} 
	// Controleren of de speler al kan reizen
	$reizenQuery = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(reizen) AS werken,0 FROM leden_timers WHERE speler='" . $spelerInfo->id . "'");
	$reizen = mysqli_fetch_object($reizenQuery);
	if($reizen->werken > time()){
		$res = GetWaitTime(time(),$reizen->werken);
		echo print_bericht("Reizen","Je bent nog " . $res . " aan het wachten voor je terug mag reizen.");
		exit;
	}

	$landenQuery = mysqli_query($con,"SELECT * FROM landen");

	$currentAirportQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $spelerInfo->land . "'");
	$currentLand = mysqli_fetch_object($currentAirportQuery);

	$airportQuery = mysqli_query($con,"SELECT * FROM landen_vliegveld WHERE land='" . $spelerInfo->land . "'");
	$airport = mysqli_fetch_object($airportQuery);

	$eigenaarTxt = "";
	if($airport->speler == 0)
	{
		$eigenaarTxt = "<a href=\"airport.php?buy\">Koop vliegveld voor &euro; 20.000.000!</a>";
	} else {
		$eigenaarQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $airport->speler . "'");
		$eigenaar = mysqli_fetch_object($eigenaarQuery);
		
		$eigenaarTxt = "<a href=\"speler_profiel.php?id=" . $eigenaar->id . "\">" . $eigenaar->login . "</a>";
	}
	if(isset($_GET["buy"])){
		$bericht = "Er ging iets mis bij het aankopen van het vliegveld.";
		$gelukt = true;
		$land = mysqli_real_escape_string($con,test_input($_GET["buy"]));

		$ownerQuery = mysqli_query($con,"SELECT * FROM landen_vliegveld WHERE speler='" . $spelerInfo->id . "'");
		if(mysqli_num_rows($ownerQuery) == 1)
		{
			$gelukt = false;
			$bericht = "Je mag maar 1 vliegveld bezitten.";
		}
			
		if($gelukt && $spelerInfo->cash < 20000000){
			$gelukt = false;
			$bericht = "Je hebt &euro; 20.000.000 cash nodig om dit vliegveld te kopen.";
		}
			
		if($gelukt){
			$vliegveldQuery = mysqli_query($con,"SELECT * FROM landen_vliegveld WHERE land='" . $spelerInfo->land . "'");
			if(mysqli_num_rows($vliegveldQuery) == 1){
				$vliegveld = mysqli_fetch_object($vliegveldQuery);
				if($vliegveld->speler == 0){
					mysqli_query($con,"UPDATE leden SET cash=cash-'20000000' WHERE id='" . $spelerInfo->id . "'");
					mysqli_query($con,"UPDATE landen_vliegveld SET speler='" . $spelerInfo->id . "' WHERE land='" . $spelerInfo->land . "'");
					
					$bericht = "Je hebt dit vliegveld met succes gekocht!";
				} else {
					$gelukt = false;
					$bericht = "Dit vliegveld heeft al een eigenaar.";
				}
			}
		}

		print_bericht("Reizen",$bericht);
	}
	if(isset($_POST["travel"])){
		$land = mysqli_real_escape_string($con,test_input($_POST["country"]));
		if(is_numeric($land)){
			$bericht = "";
			$gelukt = true;
			
			$vervoerQuery = mysqli_query($con,"SELECT * FROM shop_vervoer WHERE id='" . $spelerInfo->vervoer . "'");
			$vervoer = mysqli_fetch_object($vervoerQuery);
			
			if($land == $spelerInfo->land){
				$gelukt = false;
				$bericht = "Je bent al in dit land!";
			}
			if($gelukt){
				$landenQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $land . "'");
				if($gelukt && mysqli_num_rows($landenQuery) != 1){
					$gelukt = false;
					$bericht = "Je moet een geldig land selecteren!";
				}
				
				if($gelukt && $spelerInfo->cash >= $vervoer->reisPrijs){
					$land = mysqli_fetch_object($landenQuery);
					mysqli_query($con,"UPDATE leden SET cash=cash-'" . $vervoer->reisPrijs . "', land='" . $land->id . "' WHERE id='" . $spelerInfo->id . "'");
					$newTimer = strtotime(date("Y-m-d H:i:s"))+$vervoer->reisTijd;
					mysqli_query($con,"UPDATE leden_timers SET reizen='" . date("Y-m-d H:i:s",$newTimer) . "'");
					
					$vliegveldQuery = mysqli_query($con,"SELECT * FROM landen_vliegveld WHERE land='" . $spelerInfo->land . "'");
					$vliegveld = mysqli_fetch_object($vliegveldQuery);
					if($vliegveld->speler != 0){
						$omzet = $vervoer->reisPrijs/2;
						mysqli_query($con,"UPDATE landen_vliegveld SET omzet=omzet+'" . $omzet . "' WHERE id='" . $vliegveld->id . "'");
					}
					$bericht = "Je bent met succes naar " . utf8_encode($land->land) . " afgezakt.";
				}
			}
			print_bericht("Reizen",$bericht);
			exit;
		} else {
			print_bericht("Reizen","Je moet een geldig land selecteren!");
			exit;	
		}
	}
	if(isset($_POST["pin"])){
		$bericht = "Er ging iets mis.";
		$vliegveldQuery = mysqli_query($con,"SELECT * FROM landen_vliegveld WHERE land='" . $spelerInfo->land . "' AND speler='" . $spelerInfo->id . "'");
		if(mysqli_num_rows($vliegveldQuery) == 1)
		{
			$vliegveld = mysqli_fetch_object($vliegveldQuery);
			if($vliegveld->omzet >= 1){
				mysqli_query($con,"UPDATE leden SET cash=cash+'" . $vliegveld->omzet . "' WHERE id='" . $spelerInfo->id . "'");
				mysqli_query($con,"UPDATE landen_vliegveld SET omzet='0' WHERE land='" . $spelerInfo->land . "' AND speler='" . $spelerInfo->id . "'");
				$bericht = "Je hebt met succes &euro; " . formatDecimaal($vliegveld->omzet) . " afgehaald.";
			} else {
				$bericht = "Er staat geen geld op de bank van dit vliegveld.";
			}
		}
		print_bericht("Reizen",$bericht);
	}
?>

<table width="550px" colspan="3">
	<tr>
		<td class='table_subTitle' colspan='3'>Vliegveld in <?php echo utf8_encode($currentLand->land); ?></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="20%" colspan="1">Eigenaar</td>
		<td class="table_mainTxt padding_5" width="80%" colspan="2"><?php echo $eigenaarTxt; ?></td>
	</tr>
	<?php if($airport->speler == $spelerInfo->id){ ?>
		<tr>
			<td class="table_mainTxt padding_5" width="20%" colspan="1">Omzet</td>
			<td class="table_mainTxt padding_5" width="80%" colspan="2">&euro; <?php echo formatDecimaal($airport->omzet); ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"></td>
			<td class="table_mainTxt padding_5" width="80%" colspan="2">
				<form method="post" action="airport.php"><input type="submit" class="button_form" value="Alles afhalen" name="pin" /></form>
			</td>
		</tr>
	<?php } ?>
</table>
<?php
	$vervoerQuery = mysqli_query($con,"SELECT * FROM shop_vervoer WHERE id='" . $spelerInfo->vervoer . "'");
	$vervoer = mysqli_fetch_object($vervoerQuery);
	
	$reisTijd = $vervoer->reisTijd/60;
?>
<form method="post" action="airport.php">
	<table width="550px" colspan="4">
		<tr>
			<td class='table_subTitle' colspan='4'>Reizen</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="40%" colspan="1">Selecteer een land</td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1">
				<select class="select_field" name="country">
					<?php while($land = mysqli_fetch_object($landenQuery)){ ?>
						<option value="<?php echo $land->id; ?>"><?php echo utf8_encode($land->land); ?></option>
					<?php } ?>
				</select>
			</td>
			<td class="table_mainTxt padding_5 outline" width="20%" colspan="1">Reistijd</td>
			<td class="table_mainTxt padding_5 outline" width="20%" colspan="1">Kosten</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="40%" colspan="1">Vervoersmiddel</td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1">
				<p class="padding_5"><?php echo $vervoer->naam; ?></p>
			</td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><?php echo $reisTijd; ?> minuten</td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1">&euro; <?php echo formatDecimaal($vervoer->reisPrijs); ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="40%" colspan="1"></td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><input type="submit" class="button_form" name="travel" value="Reizen" /></td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"></td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"></td>
		</tr>
	</table>
</form>