<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />	
<?php
	include("check_login.php");
	include("check_jail.php");
	
	$heistLeaderQuery = mysqli_query($con,"SELECT * FROM crime_heist WHERE leader='" . $spelerInfo->id . "'");
	$heistDriverQuery = mysqli_query($con,"SELECT * FROM crime_heist WHERE driver='" . $spelerInfo->id . "'");
	
	$gn1 = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(heist) AS werken,0 FROM leden_timers WHERE speler='" . $spelerInfo->id . "'");
	$gn  = mysqli_fetch_object($gn1);  

	if($gn->werken > time()){
		$res = GetWaitTime(time(),$gn->werken);
		print print_bericht("Heist","Je moet nog " . $res . " minuten wachten voor je een nieuwe heist mag doen.");
		exit;
	}
	
	// Zet de heist stop
	if(isset($_POST["stop"])){
		// Check of leider de heist stopt
		if(mysqli_num_rows($heistLeaderQuery) == 1){
			$heist = mysqli_fetch_object($heistLeaderQuery);
			
			mysqli_query($con,"DELETE FROM crime_heist WHERE id='" . $heist->id . "'");
			$mes = "";
			if($heist->car != 0){
				mysqli_query($con,"DELETE FROM autos_leden WHERE id='" . $heist->car . "'");
				$mes = "De leider heeft de heist gestopt. Je bent hierdoor je auto kwijt.";
			} else {
				$mes = "De leider heeft de heist gestopt.";
			}
			mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $heist->leader . "','" . $heist->driver . "','Heist Gestopt','" . $mes . "',NOW())");
			print print_bericht("Heist","Je hebt de heist stop gezet.");
			exit;
		} else if(mysqli_num_rows($heistDriverQuery) == 1){
			$heist = mysqli_fetch_object($heistDriverQuery);
			if($heist->car != 0){
				mysqli_query($con,"DELETE FROM autos_leden WHERE id='" . $heist->car . "'");
			}
			mysqli_query($con,"DELETE FROM crime_heist WHERE id='" . $heist->id . "'");
			mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $heist->driver . "','" . $heist->leader . "','Heist Gestopt','Je driver heeft de heist gestopt. Je bent je kogels kwijt.',NOW())");
			print print_bericht("Heist","Je hebt de heist stop gezet.");
			exit;
		} else {
			print print_bericht("Heist","U hebt geen bevoegdheden om deze pagina op te vragen.");
			exit;
		}
	}
	// Als de bestuurder een auto selecteert
	if(isset($_POST["carSelect"])){
		$car = mysqli_real_escape_string($con,test_input($_POST["car"]));
		
		if(!is_numeric($car) && !empty($car)){
			print_bericht("Heist","Je moet een geldige auto selecteren.");
			exit;
		}
		if(mysqli_num_rows($heistDriverQuery) <= 0){
			print_bericht("Heist","Je bent niet uitgenodigd voor een heist.");
			exit;
		}
		$heist = mysqli_fetch_object($heistDriverQuery);
		$carQuery = mysqli_query($con,"SELECT * FROM autos_leden WHERE id='" . $car . "' AND land='" . $heist->land . "'");
		if(mysqli_num_rows($carQuery) <= 0){
			$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $heist->land . "'");
			$land = mysqli_fetch_object($landQuery);
			print_bericht("Heist","Deze auto staat in het verkeerde land. Je moet een geldige auto selecteren in " . formatCountry($land->land) . ".");
			exit;
		}
		
		$heistCar = mysqli_fetch_object($carQuery);
		if($heistCar->schade > 95){
			print_bericht("Heist","De auto mag niet meer dan 95% schade hebben.");
			exit;
		}
		
		mysqli_query($con,"UPDATE crime_heist SET car='" . $heistCar->id . "' WHERE id='" . $heist->id . "'");
		$bericht = $spelerInfo->login . " heeft de heist geaccepteerd en een auto geselecteerd. Je kan de heist nu uitvoeren.";
		mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $spelerInfo->id . "','" . $heist->leader . "','Heist Geaccepteerd','" . $bericht . "',NOW())");
		print_bericht("Heist","Je hebt met succes een auto geselecteerd. Je moet nu wachten te de leider de heist uitvoerd.");
		exit;
	}
	// Als de leider de heist uitvoert
	if(isset($_POST["start"])){
		$heist = mysqli_fetch_object($heistLeaderQuery);
		
		$carQuery = mysqli_query($con,"SELECT * FROM autos_leden WHERE id='" . $heist->car . "'");
		if(mysqli_num_rows($carQuery) <= 0){
			print_bericht("Heist","De auto waarmee je van plan was om de heist uit te voeren bestaat niet meer.");
			exit;
		}
		$car = mysqli_fetch_object($carQuery);
		
		$carTypeQuery = mysqli_query($con,"SELECT * FROM autos_types WHERE id='" . $car->typeid . "'");
		$carType = mysqli_fetch_object($carTypeQuery);
		
		$rangGain = 0;
		$bericht = "";
		$kans = rand(1,100);
		if($kans >= 40){
			$price = rand(1,10);
			switch($price){
				case 1:
				case 2:
				case 3:
					$profit = rand(250000,500000);
					$profit = $profit*$carType->id;
					
					$tmp = $profit-(($profit/100)*$car->schade);
					
					$rangGain = 1.5;
					$bericht = "De heist is gelukt en jullie hebben &euro; " . formatDecimaal($tmp) . " winst gemaakt.";
					$splitProfit = floor($profit/2);
					
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $splitProfit . "' WHERE id='" . $heist->leader . "'");
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $splitProfit . "' WHERE id='" . $heist->driver . "'");
				break;
				case 4:
					$profit = rand(100000,250000);
					$profit = $profit*$carType->id;
					
					$tmp = $profit-(($profit/100)*$car->schade);
					
					$kogels = rand(750,1250);
					$rangGain = 2;
					
					$bericht = "De heist is gelukt en jullie hebben &euro; " . formatDecimaal($tmp) . " winst gemaakt en vonden ook een krat met " . formatDecimaal($kogels) . " kogels. De buit werd verdeeld onder jou en je driver.";
					$splitProfit = floor($tmp/2);
					$splitKogels = floor($kogels/2);
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $splitProfit . "',kogels=kogels+'" . $splitKogels . "' WHERE id='" . $heist->leader . "'");
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $splitProfit . "',kogels=kogels+'" . $splitKogels . "' WHERE id='" . $heist->driver . "'");
				break;
				
				case 5:
				case 6:
					$kogels = rand(1500,2250);
					$rangGain = 1;
					$bericht = "Jullie slopen een wapenwinkel binnen. In alle stilte pakten jullie zoveel dat je kon dragen en jullie geraakte weg met " . formatDecimaal($kogels) . " kogels! De buit werd verdeeld onder jou en je driver.";
					
					$splitKogels = floor($kogels/2);
					mysqli_query($con,"UPDATE leden SET kogels=kogels+'" . $splitKogels . "' WHERE id='" . $heist->leader . "'");
					mysqli_query($con,"UPDATE leden SET kogels=kogels+'" . $splitKogels . "' WHERE id='" . $heist->driver . "'");
				break;
				
				case 7:
				case 8:
					$drugs = rand(1500,2250);
					$rangGain = 1;
					$bericht = formatCountry("Jullie stoppen aan een container in de haven. Getipt door een junkie knippen jullie het slot van de container door. Bingo, de drugs zaten verstopt in blikken met tomaten! Jullie maakte " . formatDecimaal($drugs) . " kilo cocaïne buit! De buit werd verdeeld onder jou en je driver.");
					
					$splitDrugs = floor($drugs/2);
					mysqli_query($con,"UPDATE leden SET coke=coke+'" . $drugs . "' WHERE id='" . $heist->leader . "'");
					mysqli_query($con,"UPDATE leden SET coke=coke+'" . $drugs . "' WHERE id='" . $heist->driver . "'");
				break;
				
				case 9:
					$bericht = "Jullie vallen de bank binnen en roepen: Handen in de lucht, dit is... Tot jullie verbazing werden jullie opgewacht door een leger aan politie. Je had die junkie nooit mogen vertrouwen... Jullie werden opgepakt!";
					$jailTime = date("Y-m-d H:i:s",(time()+300));
					
					mysqli_query($con,"UPDATE leden_timers SET gevangenis='" . $jailTime . "' WHERE speler='" . $heist->leader . "'");
					mysqli_query($con,"UPDATE leden_timers SET gevangenis='" . $jailTime . "' WHERE speler='" . $heist->driver . "'");
				break;
				
				case 10:
					$profit = rand(750000,1500000);
					$kogels = rand(750,1500);
					$rangGain = 5;
					$bericht = "Jullie liepen rond in de stad tot je plots een geldtransport zag staan. Jullie keken elkaar aan en net op dat moment zag je een bewaker de achterdeur openen. Jullie twijfelde geen seconde en liepen met getrokken wapens op de bewaker af. Jij sloeg de bewaker met je geweer op het hoofd en hij viel bewusteloos neer. Jullie pakten wat je kon dragen en verlieten razendsnel de plaats delict. Jullie kwamen weg met &euro; " . formatDecimaal($profit) . ". Ook stalen jullie de ammunitie van de bewaker en maakten " . formatDecimaal($kogels) . " kogels buit! De buit werd verdeeld onder jou en je driver.";
					$splitProfit = floor($profit/2);
					$splitKogels = floor($kogels/2);
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $splitProfit . "',kogels=kogels+'" . $splitKogels . "' WHERE id='" . $heist->leader . "'");
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $splitProfit . "',kogels=kogels+'" . $splitKogels . "' WHERE id='" . $heist->driver . "'");
				break;
				
				default:
				break;
			}
		} else {
			$jailChance = rand(1,100);
			if($jailChance >= 75){
				$bericht = "Jullie plannen lekte uit! De politie stond aan jullie deur en na een huiszoeking was er genoeg bewijs om jullie op te sluiten!";
				$jailTime = date("Y-m-d H:i:s",(time()+300));
					
				mysqli_query($con,"UPDATE leden_timers SET gevangenis='" . $jailTime . "' WHERE speler='" . $heist->leader . "'");
				mysqli_query($con,"UPDATE leden_timers SET gevangenis='" . $jailTime . "' WHERE speler='" . $heist->driver . "'");
			
			} else {
				$bericht = "de heist is mislukt maar jullie geraakte op tijd weg van de politie.";
			}
		}
		$waitTime = date("Y-m-d H:i:s",(time()+3600));
		mysqli_query($con,"UPDATE leden_timers SET heist='" . $waitTime . "' WHERE speler='" . $heist->leader . "'");
		mysqli_query($con,"UPDATE leden_timers SET heist='" . $waitTime . "' WHERE speler='" . $heist->driver . "'");
		
		mysqli_query($con,"DELETE FROM autos_leden WHERE id='" . $heist->car . "'");
		mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $heist->leader . "','" . $heist->driver . "','Heist uitgevoerd','" . $bericht . "',NOW())");
		
		//--------------------
		// RANG GEVEN AAN BEIDE SPELERS EN TIMERS AANPASSEN
		if($rangGain != 0){
			$leaderQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $heist->leader . "'");
			$leader = mysqli_fetch_object($leaderQuery);
			
			// Geef leider rangvordering
			if($leader->rangvordering+$rangGain >= 100){
				// Als de speler promotie maakt
				$newRang = $leader->rangvordering-100;
				if($newRang < 0){
					$newRang = 0;
				}
					
				// Rang bericht versturen
				$sendMessage = false;
				$newRank = $leader->rang+1;
					
				if($newRank > count($gameRangen)-1){ 
					$newRang = '0.0';
					$sendMessage = false;
				}
				mysqli_query($con,"UPDATE leden SET rang='" . $newRank . "',rangvordering='" . $newRang . "' WHERE id='" . $leader->id . "'");
				if($sendMessage){
					mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('0','" . $leader->id . "','Gepromoveerd','Gefeliciteerd, na die laatste misdaad heb je promotie gekregen. Je mag jezelf nu " . $gameRangen[$newRang] . " noemen!',NOW())");
				}
			} else {
				mysqli_query($con,"UPDATE leden SET rangvordering=rangvordering+'" . $rangGain . "' WHERE id='" . $leader->id . "'");
			}
			
			$driverQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $heist->driver . "'");
			$driver = mysqli_fetch_object($driverQuery);
			
			// Geef driver rangvordering
			if($driver->rangvordering+$rangGain >= 100){
				// Als de speler promotie maakt
				$newRang = $driver->rangvordering-100;
				if($newRang < 0){
					$newRang = 0;
				}
					
				// Rang bericht versturen
				$sendMessage = false;
				$newRank = $driver->rang+1;
					
				if($newRank > count($gameRangen)-1){ 
					$newRang = '0.0';
					$sendMessage = false;
				}
				mysqli_query($con,"UPDATE leden SET rang='" . $newRank . "',rangvordering='" . $newRang . "' WHERE id='" . $driver->id . "'");
				if($sendMessage){
					mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('0','" . $driver->id . "','Gepromoveerd','Gefeliciteerd, na die laatste misdaad heb je promotie gekregen. Je mag jezelf nu " . $gameRangen[$newRang] . " noemen!',NOW())");
				}
			} else {
				mysqli_query($con,"UPDATE leden SET rangvordering=rangvordering+'" . $rangGain . "' WHERE id='" . $driver->id . "'");
			}
		}		
		
		mysqli_query($con,"DELETE FROM crime_heist WHERE id='" . $heist->id . "'");
		print_bericht("Heist",$bericht);
		exit;
	}
	// Als een speler een nieuwe heist start
	if(isset($_POST["go"])){
		if($spelerInfo->kogels < 500){
			print_bericht("Heist","Om een heist te starten heb je 500 kogels nodig!");
			exit;
		}
		
		$driver = mysqli_real_escape_string($con,test_input($_POST["driver"]));
		
		if(!checkUserName($driver)){
			print_bericht("Heist","Je moet een geldige gebruikersnaam opgeven.");
			exit;
		}
		
		$userQuery = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $driver . "' AND status='levend' AND ban='0'");
		if(mysqli_num_rows($userQuery) <= 0){
			print_bericht("Heist","Je driver moet een levende speler zijn en mag niet op de schandpaal staan.");
			exit;
		}
		$user = mysqli_fetch_object($userQuery);
		
		$driverWaitQuery = mysqli_query($con,"SELECT * FROM leden_timers WHERE speler='" . $user->id . "'");
		$driverWait = mysqli_fetch_object($driverWaitQuery);
		if(strtotime($driverWait->heist) > time()){
			print_bericht("Heist","Je driver kan nog geen nieuwe heist uitvoeren op dit ogenblik.");
			exit;
		}
		
		$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $spelerInfo->land . "'");
		$land = mysqli_fetch_object($landQuery);
		$bericht = "Je bent door <a href=\"speler_profiel.php?x=" . $spelerInfo->id . "\">" . $spelerInfo->login . "</a> uitgenodigd om een heist uit te voeren in " . formatCountry($land->land) . ". Ga naar de heist pagina om een auto te accepteren of om de heist stop te zetten.";
		
		
		
		mysqli_query($con,"UPDATE leden SET kogels=kogels-'500' WHERE id='" . $spelerInfo->id . "'");
		mysqli_query($con,"INSERT INTO crime_heist (leader,driver,land) VALUES ('" . $spelerInfo->id . "','" . $user->id . "','" . $spelerInfo->land . "')");
		mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $spelerInfo->id . "','" . $user->id . "','Heist Driver','" . $bericht . "',NOW())");
		print_bericht("Heist","Je hebt " . $user->login . " uitgenodigd voor een heist. Als deze speler een auto heeft geselecteerd kan je de heist uitvoeren.");
		exit;
	}
?>

<table width="550px" class="inhoud_table" colspan="1">
	<tr>
		<td class='table_subTitle' colspan='1'>Heist</td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="100%" colspan="1">
			<h2 class="padding_5">Pleeg een heist</h2>
			<p class="padding_5">Wil je wat meer verdienen? Pleeg dan een heist samen met een medespeler. Je kan elk uur een nieuwe heist plegen.</p>
			<p class="padding_5">Een van jullie moet de heist organiseren en heeft daarvoor 500 kogels nodig. Terwijl je partner in crime een voertuig zal besturen.</p>
		</td>
	</tr>
	<?php if(mysqli_num_rows($heistLeaderQuery) == 1){ 
		$heist = mysqli_fetch_object($heistLeaderQuery); ?>
		<tr>
			<td class="table_mainTxt outline padding_5" width="100%" colspan="1">	
				<h2 class="padding_5">Heist is bezig...</h2>
				<?php if($heist->car == 0){ ?>
					<p class="padding_5">Je bent aan het wachten op jou bestuurder. Je kan de heist stoppen maar dan ben je je kogels kwijt.</p>
					<p class="padding_5">
						<form method="post" action="crime_heist.php">
							<input type="submit" name="stop" value="Heist Stoppen" class="button_form padding_5" />
						</form>
					</p>
				<?php } else { ?>
					<p class="padding_5">Je bestuurder is er klaar voor!</p>
					<p class="padding_5">
						<form method="post" action="crime_heist.php">
							<input type="submit" name="start" value="Uitvoeren" class="button_form padding_5" />
						</form>
					</p>
				<?php } ?>
			</td>
		</tr>
	<?php } else if(mysqli_num_rows($heistDriverQuery) == 1){
		$heist = mysqli_fetch_object($heistDriverQuery); ?>
		<tr>
			<td class="table_mainTxt outline padding_5" width="100%" colspan="1">
				<h2 class="padding_5">Selecteer een voertuig</h2>
				<?php if($heist->car == 0){ 
					$carQuery = mysqli_query($con,"SELECT * FROM autos_leden WHERE eigenaar='" . $spelerInfo->id . "' AND land='" . $spelerInfo->land . "'");
					if(mysqli_num_rows($carQuery) <= 0){ ?>
						<p class="padding_5">Je hebt momenteel nog geen autos in je garage!</p>
					<?php } else {	?>
						<p class="padding_5">
							<form method="post" action="crime_heist.php">
								<select class="select_field padding_5" width="200px" name="car">
									<?php $i = 0; while($auto = mysqli_fetch_object($carQuery)){ 
										$carTypeQuery = mysqli_query($con,"SELECT * FROM autos_types WHERE id='" . $auto->typeid . "'");
										$type = mysqli_fetch_object($carTypeQuery);
									?>
										<option value="<?php echo $auto->id; ?>" <?php echo $i == 0 ? "checked" : ""; ?>>#<?php echo $auto->id; ?>: <?php echo $type->name; ?></option>
									<?php $i++; } ?>
								</select>
								<input type="submit" name="carSelect" value="Selecteer" class="button_form padding_5" />
							</form>
						</p>
					<?php } ?>
				<?php } else { ?>
						<p class="padding_5">Je bent er klaar voor, de leider moet alleen de heist nog uitvoeren!</p>
						<form method="post" action="crime_heist.php">
							<input type="submit" name="stop" value="Heist Stoppen" class="button_form padding_5" />
						</form>
				<?php } ?>
	<?php } else {?>
		<tr>
			<td class="table_mainTxt outline padding_5" width="100%" colspan="1">	
				<h2 class="padding_5"><label for="driver">Nodig een speler uit</label></h2>
				<p class="padding_5">
					<form method="post" action="crime_heist.php">
						<input type="text" maxlength="20" name="driver" class="input_form padding_5" id="driver" /> 
						<input type="submit" name="go" value="Uitnodigen" class="button_form padding_5" />
					</form>
				</p>
			</td>
		</tr>
	<?php } ?>
</table>