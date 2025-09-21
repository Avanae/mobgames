<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");

	if($spelerInfo->mini_banner == 1){
		print "<img src='images/headers/werken.jpg' width='550px' height='120px' alt='wietplantage foto' />";
	}
	$gn1 = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(misdaad) AS werken,0 FROM leden_timers WHERE id='" . $spelerInfo->id . "'");
	$gn = mysqli_fetch_object($gn1);  

	if($gn->werken > time()){
		$res = GetWaitTime(time(),$gn->werken);
		print print_bericht("Misdaad","U bent nog " . $res . " minuten aan het wachten.");
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
			echo print_bericht("Misdaad","Er ging iets mis.");
			exit;
		}
		
		if($crimeType != null){
			$opbrengstMisdaad = rand(250,500);
			$multiplier = 0;
			$rang = 0.5;
			$gelukt = rand(1,100);
			$kans = 0;
			$message = "Error";
			switch($crimeType){
				case "1":
					$kans = 90;
					$multiplier = 1;
				break;
				case "2":
					$kans = 60;
					$multiplier = 10;
					$rang = 1;
				break;
				case "3":
					$kans = 30;
					$multiplier = 50;
					$rang = 2;
				break;
				default:
					$kans = 30;
					$multiplier = 50;
					$rang = 2;
				break;
			}
			$opbrengstMisdaad = $opbrengstMisdaad*$multiplier;
			$bedrag = formatDecimaal($opbrengstMisdaad);
			if($gelukt <= $kans){
				// Controleren of speler in een familie zit
				if($spelerInfo->familie != 0){
					// Familie van de speler laden
					$familieQuery = mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerInfo->familie . "'");
					
					if(mysqli_num_rows($familieQuery) != 0)
					{
						$familie = mysqli_fetch_object($familieQuery);
						// Familie belastingen ophalen
						$familieBelastingQuery = mysqli_query($con,"SELECT * FROM families_belasting WHERE familieid='" . $familie->id . "'");
						$familieBelasting = mysqli_fetch_object($familieBelastingQuery);
						if($familieBelasting->misdaadTax != 0){
							$familieOpbrengst = ($opbrengstMisdaad/100)*$familieBelasting->misdaadTax;
							$nieuweOpbrengst = $opbrengstMisdaad-$familieOpbrengst;
							$nieuwbedrag = formatDecimaal($nieuweOpbrengst);
							
							$message =  "De overval is gelukt en je hebt &euro; " . $bedrag . " buit gemaakt, na de " . $familieBelasting->misdaadTax . " % belasting van je familie hou je nog &euro; " . $nieuwbedrag . " over, de familie bedankt je.";
							$opbrengstMisdaad = $nieuweOpbrengst;
							mysqli_query($con,"UPDATE  families SET geld=geld+'" . $familieOpbrengst . "' WHERE id='" . $familie->id . "'");
						} else {
							$message =  "De overval is gelukt en je hebt &euro; " . $bedrag . " buit gemaakt. De familie heeft de belasting op 0 % staan om hun loyale leden te bedanken.";
						}
					} else {
						$message =  "Could not load the familie!";
					}
				} else {
					$message =  "De overval is gelukt en je hebt &euro; " . $bedrag . " buit gemaakt.";
				}
				mysqli_query($con,"UPDATE leden SET cash=cash+'" . $opbrengstMisdaad . "' WHERE id='" . $spelerInfo->id . "'");
				$waitTime = date("Y-m-d H:i:s",(time()+120));
				mysqli_query($con,"UPDATE leden_timers SET misdaad='" . $waitTime ."' WHERE speler='" . $spelerInfo->id . "'");
				if($spelerInfo->rangvordering+$rang >= 100){
					
					// Als de speler promotie maakt
					$newRang = $spelerInfo->rangvordering-100;
					if($newRang < 0){
						$newRang = 0;
					}
					// Rang bericht versturen
					$newRank = $spelerInfo->rang+1;
					$sendMessage = false;
					
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
				echo print_bericht("Misdaad",$message);
				exit;
			} else {
				$jail = rand(1,100);
				if($jail >= 60){
					$time = date("Y-m-d H:i:s",(time()+60));
					mysqli_query($con,"UPDATE leden_timers SET misdaad=NOW(),gevangenis='" . $time . "' WHERE speler='" . $spelerInfo->id . "'");
					echo print_bericht("Misdaad","Helaas, de overval is mislukt en je bent opgepakt door de politie!");
				} else {
					mysqli_query($con,"UPDATE leden_timers SET misdaad=NOW() WHERE speler='" . $spelerInfo->id . "'");
					echo print_bericht("Misdaad","Helaas, de overval is mislukt!");
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
<form method="post" action="crime_small.php">
<table width="550px" class="inhoud_table" colspan="2">
	<tr>
		<td class="table_subTitle center" width="50%" colspan="2">Misdaad uitvoeren</td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="20%" colspan="1">90% kans</td>
		<td class="table_mainTxt padding_5" width="80%" colspan="1"><input type="radio" name="crime" value="1" id="one" checked /><label for="one"> Overval iemand random op de straat en hoop op het beste.</label></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="20%" colspan="1">60% kans</td>
		<td class="table_mainTxt padding_5" width="80%" colspan="1"><input type="radio" name="crime" value="2" id="two" /><label for="two"> Doe een overval op een winkel</label></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="20%" colspan="1">30% kans</td>
		<td class="table_mainTxt padding_5" width="80%" colspan="1"><input type="radio" name="crime" value="3" id="three" /><label for="three"> Pleeg een bankoverval</label></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5" width="20%" colspan="1"></td>
		<td class="table_mainTxt" width="80%" colspan="1"><input type="submit" class="button_form" value="Job uitvoeren" name="doJob" /></td>
	</tr>
	<!--
	<tr>
		<td class="table_mainTxt" width="50%" colspan="1"><label for="four"> Overval speler (10% kans)</label></td>
		<td class="table_mainTxt" width="50%" colspan="1"><input type="text" maxlength="20" size="30" name="playerName" id="four" /></td>
	</tr>
	
	--->
</table>
</form>