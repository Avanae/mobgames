<?php
	include('config.php');
	include('include/functions.php');
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	
	$famrang = $famRangen[$spelerInfo->familierang];
	$wapen = $wapenRangen[$spelerInfo->wapen];
	$verdediging = $verdedigingRangen[$spelerInfo->verdediging];
	$cash = formatDecimaal($spelerInfo->cash);
	$bank = formatDecimaal($spelerInfo->bank);
	$kogels = formatDecimaal($spelerInfo->kogels);
	
	$rang = formatDecimaal($spelerInfo->rang);
	if($spelerInfo->rang >= count($gameRangen)){
		$rang = $gameRangen[count($gameRangen)-1] . " ( LVL: " . $spelerInfo->rang . ")";
	} else {
		$rang = $gameRangen[$spelerInfo->rang];
	}
	//$rang = formatDecimaal($spelerInfo->rang);
	$vordering = $spelerInfo->rangvordering;

	$vervoerQuery = mysqli_query($con,"SELECT * FROM shop_vervoer WHERE id='" . $spelerInfo->vervoer . "'");
	$vervoer = mysqli_fetch_object($vervoerQuery);
	$vervoersmiddel = $vervoer->naam;

	$landensql = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $spelerInfo->land . "'");
	$locatie = mysqli_fetch_object($landensql);

	if($spelerInfo->familie == 0){
		$familie = "Geen";
	} else if($spelerInfo->familie != 0){
		$famQuery = mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerInfo->familie . "'");
		$fam = mysqli_fetch_object($famQuery);
		$familie = "<a href='familie.php?pagina=" . $spelerInfo->familie . "'>" . $fam->naam . "</a>";
	}
?>

<?php

if(isset($_GET['x'])=="bezittingen"){
print "<table width='550px' class=\"inhoud_table\" colspan='5'>";
	if($spelerInfo->sniper != 0 || $spelerInfo->antisniper != 0 || $spelerInfo->bazooka != 0 || $spelerInfo->antibazooka != 0 || $spelerInfo->minigun != 0){
		if($spelerInfo->mini_banner == 1){
			print"<tr>
				<td colspan='5' class='table_mainTxt'><img src='images/headers/bezitting.jpg' width='550px' height='120px' alt='bezittingen pic' /></td>
			</tr>";
			}
		print "
			
			<tr>
				<td class='table_subTitle' colspan='5'>Persoonlijke bezittingen beheren</td>
			</tr>
			<tr>
				<td class='table_mainTxt limegreen bold center outline' colspan='1' width='20%'></td>
				<td class='table_mainTxt limegreen bold center outline' colspan='1' width='25%'>Item</td>
				<td class='table_mainTxt limegreen bold center outline' colspan='1' width='5%'>Aantal</td>
				<td class='table_mainTxt limegreen bold center outline' colspan='1' width='20%'>Verkoopprijs</td>
				<td class='table_mainTxt limegreen bold center outline' colspan='1' width='30%'>Verkopen</td>
			</tr>
		";
	// speciale wapens weergeven
	/*
		for($i=1; $i <= 5; $i++){
			if($spelerInfo->{$speciaalWapenArray[$i]} == 1){
				$pppoen = formatDecimaal($speciaalWapenVerkoopPrijsArray[$i]);
				print "
					<tr>
						<td class='table_mainTxt center' colspan='1'><img src='" . $speciaalWapenPicArray[$i] . "' alt='" . $speciaalWapenPicArray[$i] . "' /></td>
						<td class='table_mainTxt' colspan='1'>" . $speciaalWapenNaamArray[$i] . "</td>
						<td class='table_mainTxt center' colspan='1'>1 X</td>
						<td class='table_mainTxt' colspan='1'>&euro; " . $pppoen . "</td>
						<td class='table_mainTxt' colspan='1'><a href='winkel.php?sv=" . $i . "'>Verkoop " . $speciaalWapenNaamArray[$i] . "</a></td>
					</tr>
				";
			}
		}*/
	}
	// normale wapen weergeven 
	if($spelerInfo->wapen >= 1){
		$pppoen = formatDecimaal($wapenVerkoopPrijsArray[$spelerInfo->wapen]);
		print "
					<tr>
						<td class='table_subTitle' colspan='5'>Normaal wapen</td>
					</tr>
					<tr>
						<td class='table_mainTxt center' colspan='1'><img src='" . $wapenPicArray[$spelerInfo->wapen] . "' alt='" . $wapenRangen[$spelerInfo->wapen] . "' /></td>
						<td class='table_mainTxt' colspan='1'>" . $wapenRangen[$spelerInfo->wapen] . "</td>
						<td class='table_mainTxt center' colspan='1'>1 X</td>
						<td class='table_mainTxt' colspan='1'>&euro; " . $pppoen . "</td>
						<td class='table_mainTxt' colspan='1'><a href='winkel.php?wv=" . $spelerInfo->wapen . "'>Verkoop " . $wapenRangen[$spelerInfo->wapen] . "</a></td>
					</tr>
		";
	}
	// normale bescherming weergeven
	if($spelerInfo->verdediging >= 1){
		$pppoen = formatDecimaal($verdedigingVerkoopPrijsArray[$spelerInfo->verdediging]);
		print "
						<tr>
							<td class='table_subTitle' colspan='5'>Verdediging</td>
						</tr>
						<tr>
						<td class='table_mainTxt center' colspan='1'><img src='" . $verdedigingPicArray[$spelerInfo->verdediging] . "' alt='" . $verdedigingRangen[$spelerInfo->verdediging] . "' /></td>
						<td class='table_mainTxt' colspan='1'>" . $verdedigingRangen[$spelerInfo->verdediging] . "</td>
						<td class='table_mainTxt center' colspan='1'>1 X</td>
						<td class='table_mainTxt' colspan='1'>&euro; " . $pppoen . "</td>
						<td class='table_mainTxt' colspan='1'><a href='winkel.php?vv=" . $spelerInfo->verdediging . "'>Verkoop " . $verdedigingRangen[$spelerInfo->verdediging] . "</a></td>
					</tr>
		";
	}
	// Vervoer weergeven weergeven
	if($spelerInfo->vervoer >= 2){
		$vervoerQuery = mysqli_query($con,"SELECT * FROM shop_vervoer WHERE id='" . $spelerInfo->vervoer . "'");
		$vervoer = mysqli_fetch_object($vervoerQuery);
		$pppoen = formatDecimaal(($vervoer->prijs/2));
		print "
						<tr>
							<td class='table_subTitle' colspan='5'>Vervoersmiddel</td>
						</tr>
						<tr>
						<td class='table_mainTxt center' colspan='1'></td>
						<td class='table_mainTxt' colspan='1'>" . $vervoer->naam . "</td>
						<td class='table_mainTxt center' colspan='1'>1 X</td>
						<td class='table_mainTxt' colspan='1'>&euro; " . $pppoen . "</td>
						<td class='table_mainTxt' colspan='1'><a href='winkel.php?selltransport'>Verkoop " . $vervoer->naam . "</a></td>
					</tr>
		";
	}
	print"</table>";
	exit;
} else
if(isset($_GET['y'])=="moord"){
	$spelerMoordPogingn = mysqli_query($con,"SELECT * FROM moordpogingen WHERE schutter='" . $spelerInfo->id . "' ORDER BY datum DESC");
	$spelerSlachtofferPogingn = mysqli_query($con,"SELECT * FROM moordpogingen WHERE slachtoffer='" . $spelerInfo->id . "' ORDER BY datum DESC");
	$pogingStatus = array("Backfire kill","<font color='limegreen' class='bold'>OVERLEEFD</font>","<font color='red' class='bold'>DOOD</font>");
	if($spelerInfo->mini_banner == 1){
		
			print"<tr><table width='550px'>
			<td class='table_mainTxt' colspan='4'><img src='images/headers/moord.jpg' alt='moord pic' width='550px' height='120px' /></td>
		</tr></table>";
			}
	print "
	<table width='550px' class=\"inhoud_table\">
		
		<tr>
		<td class='table_subTitle' colspan='4'>Jouw moordpogingen</td>
	<tr>
		<td class='table_maintxt outline' width='10%'>Id</td>
		<td class='table_maintxt outline' width='20%'>Slachtoffer</td>
		<td class='table_maintxt outline' width='20%'>Status</td>
		<td class='table_maintxt outline' width='50%'>Datum</td>
	</tr>
	";
	$i = 1;
	
	while($spelerMoordPogingen = mysqli_fetch_object($spelerMoordPogingn)){
		$slId2 = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $spelerMoordPogingen->slachtoffer . "'");
		$slId = mysqli_fetch_object($slId2);
		print "
		<tr>
			<td class='table_maintxt'>" . $i . "</td>
			<td class='table_maintxt'><a href='speler_profiel.php?x=" . $slId->id . "'>" . $slId->login . "</a></td>
			<td class='table_maintxt'>" . $pogingStatus[$spelerMoordPogingen->status] . "</td>
			<td class='table_maintxt'>" . $spelerMoordPogingen->datum . "</td>
		</tr>
		";	
		$i++;
	}
	print "</table>
	<table width='550px' class=\"inhoud_table\">
	<tr>
		<td class='table_subTitle' colspan='4'>Zij schoten op jouw</td>
	<tr>
	<tr>
		<td class='table_maintxt outline' width='10%'>Id</td>
		<td class='table_maintxt outline' width='20%'>Dader</td>
		<td class='table_maintxt outline' width='20%'>Status</td>
		<td class='table_maintxt outline' width='50%'>Datum</td>
	</tr>
	";
	
	if(mysqli_num_rows($spelerSlachtofferPogingn) >= 1)
	{
		$c = 1;
		while($spelerSlachtofferPogingen = mysqli_fetch_object($spelerSlachtofferPogingn)){
			$slId2 = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $spelerSlachtofferPogingen->schutter . "'");
			$slId = mysqli_fetch_object($slId2);
				print "
				<tr>
				<td class='table_maintxt'>" . $c . "</td>
				<td class='table_maintxt'><a href='speler_profiel.php?x=" . $slId->id . "'>" . $slId->login . "</a></td>
				<td class='table_maintxt'>" . $pogingStatus[$spelerSlachtofferPogingen->status] . "</td>
				<td class='table_maintxt'>" . $spelerSlachtofferPogingen->datum . "</td>
				</tr>
				";	
				$c++;
		}
	}
	print "</table>";
	exit();
} 
if(isset($_POST['layout'])){
	$minibanner = mysqli_real_escape_string($con,test_input($_POST['mini_banner']));
	$banner = mysqli_real_escape_string($con,test_input($_POST['banner']));
	
	$bericht = "Er is niets aangepast.";
	if(isset($_POST["mini_banner"])){
		if(is_numeric($minibanner) && !empty($minibanner)){
			mysqli_query($con,"UPDATE leden SET mini_banner='" . $minibanner . "' WHERE id='" . $spelerInfo->id . "'");
			$bericht = "Je hebt met succes de wijzigingen uitgevoerd.";
		} else {
			$bericht = "<p>Gelieve een geldige keuze te maken voor de mini banner.</p>";
		}
	}
	if(isset($_POST["banner"])){
		if(is_numeric($banner) && !empty($banner) || $banner == 0){
			mysqli_query($con,"UPDATE leden SET banner='" . $banner . "' WHERE id='" . $spelerInfo->id . "'");
			$bericht = "Je hebt met succes de wijzigingen uitgevoerd.";
		} else {
			$bericht = "<p>Gelieve een geldige keuze te maken voor de banner.</p>";
		}
	}
	
	print_bericht("Profiel",$bericht);
	exit();
}
if(isset($_POST['informatie'])){
	$geslachtVar = mysqli_real_escape_string($con,test_input($_POST['geslacht']));
	$profielInfoVar = mysqli_real_escape_string($con,test_input($_POST['profiel_tekst']));
	$profielPic = mysqli_real_escape_string($con,test_input($_POST['profiel_pic']));
	if(file_exists($profielPic)){} else {$profielPic = "images/system/noimage.jpeg";}
	if(is_numeric($geslachtVar) && isset($_POST['profiel_tekst'])){
		mysqli_query($con,"UPDATE leden SET profiel_info='" . $profielInfoVar . "',geslacht='" . $geslachtVar . "',profiel_pic='" . $profielPic . "' WHERE id='" . $spelerInfo->id . "'");
		print_succes("Profiel - informatie update","Je hebt met succes de wijzigingen uitgevoerd.");
		exit();
	} else{
		print_error("Profiel - informatie update","Gelieve geldige informatie op te geven.");
		exit();
	}
	
	
}
if(isset($_GET['z'])=="instellingen"){
?>
<form method="post" action="vooruitgang.php">
	<table width='550px' class="inhoud_table" colspan="2">
		<tr>
			<td colspan="2" class="table_subTitle">INFORMATIE WIJZIGEN</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_left" colspan="1" width="50%"><label for="pt">Profiel informatie</label></td>
			<td class="table_mainTxt" colspan="1" width="50%"><textarea class="input_form" rows="5" cols="45" name="profiel_tekst" id="pt"><?php echo $spelerInfo->profiel_info;  ?></textarea></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_left" colspan="1" width="50%"><label for="geslacht">Geslacht</label></td>
			<?php 
				if($spelerInfo->geslacht == 0){
					$geslachtTxt = "<option value='0' selected='true'>Man</option><option value='1'>Vrouw</option>";
				} else {
					$geslachtTxt = "<option value='0' selected='true'>Man</option><option value='1' selected='true'>Vrouw</option>";
				}
			?>
			<td class="table_mainTxt" colspan="1" width="50%"><select name="geslacht" id="geslacht" class="select_field padding_5"><?php echo $geslachtTxt; ?></select></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_left" colspan="1" width="50%"><label for="pf">Profiel foto</label></td>
			<td class="table_mainTxt" colspan="1" width="50%"><input type="input" name="profiel_pic" class="input_form" maxlength="100" id="pf" value="<?php echo $spelerInfo->profiel_pic ?>" /><span class="red">(Max 100 tekens)</span></td>
		</tr>
		<tr>
			<td class="table_mainTxt" colspan="2" width="100%"><input type="submit" name="informatie" value="Informatie opslaan" class="button_form" /></td>
		</tr>
	</table>
	<table width='550px' class="inhoud_table" colspan="2">
		<tr>
			<td colspan="2" class="table_subTitle">LAY-OUT WIJZIGEN</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_left" colspan="1" width="50%"><label for="mini">Mini banners weergeven?</label></td>
			<td class="table_mainTxt" colspan="1" width="50%"><select name="mini_banner" width="50" class="select_field padding_5" id="mini"><option value="1" <?php echo ($spelerInfo->mini_banner == 1 ? "selected" : ""); ?>>Ja</option><option value="0" <?php echo ($spelerInfo->mini_banner == 0 ? "selected" : ""); ?>>Nee</option></select></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_left" colspan="1" width="50%"><label for="banner">Banner weergeven?</label></td>
			<td class="table_mainTxt" colspan="1" width="50%"><select name="banner" id="banner" class="select_field padding_5"><option value="1" <?php echo ($spelerInfo->banner == 1 ? "selected" : ""); ?>>Ja</option><option value="0" <?php echo ($spelerInfo->banner == 0 ? "selected" : ""); ?>>Nee</option></select></td>
		</tr>
		<tr>
			<td class="table_mainTxt" colspan="4" width="100%"><input type="submit" name="layout" value="Layout opslaan" class="button_form" /></td>
		</tr>
	</table>
</form>

<?php
}
if(!isset($_GET['y']) && !isset($_GET['x']) && !isset($_GET['z']) && !isset($_POST['layout'])) {
	$timerSql = mysqli_query($con,"SELECT * FROM leden_timers WHERE speler='" . $spelerInfo->id . "'");
	$timer = mysqli_fetch_object($timerSql);
	
	$crimeTime = strtotime($timer->misdaad) >time() ? GetWaitTime(time(),strtotime($timer->misdaad)) : "Nu";
	$autoTime = strtotime($timer->auto) >time() ? GetWaitTime(time(),strtotime($timer->auto)) : "Nu";
	
	include("javascript/framewerk.php");
?>
	
	<script>
		var waitTimer;
		
		ExeGetReq("Jscalls.php?PlayerCrimeTimers",setCrimeTimers);
		function setCrimeTimers(result){
			var timers = JSON.parse(result);
			waitTimer = setInterval(function(){
				var countDownMisdaad = new Date(timers['misdaad']).getTime();
				UpdateWaitTimers("timerCrime",new Date(timers['misdaad']).getTime());
				UpdateWaitTimers("timerGta",new Date(timers['auto']).getTime());
				UpdateWaitTimers("timerTravel",new Date(timers['reizen']).getTime());
				UpdateWaitTimers("timerJail",new Date(timers['gevangenis']).getTime());
				UpdateWaitTimers("timerWork",new Date(timers['werken']).getTime());
				UpdateWaitTimers("timerHeist",new Date(timers['heist']).getTime());
			},1000);
		}
		function UpdateWaitTimers(timerName,futureDate){
			var now = new Date().getTime();
			var distance = futureDate-now;
			var res = "";
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);
			if(hours > 0){
				res = hours + "h ";
			}
			if(minutes > 0){
				res = res + minutes + "m ";
			}
			if(seconds >= 0){
				res = res + seconds + "s ";
			}
			
			if(distance < 0){
				if(timerName == 'timerJail'){
					res = "Nee";
				} else {
					res = "Nu";
				}
			}
			document.getElementById(timerName).innerHTML = res;
		}
	</script>
	<table width='550px' class="inhoud_table" colspan="4">
		<?php if($spelerInfo->mini_banner == 1){ ?>
			<tr>
				<td colspan="4" class="table_mainTxt"><img src="images/headers/bezitting.jpg" width="550px" height="120px" alt="villa pic" /></td>
			</tr>
		<?php } ?>
		<tr>
			<td class="subTitle" width="100%" colspan="4">Algemene Informatie</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><img src='images/system/icons/person_icon.gif' width='15px' class='align_middle' alt='gebruiker icoon' /><span class="padding_left">Login naam</span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><?php echo $spelerInfo->login; ?></td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><span class="padding_left">Status</span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><?php echo $spelerInfo->status; ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><img src='images/icons/heart.gif' width='15px' class='align_middle' alt='leven icoon' /><span class="padding_left"><?php echo $spelerInfo->leven; ?></span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><img src='images/icons/kavel.gif' width='15px' class='align_middle' alt='kavel icoon' /><span class="padding_left"><?php echo utf8_encode($locatie->land); ?></span></td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><span class="padding_left">Geslacht</span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><?php if($spelerInfo->geslacht == 1){ $ges = "Vrouw";}else{$ges = "Man";} echo $ges; ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><img src='images/icons/rank.gif' width='15px' class='align_middle' alt='rang icoon' /><span class="padding_left">Level</span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><?php echo $rang; ?></td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><img src='images/icons/rank_vordering.gif' width='15px' class='align_middle' alt='rang icoon' /><span class="padding_left">Vordering</span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><?php echo $spelerInfo->rangvordering; ?></td>
		</tr>
		<tr>
			<td class="subTitle" width="100%" colspan="4">Familie informatie</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%"><span class="padding_left">Familie</span></td>
			<td class="table_mainTxt padding_5" width="30%"><?php echo $familie; ?></td>
			<td class="table_mainTxt padding_5" width="20%"><span class="padding_left">Familie status</span></td>
			<td class="table_mainTxt padding_5" width="30%"><?php echo $famrang; ?></td>
		</tr>
		<tr>
			<td class="subTitle" width="100%" colspan="4">Bezitting Informatie</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><img src='images/icons/defense.gif' width='15px' class='align_middle' alt='verdediging icoon' /><span class="padding_left">Verdediging</span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><?php echo $verdediging; ?></td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><img src='images/icons/wapen.png' width='15px' class='align_middle' alt='wapen icoon' /><span class="padding_left">Wapen</span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><?php echo $wapen; ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><img src='images/icons/kogels.gif' width='15px' class='align_middle' alt='kogel icoon' /><span class="padding_left">Kogels</span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><?php echo $kogels; ?></td>
			<td class="table_mainTxt padding_5" width="20%" colspan="1"><span class="padding_left">Vervoer</span></td>
			<td class="table_mainTxt padding_5" width="30%" colspan="1"><?php echo $vervoersmiddel; ?></td>
			
		</tr>
		
		<tr>
			<td class="subTitle" width="100%" colspan="4">Financi<?php echo utf8_encode('ë'); ?>le informatie</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%"><img src='images/icons/coins/coins2.gif' width='15px' class='align_middle' alt='contant icoon' /><span class="padding_left">Cash geld</span></td>
			<td class="table_mainTxt padding_5" width="30%">&euro; <?php echo $cash; ?></td>
			<td class="table_mainTxt padding_5" width="20%"><img src='images/icons/bank.gif' width='15px' class='align_middle' alt='bank icoon' /><span class="padding_left">Bank geld</span></td>
			<td class="table_mainTxt padding_5" width="30%">&euro; <?php echo $bank; ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%"><img src='images/icons/rank.gif' width='15px' class='align_middle' alt='contant icoon' /><span class="padding_left">VIP coins</span></td>
			<td class="table_mainTxt padding_5" width="30%"><?php echo $spelerInfo->vipCoins; ?></td>
			<td class="table_mainTxt padding_5" width="20%"></td>
			<td class="table_mainTxt padding_5" width="30%"></td>
		</tr>
		<tr>
			<td class="subTitle" width="100%" colspan="4">Wachttijden</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%"><span class="padding_left">Misdaad</span></td>
			<td class="table_mainTxt padding_5" width="30%"><span id="timerCrime">Loading...</span></td>
			<td class="table_mainTxt padding_5" width="20%"><span class="padding_left">Auto Diefstal</span></td>
			<td class="table_mainTxt padding_5" width="30%"><span id="timerGta">Loading...</span></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%"><span class="padding_left">Heist</span></td>
			<td class="table_mainTxt padding_5" width="30%"><span id="timerHeist">Loading...</span></td>
			<td class="table_mainTxt padding_5" width="20%"><span class="padding_left">Gevangenis</span></td>
			<td class="table_mainTxt padding_5" width="30%"><span id="timerJail">Loading...</span></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="20%"><span class="padding_left">Reizen</span></td>
			<td class="table_mainTxt padding_5" width="30%"><span id="timerTravel">Loading...</span></td>
			<td class="table_mainTxt padding_5" width="20%"><span class="padding_left">Werken</span></td>
			<td class="table_mainTxt padding_5" width="30%"><span id="timerWork">Loading...</span></td>
		</tr>
	</table>
<?php
}
?>