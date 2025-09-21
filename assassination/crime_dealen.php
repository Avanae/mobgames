<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");
	
	if($spelerInfo->mini_banner == 1){
		echo "<table width=\"550px\" colspan=\"5\">
		<tr>
			<td colspan='5'><img src='images/headers/wietplantage.jpg' width='550px' height='120px' alt='drugs dealen' /></td>
		</tr></table>";
	}
	$totalDrugs = $spelerInfo->wiet+$spelerInfo->hasj+$spelerInfo->xtc+$spelerInfo->coke;
	if(isset($_POST["buy"])){
		$wiet = mysqli_real_escape_string($con,test_input($_POST["amount_wiet"]));
		$hasj = mysqli_real_escape_string($con,test_input($_POST["amount_hasj"]));
		$xtc = mysqli_real_escape_string($con,test_input($_POST["amount_xtc"]));
		$coke = mysqli_real_escape_string($con,test_input($_POST["amount_coke"]));
		
		
		if(!is_numeric($wiet) || !is_numeric($hasj) || !is_numeric($xtc) || !is_numeric($coke)){
			print_bericht("Drugs Dealen","Je gaf ongeldige aantallen in.");
			exit;
		}
		if($wiet == 0 && $hasj == 0 && $xtc == 0 && $coke == 0){
			print_bericht("Drugs Dealen","Je moet op zijn minste 1 eenheid drugs kopen, wat een flater!");
			exit;
		}
		if($wiet < 0 || $hasj < 0 || $xtc < 0 || $coke < 0){
			print_bericht("Drugs Dealen","Je gaf ongeldige aantallen in.");
			exit;
		}
		
		$maxDrugs = $spelerInfo->rang*100;
		if($maxDrugs <= $totalDrugs){
			print_bericht("Drugs Dealen","Je zit al aan de maximale hoeveelheid drugs dat je kan bezitten.");
			exit;
		}
		
		$newTotalAmount = $wiet+$hasj+$xtc+$coke+$totalDrugs;
		
		if($newTotalAmount > $maxDrugs){
			print_bericht("Drugs Dealen","Je bezit al genoeg drugs, verkoop eerst wat je al hebt!");
			exit;
		}
		$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $spelerInfo->land . "'");
		$land = mysqli_fetch_object($landQuery);
		
		$wietPrijs = $wiet*$land->wietPrijs;
		$hasjPrijs = $hasj*$land->hasjPrijs;
		$xtcPrijs = $xtc*$land->xtcPrijs;
		$cokePrijs = $coke*$land->cokePrijs;
		
		$totalPrijs = $wietPrijs+$hasjPrijs+$xtcPrijs+$cokePrijs;
		if($spelerInfo->cash < $totalPrijs){
			print_bericht("Drugs Dealen","Je hebt " . formatDecimaal($totalPrijs) . " nodig om deze hoeveelheid drugs te kopen.");
			exit;
		}
		$totalPrijs¨=0;
		$newAmount = $wiet+$hasj+$xtc+$coke;
		mysqli_query($con,"UPDATE leden SET cash=cash-'" . $totalPrijs . "',wiet=wiet+'" . $wiet . "',hasj=hasj+'" . $hasj . "',xtc=xtc+'" . $xtc . "',coke=coke+'" . $coke . "' WHERE id='" . $spelerInfo->id . "'");
		print_bericht("Drugs Dealen","Je hebt " . formatDecimaal($newAmount) . " eenheden drugs gekocht voor &euro; " . formatDecimaal($totalPrijs) . ".");
		exit;
	}
	if(isset($_POST["sell"])){	
		$wiet = mysqli_real_escape_string($con,test_input($_POST["amount_wiet"]));
		$hasj = mysqli_real_escape_string($con,test_input($_POST["amount_hasj"]));
		$xtc = mysqli_real_escape_string($con,test_input($_POST["amount_xtc"]));
		$coke = mysqli_real_escape_string($con,test_input($_POST["amount_coke"]));
		
		if(!is_numeric($wiet) || !is_numeric($hasj) || !is_numeric($xtc) || !is_numeric($coke)){
			print_bericht("Drugs Dealen","Je gaf ongeldige aantallen in.");
			exit;
		}
		
		if($wiet == 0 && $hasj == 0 && $xtc == 0 && $coke == 0){
			print_bericht("Drugs Dealen","Je moet op zijn minste 1 eenheid drugs verkopen om winst te maken, wat een flater!");
			exit;
		}
		$bericht = "Er ging iets mis.";
		
		$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $spelerInfo->land . "'");
		$land = mysqli_fetch_object($landQuery);
		if($wiet > $spelerInfo->wiet || $wiet == 0){
			if($wiet != 0){
				$bericht = "<p>Zoveel wiet bezit je niet!</p>";
			}
		} else {
			$profit = $wiet*$land->wietPrijs;
			
			if($spelerInfo->familie != 0){
				$famQuery = mysqli_query($con,"SELECT * FROM families_belasting WHERE familieid='" . $spelerInfo->familie . "'");
				$familie = mysqli_fetch_object($famQuery);
				if($familie->smokkelTax >= 1){
					$famProfit = floor(($profit/100)*$familie->smokkelTax);
					$newProfit = $profit-$famProfit;
					$bericht = "<p>Je verkocht " . formatDecimaal($wiet) . " wiet voor &euro; " . formatDecimaal($profit) . ". Je hebt &euro; " . formatDecimaal($famProfit) . " moeten afgeven aan je familie en hield dan nog " . formatDecimaal($newProfit) . " over voor jezelf!</p>";
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $newProfit . "', wiet=wiet-'" . $wiet . "' WHERE id='" . $spelerInfo->id . "'");
					mysqli_query($con,"UPDATE families SET geld=geld+'" . $famProfit . "' WHERE id='" . $spelerInfo->familie . "'");
					
					
				} else {
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "', wiet=wiet-'" . $wiet . "' WHERE id='" . $spelerInfo->id . "'");
					$bericht = "<p>Je verkocht " . formatDecimaal($wiet) . " wiet voor &euro; " . formatDecimaal($profit) . ".</p>";
				}
			} else {
				mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "', wiet=wiet-'" . $wiet . "' WHERE id='" . $spelerInfo->id . "'");
				$bericht = "<p>Je verkocht " . formatDecimaal($wiet) . " wiet voor &euro; " . formatDecimaal($profit) . ".</p>";
			}
		}
		if($hasj > $spelerInfo->hasj || $hasj == 0){
			if($hasj != 0){
				$bericht = $bericht . "<p>Zoveel hasj bezit je niet!</p>";
			}
		} else {
			$profit = $hasj*$land->hasjPrijs;
			
			if($spelerInfo->familie != 0){
				$famQuery = mysqli_query($con,"SELECT * FROM families_belasting WHERE familieid='" . $spelerInfo->familie . "'");
				$familie = mysqli_fetch_object($famQuery);
				if($familie->smokkelTax >= 1){
					$famProfit = floor(($profit/100)*$familie->smokkelTax);
					$newProfit = $profit-$famProfit;
					$bericht = "<p>Je verkocht " . formatDecimaal($hasj) . " hasj voor &euro; " . formatDecimaal($profit) . ". Je hebt &euro; " . formatDecimaal($famProfit) . " moeten afgeven aan je familie en hield dan nog " . formatDecimaal($newProfit) . " over voor jezelf!</p>";
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $newProfit . "', hasj=hasj-'" . $hasj . "' WHERE id='" . $spelerInfo->id . "'");
					mysqli_query($con,"UPDATE families SET geld=geld+'" . $famProfit . "' WHERE id='" . $spelerInfo->familie . "'");
					
					
				} else {
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "', hasj=hasj-'" . $hasj . "' WHERE id='" . $spelerInfo->id . "'");
					$bericht = "<p>Je verkocht " . formatDecimaal($hasj) . " hasj voor &euro; " . formatDecimaal($profit) . ".</p>";
				}
			} else {
				mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "', hasj=hasj-'" . $hasj . "' WHERE id='" . $spelerInfo->id . "'");
				$bericht = "<p>Je verkocht " . formatDecimaal($hasj) . " hasj voor &euro; " . formatDecimaal($profit) . ".</p>";
			}
		}
		if($xtc > $spelerInfo->xtc || $xtc == 0){
			if($xtc != 0){
				$bericht = $bericht . "<p>Zoveel xtc bezit je niet!</p>";
			}
		} else {
			$profit = $xtc*$land->xtcPrijs;
			
			if($spelerInfo->familie != 0){
				$famQuery = mysqli_query($con,"SELECT * FROM families_belasting WHERE familieid='" . $spelerInfo->familie . "'");
				$familie = mysqli_fetch_object($famQuery);
				if($familie->smokkelTax >= 1){
					$famProfit = floor(($profit/100)*$familie->smokkelTax);
					$newProfit = $profit-$famProfit;
					$bericht = "<p>Je verkocht " . formatDecimaal($xtc) . " xtc voor &euro; " . formatDecimaal($profit) . ". Je hebt &euro; " . formatDecimaal($famProfit) . " moeten afgeven aan je familie en hield dan nog " . formatDecimaal($newProfit) . " over voor jezelf!</p>";
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $newProfit . "', xtc=xtc-'" . $xtc . "' WHERE id='" . $spelerInfo->id . "'");
					mysqli_query($con,"UPDATE families SET geld=geld+'" . $famProfit . "' WHERE id='" . $spelerInfo->familie . "'");
					
					
				} else {
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "', xtc=xtc-'" . $xtc . "' WHERE id='" . $spelerInfo->id . "'");
					$bericht = "<p>Je verkocht " . formatDecimaal($xtc) . " xtc voor &euro; " . formatDecimaal($profit) . ".</p>";
				}
			} else {
				mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "', xtc=xtc-'" . $xtc . "' WHERE id='" . $spelerInfo->id . "'");
				$bericht = "<p>Je verkocht " . formatDecimaal($xtc) . " xtc voor &euro; " . formatDecimaal($profit) . ".</p>";
			}
		}
		if($coke > $spelerInfo->coke || $coke == 0){
			if($coke != 0){
				$bericht = $bericht . "<p>Zoveel coke bezit je niet!</p>";
			}
		} else {
			$profit = $coke*$land->cokePrijs;
			
			if($spelerInfo->familie != 0){
				$famQuery = mysqli_query($con,"SELECT * FROM families_belasting WHERE familieid='" . $spelerInfo->familie . "'");
				$familie = mysqli_fetch_object($famQuery);
				if($familie->smokkelTax >= 1){
					$famProfit = floor(($profit/100)*$familie->smokkelTax);
					$newProfit = $profit-$famProfit;
					$bericht = "<p>Je verkocht " . formatDecimaal($coke) . " coke voor &euro; " . formatDecimaal($profit) . ". Je hebt &euro; " . formatDecimaal($famProfit) . " moeten afgeven aan je familie en hield dan nog " . formatDecimaal($newProfit) . " over voor jezelf!</p>";
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $newProfit . "', coke=coke-'" . $coke . "' WHERE id='" . $spelerInfo->id . "'");
					mysqli_query($con,"UPDATE families SET geld=geld+'" . $famProfit . "' WHERE id='" . $spelerInfo->familie . "'");
					
					
				} else {
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "', coke=coke-'" . $coke . "' WHERE id='" . $spelerInfo->id . "'");
					$bericht = "<p>Je verkocht " . formatDecimaal($coke) . " coke voor &euro; " . formatDecimaal($profit) . ".</p>";
				}
			} else {
				mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "', coke=coke-'" . $coke . "' WHERE id='" . $spelerInfo->id . "'");
				$bericht = "<p>Je verkocht " . formatDecimaal($coke) . " coke voor &euro; " . formatDecimaal($profit) . ".</p>";
			}
		}
		
		print_bericht("Drugs Dealen",$bericht);
		exit;
	}
?>

<table width="550px" class="inhoud_table" colspan="5">
	<tr>
		<td class='table_subTitle' colspan='5'>Drugs Dealen</td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="100%" colspan="5">Handel hier in drugs. Jij mag maximum <?php echo formatDecimaal($spelerInfo->rang*100); ?> eenheden drugs kopen met je huidige rank.</td>
	</tr>
</table>
<table width="550px" class="inhoud_table" colspan="5">
	<tr>
		<td class='table_subTitle' colspan='5'>Drugs</td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="30%" colspan="1">Type</td>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">Prijs/eenheid</td>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">In bezit</td>
		<td class="table_mainTxt outline padding_5" width="40%" colspan="2">Hoeveelheid</td>
	</tr>
	<form method="post" target="">
	<?php
	$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $spelerInfo->land . "'");
	$land = mysqli_fetch_object($landQuery);
	print"
	<tr>
		<td class='table_mainTxt'>Wiet</td>
		<td class='table_mainTxt'>&euro; " . formatDecimaal($land->wietPrijs) . "</td>
		<td class='table_mainTxt'>" . formatDecimaal($spelerInfo->wiet) . "</td>
		<td class='table_mainTxt padding_2'><input type=\"text\" maxlength=\"6\" class=\"input_form padding_2\" name=\"amount_wiet\" value=\"0\" size=\"17\" /></td>
	</tr>
	<tr>
		<td class='table_mainTxt'>Hasj</td>
		<td class='table_mainTxt'>&euro; " . formatDecimaal($land->hasjPrijs) . "</td>
		<td class='table_mainTxt'>" . formatDecimaal($spelerInfo->hasj) . "</td>
		<td class='table_mainTxt padding_2'><input type=\"text\" maxlength=\"6\" class=\"input_form padding_2\" name=\"amount_hasj\" value=\"0\" size=\"17\" /></td>
	</tr>
	<tr>
		<td class='table_mainTxt'>XTC</td>
		<td class='table_mainTxt'>&euro; " . formatDecimaal($land->xtcPrijs) . "</td>
		<td class='table_mainTxt'>" . formatDecimaal($spelerInfo->xtc) . "</td>
		<td class='table_mainTxt padding_2'><input type=\"text\" maxlength=\"6\" class=\"input_form padding_2\" name=\"amount_xtc\" value=\"0\" size=\"17\" /></td>
	</tr>
	<tr>
		<td class='table_mainTxt'>Cocaïne</td>
		<td class='table_mainTxt'>&euro; " . formatDecimaal($land->cokePrijs) . "</td>
		<td class='table_mainTxt'>" . formatDecimaal($spelerInfo->coke) . "</td>
		<td class='table_mainTxt padding_2'><input type=\"text\" maxlength=\"6\" class=\"input_form padding_2\" name=\"amount_coke\" value=\"0\" size=\"17\" /></td>
	</tr>
	";

	?>
	<tr>
		<td class='table_mainTxt' colspan="3">In bezit: <?php echo formatDecimaal($totalDrugs); ?> / <?php echo formatDecimaal($spelerInfo->rang*100); ?> </td>
		<td class='table_mainTxt' colspan="1"><input type="submit" class="button_form" name="buy" value="Koop" /> <input type="submit" class="button_form" name="sell" value="Verkoop" /></td>
	</tr>
	</form>
</table>