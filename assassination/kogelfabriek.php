<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");

if($spelerInfo->mini_banner == 1){
	print "<img src='images/headers/bulletfactory.jpg' width='550px' height='120px' alt='kogels foto' />";
}

$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $spelerInfo->land . "'");
$land = mysqli_fetch_object($landQuery);

// De kogelfabriek laden
$kogelfabriekQuery = mysqli_query($con,"SELECT * FROM casino_objecten WHERE type='4' AND land='" . $spelerInfo->land . "'");
// Controleren of de kogelfabriek bestaat, zoniet aanmaken en opnieuw ophalen
if(mysqli_num_rows($kogelfabriekQuery) <= 0){
	mysqli_query($con,"INSERT INTO casino_objecten (type,land) VALUES ('4','" . $spelerInfo->land . "')");
	$kogelfabriekQuery = mysqli_query($con,"SELECT * FROM casino_objecten WHERE type='4' AND land='" . $spelerInfo->land . "'");
}
$kogelfabriek = mysqli_fetch_object($kogelfabriekQuery);
?>
<?php 
if(isset($_POST["buyBullets"])){
	$aantal = mysqli_real_escape_string($con,test_input($_POST["aantal"]));
	
	if(!is_numeric($aantal)){
		print_bericht("Kogelfabriek","Er ging iets mis");
		exit;
	}
	
	$prijs = $kogelfabriek->maxinzet*$aantal;
	if($spelerInfo->cash < $prijs){
		print_bericht("Kogelfabriek","Je hebt niet genoeg geld!");
	} else if($aantal > $kogelfabriek->mininzet){
		print_bericht("Kogelfabriek","Zoveel kogels heeft deze kogelfabriek niet!");
	} else {
		mysqli_query($con,"UPDATE casino_objecten SET mininzet=mininzet-'" . $aantal . "' WHERE id='" . $kogelfabriek->id . "'");
		mysqli_query($con,"UPDATE leden SET cash=cash-'" . $prijs . "',kogels=kogels+'" . $aantal . "' WHERE id='" . $spelerInfo->id . "'");
		if($kogelfabriek->eigenaar != 0){
			// Hier de taks van de familie berekenen en verwerken
			$omzet = round($prijs/100);
			mysqli_query($con,"UPDATE casino_objecten SET omzet=omzet+'" . $omzet . "' WHERE id='" . $kogelfabriek->id . "'");
		}
		print_bericht("Kogelfabriek","Je hebt met succes " . formatDecimaal($aantal) . " kogels gekocht voor &euro; " . formatDecimaal($prijs) . ".");
	}
	
}
// Als een speler de kogelfabriek koopt
if(isset($_GET["buy"])){
	if($spelerInfo->cash < $kogelfabriek->prijs){
		print_bericht("Kogelfabriek","Je hebt niet genoeg geld om deze kogelfabriek te kopen!");
	} else if($kogelfabriek->eigenaar == 0){
		mysqli_query($con,"UPDATE leden SET cash=cash-'" . $kogelfabriek->prijs . "' WHERE id='" . $spelerInfo->id . "'");
		if($kogelfabriek->eigenaar == 0){
			mysqli_query($con,"UPDATE casino_objecten SET eigenaar='" . $spelerInfo->id . "',mininzet='0',maxinzet='10000',omzet='0' WHERE id='" . $kogelfabriek->id . "'");
		}
		print_bericht("Kogelfabriek","Je hebt deze kogelfabriek gekocht voor &euro; " . formatDecimaal($kogelfabriek->prijs) . "!");
	} else {
		// Mogelijk een verdachte poging (hack of crawler)
		print_bericht("Kogelfabriek","Deze kogelfabriek is niet te koop!");
	}
}

?>

<table width='550px' class="inhoud_table">
	<tr>
		<td class='table_subTitle' colspan="4">Kogelfabriek in <?php echo utf8_encode($land->land); ?></td>
	</tr>
	<?php if($kogelfabriek->eigenaar == 0){ ?>
		<tr>
			<td class='table_mainTxt outline padding_5' width="100%" colspan="4"><a href="kogelfabriek.php?buy=<?php echo $kogelfabriek->id; ?>">Koop deze kogelfabriek voor &euro; <?php echo formatDecimaal($kogelfabriek->prijs); ?></a></td>
		</tr>
	<?php } else { ?>
		<?php
			$ownerQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $kogelfabriek->eigenaar . "'");
			$owner = mysqli_fetch_object($ownerQuery);
		?>
		<tr>
			<td class='table_mainTxt padding_5 outline' width="20%">Eigenaar</td>
			<td class='table_mainTxt padding_5' width="30%"><a href="speler_profiel.php?x=<?php echo $owner->id; ?>"><?php echo $owner->login; ?></a></td>
			<td class='table_mainTxt padding_5' width="20%"></td>
			<td class='table_mainTxt padding_5' width="30%"></td>
		</tr>
	<?php } ?>
	<tr>
		<td class='table_mainTxt padding_5 outline' width="20%">KogelPrijs</td>
		<td class='table_mainTxt padding_5' width="30%">&euro; <?php echo formatDecimaal($kogelfabriek->maxinzet); ?></td>
		<td class='table_mainTxt padding_5' width="20%"></td>
		<td class='table_mainTxt padding_5' width="30%"></td>
	</tr>
	<tr>
		<td class='table_mainTxt padding_5 outline' width="20%">Kogels</td>
		<td class='table_mainTxt padding_5' width="30%"><?php echo formatDecimaal($kogelfabriek->mininzet); ?></td>
		<td class='table_mainTxt padding_5' width="20%"></td>
		<td class='table_mainTxt padding_5' width="30%"></td>
	</tr>
	<form method="post" action="">
	<tr>
		<td class='table_mainTxt padding_5 outline' width="20%">Kopen</td>
		<td class='table_mainTxt padding_5' width="30%"><input class="input_form" type="text" maxlength="9" name="aantal" id="aantal" /></td>
		<td class='table_mainTxt padding_5' width="20%"></td>
		<td class='table_mainTxt padding_5' width="30%"></td>
	</tr>
	<tr>
		<td class='table_mainTxt padding_5' width="20%"></td>
		<td class='table_mainTxt padding_5' width="30%"><input type="submit" class="button_form" name="buyBullets" value="Kopen" /></td>
		<td class='table_mainTxt padding_5' width="20%"></td>
		<td class='table_mainTxt padding_5' width="30%"></td>
	</tr>
	</form>
</table>

<?php 
if($kogelfabriek->eigenaar == $spelerInfo->id){ 
	// Als de eigenaar de instellingen van de kogelfabriek veranderd
	if(isset($_POST["changeSettings"])){ 
		$cash = mysqli_real_escape_string($con,test_input($_POST["withdraw"]));
		$bullets = mysqli_real_escape_string($con,test_input($_POST["kogels"]));
		$price = mysqli_real_escape_string($con,test_input($_POST["kogelprijs"]));
		
		if(!is_numeric($cash) || !is_numeric($bullets) || !is_numeric($price)){
			print_bericht("Kogelfabriek","Er ging iets mis");
			exit;
		}
		
		$message = "";
		if($price != 0){
			if($price >= 1000 || $price <= 99){
				$message = $message . "<p>De prijs moet onder de &euro; 1.000 liggen en boven &euro; 99.</p>";
			} else {
				mysqli_query($con,"UPDATE casino_objecten SET maxinzet='" . $price . "' WHERE id='" . $kogelfabriek->id . "'");
				$message = $message . "<p>De nieuwe kogelprijs bedraagt nu &euro; " . formatDecimaal($price) . "</p>";
			}
		}
		if($bullets != 0){
			if($bullets > $kogelfabriek->mininzet || $bullets <= 0){
				$message = $message . "<p>Je kan maar maximum " . $kogelfabriek->mininzet . " kogels ophalen in deze kogelfabriek.</p>";
			} else {
				mysqli_query($con,"UPDATE casino_objecten SET mininzet=mininzet-'" . $bullets . "' WHERE id='" . $kogelfabriek->id . "'");
				mysqli_query($con,"UPDATE leden SET kogels=kogels+'" . $bullets . "' WHERE id='" . $spelerInfo->id . "'");
				$message = $message . "<p>Je hebt met succes " . formatDecimaal($bullets) . " kogels opgehaald.</p>";

			}
		}
		if($cash != 0){
			if($cash > $kogelfabriek->omzet || $cash < 0){
				$message = $message . "<p>Je kan maar maximum &euro; " . $kogelfabriek->omzet . " ophalen in deze kogelfabriek en het bedrag moet hoger dan &euro; 0 zijn.</p>";
			} else {
				mysqli_query($con,"UPDATE casino_objecten SET omzet=omzet-'" . $cash . "' WHERE id='" . $kogelfabriek->id . "'");
				mysqli_query($con,"UPDATE leden SET cash=cash+'" . $cash . "' WHERE id='" . $spelerInfo->id . "'");
				$message = $message . "<p>Je hebt met succes &euro; " . formatDecimaal($cash) . " opgehaald.</p>";
			}
		}
		print_bericht("Kogelfabriek beheer",$message);
	} 
	// Als de eigenaar de kogelfabriek verkoopt
	if(isset($_GET["sell"])){
		if($kogelfabriek->eigenaar == $spelerInfo->id){
			mysqli_query($con,"UPDATE leden SET cash=cash+'" . round($kogelfabriek->prijs/2) . "' WHERE id='" . $spelerInfo->id . "'");
			if($kogelfabriek->eigenaar == 0){
				mysqli_query($con,"UPDATE casino_objecten SET eigenaar='0',mininzet='0',maxinzet='999',omzet='0' WHERE id='" . $kogelfabriek->id . "'");
			}
			print_bericht("Kogelfabriek","Je hebt de kogelfabriek verkocht voor &euro; " . formatDecimaal(round($kogelfabriek->prijs/2)) . "!");
		} else {
			// Mogelijk een verdachte poging (hack of crawler)
			print_bericht("Kogelfabriek","Deze kogelfabriek is niet van jou!");
		}
	}
	?>

	<table width='550px' class="inhoud_table">
		<form method="post" action="kogelfabriek.php">
			<tr>
				<td class='table_subTitle' colspan="4">Kogelfabriek beheren</td>
			</tr>
			<tr>
				<td class='table_mainTxt padding_5 outline' width="20%"><label for="bulletPrice">Kogelprijs</label></td>
				<td class='table_mainTxt padding_5' width="30%"><input class="input_form" type="text" name="kogelprijs" id="bulletPrice" maxlength="4" value="0" /></td>
				<td class='table_mainTxt padding_5 outline' width="20%"><label for="bulletTotal">Kogels</label></td>
				<td class='table_mainTxt padding_5' width="30%"><input class="input_form" type="text" name="kogels" id="bulletTotal" value="0" /></td>
			</tr>
			<tr>
				<td class='table_mainTxt padding_5 outline' width="20%">Omzet</td>
				<td class='table_mainTxt padding_5' width="30%">&euro; <?php echo formatDecimaal($kogelfabriek->omzet); ?></td>
				<td class='table_mainTxt padding_5 outline' width="20%"><label for="withdraw">Afhalen</label></td>
				<td class='table_mainTxt padding_5' width="30%"><input class="input_form" type="text" name="withdraw" id="withdraw" value="0" /></td>
			</tr>
			<tr>
				<td class='table_mainTxt padding_5' width="20%"></td>
				<td class='table_mainTxt padding_5' width="30%"><input type="submit" class="button_form" name="changeSettings" value="Uitvoeren" /></td>
				<td class='table_mainTxt padding_5' width="20%"></td>
				<td class='table_mainTxt padding_5' width="30%"></td>
			</tr>
		</form>
	</table>
	
	<?php
		// De timestamp ophalen om te controleren of de eigenaar kan produceren
		$producerenQuery = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(datumActie) AS werken,0 FROM casino_objecten WHERE id='" . $kogelfabriek->id . "'");
		$produceren = mysqli_fetch_object($producerenQuery);
		// Als de eigenaar kogels produceert
		if(isset($_POST["produceer"])){
			$option = mysqli_real_escape_string($con,test_input($_POST["option"]));
			
			if(!is_numeric($option)){
				print_bericht("Kogelfabriek","Er ging iets mis");
				exit;
			}
			
			if($kogelfabriek->eigenaar == $spelerInfo->id){
				$price = 0;
				$bullets = 0;
				switch($option){
					case "1":
						$price = 500000;
						$bullets = 500;
					break;
					case "2":
						$price = 1350000;
						$bullets = 1500;
					break;
					case "3":
						$price = 4500000;
						$bullets = 5000;
					break;
					default:
					break;
				}
				if($produceren->werken+1800 > time()){
					$verschil1             = ($produceren->werken+1800) - time() - 86400;
					$verschil              = date("i:s", $verschil1);
					print print_bericht("Kogels produceren","Je moet nog " . $verschil . " minuten aan het wachten.");
				}else if($spelerInfo->cash < $price){
					echo print_bericht("Kogels produceren","Je moet &euro; " . formatDecimaal($price) . " hebben om " . formatDecimaal($bullets). " te produceren!");
				}else {
					mysqli_query($con,"UPDATE leden SET cash=cash-'" . $price . "' WHERE id='" . $spelerInfo->id . "'");
					mysqli_query($con,"UPDATE casino_objecten SET mininzet=mininzet+'" . $bullets . "',datumActie='" . date("Y-m-d H:i:s") . "' WHERE id='" . $kogelfabriek->id . "'");
					print_bericht("Kogelfabriek","Je hebt met succes " . formatDecimaal($bullets) . " geproduceerd voor &euro; " . formatDecimaal($price) . " en je kan over 30 minuten opnieuw produceren!");
				}
			} else {
				// Mogelijk een verdachte poging (hack of crawler)
				print_bericht("Kogelfabriek","Deze kogelfabriek is niet van jou!");
			}
		} else {?>
			<table width='550px' class="inhoud_table">
				<form method="post" action="kogelfabriek.php">
					<tr>
						<td class='table_subTitle' colspan="4">Kogelfabriek produceren</td>
					</tr>
					<?php if($produceren->werken+1800 < time()){ ?>
						<tr>
							<td class='table_mainTxt padding_5 outline' width="20%"><label for="option1"><input type="radio" name="option" id="option1" value="1" checked="true" /> 500 kogels</label></td>
							<td class='table_mainTxt padding_5' width="30%">30 minuten</td>
							<td class='table_mainTxt padding_5' width="50%" colspan="2">&euro; 500.000</td>
						</tr>
						<tr>
							<td class='table_mainTxt padding_5 outline' width="20%"><label for="option2"><input type="radio" name="option" id="option2" value="2" /> 1500 kogels</label></td>
							<td class='table_mainTxt padding_5' width="30%">30 minuten</td>
							<td class='table_mainTxt padding_5' width="50%" colspan="2">&euro; 1.350.000</td>
						</tr>
						<tr>
							<td class='table_mainTxt padding_5 outline' width="20%"><label for="option3"><input type="radio" name="option" id="option3" value="3" /> 5000 kogels</label></td>
							<td class='table_mainTxt padding_5' width="30%">30 minuten</td>
							<td class='table_mainTxt padding_5' width="50%" colspan="2">&euro; 4.500.000</td>
						</tr>
						<tr>
							<td class='table_mainTxt padding_5' width="20%"></td>
							<td class='table_mainTxt padding_5' width="30%"><input type="submit" class="button_form" name="produceer" value="Produceer kogels" /></td>
							<td class='table_mainTxt padding_5' width="20%"></td>
							<td class='table_mainTxt padding_5' width="30%"></td>
						</tr>
					<?php } else { 
						$verschil1 = ($produceren->werken+1800) - time() - 86400;
						$verschil = date("i:s", $verschil1); ?>
						<tr>
							<td class='table_mainTxt padding_5' width="1000%" colspan="4">Je moet nog <?php echo $verschil; ?> wachten voor je weer kogels kan produceren.</td>
						</tr>
					<?php } ?>
				</form>
			</table>	
		<?php } ?>
	<table width='550px' class="inhoud_table">
		<tr>
			<td class='table_subTitle' colspan="4">Kogelfabriek Verkopen</td>
		</tr>
		<tr>
			<td class='table_mainTxt padding_5' width="100%" colspan="4"><a href="kogelfabriek.php?sell">Klik hier om deze kogelfabriek te verkopen voor &euro; <?php echo formatDecimaal(round($kogelfabriek->prijs/2)); ?></a></td>
		</tr>
	</table>
<?php } ?>