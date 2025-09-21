<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php 
	include("check_login.php");
	include("check_jail.php");
	
	if(isset($_SESSION['id'])){
		if($spelerInfo->mini_banner == 1){
		echo "<table width=\"550px\" colspan=\"1\">
		<tr>
			<td colspan='1'><img src='images/headers/villa.jpg' width='550px' height='120px' alt='kerkhof' /></td>
		</tr></table>";
		}
	}
	
	// Controleren of de speler een huis heeft in dit land
	$villaQuery = mysqli_query($con,"SELECT * FROM leden_huizen WHERE speler='" . $spelerInfo->id . "' AND land='" . $spelerInfo->land . "'");
	
	
	if(isset($_POST["buy"])){
		$id = mysqli_real_escape_string($con,test_input($_POST["villaType"]));
		
		if(!is_numeric($id)){
			print_bericht("Huis","Er ging iets mis.");
			exit;
		}
		
		$huisQuery = mysqli_query($con,"SELECT * FROM villa_types WHERE id='" . $id . "'");
		if(mysqli_num_rows($huisQuery) != 1){
			print_bericht("Huis","Dit huis bestaat niet.");
			exit;
		}
		
		$huis = mysqli_fetch_object($huisQuery);
		if($spelerInfo->cash < $huis->prijs){
			print_bericht("Huis","Je hebt niet genoeg geld op een " . $huis->naam . " te kopen.");
			exit;
		} else {
			$checkHuisQuery = mysqli_query($con,"SELECT * FROM leden_huizen WHERE speler='" . $spelerInfo->id . "' AND land='" . $spelerInfo->land . "'");
			if(mysqli_num_rows($checkHuisQuery) == 0){
				mysqli_query($con,"UPDATE leden SET cash=cash-'" . $huis->prijs . "' WHERE id='" . $spelerInfo->id . "'");
				mysqli_query($con,"INSERT INTO leden_huizen (type,land,speler) VALUES ('" . $huis->id . "','" . $spelerInfo->land . "','" . $spelerInfo->id . "')");
				print_bericht("Huis","Je hebt een " . $huis->naam . " gekocht.");
				exit;
				
			} else {
				print_bericht("Huis","Je hebt al een huis in dit land, verkoop eerst je huis om een ander type te kopen.");
				exit;
			}
		}	
	}
	if(isset($_POST["sellhouse"])){
		if(!mysqli_num_rows($villaQuery)){
			print_bericht("Villa","Je hebt geen huis in dit land.");
			exit;	
		}
		
		$villa = mysqli_fetch_object($villaQuery);
		$villaTypesQuery = mysqli_query($con,"SELECT * FROM villa_types WHERE id='" . $villa->type . "'");
		
		$type = mysqli_fetch_object($villaTypesQuery);
		
		mysqli_query($con,"UPDATE leden SET cash=cash+'" . ($type->prijs/2) . "' WHERE id='" . $spelerInfo->id . "'");
		mysqli_query($con,"DELETE FROM leden_huizen WHERE id='" . $villa->id . "'");
		mysqli_query($con," DELETE FROM villa_plantages WHERE huisid='" . $villa->id . "'");
		print_bericht("Villa","Je hebt je huis met succes verkocht voor &euro; " . ($type->prijs/2) . ".");
		exit;
	}
	
	if(isset($_POST["plant"])){
		$total = mysqli_real_escape_string($con,test_input($_POST["total"]));
		
		if(!is_numeric($total)){
			print_bericht("Wietplantage","Je gaf een ongeldig aantal planten in.");
			exit;
		}
		
		if(mysqli_num_rows($villaQuery) == 0){
			print_bericht("Wietplantage","Je moet eerst een huis kopen in dit land voor je een wietplantage kan opzetten.");
			exit;
		}
		
		$villa = mysqli_fetch_object($villaQuery);
		$typeQuery = mysqli_query($con,"SELECT * FROM villa_types WHERE id='" . $villa->type . "'");
		$type = mysqli_fetch_object($typeQuery);
		
		if($total > $type->maxPlanten){
			print_bericht("Wietplantage","Je mag maar maximum " . formatDecimaal($type->maxPlanten) . " zetten.");
			exit;
		}
		$totaalPrijs = ($total*750);
		if($spelerInfo->cash < $totaalPrijs){
			print_bericht("Wietplantage","Je hebt &euro; " . formatDecimaal($totaalPrijs). " nodig om " . formatDecimaal($totaalPrijs) . " planten te zetten.");
			exit;
		}
		
		// Controleren of er al een plantage aanwezig is
		$plantageQuery = mysqli_query($con,"SELECT * FROM villa_plantages WHERE huisid='" . $villa->id . "'");
		if(mysqli_num_rows($plantageQuery) != 0){
			print_bericht("Wietplantage","Er staat al een wietplantage in dit huis.");
			exit;
		}
		
		mysqli_query($con,"UPDATE leden SET cash=cash-'" . $totaalPrijs . "' WHERE id='" . $spelerInfo->id . "'");
		mysqli_query($con,"INSERT INTO villa_plantages (huisid,planten,oogsten) VALUES ('" . $villa->id . "','" . $total . "',NOW())");
		print_bericht("Wietplantage","Je hebt met succes een wietplantage opgezet. Ga naar je huis om het te bezoeken.");
		exit;
	}
	if(isset($_POST["water"])){
		
		if(mysqli_num_rows($villaQuery) == 0){
			print_bericht("Wietplantage","Je moet eerst een huis kopen voor je een wietplantage kan opzetten.");
			exit;
		}
		$villQuery = mysqli_query($con,"SELECT * FROM leden_huizen WHERE speler='" . $spelerInfo->id . "' AND land='" . $spelerInfo->land . "'");
		$villa = mysqli_fetch_object($villQuery);
		
		$plantageQuery = mysqli_query($con,"SELECT * FROM villa_plantages WHERE huisid='" . $villa->id . "'");
		if(mysqli_num_rows($plantageQuery) <= 0){
			print_bericht("Wietplantage","Je hebt geen wietplantage in dit land.");
			exit;
		}
		
		$plantage = mysqli_fetch_object($plantageQuery);
		// controleren of planten dood zijn (2 uur na oogstdatum
		if(time() > (strtotime($plantage->oogsten)+7200)){
			print_bericht("Wietplantage","Je bent te laat! Je planten zijn allemaal doodgegaan omdat ze zonder water zaten.");
			exit;
		} else {
			// Controleren of speler te vroeg water geeft
			if(time() < strtotime($plantage->oogsten)){
				print_bericht("Wietplantage","Je bent te vroeg! Je hebt je planten teveel water gegeven en de oogst beschadigd.");
				mysqli_query($con,"UPDATE villa_plantages SET oogsten='NOW()', health=health-'20',level=level+'1' WHERE id='" . $plantage->id . "'");
				exit;
			} else {
				// Controleren of de platen geoogst worden of bewaterd
				if($plantage->level < 5){
					mysqli_query($con,"UPDATE villa_plantages SET oogsten='NOW()',level=level+'1' WHERE id='" . $plantage->id . "'");
					print_bericht("Wietplantage","Je hebt je planten water gegeven. Goed bezig!");
					exit;
				} else {
					$rand = rand(1,3);
					$percentage = floor((($plantage->planten*$rand)/100)*$plantage->health);
					mysqli_query($con,"DELETE FROM villa_plantages WHERE id='" . $plantage->id . "'");
					mysqli_query($con,"UPDATE leden SET wiet=wiet+'" . $percentage . "' WHERE id='" . $spelerInfo->id . "'");
					print_bericht("Wietplantage","Je hebt je planten ge-oogst en hiermee " . formatDecimaal($percentage) . " kilo wiet gekregen!");
					exit;
				}
			}
		}
	}
	if(mysqli_num_rows($villaQuery) == 0){
		$villaTypesQuery = mysqli_query($con,"SELECT * FROM villa_types");	
?>
	<table width="550px" class="inhoud_table" colspan="3">
		<tr>
			<td class="table_subTitle" width="50%" colspan="3">Huis Informatie</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="100%" colspan="3">Je hebt nog geen huis in dit land. In een huis kan je wietplanten zetten en je verschuilen tegen moordpogingen.</td>
		</tr>
	</table>		
	<form method="post" action="villa.php">
		<table width="550px" class="inhoud_table" colspan="3">
			<tr>
				<td class="table_subTitle" width="50%" colspan="3">Huis Kopen</td>
			</tr>
			<tr>
				<td class="table_mainTxt outline padding_5" width="40%" colspan="1">Naam</td>
				<td class="table_mainTxt outline padding_5" width="30%" colspan="1">Prijs</td>
				<td class="table_mainTxt outline padding_5" width="30%" colspan="1">Maximum Wietplanten</td>
			</tr>
			<?php while($villa = mysqli_fetch_object($villaTypesQuery)){ ?>
			<tr>
				<td class="table_mainTxt" width="40%" colspan="1"><input type="radio" name="villaType" value="<?php echo $villa->id; ?>" id="v_<?php echo $villa->id; ?>" <?php echo ($villa->id == 1 ? "checked" : ""); ?>/><label for="v_<?php echo $villa->id; ?>"> <?php echo $villa->naam; ?></label></td>
				<td class="table_mainTxt" width="30%" colspan="1">&euro; <?php echo formatDecimaal($villa->prijs); ?></td>
				<td class="table_mainTxt" width="30%" colspan="1"><?php echo formatDecimaal($villa->maxPlanten); ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td class="table_mainTxt" width="100%" colspan="3"><input type="submit" class="button_form" name="buy" value="Kopen" /></td>
			</tr>
		</table>
	</form>
<?php 
} else {
	
	$villa = mysqli_fetch_object($villaQuery);
	
	$typeQuery = mysqli_query($con,"SELECT * FROM villa_types WHERE id='" . $villa->type . "'");
	$type = mysqli_fetch_object($typeQuery);
?>
	<table width="550px" class="inhoud_table" colspan="3">
		<tr>
			<td class="table_subTitle" width="100%" colspan="3">Jou huis</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5 outline" width="80%" colspan="2">Je kan dit huis verkopen voor &euro; <?php echo formatDecimaal($type->prijs/2); ?>. Opgelet, als je dit huis verkoopt dan ben je ook jou wietplantage in het huis kwijt!</td>
			<td class="table_mainTxt padding_5" width="20%" colspan="2"><form method="post" action="villa.php"><input type="submit" class="button_form" name="sellhouse" value="Verkopen" /></td>
		</tr>

	</table>
	<table width="550px" class="inhoud_table" colspan="4">
		<tr>
			<td class="table_subTitle" width="50%" colspan="4">Wietplantage</td>
		</tr>
		<?php
			$wietplantageQuery = mysqli_query($con,"SELECT * FROM villa_plantages WHERE huisid='" . $villa->id . "'");
			if(mysqli_num_rows($wietplantageQuery) == 0){
		?>
				<tr>
					<td class="table_mainTxt outline padding_5" width="100%" colspan="3">Hier kan je een wietplantage opzetten. Per plant kost het je &euro; 750. Een plant kan 1-3 kilo wiet droppen wanneer je deze oogst.</td>
				</tr>
				<form method="post" action="villa.php">
					<tr>
						<td class="table_mainTxt padding_5" width="30%" colspan="1"><label for="total">Aantal planten</label></td>
						<td class="table_mainTxt" width="70%" colspan="3"><input type="text" name="total" id="total" class="input_form padding_5" maxlength="6" /> / <?php echo formatDecimaal($type->maxPlanten); ?></td>
					</tr>
					<tr>
						<td class="table_mainTxt" width="30%" colspan="1"></td>
						<td class="table_mainTxt" width="70%" colspan="3"><input type="submit" name="plant" class="button_form" value="Planten" /></td>
					</tr>
				</form>
			<?php } else { 
				$wietplantage = mysqli_fetch_object($wietplantageQuery);
			?>
			<link href="stylesheets/gps_stijl.css" type="text/css" rel="stylesheet" />
				<tr>
					<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Status</td>
					<td class="table_mainTxt outline padding_5" width="80%" colspan="3">
						<?php if(time() < strtotime($wietplantage->oogsten)){ ?>
							WACHTEN
						<?php } elseif(time() > strtotime($wietplantage->oogsten) && time() < strtotime($wietplantage->oogsten)+7200) { ?>
							<span class="limegreen"><?php echo ($wietplantage->level < 4 ? "WATER GEVEN" : "OOGSTEN"); ?>!</span>
						<?php } else { ?>
							<span class="red">TE LAAT</span>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="table_mainTxt outline padding_5" width="100%" colspan="4">
						<form method="post" action="villa.php">
							<input type="submit" name="water" class="button_form padding_5" value="<?php echo ($wietplantage->level < 4 ? "WATER GEVEN" : "OOGSTEN"); ?>" />
						</form>
					</td>
				</tr>
				<tr>
					<td class="table_mainTxt outline" width="20%" colspan="1">Gezondheid</td>
					<td class="" width="30%" colspan="1"><?php printStatusBar($wietplantage->health); ?></td>
					<td class="table_mainTxt outline" width="20%" colspan="1">Status</td>
					<td class="" width="30%" colspan="1"><?php printStatusBar(($wietplantage->level*20)); ?></td>
				</tr>			
				<tr>
					<td class="table_mainTxt padding_5 outline" width="20%" colspan="1">Aantal planten</td>
					<td class="table_mainTxt" width="30%" colspan="1"><?php echo formatDecimaal($wietplantage->planten); ?></td>
				</tr>
			<?php } ?>
	</table>
<?php } ?>