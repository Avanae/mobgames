<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");

	if($spelerInfo->mini_banner == 1){
		print "<img src='images/headers/cartheft.jpg' width='550px' height='120px' alt='auto diefstal foto' />";
	}
	$gn1 = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(auto) AS werken,0 FROM leden_timers WHERE speler='" . $spelerInfo->id . "'");
	$gn  = mysqli_fetch_object($gn1);
	
	if($gn->werken > time()){
		$res = GetWaitTime(time(),$gn->werken);
		print print_bericht("Auto stelen","U bent nog " . $res . " minuten aan het wachten.");
		exit;
	}
if(isset($_POST["doJob"])){
	// Controleren of het speler veld is ingevuld
	//$playerName = mysqli_real_escape_string($con,$_POST["playerName"]);
	//if($playerName != null){
		// Als de speler een andere speler overvalt
	//} else {
		$crimeType = mysqli_real_escape_string($con,test_input($_POST["crime"]));
		
		if(!is_numeric($crimeType)){
			echo print_bericht("Auto stelen","Er ging iets mis.");
			exit;
		}
		
		if($crimeType != null){
			$autoId = 1;
			$rang = 0.5;
			$gelukt = rand(1,100);
			$kans = 0;
			$message = "Error";
			switch($crimeType){
				case "1":
					$kans = 50;
					$rang = 3;
					$autoId = rand(1,3);
				break;
				case "2":
					$kans = 30;
					$autoId = rand(3,5);
					$rang = 2;
				break;
				case "3":
					$kans = 10;
					$autoId = rand(3,6);
					$rang = 2;
				break;
				case "4":
					$kans = 5;
					$autoId = rand(4,8);
					$rang = 2;
				break;
				default:
					$kans = 30;
					$autoId = rand(1,3);
					$rang = 2;
				break;
			}
			if($gelukt <= $kans){
				$autoQuery = mysqli_query($con,"SELECT * FROM autos_types WHERE id='" . $autoId . "'");
				$autoInfo = mysqli_fetch_object($autoQuery);
				$schade = rand(0,100);
				$waarde = round($autoInfo->value-(($autoInfo->value/100)*$schade));
				$message =  "De diefstal is gelukt en je hebt een <span class=\"bold\">" . $autoInfo->name . "</span> buit gemaakt met <span class=\"bold\">" . $schade . " %</span> schade ter waarde van <span class=\"bold\">&euro; " . formatDecimaal($waarde) . "</span>!";
				
				$waitTime = date("Y-m-d H:i:s",(time()+180));
				mysqli_query($con,"UPDATE leden_timers SET auto='" . $waitTime . "' WHERE speler='" . $spelerInfo->id . "'");
				mysqli_query($con,"INSERT INTO autos_leden (typeid,schade,eigenaar,land) VALUES ('" . $autoId . "','" . $schade . "','" . $spelerInfo->id . "','" . $spelerInfo->land . "')");
				$playerSql = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $spelerInfo->id . "'");
				$player = mysqli_fetch_object($playerSql);
				if($player->rangvordering+$rang >= 100){
					// Als de speler promotie maakt
					$newRang = $player->rangvordering-100;
					if($newRang < 0){
						$newRang = 0;
					}
					
					// Rang bericht versturen
					$sendMessage = false;
					$newRank = $spelerInfo->rang+1;
					/*if($newRank > count($gameRangen)-1){ 
						$newRank = count($gameRangen)-1; 
						$newRang = '99.99';
						$sendMessage = false;
					}*/
					if($newRank > count($gameRangen)-1){ 
						$newRang = '0.0';
						$sendMessage = false;
					}
					mysqli_query($con,"UPDATE leden SET rang='" . $newRank . "',rangvordering='" . $newRang . "' WHERE id='" . $spelerInfo->id . "'");
					if($sendMessage){
						mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('0','" . $spelerInfo->id . "','Gepromoveerd','Gefeliciteerd, na die laatste misdaad heb je promotie gekregen. Je mag jezelf nu " . $gameRangen[$newRang] . " noemen!',NOW())");
					}
				} else {
					mysqli_query($con,"UPDATE leden SET rangvordering=rangvordering+'" . $rang . "' WHERE id='" . $spelerInfo->id . "'");
				}
				echo print_bericht("Auto stelen",$message);
				exit;
			} else {
				$jail = rand(1,100);
				if($jail >= 60){
					$time = date("Y-m-d H:i:s",(time()+180));
					mysqli_query($con,"UPDATE leden_timers SET auto=NOW(),gevangenis='" . $time . "' WHERE speler='" . $spelerInfo->id . "'");
					echo print_bericht("Auto stelen","Helaas, het is niet gelukt om een auto te stelen en je bent opgepakt door de politie.");
				} else {
					mysqli_query($con,"UPDATE leden_timers SET auto=NOW() WHERE speler='" . $spelerInfo->id . "'");
					echo print_bericht("Auto stelen","Helaas, het is niet gelukt om een auto te stelen.");
				}
				exit;
			}
		} else {
			echo "Geen geldige misdaad";
			exit;
		}
	//}
	
}	
?>
<form method="post" action="crime_big.php">
<table width="550px" class="inhoud_table" colspan="2">
	<tr>
		<td class="table_subTitle center" width="50%" colspan="2">Auto stelen</td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">Kans</td>
		<td class="table_mainTxt outline padding_5" width="85%" colspan="1">Type</td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="15%" colspan="1">50%</td>
		<td class="table_mainTxt padding_5" width="85%" colspan="1"><input type="radio" name="crime" value="1" id="one" checked /><label for="one"> Probeer een auto op straat te stelen.</label></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="15%" colspan="1">30%</td>
		<td class="table_mainTxt padding_5" width="85%" colspan="1"><input type="radio" name="crime" value="2" id="two" /><label for="two"> Breek in bij iemand thuis en steel de auto uit de garage.</label></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="15%" colspan="1">10%</td>
		<td class="table_mainTxt padding_5" width="85%" colspan="1"><input type="radio" name="crime" value="3" id="three" /><label for="three"> Breek in bij een autozaak en steel een auto op de kosten van de zaak.</label></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="15%" colspan="1">5%</td>
		<td class="table_mainTxt padding_5" width="85%" colspan="1"><input type="radio" name="crime" value="4" id="four" /><label for="four"> Doe een poging om in te breken bij een beroemde ster om zijn auto te stelen...</label></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="15%" colspan="1"></td>
		<td class="table_mainTxt" width="85%" colspan="1"><input type="submit" class="button_form" value="Job uitvoeren" name="doJob" /></td>
	</tr>
	<!--
	<tr>
		<td class="table_mainTxt" width="50%" colspan="1"><label for="four"> Overval speler (10% kans)</label></td>
		<td class="table_mainTxt" width="50%" colspan="1"><input type="text" maxlength="20" size="30" name="playerName" id="four" /></td>
	</tr>
	
	--->
</table>
</form>