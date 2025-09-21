<?php
	include("config.php");
	include("include/functions.php");
	
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php 
	include("check_login.php");
	include("check_jail.php");
	
	if($spelerInfo->mini_banner == 1){ 
		echo "<img src='images/headers/weaponshop.jpg' width='550px' height='120px' alt='wapenwinkel pic' />";
	} 
// wapen kopen uitvoeren
if(isset($_POST['wapen'])){
	$vvvvId = mysqli_real_escape_string($con,test_input($_POST['weapon']));
	if(!is_numeric($vvvvId)){
		echo print_bericht("Winkel","Dit is geen geldige bewerking!");
		exit;
	}
	if($spelerInfo->cash < $wapenPrijsArray[$vvvvId]){
		echo print_bericht("Winkel","Je hebt niet genoeg geld om deze actie uit te voeren!");
		exit;
	} else if($spelerInfo->wapen >= 1){
		echo print_bericht("Winkel","Je moet eerst je wapen verkopen voor je een ander wapen kan kopen.");
		exit;
	} else {
		$pppoen = number_format($wapenPrijsArray[$vvvvId],0,",",".");
		mysqli_query($con,"UPDATE leden SET wapen='" . $vvvvId . "', cash=cash-'" .$wapenPrijsArray[$vvvvId] . "' WHERE id='" . $spelerInfo->id . "'");
		echo print_bericht("Winkel","Je hebt een " . $wapenRangen[$vvvvId] . " gekocht voor &euro; " . $pppoen . ", deze is nu tussen je bezittingen gezet.");
		exit;
	}
}
// verdediging kopen uitvoeren
if(isset($_POST['defence'])){
	$vvvvId = mysqli_real_escape_string($con,test_input($_POST['verdediging']));
	if(!is_numeric($vvvvId)){
		echo print_bericht("Winkel","Dit is geen geldige bewerking!");
		exit;
	}
	if($spelerInfo->cash < $verdedigingPrijsArray[$vvvvId]){
		echo print_bericht("Winkel","Je hebt niet genoeg geld om deze actie uit te voeren!");
		exit;
	} else if($spelerInfo->verdediging >= 1){
		echo print_bericht("Winkel","Je moet eerst je verdediging verkopen voor je een andere verdediging kan kopen.");
		exit;
	} else {
		$pppoen = number_format($verdedigingPrijsArray[$vvvvId],0,",",".");
		$verdedigingPrijs = $verdedigingPrijsArray[$vvvvId];
		mysqli_query($con,"UPDATE leden SET verdediging='" . $vvvvId . "', cash=cash-'" . $verdedigingPrijsArray[$vvvvId] . "' WHERE id='" . $spelerInfo->id . "'");
		echo print_bericht("Winkel","Je hebt een " . $verdedigingRangen[$vvvvId] . " gekocht voor &euro; " . $pppoen . ", deze is nu tussen je bezittingen gezet.");
		exit;
	}
}
// speciaal wapen kopen uitvoeren
/*
if(isset($_POST['special'])){
	$vvvvId = mysqli_real_escape_string($con,test_input($_POST['speciaal']));
	$wbes = $speciaalWapenArray[$vvvvId];
	if(!is_numeric($vvvvId)){
		echo print_bericht("Speciaal kopen","Sorry, dit is geen geldig item!");
		exit;
	} else {
		for($i=1; $i <= count($speciaalWapenArray)-1; $i++){
			if($speciaalWapenArray[$i] == $wbes){ $best = 1; }
		}
	}
	if($best != 1){
		echo print_bericht("Winkel","Dit is geen geldige bewerking!");
		exit;
	}
	if($spelerInfo->{$speciaalWapenArray[$vvvvId]} == 1){
		echo print_bericht("Winkel","Je hebt al een " . $speciaalWapenNaamArray[$vvvvId] . " in je bezit.");
		exit;
	} else if($spelerInfo->cash < $speciaalWapenPrijsArray[$vvvvId]){
		echo print_bericht("Winkel","Je hebt niet genoeg geld om dit wapen te kopen!");
		exit;
	}  else {
		$pppoen = formatDecimaal($speciaalWapenPrijsArray[$vvvvId]);
		mysqli_query($con,"UPDATE leden SET " . $speciaalWapenArray[$vvvvId] . "='1', cash=cash-'" . $speciaalWapenPrijsArray[$vvvvId] . "' WHERE id='" . $spelerInfo->id . "'");
		echo print_bericht("Winkel","Je hebt een " . $speciaalWapenNaamArray[$vvvvId] . " gekocht voor &euro; " . $pppoen . ", deze is nu tussen je bezittingen gezet.");
		exit;
	}
}*/
// Vervoer kopen
if(isset($_POST["transport"])){
	$gelukt = true;
	$bericht = "Er ging iets mis.";
	$id = mysqli_real_escape_string($con,test_input($_POST["vervoer"]));
	if(!is_numeric($id)){
		$bericht = "Je moet een geldig vervoersmiddel selecteren.";
		$gelukt = false;
	}
	
	if($gelukt && $spelerInfo->vervoer != 1){
		$bericht = "Je moet eerst je ander vervoersmiddel verkopen.";
		$gelukt = false;
	}
	if($gelukt){
		$vervoerQuery = mysqli_query($con,"SELECT * FROM shop_vervoer WHERE id='" . $id . "'");
		if(mysqli_num_rows($vervoerQuery) == 1){
			$vervoer = mysqli_fetch_object($vervoerQuery);
			if($spelerInfo->cash >= $vervoer->prijs){
				$bericht = "Je hebt een " . $vervoer->naam . " gekocht voor &euro; " . formatDecimaal($vervoer->prijs) . ".";
				mysqli_query($con,"UPDATE leden SET cash=cash-'" . $vervoer->prijs . "', vervoer='" . $id . "' WHERE id='" . $spelerInfo->id . "'");
			} else {
				$bericht = "Je hebt niet genoeg geld om een " . $vervoer->naam . " te kopen.";
			}
		} else {
			$bericht = "Dit vervoersmiddel bestaat niet!";
		}
	}
	echo print_bericht("Winkel",$bericht);
}

// wapen verkopen uitvoeren
if(isset($_GET['wv'])){
	$wapenId = mysqli_real_escape_string($con,test_input($_GET['wv']));
	if(!is_numeric($wapenId)){
		echo print_bericht("Wapen verkopen","Sorry, dit is geen geldig item!");
		exit;
	} else {
		$pppoen = formatDecimaal($wapenVerkoopPrijsArray[$spelerInfo->wapen]);
		echo print_bericht("Wapen verkopen","Je hebt je " . $wapenRangen[$spelerInfo->wapen] . " verkocht voor &euro; " . $pppoen . "");
		mysqli_query($con,"UPDATE leden SET wapen='0', cash=cash+'" . $wapenVerkoopPrijsArray[$spelerInfo->wapen] . "' WHERE id='" . $spelerInfo->id . "'");
		exit;
	}
}
// verdediging verkopen uitvoeren
if(isset($_GET['vv'])){
	$vId = mysqli_real_escape_string($con,test_input($_GET['vv']));
	if(!is_numeric($vId)){
		echo print_bericht("Verdediging verkopen","Sorry, dit is geen geldig item!");
		exit;
	} else {
		$pppoen = formatDecimaal($verdedigingVerkoopPrijsArray[$spelerInfo->verdediging]);
		echo print_bericht("Verdediging verkopen","Je hebt je " . $verdedigingRangen[$spelerInfo->verdediging] . " verkocht voor &euro; " . $pppoen . "");
		mysqli_query($con,"UPDATE leden SET verdediging='0', cash=cash+'" . $verdedigingVerkoopPrijsArray[$spelerInfo->verdediging] . "' WHERE id='" . $spelerInfo->id . "'");
		exit;
	}
}
// speciaal wapen vekropen
/*
if(isset($_GET['sv'])){
	$vId = mysqli_real_escape_string($con,test_input($_GET['sv']));
	if(!is_numeric($vId)){
		echo print_bericht("Wapen verkopen","Sorry, dit is geen geldig item!");
		exit;
	}
	$sql = "SELECT * FROM leden WHERE " . $speciaalWapenArray[$vId] . "='1' AND id='" . $spelerInfo->id . "'";
	$ssst = mysqli_query($con,$sql);

	if(mysqli_num_rows($ssst) <= 0){
		echo print_bericht("Winkel","Dit is geen geldige bewerking!");
		exit;
	} else {
		$pppoen = formatDecimaal($speciaalWapenVerkoopPrijsArray[$vId]);
		echo print_bericht("Speciaal wapen verkopen","Je hebt je " . $speciaalWapenNaamArray[$vId] . " verkocht voor &euro;  " . $pppoen . "");
		mysqli_query($con,"UPDATE leden SET " . $speciaalWapenArray[$vId] . "='0', cash=cash+'" . $speciaalWapenVerkoopPrijsArray[$vId] . "' WHERE id='" . $spelerInfo->id . "'");
		exit;
	}
}
*/
//Vervoer verkopen
if(isset($_GET["selltransport"])){
	$bericht = "Er ging iets mis.";
	if($spelerInfo->vervoer >= 2){
		$vervoerQuery = mysqli_query($con,"SELECT * FROM shop_vervoer WHERE id='" . $spelerInfo->vervoer . "'");
		if(mysqli_num_rows($vervoerQuery) == 1){
			$vervoer = mysqli_fetch_object($vervoerQuery);
			$poen = $vervoer->prijs/2;
			mysqli_query($con,"UPDATE leden SET cash=cash+'" . $poen . "', vervoer='1' WHERE id='" . $spelerInfo->id . "'");
			$bericht = "Je hebt een " . $vervoer->naam . " verkocht voor &euro; " . formatDecimaal($poen) . ".";
		}
	}
	echo print_bericht("Winkel",$bericht);
}

?>
<table width="550px" colspan="1">
	<tr>
		<td class="table_subTitle center" width="50%" colspan="1">De Winkel</td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" colspan="1">Het is belangrijk als crimineel om je veiligheid onder controle te houden. Voor een scherpe prijs kan u hier enkele van de laatste nieuwe snufjes kopen!</td>
	</tr>
</table>
<form method="post" action="winkel.php">
	<table width="550px" class="inhoud_table" colspan="2">
		<tr>
			<td class="table_subTitle center" width="50%" colspan="2">Wapens</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="weapon" value="1" id="knuppel" checked /><label for="knuppel"> Knuppel</label></td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 150.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="weapon" value="2" id="revolver" /><label for="revolver"> Revolver</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 500.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="weapon" value="3" id="9mm" /><label for="9mm"> 9MM</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 2.500.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="weapon" value="4" id="uzi" /><label for="uzi"> Uzi</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 15.000.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="weapon" value="5" id="ak47" /><label for="ak47"> AK-47</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 50.000.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="100%" colspan="2"><input type="submit" class="button_form" value="Koop wapen" name="wapen" /></td>
		</tr>
	</table>
</form>
<form method="post" action="winkel.php">
	<table width="550px" class="inhoud_table" colspan="2">
		<tr>
			<td class="table_subTitle center" width="50%" colspan="2">Verdediging</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="verdediging" value="1" id="helm" checked /><label for="knuppel"> Kogelwerende helm</label></td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 1.500.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="verdediging" value="2" id="vest" /><label for="vest"> Kogelwerend vest</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 5.000.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="verdediging" value="3" id="auto" /><label for="auto"> Kogelwerende limo</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 25.000.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="100%" colspan="2"><input type="submit" class="button_form" value="Koop verdediging" name="defence" /></td>
		</tr>
	</table>
</form>
<?php /*
<form method="post" action="winkel.php">
	<table width="550px" class="inhoud_table" colspan="2">
		<tr>
			<td class="table_subTitle center" width="50%" colspan="2">Speciaal</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="speciaal" value="1" id="sss" checked /><label for="sss">[<a href="faq.php?x=sniper">?</a>] Sniper Rifle .50</label></td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 250.000.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="speciaal" value="2" id="rrr" /><label for="rrr">[<a href="faq.php?x=bazooka">?</a>] Rocket Launcher</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 250.000.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="speciaal" value="5" id="hhh" /><label for="hhh">[<a href="faq.php?x=minigun">?</a>] Minigun</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 500.000.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="speciaal" value="4" id="ggg" /><label for="ggg">[<a href="faq.php?x=antibazooka">?</a>]Radar bescherming</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 150.000.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="50%" colspan="1"><input type="radio" name="speciaal" value="3" id="zzz" /><label for="zzz">[<a href="faq.php?x=antisniper">?</a>] Anti sniper spotter</td>
			<td class="table_mainTxt" width="50%" colspan="1">&euro; 100.000.000</td>
		</tr>
		<tr>
			<td class="table_mainTxt" width="100%" colspan="2"><input type="submit" class="button_form" value="Koop speciaal onderdeel" name="special" /></td>
		</tr>
	</table>
</form>
*/ ?>
<?php
	$vervoerQuery = mysqli_query($con,"SELECT * FROM shop_vervoer");
?>
<form method="post" action="winkel.php">
	<table width="550px" class="inhoud_table" colspan="4">
		<tr>
			<td class="table_subTitle center" width="100%" colspan="4">Vervoermiddelen</td>
		</tr>
		<tr>
			<td class="table_mainTxt outline padding_5" width="40%" colspan="1">Naam</td>
			<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Aankoop prijs</td>
			<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Reis Prijs</td>
			<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Reis Tijd</td>
		</tr>
		<?php while($vervoer = mysqli_fetch_object($vervoerQuery)){ ?>
			<tr>
				<td class="table_mainTxt" width="40%" colspan="1"><input type="radio" name="vervoer" value="<?php echo $vervoer->id; ?>" id="v_<?php echo $vervoer->id; ?>" <?php echo ($spelerInfo->vervoer == $vervoer->id ? "checked" : "") ?> /><label for="v_<?php echo $vervoer->id; ?>"><?php echo $vervoer->naam; ?></label></td>
				<td class="table_mainTxt" width="20%" colspan="1">&euro; <?php echo formatDecimaal($vervoer->prijs); ?></td>
				<td class="table_mainTxt" width="20%" colspan="1">&euro; <?php echo formatDecimaal($vervoer->reisPrijs); ?></td>
				<td class="table_mainTxt" width="20%" colspan="1"><?php echo ($vervoer->reisTijd/60); ?> minuten</td>
			</tr>
		<?php } ?>
		<tr>
			<td class="table_mainTxt" width="100%" colspan="2"><input type="submit" class="button_form" value="Koop vervoer" name="transport" /></td>
		</tr>
	</table>
</form>