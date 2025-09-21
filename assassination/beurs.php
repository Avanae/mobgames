<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");
	
	if(isset($_POST["buy"])){
		$aandelenQuery = mysqli_query($con,"SELECT * FROM beurs");
		
		$response = "";
		$gelukt = false;
		while($aandeel = mysqli_fetch_object($aandelenQuery)){
			if(isset($_POST["amount_" . $aandeel->id])){
				$amount = mysqli_real_escape_string($con,test_input($_POST["amount_" . $aandeel->id]));
				if(is_numeric($amount)){
					// Alleen aandelen kopen die effectief worden aangekocht
					if($amount > 0){
						$amountTxt = $amount == 1 ? "aandeel" : "aandelen";
						$price = $amount*$aandeel->prijs;
						if($spelerInfo->cash >= $price){
							$buyGelukt = true;
							$spelerAandeelQuery = mysqli_query($con,"SELECT * FROM leden_beurs WHERE speler='" . $spelerInfo->id . "' AND aandeelid='" . $aandeel->id . "'");
							
							$maxaandelen = $spelerInfo->rang*100;
							if(mysqli_num_rows($spelerAandeelQuery) == 1){
								$spelerAandeel = mysqli_fetch_object($spelerAandeelQuery);
								
								if($maxaandelen >= ($spelerAandeel->aantal+$amount)){
									mysqli_query($con,"UPDATE leden_beurs SET aantal=aantal+'" . $amount . "' WHERE id='" . $spelerAandeel->id . "'");
									mysqli_query($con,"UPDATE leden SET cash=cash-'" . $price . "' WHERE id='" . $spelerInfo->id . "'");
								} else {
									$buyGelukt = false;
									$response = $response . "<p class=\"red\">Je mag maar " . formatDecimaal($maxaandelen) . " aandelen per bedrijf bezitten.</p>";
								}
							} else {
								if($maxaandelen >= $amount){
									mysqli_query($con,"INSERT INTO leden_beurs (speler,aandeelid,aantal) VALUES ('" . $spelerInfo->id . "','" . $aandeel->id . "','" . $amount . "')");
								} else {
									$buyGelukt = false;
									$response = $response . "<p class=\"limgreen\">Je mag maar " . formatDecimaal($maxaandelen) . " aandelen per bedrijf bezitten.</p>";
								}
							}
							if($buyGelukt){
								
								$response = $response . "<p class=\"limgreen\">Je kocht " . formatDecimaal($amount) . " " . $amountTxt . " van " . $aandeel->titel . ".</p>";
							}
						} else {
							$response = $response . "<p class=\"red\">Je hebt niet genoeg geld om " . formatDecimaal($amount) . " " . $amountTxt . " van " . $aandeel->titel . " te kopen.</p>";
						}
						$gelukt = true;
					}
				} else {
					$response = $response . "<p class=\"red\">Aandeel bestaat niet.</p>";
				}
			}
		}
		if($gelukt){
			echo print_bericht("Beurs",$response);
		}
	}
	if(isset($_POST["sell"])){
		$aandelenQuery = mysqli_query($con,"SELECT * FROM beurs");
		
		$response = "";
		$gelukt = false;
		while($aandeel = mysqli_fetch_object($aandelenQuery)){
			if(isset($_POST["amount_" . $aandeel->id])){
				$amount = mysqli_real_escape_string($con,test_input($_POST["amount_" . $aandeel->id]));
				if(is_numeric($amount)){
					// Alleen aandelen kopen die effectief worden aangekocht
					if($amount > 0){
						$amountTxt = $amount == 1 ? "aandeel" : "aandelen";
						$buyGelukt = true;
						$spelerAandeelQuery = mysqli_query($con,"SELECT * FROM leden_beurs WHERE speler='" . $spelerInfo->id . "' AND aandeelid='" . $aandeel->id . "'");

						if(mysqli_num_rows($spelerAandeelQuery) == 1){
							$spelerAandeel = mysqli_fetch_object($spelerAandeelQuery);
							if($amount <= $spelerAandeel->aantal){
								$gelukt = true;
								$profit = $spelerAandeel->aantal*$aandeel->prijs;
								$response = $response . "<p class=\"limegreen\">Je hebt " . formatDecimaal($amount) . " " . $amountTxt . " van " . $aandeel->titel . " verkocht voor &euro; " . formatDecimaal($profit) . ".</p>";
								mysqli_query($con,"UPDATE leden_beurs SET aantal=aantal-'" . $amount . "' WHERE id='" . $spelerAandeel->id . "'");
								mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "' WHERE id='" . $spelerInfo->id . "'");
								
							} else {
								$response = $response . "<p class=\"red\">Je hebt zoveel aandelen van " . $aandeel->titel . " niet.</p>";
							}
						} else {
							$response = $response . "<p class=\"red\">Je hebt geen aandelen van " . $aandeel->titel . ".</p>";
						}
					}
				} else {
					$response = $response . "<p class=\"red\">Aandeel bestaat niet.</p>";
				}
			}
		}
		if($gelukt){
			echo print_bericht("Beurs",$response);
		}
	}
?>

<table width="550px" class="inhoud_table" colspan="5">
	<tr>
		<td class='table_subTitle' colspan='5'>Informatie Beurs</td>
	</tr>
	<tr>
		<td class="table_mainTxt" width="100%" colspan="5">Handel hier in aandelen. Jij mag <?php echo formatDecimaal($spelerInfo->rang*100); ?> aantal aandelen per bedrijf kopen. De prijzen veranderen elke 30 minuten. Je maximum aantal aandelen gaat omhoog wanneer je promoveert en een nieuwe rang haalt.</td>
	</tr>
</table>
<table width="550px" class="inhoud_table" colspan="5">
	<tr>
		<td class='table_subTitle' colspan='5'>De beurs</td>
	</tr>
	<tr>
		<td class="table_mainTxt outline" width="20%" colspan="1">Bedrijf</td>
		<td class="table_mainTxt outline" width="15%" colspan="1">Prijs/aandeel</td>
		<td class="table_mainTxt outline" width="15%" colspan="1">Vorige Prijs</td>
		<td class="table_mainTxt outline" width="15%" colspan="1">In bezit</td>
		<td class="table_mainTxt outline" width="35%" colspan="1"></td>
	</tr>
	<form method="post" target="">
	<?php
	$aandelenQuery = mysqli_query($con,"SELECT * FROM beurs");
	while($aandeel = mysqli_fetch_object($aandelenQuery)){
		$spelerAantal = "0";
		$spelerAandeelQuery = mysqli_query($con,"SELECT * FROM leden_beurs WHERE speler='" . $spelerInfo->id . "' AND aandeelid='" . $aandeel->id . "'");
		if(mysqli_num_rows($spelerAandeelQuery) == 1){
			$spelerAandeel = mysqli_fetch_object($spelerAandeelQuery);
			$spelerAantal = formatDecimaal($spelerAandeel->aantal);
		}
		
		$price = ($aandeel->prijs >= $aandeel->vorigeprijs) ? "<span class=\"limegreen\">&euro; " . formatDecimaal($aandeel->prijs) . "</span>" : "<span class=\"red\">&euro; " . formatDecimaal($aandeel->prijs) . "</span>";
		print"
		<tr>
			<td class='table_mainTxt'>" . $aandeel->titel . "</td>
			<td class='table_mainTxt'>" . $price . "</td>
			<td class='table_mainTxt'>&euro; " . formatDecimaal($aandeel->vorigeprijs) . "</td>
			<td class='table_mainTxt'>" . $spelerAantal . "</td>
			<td class='table_mainTxt'><input type=\"text\" maxlength=\"5\" class=\"input_form\" name=\"amount_" . $aandeel->id . "\" value=\"0\" size=\"8\" /></td>
		</tr>
		
		";
	}
	?>
	<tr>
		<td class='table_mainTxt'></td>
		<td class='table_mainTxt'></td>
		<td class='table_mainTxt'></td>
		<td class='table_mainTxt'></td>
		<td class='table_mainTxt'><input type="submit" class="button_form" name="buy" value="Koop" /> <input type="submit" class="button_form" name="sell" value="Verkoop" /></td>
	</tr>
	</form>
</table>