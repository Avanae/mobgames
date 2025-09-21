<?php
include("config.php");
include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");
	
$select1 = mysqli_query($con,"SELECT * FROM casino_objecten WHERE type='1' AND land='" . $spelerInfo->land . "'");
if(mysqli_num_rows($select1) <= 0){
	mysqli_query($con,"INSERT INTO casino_objecten (type,land) VALUES ('1','" . $spelerInfo->land . "')");
	$select1 = mysqli_query($con,"SELECT * FROM casino_objecten WHERE type='1' AND land='" . $spelerInfo->land . "'");
}
$casinoInfo = mysqli_fetch_object($select1);
$maxInzet = formatDecimaal($casinoInfo->maxinzet);
$link = "<p><a href='nummer_raden.php'>Ga terug</a></p>";
$minInzet = formatDecimaal($casinoInfo->mininzet);
$omzet = formatDecimaal($casinoInfo->omzet);
$omzetGeformateerd = "&euro; " . formatDecimaal($casinoInfo->omzet);
$bank = "&euro; " . formatDecimaal($casinoInfo->bank);
$lll = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $casinoInfo->land . "'");
$land = mysqli_fetch_object($lll);
$prix = formatDecimaal($casinoInfo->prijs);
if($casinoInfo->eigenaar == 0){
	$eigenaar = "<a href='nummer_raden.php?a=buy' title='Koop dit object voor &euro; " . $prix . "'>Geen</a>";
} else if($casinoInfo->eigenaar != 0){
	$e = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $casinoInfo->id . "'");
	$eige = mysqli_fetch_object($e);
	$eigenaar = "<a href='speler_profiel.php?x=" . $eige->id . "'>" . $eige->naam . "</a>";
}

if($omzet < 0){
	$omzet = "<font color='red'>" . $omzetGeformateerd . "</font>";
} elseif($omzet >= 1) {
	$omzet = "<font color='limegreen'>" . $omzetGeformateerd . "</font>";
} else {
	$omzet = "<font color='white'>" . $omzetGeformateerd . "</font>";
}

// casino kopen
if(isset($_GET["a"])){
	$bericht = "";
	if(mysqli_real_escape_string($con,test_input($_GET["a"])) != "buy" || $casinoInfo->eigenaar != 0){$bericht = "Yeah right...";}
	else if($spelerInfo->cash < $casinoInfo->prijs && $bericht == ""){$bericht = "Je hebt niet genoeg geld om dit object te kopen. De prijs is &euro; " . $prix . ".";}
	else {
		$bericht = "Je hebt dit object gekocht voor &euro; " . $prix . ". Succes met je nieuwe casino.";
		$sql1 = "UPDATE casino_objecten SET eigenaar='" . $spelerInfo->id . "' WHERE id='" . $casinoInfo->id . "'";
		$sql2 = "UPDATE leden SET cash=cash-'" . $casinoInfo->prijs . "' WHERE id='" . $spelerInfo->id . "'";
		mysqli_query($con,$sql1);
		mysqli_query($con,$sql2);
	}
	print_bericht("Casino Kopen",$bericht . "");
	exit;
}
// casino bank managen
if(isset($_POST["pin"])){
	$bericht = "";
	if($_POST["bedrag"] != ""){
		$postVar = mysqli_real_escape_string($con,test_input($_POST["bedrag"]));
		if(is_numeric($postVar)){
			if($casinoInfo->bank >= $postVar){
				$num = formatDecimaal($postVar);
				$sql1 = "UPDATE leden SET cash=cash+'" . $postVar . "' WHERE id='" . $spelerInfo->id . "'";
				$sql2 = "UPDATE casino_objecten SET bank=bank-'" . $postVar . "' WHERE eigenaar='" . $spelerInfo->id . "' AND id='" . $casinoInfo->id . "'";
				mysqli_query($con,$sql1);
				mysqli_query($con,$sql2);
				$bericht = "<span class='limegreen'>Je hebt &euro; " . $num . " van je rekening gehaald.</span>";
			} else {
				$bericht = "<span class='red'>Je hebt zoveel geld niet op je bank!</span>";
			}
		} else{
			$bericht = "<span class='red'>Dit is geen geldig bedrag!</span>";
		}
	}else{
		$bericht = "<span class='red'>Je hebt geen bedrag opgegeven!</span>";
	}
	print_bericht("Casino geld afhalen",$bericht . "" . $link);
	exit;
}
if(isset($_POST["stort"])){
	$bericht = "";
	if($_POST["bedrag"] != ""){
		$postVar = mysqli_real_escape_string($con,test_input($_POST["bedrag"]));
		if(is_numeric($postVar)){
			if($spelerInfo->cash >= $postVar){
				$num = formatDecimaal($postVar);
				$sql1 = "UPDATE leden SET cash=cash-'" . $postVar . "' WHERE id='" . $spelerInfo->id . "'";
				$sql2 = "UPDATE casino_objecten SET bank=bank+'" . $postVar . "' WHERE eigenaar='" . $spelerInfo->id . "' AND id='" . $casinoInfo->id . "'";
				mysqli_query($con,$sql1);
				mysqli_query($con,$sql2);
				$bericht = "<span class='limegreen'>Je hebt &euro; " . $num . " op je casino rekening gestort.</span>";
			} else {
				$bericht = "<span class='red'>Je hebt zoveel geld niet cash!</span>";
			}
		} else{
			$bericht = "<span class='red'>Dit is geen geldig bedrag!</span>";
		}
	}else{
		$bericht = "<span class='red'>Je hebt geen bedrag opgegeven!</span>";
	}
	print_bericht("Casino geld sorten",$bericht . "" . $link);
	exit;
}

	
if(isset($_POST["wijziginstellingen"])){
	$userNaam = mysqli_real_escape_string($con,test_input($_POST["eigenaar"]));
	if($userNaam != ""){
		$geb = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $userNaam . "' AND status='levend' AND ban='0'");
		if(mysqli_num_rows($geb) >= 1){
			$newOwner = mysqli_fetch_object($geb);
			if($spelerInfo->login == $casinoInfo->eigenaar){
				mysqli_query($con,"UPDATE casino_objecten SET eigenaar='" . $newOwner->id . "' WHERE type='1' AND land='" . $spelerInfo->land . "'");
				$bericht = "<p class='limegreen'>Je hebt dit object aan " . $userNaam . " gegeven!</p>";
			} else {
				$bericht = "<p class='red'>Dit is niet mogelijk!</p>";
			}
		} else {
			$bericht = "<p class='red'>Er is geen gebruiker met deze naam of deze gebruiker leeft niet meer!</p>";
		}
	} else {
		$max = mysqli_real_escape_string($con,test_input($_POST["maxbedrag"]));
		$min = mysqli_real_escape_string($con,test_input($_POST["minbedrag"]));
		if(!is_numeric($max)){$bericht = "<p class='red'>Dit is geen geldig bedrag!</p>";}
		if(!is_numeric($min)){$bericht = "<p class='red'>Dit is geen geldig bedrag!</p>";}
		if($max == ""){$bericht = "<p class='red'>Je moet bij het maximum bedrag wel een bedrag opgeven, of wil je failliet gaan?</p>";}
		if($max != "" && $min == ""){$min = round(($max/100)*10);}
		if($max != "" && $min != ""){
			if($min > $max){
				$bericht = "<p class='red'>De minimum inzet moet lager zijn dan de maximum inzet.</p>";
			} else {
				mysqli_query($con,"UPDATE casino_objecten SET mininzet='" . $min . "', maxinzet='" . $max . "' WHERE id='" . $casinoInfo->id . "'");
				$bericht = "<p class='limegreen'>Je hebt de instellingen van je casino met succes gewijzigd.</p>";
			}
		}
	}
	print_bericht("Instellingen wijzigen",$bericht . "" . $link);
	exit;
}
?>

<table width="550px" colspan="4" class="outline margin_bottom">
	<tr>
		<td class="table_subtitle" colspan="4">Nummer raden</td>
	</tr>
	<tr>
		<td class="table_mainTxt" colspan="4"><p>Bij het nummer raden draait het allemaal om het juiste nummer. Raad u het juiste nummer tussen 1 en 10 dan ontvangt u uw inzet x10. Dus &euro; 1.000 wordt &euro; 10.000.</p></td>
	</tr>
</table>
<table width="550px" colspan="4" class="margin_bottom">	
	<tr>
		<td class="table_subtitle" colspan="4">Casino informatie in <?php echo utf8_encode($land->land); ?></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_left_5" colspan="1" width="20%">Maximum inzet</td>
		<td class="table_mainTxt" colspan="1" width="25%"><span class="bold">&euro; <?php echo $maxInzet; ?></span></td>
		<td class="table_mainTxt" colspan="1" width="20%">Minimum inzet</td>
		<td class="table_mainTxt" colspan="1" width="35%"><span class="bold">&euro; <?php echo $minInzet; ?></span></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_left_5" colspan="1" width="20%">Eigenaar</td>
		<td class="table_mainTxt" colspan="1" width="25%"><span class="bold"><?php echo $eigenaar; ?></span></td>
		<td class="table_mainTxt" colspan="1" width="20%">Omzet casino</td>
		<td class="table_mainTxt" colspan="1" width="35%"><span class="bold"><?php echo $omzet; ?></span></td>
	</tr>
	<form method="post" action="hoger_lager.php">
</table>
<table width="550px" colspan="4" class="margin_bottom outline darkgray">	
	<tr>
		<td class="table_subtitle margin_top" colspan="4">Gokken</td>
	</tr>
	<tr>
		<td class="table_mainTxt" colspan="1" width="20%"><label for="bedrag"><p class="center unselectable">Bedrag</p></label></td>
		<td class="table_mainTxt" colspan="1" width="3%"><input type="text" size="20" maxlength="9" name="inzet" class="input_form" id="bedrag"/></td>
		<td class="table_mainTxt" colspan="1" width="20%"><label for="nr"><p class="center unselectable">Nummer</p></label></td>
		<td class="table_mainTxt" colspan="1" width="30%"><input type="text" size="20" maxlength="9" name="nummer" class="input_form" id="nr"/></td>
	</tr>
	<tr>
		<td class="table_mainTxt center" colspan="4" width="100%"><input type="submit" name="gok" value="Riskeer het" class="button_form" /></td>
	</tr>
	</form>
</table>
<?php
if(isset($_POST['gok'])){
	$inzet = mysqli_real_escape_String($con,test_input($_POST['inzet']));
	$getal = mysqli_real_escape_String($con,test_input($_POST['nummer']));
	$random = "";
	if($casinoInfo->eigenaar == $spelerInfo->id){
		$bericht = "<p><span class='red'>Je kan niet in je eigen casino gokken.</span></p><p><a href='hoger_lager.php'>Ga terug</a></p>";
	}else if(!is_numeric($inzet) || !is_numeric($getal)){ 
		$bericht = "<span class='red'>ongeldig bedrag!</span>";
	} elseif($spelerInfo->cash < $inzet){
		$bericht = "<span class='red'>Je hebt niet genoeg geld zoveel in te zetten.</span>";
	}  elseif($inzet > $casinoInfo->maxinzet){
		$bericht = "<span class='red'>Hiermee ga je over de maximale inzet.</span>";
	}elseif($inzet < $casinoInfo->mininzet){
		$bericht = "<span class='red'>Je moet op zijn minst het minimum bedrag inzetten eh.</span>";
	}elseif($getal <= 0 || $getal >= 11){
		$bericht = "<span class='red'>Je moet een getal tussen 1 en 10 ingeven.</span>";
	}else {
		$randomGetal = rand(1,10);
		$inzet1 = formatDecimaal($inzet);
		if($getal == $randomGetal){
			$gewonnen = ($inzet*10);
			$gewonnen1 = formatDecimaal($gewonnen);
			mysqli_query($con,"UPDATE casino_objecten SET omzet=omzet-'" . $gewonnen . "', bank=bank-'" . $gewonnen . "' WHERE type='1' AND land='" . $spelerInfo->land . "'");
			mysqli_query($con,"UPDATE leden SET cash=cash+'" . $gewonnen . "' WHERE id='" . $spelerInfo->id . "'");
			if($casinoInfo->bank <= 0){
				$result = "<span class='limegreen'>" . $gewonnen1 . " gewonnen.</span> <p>De eigenaar van dit casino is failliet en het casino is terug te koop</p>";
				mysqli_query($con,"UPDATE casino_objecten SET eigenaar='0', omzet='0', bank='500000' WHERE type='1' AND land='" . $spelerInfo->land . "'");
			} else {
				$result = "<span class='limegreen'>" . $gewonnen1 . " gewonnen</span>";
			}
		} else {
			mysqli_query($con,"UPDATE casino_objecten SET omzet=omzet+'" . $inzet . "', bank=bank+'" . $inzet . "' WHERE type='1' AND land='" . $spelerInfo->land . "'");
			mysqli_query($con,"UPDATE leden SET cash=cash-'" . $inzet . "' WHERE id='" . $spelerInfo->id . "'");
			$result = "<span class='red'>" . $inzet1 . " verloren</span>";
		}
		$random = "<tr><td class='table_mainTxt' width='550px'>Het nummer was " . $randomGetal . "</td></tr>";
		$bericht = "Je hebt &euro; " . $inzet1 . " ingezet en &euro; " . $result . "!";
	}
	print_bericht("Uitkomst",$bericht . "" . $link);
	exit;
}
?>
<?php
if($spelerInfo->id == $casinoInfo->eigenaar){
	print"
	<table width='550px' colspan='4' class='margin_bottom'>
	<form method='post' action='nummer_raden.php'>
		<tr>
			<td class='table_subtitle' colspan='4'>Instellingen</td>
		</tr>
		<tr>
			<td class='table_mainTxt bold padding_5' colspan='4'>Wanneer je alleen een maximum bedrag opgeeft zal het minimum bedrag 10% van het maximum bedrag worden.
			Dus als je &euro; 5.000 als maximum bedrag opgeeft word het minimum bedrag &euro; 500.
			</td>
		</tr>
		<tr>
			<td class='table_mainTxt padding_left_5' colspan='1' width='33%'><label for='maxbedrag'>Maximum Bedrag</label></td>
			<td class='table_mainTxt' colspan='1' width='33%'><label for='minbedrag'>Minimum bedrag</label></td>
			<td class='table_mainTxt' colspan='2' width='34%'><label for='g'>Nieuwe eigenaar</label></td>
			
		</tr>
		<tr>
			<td class='table_mainTxt padding_left_5' colspan='1' width='33%'><input type='text' size='20' maxlength='9' name='maxbedrag' class='input_form' id='maxbedrag'/></td>
			<td class='table_mainTxt' colspan='1' width='33%'><input type='text' size='20' maxlength='9' name='minbedrag' class='input_form' id='minbedrag' /></td>
			<td class='table_mainTxt' colspan='2' width='34%'><input type='text' size='20' maxlength='9' name='eigenaar' class='input_form' id='g'/></td>
		</tr>
		<tr>
			<td class='table_mainTxt' colspan='4' width='100%'><input type='submit' name='wijziginstellingen' value='Wijzig instellingen' class='button_form' /></td>
		</tr>
	</form>
	</table>
	";
	print "	
	<table width='550px' colspan='4' class='margin_bottom'>
	<form method='post' action='nummer_raden.php'>
		<tr>
			<td class='table_subtitle' colspan='4'>Cashflow</td>
		</tr>
		<tr>
			<td class='table_mainTxt padding_left_5' colspan='2' width='50%'>Bank geld</td>	
			<td class='table_mainTxt padding_left_5' colspan='2' width='50%'><span class='limegreen'>" . $bank . "</span></td>
		</tr>
		<tr>
			<td class='table_mainTxt padding_left_5' colspan='2' width='50%'><label for='jg'>Bedrag</label><input type='text' size='20' maxlength='9' name='bedrag' id='jg' class='input_form margin_left' /></td>
			<td class='table_mainTxt' colspan='1' width='25%'><input type='submit' name='pin' value='Geld afhalen' class='button_form' /></td>
			<td class='table_mainTxt' colspan='1' width='25%'><input type='submit' name='stort' value='Geld storten' class='button_form' /></td>
		</tr>
	</form>
	</table>
	";
}
?>