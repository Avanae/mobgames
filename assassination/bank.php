<?php
	include("config.php");
	include("include/functions.php");
	
//------------------------------------------------------------
// nog te doen aan bank:
// 1) 
$bankgeld = formatDecimaal($spelerInfo->bank);
$verz = mysqli_query($con,"SELECT * FROM donatie_logs WHERE zender='" . $spelerInfo->id . "' AND type='bank' ORDER BY datum DESC LIMIT 0,10");
$ontv = mysqli_query($con,"SELECT * FROM donatie_logs WHERE ontvanger='" . $spelerInfo->id . "' AND type='bank' ORDER BY datum DESC LIMIT 0,10");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");
	
?>
<table width="550px" class="inhoud_table" colspan="3">
<tr>
	<td class="table_subTitle" colspan="3">De bank</td>
</tr>
<tr>
	<td class="table_mainTxt outline" width="25%" colspan="1">Cash geld</td>
	<td class="table_mainTxt" width="75%" colspan="2">&euro; <?php echo formatDecimaal($spelerInfo->cash); ?></td>
</tr>
<tr>
	<td class="table_mainTxt outline" width="25%" colspan="1">Geld op je bank</td>
	<td class="table_mainTxt" width="75%" colspan="2">&euro; <?php echo $bankgeld; ?></td>
</tr>
<tr>
	<td class="subTitle" width="100%" colspan="3">Pinnen / storten</td>
</tr>
<form method="post" action="bank.php">
	<tr>
		<td class="table_mainTxt outline" width="25%" colspan="1"><label for="1">Bedrag</label></td>
		<td class="table_mainTxt" width="75%" colspan="2"><input class="input_form" type="text" maxlength="9" size="25" name="bedrag" id="1" /> &euro;</td>
	</tr>
	<tr>
		<td class="table_mainTxt" width="25%" colspan="1"></td>
		<td class="table_mainTxt" width="75%" colspan="2"><input type="submit" class="button_form" name="stort" value="Stort" /><input type="submit" class="button_form" name="pin" value="Pin" /></td>
	</tr>
</form>
<?php
if(isset($_POST['stort'])){
	$bedrag = trim(mysqli_real_escape_string($con,test_input($_POST['bedrag'])));
	if(!is_numeric($bedrag)){
		$bericht = "<font color='red'>Dit is geen geldig bedrag!</font>";
	} elseif($spelerInfo->cash < $bedrag){
		$bericht = "<font color='red'>Je hebt niet genoeg geld contant om zoveel te storten!</font>";
	} else {
	mysqli_query($con,"UPDATE leden SET cash=cash-'" . $bedrag . "', bank=bank+'" . $bedrag . "' WHERE id='" . $spelerInfo->id . "'");
		$bericht = "<font color='limegreen'>Je hebt het geld succesvol gestort.</font>";
	}
	print "<tr><td class='subTitle' colspan='3'>Geld storten</td></tr><tr><td class='table_mainTxt' colspan='3'>" . $bericht . "</td>";
}
if(isset($_POST['pin'])){
	$bedrag = trim(mysqli_real_escape_string($con,test_input($_POST['bedrag'])));
	if(!is_numeric($bedrag)){
		$bericht = "<font color='red'>Dit is geen geldig bedrag!</font>";
	} elseif($spelerInfo->bank < $bedrag){
		$bericht = "<font color='red'>Zoveel geld staat er niet op je rekening!</font>";
	} else {
	mysqli_query($con,"UPDATE leden SET cash=cash+'" . $bedrag . "', bank=bank-'" . $bedrag . "' WHERE id='" . $spelerInfo->id . "'");
		$bericht = "<font color='limegreen'>Je hebt het geld succesvol gepint.</font>";
	}
	print "<tr><td class='subTitle' colspan='3'>Geld pinnen</td></tr><tr><td class='table_mainTxt' colspan='3'>" . $bericht . "</td>";
}
?>
<tr>
	<td class="subTitle" width="100%" colspan="3">Nieuwe transactie</td>
</tr>
<form method="post" action="bank.php">
	<tr>
		<td class="table_mainTxt outline" width="25%" colspan="1"><label for="1">Bedrag</label></td>
		<td class="table_mainTxt" width="75%" colspan="2"><input class="input_form" type="text" maxlength="10" size="25" name="bedrag" id="1" /> &euro;</td>
	</tr>
	<tr>
		<td class="table_mainTxt outline" width="25%" colspan="1">Begunstigde</td>
		<td class="table_mainTxt" width="75%" colspan="2"><input class="input_form" type="text" maxlength="10" size="25" name="ont" id="1" /></td>
	</tr>
	<tr>
		<td class="table_mainTxt" width="25%" colspan="1"></td>
		<td class="table_mainTxt" width="75%" colspan="2"><input type="submit" class="button_form" name="transactie" value="verzend geld" /></td>
	</tr>
</form>
<?php
if(isset($_POST['transactie'])){
	$bedrag = trim(mysqli_real_escape_string($con,test_input($_POST['bedrag'])));
	$gebruiker = trim(mysqli_real_escape_string($con,test_input($_POST['ont'])));
	
	if(preg_match('/^[a-zA-Z0-9_\-]+$/',$gebruiker) == 0){
		print_bericht("Bank Transactie","Dit is geen geldige invoer.");
		exit;
	}
	$sssssss = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $gebruiker . "'");

	$userCheck = mysqli_fetch_object($sssssss);
	$gelukt = true;

	if(!is_numeric($bedrag)){
		$bericht = "<font color='red'>Dit is geen geldig bedrag!</font>";
	}elseif(!$userCheck){
		$bericht = "<font color='red'>Deze gebruiker bestaat niet!</font>";
	}elseif($spelerInfo->bank < $bedrag){
		$bericht = "<font color='red'>Zoveel geld staat er niet op je rekening!</font>";
	} else {
		mysqli_query($con,"UPDATE leden SET bank=bank-'" . $bedrag . "' WHERE id='" . $spelerInfo->id . "'");
		mysqli_query($con,"UPDATE leden SET bank=bank+'" . $bedrag . "' WHERE id='" . $userCheck->id . "'");
		mysqli_query($con,"INSERT INTO donatie_logs (zender,ontvanger,bedrag,datum,type) VALUES ('" . $spelerInfo->id . "','" . $userCheck->id . "','" . $bedrag . "',NOW(),'bank')");
		$bericht = "<font color='limegreen'>Je hebt het geld succesvol overgeschreven naar " . $gebruiker . ".</font>";
	}
	print "<tr><td class='subTitle' colspan='3'>Geld pinnen</td></tr><tr><td class='table_mainTxt' colspan='3'>" . $bericht . "</td></tr>";
}
?>

<tr>
	<td class="subTitle" width="100%" colspan="3">Laatste 10 verzonden transacties</td>
</tr>
<tr>
	<td class='table_maintxt outline' colspan="1" width='20%'>Naar</td>
	<td class='table_maintxt outline' colspan="1" width='40%'>Bedrag</td>
	<td class='table_maintxt outline' colspan="1" width='40%'>Datum</td>
</tr>
<?php
	while($transactieInfo = mysqli_fetch_object($verz)){
		$bedr = formatDecimaal($transactieInfo->bedrag);
		$spelerID2 = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $transactieInfo->ontvanger . "'");
		$spelerID = mysqli_fetch_object($spelerID2);
			print "
			<tr>
			<td class='table_maintxt'><a href='speler_profiel.php?x=" . $spelerID->id . "'>" . $spelerID->login . "</a></td>
			<td class='table_maintxt'>&euro; " . $bedr . "</td>
			<td class='table_maintxt'>" . $transactieInfo->datum . "</td>
			</tr>
			";	
	}
?>
<tr>
	<td class="subTitle" width="100%" colspan="3">Laatste 10 ontvangen transacties</td>
</tr>
<tr>
	<td class='table_maintxt outline' colspan="1" width='20%'>Van</td>
	<td class='table_maintxt outline' colspan="1" width='40%'>Bedrag</td>
	<td class='table_maintxt outline' colspan="1" width='40%'>Datum</td>
</tr>
<?php
	while($transactieInfo = mysqli_fetch_object($ontv)){
		$bedr = formatDecimaal($transactieInfo->bedrag);
		$spelerID2 = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $transactieInfo->zender . "'");
		$spelerID = mysqli_fetch_object($spelerID2);
			print "
			<tr>
			<td class='table_maintxt'><a href='speler_profiel.php?x=" . $spelerID->id . "'>" . $spelerID->login . "</a></td>
			<td class='table_maintxt'>&euro; " . $bedr . "</td>
			<td class='table_maintxt'>" . $transactieInfo->datum . "</td>
			</tr>
			";	
	}
?>
</table>