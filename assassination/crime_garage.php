<?php
	include("config.php");
	include("SYSTEM_CONFIGURATION.php");
	include("include/functions.php");
	
	
	$landen = GetAllCountries($con);
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");

	if($spelerInfo->mini_banner == 1){
		print "<img src='images/headers/wietplantage.jpg' width='550px' height='120px' alt='wietplantage foto' />";
	}
if(isset($_POST["crush"])){
	if(!isset($_POST["cars"])){ 
		print_bericht("Garage","Je moet een auto selecteren om te crushen.");
		exit;
	}
	
	$cars = [];
	$i = 0;
	foreach ($_POST['cars'] as $value) {
		$carId = mysqli_real_escape_string($con,test_input($value));
		if(is_numeric($carId) && !empty($value)){
			$cars[$i] = $value;
			$i++;
		}
	}
	$bericht = "";
	$totalBullets = 0;
	$totalCrushed = 0;
	$gelukt = true;
	
	$famCrusherQuery = mysqli_query($con,"SELECT * FROM families_crusher WHERE familie='" . $spelerInfo->familie . "' AND maxAutos>crushed");
	$bericht = "";		
	foreach($cars as $carAr){
		if(!$gelukt){ break; }
		
		$carQuery = mysqli_query($con,"SELECT * FROM autos_leden WHERE id='" . $carAr . "' AND land='" . $spelerInfo->land . "' AND eigenaar='" . $spelerInfo->id . "'");
		if(mysqli_num_rows($carQuery) == 1){
			// Controleren of auto gebruikt word in een heist
			$heistQuery = mysqli_query($con,"SELECT * from crime_heist WHERE car='" . $carAr . "'");
			if(mysqli_num_rows($heistQuery) <= 0){
				$famCrusherQuery = mysqli_query($con,"SELECT * FROM families_crusher WHERE familie='" . $spelerInfo->familie . "' AND maxAutos>crushed");
				if(mysqli_num_rows($famCrusherQuery) == 1){
					$familieCrusher = mysqli_fetch_object($famCrusherQuery);
					
					mysqli_query($con,"UPDATE families_crusher SET crushed=crushed+'1' WHERE familie='" . $spelerInfo->familie . "'");
					mysqli_query($con,"DELETE FROM autos_leden WHERE id='" . $carAr . "' AND eigenaar='" . $spelerInfo->id . "'");
				
					$totalBullets = $totalBullets+$familieCrusher->kogels;
					$totalCrushed++;
					$gelukt = true;
				} else {
					$gelukt = false;
					$bericht = "Je familie crusher kan geen autos meer verwerken. Je moet wachten tot de top weer een nieuwe crusher huurt.";
				}
			} else {
				if($totalCrushed == 0){
					$bericht = "Deze auto wordt momenteel gebruikt in een heist!";
				}
			}
		}
	}
	if($totalCrushed == 0){
		print_bericht("Garage",$bericht);
	} else {
		mysqli_query($con,"UPDATE leden SET kogels=kogels+'" . $totalBullets . "' WHERE id='" . $spelerInfo->id . "'");
		print_bericht("Garage","Je hebt " . formatDecimaal($totalCrushed) . " autos gecrusht en daarmee " . formatDecimaal($totalBullets) . " kogels verdiend.");
		
	}
}
if(isset($_POST["sell"])){
	if(!isset($_POST["cars"])){ 
		print_bericht("Garage","Je moet een auto selecteren om te verkopen.");
		exit;
	}
	
	$cars = [];
	$i = 0;
	foreach ($_POST['cars'] as $value) {
		$carId = mysqli_real_escape_string($con,test_input($value));
		if(is_numeric($carId) && !empty($value)){
			$cars[$i] = $value;
			$i++;
		}
	}
	
	$bericht = "";
	$totalSellProfit = 0;
	$totalFamilyTax = 0;
	$totalSold = 0;
	$usedInHeist = false;
	foreach($cars as $carAr){
		$carQuery = mysqli_query($con,"SELECT * FROM autos_leden WHERE id='" . $carAr . "' AND land='" . $spelerInfo->land . "'");
		if(mysqli_num_rows($carQuery) == 1){
			// Controleren of auto gebruikt word in een heist
			$heistQuery = mysqli_query($con,"SELECT * from crime_heist WHERE car='" . $carAr . "'");
			if(mysqli_num_rows($heistQuery) <= 0){
			
				$auto = mysqli_fetch_object($carQuery);
				
				$autoInfoQuery = mysqli_query($con,"SELECT * FROM autos_types WHERE id='" . $auto->typeid . "'");
				$autoInfo = mysqli_fetch_object($autoInfoQuery);
				
				$totalValue = round($autoInfo->value-(($autoInfo->value/100)*$auto->schade));
				if($spelerInfo->familie != 0){
					// Familie van de speler laden
					$familieQuery = mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerInfo->familie . "'");
					$familie = mysqli_fetch_object($familieQuery);
					
					// Familie belastingen ophalen
					$familieBelastingQuery = mysqli_query($con,"SELECT * FROM families_belasting WHERE familieid='" . $familie->id . "'");
					$familieBelasting = mysqli_fetch_object($familieBelastingQuery);
					if($familieBelasting->autoTax != 0){
						$familieOpbrengst = floor($totalValue/100)*$familieBelasting->autoTax;
						$nieuweOpbrengst = $totalValue-$familieOpbrengst;
								
						mysqli_query($con,"UPDATE families SET geld=geld+'" . $familieOpbrengst . "' WHERE id='" . $familie->id . "'");
						
						$totalSellProfit = $totalSellProfit+$nieuweOpbrengst;
						$totalFamilyTax = $totalFamilyTax+$familieOpbrengst;
					} else {
						$totalSellProfit = $totalSellProfit+$totalValue;
					}
				}
				$totalSold++;
				mysqli_query($con,"DELETE FROM autos_leden WHERE id='" . $auto->id . "'");
			} else {
				if($totalSold == 0){
					$usedInHeist = true;
				}
			}
		}
		
	}
	if($totalSold >= 1){
		mysqli_query($con,"UPDATE leden SET cash=cash+'" . $totalSellProfit . "' WHERE id='" . $spelerInfo->id ."'");
		$bericht = "Je hebt met succes " . formatDecimaal($totalSold) . " autos verkocht voor &euro; " . formatDecimaal($totalSellProfit) . ". Je hebt &euro; " . formatDecimaal($totalFamilyTax) . " moeten afgeven aan je familie.";
	} else {
		if($usedInHeist){
			$bericht = "Deze auto wordt gebruikt in een heist!";
		} else {
			$bericht = "Je hebt geen auto's verkocht, zie dat je in het land van de auto(s) bent als je deze wil verkopen!";
		}
	}
	
	print_bericht("Garage",$bericht);
}
// 
$autosQuery = mysqli_query($con,"SELECT * FROM autos_leden WHERE eigenaar='" . $spelerInfo->id . "'");	
?>
<style>.padding_5 { padding:5xp; } </style>
<form method="post" action="crime_garage.php">
<script>
	function SelectAllCars()
	{
		var inputs = document.getElementsByTagName("input");
		for(var i = 0; i < inputs.length; i++) {
			if(inputs[i].type == "checkbox") {
				if(inputs[i].checked == true){
					inputs[i].checked = false;
				} else {
					inputs[i].checked = true;
				}
			}  
		}
		
	}

</script>
<table width="550px" colspan="6" class="inhoud_table">
	<tr>
		<td class="table_subTitle center" width="100%" colspan="6">Jou garage</td>
	</tr>
	<?php if(mysqli_num_rows($autosQuery) >= 1){ ?>
		<tr>
			<td class="table_mainTxt padding_5 outline" width="10%" colspan="1">#</td>
			<td class="table_mainTxt padding_5 outline" width="40%" colspan="1">Naam</td>
			<td class="table_mainTxt padding_5 outline" width="15%" colspan="1">Land</td>
			<td class="table_mainTxt padding_5 outline" width="10%" colspan="1">Schade</td>
			<td class="table_mainTxt padding_5 outline" width="20%" colspan="1">Waarde</td>
			<td class="table_mainTxt padding_5" width="5%" colspan="1"></td>
		</tr>
		<form method="post" action="crime_garage.php">
			<?php
				
				
				$totalCars = 0;
				while($auto = mysqli_fetch_object($autosQuery)){ ?>
				<?php
					$autoInfoQuery = mysqli_query($con,"SELECT * FROM autos_types WHERE id='" . $auto->typeid . "'");
					$autoInfo = mysqli_fetch_object($autoInfoQuery);
					$waarde = round($autoInfo->value-(($autoInfo->value/100)*$auto->schade));
					$totalCars++;
				?>
				<tr>
					<td class="table_mainTxt padding_5" width="10%" colspan="1"><?php echo $auto->id; ?></td>
					<td class="table_mainTxt padding_5" width="40%" colspan="1"><?php echo $autoInfo->name; ?></td>
					<td class="table_mainTxt padding_5" width="15%" colspan="1"><?php echo utf8_encode($landen[$auto->land]); ?></td>
					<td class="table_mainTxt padding_5" width="10%" colspan="1"><?php echo $auto->schade; ?> %</td>
					<td class="table_mainTxt padding_5" width="20%" colspan="1">&euro; <?php echo formatDecimaal($waarde); ?></td>
					<td class="table_mainTxt padding_5" width="5%" colspan="1"><input type="checkbox" name="cars[]" value="<?php echo $auto->id; ?>"></td>
				</tr>
			<?php } ?>
			<tr>
				<td class="table_mainTxt padding_5 outline" width="35%" colspan="2"><p>Totaal auto's:</p><p><?php echo formatDecimaal($totalCars); ?></p></td>
				</p></td>
				<td class="table_mainTxt padding_5 outline" width="65%" colspan="4">
					<input type="submit" name="sell" value="Verkoop" class="button_form" /><input type="submit" name="crush" value="Crush" class="button_form" /><input type="button" value="Selecteer alles" class="button_form" onclick="SelectAllCars()" />
				</td>
			</tr>
		</form>
	<?php } else { ?>
		<tr>
			<td class="table_mainTxt padding_5" width="100%" colspan="5">Je hebt nog geen auto's in jou garage.</td>
		</tr>
	<?php } ?>
</table>
</form>