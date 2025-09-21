<?php
	include("config.php");
	include("check_login.php");
	include("include/functions.php");
//------------------------------------------------------------
// nog te doen aan berichten:
// 1) Nieuw bericht controleren op input en dan laten verzenden
if($spelerInfo->vip == 0){
	$sqlQuery = "SELECT * FROM berichten WHERE ontvanger='" . $spelerInfo->id . "' ORDER BY datum DESC LIMIT 0,10";
} else {
	$sqlQuery = "SELECT * FROM berichten WHERE ontvanger='" . $spelerInfo->id . "' ORDER BY datum DESC LIMIT 0,20";
}
$ontvangenBeriichtInfo1 = mysqli_query($con,$sqlQuery);
$ontvangenBeriichtInfo5 = mysqli_num_rows($ontvangenBeriichtInfo1);
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<table width="550px">
	
	<tr>
		<td colspan="1" width="34%"><a href="berichten.php?b=nieuw"><div class="hyperlink_knop">Nieuw bericht</div></a></td>
		<td colspan="1" width="33%"><a href="berichten.php?x=inbox"><div class="hyperlink_knop">Inbox</div></a></td>
		<td colspan="1" width="33%"><a href="berichten.php?hggg=outbox"><div class="hyperlink_knop">Outbox</div></a></td>
	</tr>
</table>
<table width="550px">
	<?php
	//--------------------------------------------------------
	//- Een nieuw bericht pagina laden
	//--------------------------------------------------------
	if(isset($_GET['b'])=="nieuw"){
		if(isset($_GET['x'])!= null){ $receiver = $_GET['x'];} else { $receiver = ""; }
		print"
		<form method='post' action='berichten.php'>
		<tr>
			<td class='table_mainTxt' width='50%' colspan='1'>Ontvanger</td>
			<td width='50%' colspan='1'><input id='input_form' type='text' name='ontva' maxlength='20' size='50' value='" . $receiver . "' /></td>
		</tr>
		<tr>
			<td class='table_mainTxt' width='50%' colspan='1'>Onderwerp</td>
			<td width='50%' colspan='1'><input id='input_form' type='text' name='onder' maxlength='50' size='50' /></td>
		</tr>
		<tr>
			<td class='bg_black center' width='100%' colspan='2'><textarea id='input_form' name='bericht_nieuw' rows='10' cols='65'></textarea></td>
		</tr>
		<tr>
			<td class='table_mainTxt' colspan='2''><input type='submit' name='send_new' value='Verzend bericht' class='button_form' /></td>
		</tr>
		</form>
		";
		exit;
	}
	//--------------------------------------------------------
	//- inbox weergeven
	//--------------------------------------------------------
	if(isset($_GET['x'])=="inbox"){ 
	?>
		<tr>
			<td class='subTitle' colspan='4'>Berichten</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='1' width="25%">Verzender</td>
			<td class='table_mainTxt outline' colspan='1' width="30%">Onderwerp</td>
			<td class='table_mainTxt outline' colspan='1' width="30%">Datum</td>
			<td class='table_mainTxt outline' colspan='1' width="15%">Opties</td>
		</tr>
		<?php while($ontvangenBerichtInfo = mysqli_fetch_object($ontvangenBeriichtInfo1)){
			if($ontvangenBerichtInfo->zender == 0){
				$zender = "<font color='red' class='bold unselectable'>GAME BERICHT</font>";
			} else {
				$zenderId55 = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $ontvangenBerichtInfo->zender . "'");
				$zenderId = mysqli_fetch_object($zenderId55);
				$zender = "<a href='speler_profiel.php?x=" . $zenderId->id . "'>" . $zenderId->login . "</a>";
			}
			if($ontvangenBerichtInfo->status == 1){
				$onderwerp = "<font class='bold'><a href='berichten.php?read=" . $ontvangenBerichtInfo->id . "'>" . $ontvangenBerichtInfo->onderwerp . "</a></font>";
			} else {
				$onderwerp = "<a href='berichten.php?read=" . $ontvangenBerichtInfo->id . "'>" . $ontvangenBerichtInfo->onderwerp . "</a>";
			}
			print "<tr>
			<td class='table_mainTxt' colspan='1'>" . $zender . "</td>
			<td class='table_mainTxt' colspan='1'>" . $onderwerp . "</td>
			<td class='table_mainTxt' colspan='1'>" . $ontvangenBerichtInfo->datum . "</td>
			<td class='table_mainTxt' colspan='1'><a href='berichten.php?delete=" . $ontvangenBerichtInfo->id . "'>Verwijder</a></td>
			</tr>";
		}
	}
	//--------------------------------------------------------	
	//- outbox (updated)
	//--------------------------------------------------------
	if(isset($_GET['hggg'])=="outbox"){ ?>
			<tr>
				<td class='subTitle' colspan='3'>Berichten</td>
			</tr>
			<tr>
				<td class='table_mainTxt outline' colspan='1' width="33%">Ontvanger</td>
				<td class='table_mainTxt outline' colspan='1' width="33%">Onderwerp</td>
				<td class='table_mainTxt outline' colspan='1' width="34%">Datum</td>
			</tr>
		<?php
		$ontvangenBeriichtInfo1 = mysqli_query($con,"SELECT * FROM berichten WHERE zender='" . $spelerInfo->id . "' ORDER BY datum DESC LIMIT 0,10");
		while($ontvangenBerichtInfo = mysqli_fetch_object($ontvangenBeriichtInfo1)){
			$zenderId55 = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $ontvangenBerichtInfo->ontvanger . "'");
			$zenderId = mysqli_fetch_object($zenderId55);
			$zender = "<a href='speler_profiel.php?x=" . $zenderId->id . "'>" . $zenderId->login . "</a>";
			if($ontvangenBerichtInfo->status == 0){
				$onderwerp = "<font class='bold'><a href='berichten.php?read_sended=" . $ontvangenBerichtInfo->id . "'>" . test_input($ontvangenBerichtInfo->onderwerp) . "</a></font>";
			} else {
				$onderwerp = "<a href='berichten.php?read_sended=" . $ontvangenBerichtInfo->id . "'>" . test_input($ontvangenBerichtInfo->onderwerp) . "</a>";
			}
			print "<tr>
			<td class='table_mainTxt' colspan='1'>" . $zender . "</td>
			<td class='table_mainTxt' colspan='1'>" . $onderwerp . "</td>
			<td class='table_mainTxt' colspan='1'>" . $ontvangenBerichtInfo->datum . "</td>
			</tr>";
		}
	}
	//--------------------------------------------------------
	//- nieuws bericht sturen (updated)
	//--------------------------------------------------------
	if(isset($_POST['send_new'])){
		$onderwerp = mysqli_real_escape_string($con,test_input($_POST['onder']));
		$ontvanger = mysqli_real_escape_string($con,test_input($_POST['ontva']));
		$bericht2 = mysqli_real_escape_string($con,test_input($_POST['bericht_nieuw']));
		if(preg_match('/^[a-zA-Z0-9_\-!. ]+$/',$onderwerp) == 0 || empty($onderwerp)){
			$bericht = "<p class='red'>Een onderwerp mag alleen A-Z, a-z, 0-9, _ en - hebben of mag niet leeg zijn!</p>";
		} elseif(preg_match('/^[a-zA-Z0-9_\-]+$/',$ontvanger) == 0 || empty($ontvanger)){
			$bericht = "<p class='red'>De naam van een ontvanger mag alleen A-Z, a-z, 0-9, _ en - hebben of mag niet leeg zijn!</p>";
		} elseif(preg_match('/^[a-zA-Z0-9-!-?-._\-\[\]]+$/',$bericht2) == 0 || empty($bericht2)){
			$bericht = "<p class='red'>Er werdt ongeldige invoer gevonden, de inhoud van het bericht mag alleen A-Z, a-z, 0-9, _ en - hebben of mag niet leeg zijn!</p>";
		} else {
			$testLevendsql = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $ontvanger . "' AND status='levend' AND ban='0'");
			if(mysqli_num_rows($testLevendsql) == 0)
			{
				$bericht = "<p class='red'>Deze speler bestaat niet!</p>";
			} else {
				$testLevend = mysqli_fetch_object($testLevendsql);
				if($testLevend->status == "dood"){
					$bericht = "<p class='red'>Je kan geen bericht naar een dode speler sturen!</p>";
				} else {
					mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $spelerInfo->id . "','" . $testLevend->id . "','" . $onderwerp . "','" . $bericht2 . "',NOW())");
					$bericht = "<p class='limegreen'>Het bericht is verzonden</p>";
				}
			}
		}
		print_bericht("Berichten",$bericht);
	}
	//--------------------------------------------------------
	//- bericht lezen	
	//--------------------------------------------------------
	if(isset($_GET['read'])){ 
		$readId = trim(mysqli_real_escape_string($con,test_input($_GET['read'])));
		if(!is_numeric($readId) && !mpty($readId)){
			print_bericht("Berichten","Er ging iets mis met het laden van dit bericht.");
			exit;
		}
		$berichtInfo55 = mysqli_query($con,"SELECT * FROM berichten WHERE id='" . $readId . "'");
		$berichtInfo = mysqli_fetch_object($berichtInfo55);	
		$zenderId55 = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $berichtInfo->zender . "'");
		$zenderId = mysqli_fetch_object($zenderId55);
		if($berichtInfo->zender == 0){
			$zender = "GAME BERICHT";
		}  else { 
			$zender = "<a href='speler_profiel.php?x=" . $zenderId->id . "'>" . $zenderId->login . "</a>"; 
		}
		if($berichtInfo->status == 1){
			mysqli_query($con,"UPDATE berichten SET status='0' WHERE id='" . $readId . "'");
		}
		print"
		<tr>
			<td class='subTitle' colspan='4'>Bericht van: " . $zender . "</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='2' width='50%'>Onderwerp</td>
			<td class='table_mainTxt outline' colspan='2' width='50%'>Datum</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='2' width='50%'>" . $berichtInfo->onderwerp . "</td>
			<td class='table_mainTxt outline' colspan='2' width='50%'>" . $berichtInfo->datum . "</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline padding_5' colspan='4'>" . $berichtInfo->bericht . "</td>
		</tr>
		<tr>
			<td class='subTitle' colspan='4'><a href='berichten.php?reply=" . $readId . "'>Antwoord</a> | <a href='berichten.php?delete=" . $readId . "'>Verwijder</a></td>
		</tr>
		";
	
	}
	//--------------------------------------------------------
	//- verstuurd bericht lezen	
	//--------------------------------------------------------
	if(isset($_GET['read_sended'])){ 
		$readId = trim(mysqli_real_escape_string($con,test_input($_GET['read_sended'])));
		if(!is_numeric($readId) && !empty($readId)){
			print_bericht("Berichten","Er ging iets mis met het laden van dit bericht.");
			exit;
		}
		$berichtInfo55 = mysqli_query($con,"SELECT * FROM berichten WHERE id='" . $readId . "'");
		$berichtInfo = mysqli_fetch_object($berichtInfo55);	
		$zenderId55 = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $berichtInfo->zender . "'");
		$zenderId = mysqli_fetch_object($zenderId55);
		
		print"
		<tr>
			<td class='subTitle' colspan='4'>Bericht van <a href='speler_profiel.php?x=" . $zenderId->id . "'>" . $zenderId->login . "</a></td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='2' width='50%'>Onderwerp</td>
			<td class='table_mainTxt outline' colspan='2' width='50%'>Datum</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='2' width='50%'>" . $berichtInfo->onderwerp . "</td>
			<td class='table_mainTxt outline' colspan='2' width='50%'>" . $berichtInfo->datum . "</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline padding_5' colspan='4'>" . $berichtInfo->bericht . "</td>
		</tr>
		";
	}
	//--------------------------------------------------------
	//- antwoord bericht versturen
	//--------------------------------------------------------
	if(isset($_POST['ant'])){
		$bericht = mysqli_real_escape_string($con,test_input($_POST['bericht']));
		$onderwerp = mysqli_real_escape_string($con,test_input($_POST['onderwerp']));
		$zender = mysqli_real_escape_string($con,test_input($_POST['zender']));
		$id = mysqli_real_escape_string($con,test_input($_POST['id']));
		
		if(!is_numeric($id) && !empty($id)){
			print_bericht("Berichten","Er ging iets mis met het laden van dit bericht.");
			exit;
		}
		if(empty($bericht)){
			$bericht = "Je moet een bericht opgeven.";
		} else if(empty($onderwerp)){
			$bericht = "Je moet een onderwerp opgeven.";
		} else if(!is_numeric($zender) && !empty($zender)){
			$bericht = "Je moet een geldige speler opgeven.";
		} else {
			$spelerQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $zender . "' AND status='levend' AND ban='0'");
			if(mysqli_num_rows($spelerQuery) <= 0){
				$bericht = $zender ." Deze speler bestaat niet, leeft niet meer of is verbannen van het spel.";
			} else {
				$speler = mysqli_fetch_object($spelerQuery);
				mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $spelerInfo->id . "','" . $zender . "','" . $onderwerp . "','" . $bericht . "',NOW())");
				$bericht = "Je hebt het bericht naar " . $speler->login . " verstuurd.";
			}
		}
		print_bericht("Berichten",$bericht);
	}
	//--------------------------------------------------------
	//- bericht beantwoorden (updated)
	//--------------------------------------------------------
	if(isset($_GET['reply'])){ 
		$readId = mysqli_real_escape_string($con,test_input($_GET['reply']));
		if(!is_numeric($readId) && !empty($readId)){
			print_bericht("Berichten","Er ging iets mis met het selecteren van het bericht!");
			exit;
		}
		
		$berichtInfo55 = mysqli_query($con,"SELECT * FROM berichten WHERE id='" . $readId . "'");
		if(mysqli_num_rows($berichtInfo55) <= 0){
			print_bericht("Berichten","Er ging iets mis met het selecteren van het bericht!");
			exit;
		}
		
		$berichtInfo = mysqli_fetch_object($berichtInfo55);	
		if($berichtInfo->zender == 0)
		{
			print_bericht("Berichten","Je kan geen bericht naar deze afzender sturen!");
			exit;
		}
		$zenderId55 = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $berichtInfo->zender . "' AND status='levend' AND ban='0'");
		if(mysqli_num_rows($zenderId55) <= 0){
			print_bericht("Berichten","Je moet een geldige speler selecten. Het kan ook zijn dat deze speler dood of is verbannen.");
			exit;
		}
		$zenderId = mysqli_fetch_object($zenderId55);
		print"
		<tr>
			<td class='subTitle' colspan='4'>Beantwoord het bericht van <a href='speler_profiel.php?x=" . $zenderId->id . "'>" . $zenderId->login . "</a></td>
		</tr>
		<tr>
			<td class='table_subTitle outline' colspan='4' width='100%'><label for='ond'>Onderwerp</label></td>
		</tr>
		<form method='post' action='berichten.php'>
			<tr>
				<td class='table_mainTxt outline' colspan='4' width='100%'><input type='text' name='onderwerp' class='input_form padding_5' maxlength='50' size='65' id='ond' value='RE:" . $berichtInfo->onderwerp . "'/><input type='hidden' name='id' value='" . $berichtInfo->id . "' /></td>
			</tr>
			<tr>
				<td class='table_mainTxt outline' colspan='4'><textarea name='bericht' class='input_form padding_5' cols='50' rows='15' maxlength='1000' size='20'></textarea>
				<input type='hidden' name='zender' value='" . $berichtInfo->zender . "' /></td>
			</tr>
			<tr>
				<td class='table_mainTxt outline' colspan='4'><input type='submit' name='ant' class='button_form padding_5' value='Zend bericht'/></td>
			</tr>
		</form>
		<tr>
			<td class='table_mainTxt' colspan='4'><a href='berichten.php?x=inbox'>Klik hier om terug naar je inbox te gaan</a></td>
		</tr>
		";
	}
	//--------------------------------------------------------
	//- bericht verwijderen
	//--------------------------------------------------------
	if(isset($_GET['delete'])){ 
		$readId = trim(mysqli_real_escape_string($con,$_GET['delete']));
		if(!is_numeric($readId)){
			print"ERROR";
			exit;
		}
		$berichtInfo55 = mysqli_query($con,"DELETE FROM berichten WHERE id='" . $readId . "'");
		print"
		<tr>
			<td class='subTitle' colspan='4'>Beantwoord verwijderd</td>
		</tr>
		<tr>
			<td class='table_mainTxt' colspan='4'>Je hebt het bericht verwijderd</td>
		</tr>
		<tr>
			<td class='table_mainTxt' colspan='4'><a href='berichten.php?x=inbox'>Klik hier om terug naar je inbox te gaan</a></td>
		</tr>
		";
	}
	?>
</table>
