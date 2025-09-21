<?php
include("config.php");
include("include/functions.php");
//---------------------------------------------------------------------
// Nog te doen voor moord werkt 100%:
// 1) Wanneer je moordpoging mislukt bericht naar slachtoffer sturen
// 2) getuige verklaring moet nog naar apparte tabel in database
// 3) Backfire maken
// 4) Testament en hitlist maken
// 5) objecten en bezittingen resetten
// 6) Uitbreiden met detective
// 7) De casino objecten laten vallen van het slachtoffer
// 8) De kavels van het slachtoffer te koop zetten
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");
?>

<table width="550px" class="inhoud_table">
<?php if($spelerInfo->mini_banner == 1){ ?>
<tr>
	<td colspan="4" class="table_mainTxt"><img src="images/headers/moord.jpg" width="550px" height="120px" alt="moord pic" /></td>
</tr>

<?php
}
if(isset($_POST['kill'])){
	$naamSlachtoffer = trim(mysqli_real_escape_string($con,test_input($_POST['slachtoffer'])));
	$aantalKogels = trim(mysqli_real_escape_string($con,test_input($_POST['kogels'])));
	$bericht = trim(mysqli_real_escape_string($con,test_input($_POST['bericht'])));
	if($bericht == ""){$bericht = "Geen";}
	$bivakMuts = trim(mysqli_real_escape_string($con,test_input($_POST['bivak'])));
	if(!is_numeric($bivakMuts)){exit;}
	
	if(preg_match('/^[a-zA-Z0-9_\-]+$/',$bericht) == 0 || preg_match('/^[a-zA-Z0-9_\-]+$/',$naamSlachtoffer) == 0){
		echo print_bericht("Speler Vermoorden","Je gaf ergens ongeldige invoer in gelieve opnieuw te proberen!");
		exit;
	}
	
	// slachtoffer info laden
	$kkk = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $naamSlachtoffer . "' AND status='levend'");
	if(mysqli_num_rows($kkk) ==0)
	{
		echo print_bericht("Speler Vermoorden","Deze speler bestaat niet of is al vermoord!");
		exit;
	}
	$slachtofferInfo = mysqli_fetch_object($kkk);
	if(!$slachtofferInfo){
		echo print_bericht("Speler Vermoorden","Deze speler bestaat niet!");
		exit;
	}
	if(!is_numeric($aantalKogels) || $aantalKogels == 0){
		echo print_bericht("Speler Vermoorden","Dit is geen geldig aantal kogels!");
		exit;
	}
	if(!is_numeric($bivakMuts)){
		echo print_bericht("Speler Vermoorden","Maak een keuze om een bivakmuts te dragen!");
		exit;
	}
	if($slachtofferInfo->ban == 1){
		echo print_bericht("Speler Vermoorden","Deze speler staat op de schandpaal!");
		exit;
	}
	if($spelerInfo->kogels < $aantalKogels){
		echo print_bericht("Speler Vermoorden","Zoveel kogels heb je niet!");
		exit;
	}
	if($slachtofferInfo->level >= 255){
		echo print_bericht("Speler Vermoorden","Jij mag niet op admins schieten!");
		exit;
	}
	if($slachtofferInfo->familie == $spelerInfo->familie && $slachtofferInfo->familie != 0){
		echo print_bericht("Speler Vermoorden","WAT IS DIT??? probeer je je eigen familie neer te halen? Dit kunnen we niet toestaan...");
		exit;
	}
	// wapen en verdediginskrachten
	$spelerWapenKracht = ($spelerInfo->wapen*2);
	$slachtofferVerdedigingKracht = ($slachtofferInfo->verdediging*5);
	
	// eerst rangverschil uitrekenen en levenspunten instellen
	$verschilrang = $slachtofferInfo->rang-$spelerInfo->rang;
	switch($verschilrang){
		case -3:
		$healthpoints = 2500;
		break;
		case -2:
		$healthpoints = 5000;
		break;
		case -1:
		$healthpoints = 7500;
		break;
		case 0:
		$healthpoints = 10000;
		break;
		case 1:
		$healthpoints = 12500;
		break;
		case 2:
		$healthpoints = 15000;
		break;case 3:
		$healthpoints = 20000;
		break;
		default:
		$healthpoints = 10000;
		break;
	}
	//aantal kogels nodig aanvaller
	/*$aanvalKracht = ($healthpoints/$spelerWapenkracht)*$spelerInfo->killskill;
	$vicdef = (($healthpoints*$slachtofferInfo->verdediging)/$spelerWapenKracht)*$slachtofferInfo->rang;*/
	$aanvallerPoints = round(((($healthpoints*(($slachtofferInfo->bodyguards+$slachtofferInfo->rang)*(1+$slachtofferVerdedigingKracht))))/(1+$spelerWapenKracht)));
	$aanvalPunt = round(($aanvallerPoints*($spelerInfo->rang))/$slachtofferInfo->rang+1);
	
	// kogels nodig berekent met kill skill
	if($spelerInfo->killskill == 100){
		$killskill = 1;
	} else if($spelerInfo->killskill == 0){
		$killskill = 99;
	} else {
		$killskill = $spelerInfo->killskill;
	}
	$aanvalMinKillskill = round($aanvallerPoints-(($aanvalPunt/100)*($killskill)));
	$aanvalGefaaldArray = array("EH??? Wie is daar? Klinkt de stem van " . $slachtofferInfo->login . " in een donkere steeg... BANG BANG BANG  er vielen schoten waarbij " . $slachtofferInfo->login . " nog net op tijd kon wegspringen. Je had een minder mistige dag moeten uitkiezen! De moord poging is mislukt.",
	"Je stapt een restaurant binnen en spot " . $slachtofferInfo->login . " in de hoek met zijn vrouw en kinderen. Je trekt je geweer en begeeft je naar het slachtoffer. Je merkte helaas niet op dat " . $slachtofferInfo->login . " zijn bodyguards bijhad en die besloten je buiten te gooien en je eens een lesje te leren! Moord poging mislukt...",
	"Je besloot om met je auto langs de stamkroeg van " . $slachtofferInfo->login . " te rijden en er een driveby op te plegen. Je komt voorbij de ramen van de kroeg en opent door je autoraam het vuren op het cafe. Achteraf vernam je dat het slachtoffer zich niet in het cafe bevond. Moord poging mislukt!",
	"Na een lange dag zoeken naar " . $slachtofferInfo->login . " heb je hem gevonden in het park. Maar het slachtoffer heeft je gespot waardoor de simpele moord op een vuurgevecht uitdraait. In de verte komt de politie, KlIK KLIK KLIK klinkt het plots. Je zit zonder kogels en moest daarom vluchten van " . $slachtofferInfo->login . ". Je moordpoging is mislukt.",
	"Je hebt op " . $slachtofferInfo->login . " geschoten met $aantalKogels kogels. Echter heeft " . $slachtofferInfo->login . " het overleeft!");
	$aanvalGeluktArray = array("Je liet er geen gras over groeien en schoot " . $slachtofferInfo->login . " aan flarden. Zelfs de politie vond het moeilijk om " . $slachtofferInfo->login . " te herkennen nadat jij ermee klaar was. " . $slachtofferInfo->login . " is nu dood!",
	"Je staat aan de favoriete stamkroeg van " . $slachtofferInfo->login . " te wachten. Geduld wordt beloont blijkt wanneer " . $slachtofferInfo->login . " buitenstapt. Je loopt naar hem doe en gaf hem de volle laag... Er schiet niets meer van " . $slachtofferInfo->login . " over. Je moord poging is gelukt.",
	"Je krijgt telefoon met de boodschap dat " . $slachtofferInfo->login . " zich in een van je vrienden zijn huis bevindt. Je vertrekt in je auto en gaat naar die vriend met getrokken wapen. Je valt binnen en schiet al je kogels als een gek in het rond. En effectief blijkt want " . $slachtofferInfo->login . " werdt 2 keer in de borst geraakt en 3 keer in het hoofd. Geen sprake van dat hij nog leeft en je moord poging is gelukt.",
	"BANG BANG BANG klonk het in de straten van België. Tot op een gegeven moment je zonder kogels valt en " . $slachtofferInfo->login . " ook. Hierop volgt een vuistgevecht waar jij als totale overwinnaar uit de ring stapt. Je hebt zo hard geslagen dat " . $slachtofferInfo->login . " dood ging aan de verwondingen. Moord poging geslaagt.",
	"Tijdens een van je rondes door je buurt met een jointje in je hand ontdek je dat " . $slachtofferInfo->login . " zich op een plein bevindt. Je stapt uit met je wapen en opent het vuren op " . $slachtofferInfo->login . " tot hij niet meer beweegt. Koelbloedig trek je van je joint en zeg je: dit zag je niet aankomen eh lozer. Moord poging gelukt.");
	
	// leven kwijt na moordpoging
	$aantalProcentHealth = round((($aantalKogels/$aanvalMinKillskill)*100));
	$aantalProcentHealth2 = ($aantalProcentHealth*-2)/2;
	$berichtPoging = rand(0,4);
	if($slachtofferInfo->leven > $aantalProcentHealth && $slachtofferInfo->leven >= 1){
		// als je op het slachtoffer schiet en hij overleeft de moordpoging
		mysqli_query($con,"UPDATE leden SET leven=leven-'" . $aantalProcentHealth2 . "' WHERE id='" . $slachtofferInfo->id . "'");
		mysqli_query($con,"UPDATE leden SET kogels=kogels-'" . $aantalKogels . "' WHERE id='" . $spelerInfo->id . "'");
		mysqli_query($con,"INSERT INTO moordpogingen (schutter,slachtoffer,datum,status,bericht,woordenkiller,bivak) VALUES ('" . $spelerInfo->id . "','" . $slachtofferInfo->id . "',NOW(),1,'" . $aanvalGefaaldArray[$berichtPoging] . "','" . $bericht . "','" . $bivakMuts . "')");
		mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('0','" . $slachtofferInfo->id . "','Moordpoging','" . $spelerInfo->login . " heeft een poging gedaan om je te vermoorden. Algoed is het mislukt maar je bent " . $aantalProcentHealth2 . " % leven kwijt!',NOW())");
		mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('0','" . $spelerInfo->id . "','Moordpoging','Je hebt een poging gedaan om " . $slachtofferInfo->login . " te vermoorden. De moordpoging is mislukt maar " . $slachtofferInfo->login . " is " . $aantalProcentHealth2 . " % leven kwijt!',NOW())");
		
		print "<table width='550px'><tr><td class='table_subTitle'>MOORDPOGING MISLUKT</td></tr><tr>
		<td class='table_mainTxt'>" . $aanvalGefaaldArray[$berichtPoging] . "</td></tr><tr><td class='table_mainTxt'><a href='moorden.php' target='main'>Klik hier om terug te gaan</a></td></tr></table>";
	} else {
		// als de moord poging gelukt is WS zenden
		$spelerInfo60 = mysqli_query($con,"SELECT * FROM leden WHERE status='levend' AND ban='0'");
		$aantalLevendeLeden = mysqli_num_rows($spelerInfo60);
		$randomSpeler = rand(1,$aantalLevendeLeden);
		$i = 1;
		while($spelerList = mysqli_fetch_object($spelerInfo60)){
			if($i == $randomSpeler){
				mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('0','" . $spelerList->id . "','Getuige verklaring','Je hebt gezien hoe " . $spelerInfo->login . ", " . $slachtofferInfo->login . " om het leven bracht.!',NOW())");
			}
			$i++;
		}
		
		// familie herinstellen na moordpoging
		$onder = mysqli_query($con,"SELECT * FROM leden WHERE familie='" . $slachtofferInfo->familie . "' AND familierang='2' AND status='levend' AND ban='0'");
		$memb = mysqli_query($con,"SELECT * FROM leden WHERE familie='" . $slachtofferInfo->familie . "' AND familierang >='1' AND status='levend' AND ban='0'");
		$opvolger = mysqli_fetch_object($onder);
		if($slachtofferInfo->familierang == 3){
			$famQuery = mysqli_query($con,"SELECT * FROM families WHERE id='" . $slachtofferInfo->familie . "'");
			$fam = mysqli_fetch_object($famQuery);
			if(!$opvolger){
				while($famMembers = mysqli_fetch_object($memb)){
					mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('0','" . $famMembers->id . "','Baas vermoord','" . $slachtofferInfo->login . " werdt vermoord en aangezien hij geen opvolger had zit je nu zonder familie!',NOW())");
				}
				mysqli_query($con,"INSERT INTO nieuws (onderwerp,datum,bericht) VALUES('Don vermoord',NOW(),'Tijdens een vuurgevecht werdt " . $slachtofferInfo->login . " neergeschoten. Hij is overleden aan de verwondingen. " . $slachtofferInfo->familie . " had geen onderbaas en door slechte organisatie is deze familie geschiedenis.')");
				mysqli_query($con,"DELETE FROM families WHERE id='" . $slachtofferInfo->familie . "'");
				mysqli_query($con,"DELETE FROM donatie_logs WHERE ontvanger='" . $slachtofferInfo->familie . "', verzender='" . $slachtofferInfo->id . "' AND type='familie'");
				mysqli_query($con,"UPDATE leden SET familierang='0', familie='0' WHERE id='" . $slachtofferInfo->id . "'");
			} else {
				mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('0','" . $opvolger->id . "','Baas vermoord','" . $slachtofferInfo->login . " werdt vermoord en jij hebt nu " . $fam->naam . " overgenomen.',NOW())");
				mysqli_query($con,"INSERT INTO nieuws (onderwerp,datum,bericht) VALUES('Don vermoord',NOW(),'Tijdens een vuurgevecht werdt <a href='speler_profiel.php?x=" . $slachtofferInfo->id . "'>" . $slachtofferInfo->login . "</a> neergeschoten. Hij is overleden aan de verwondingen en de familie werdt overgedragen aan <a href='speler_profiel.php?x=" . $opvolger->id . "'>" . $opvolger->login . "')");
				mysqli_query($con,"UPDATE leden SET familierang='3' WHERE id='" . $opvolger->id . "'");
				mysqli_query($con,"UPDATE leden SET familierang='0', familie='0' WHERE id='" . $slachtofferInfo->id . "'");
				mysqli_query($con,"UPDATE families SET baas='" . $opvolger->id . "' WHERE id='" . $fam->id . "'");
			}
		}
		// Kavels van slachtoffer ophalen en aanpassen
		$slachtofferKavelQuery = mysqli_query($con,"SELECT * FROM kavels WHERE eigenaar='" . $slachtofferInfo->id . "'");
		if(!$slachtofferKavelQuery){
			while($kavel = mysqli_fetch_object($slachtofferKavelQuery)){
				mysqli_query($con,"UPDATE kavels SET eigenaar='0' WHERE eigenaar='" . $slachtofferInfo->id . "'");
			}
		}
		
		mysqli_query($con,"UPDATE leden SET leven='0', status='dood', datumdood=NOW() WHERE id='" . $slachtofferInfo->id . "'");
		mysqli_query($con,"UPDATE leden SET kogels=kogels-'" . $aantalKogels . "', cash=cash+'" . $slachtofferInfo->cash . "', kills=kills+'1' WHERE id='" . $spelerInfo->id . "'");
		mysqli_query($con,"INSERT INTO moordpogingen (schutter,slachtoffer,datum,status,bericht,woordenkiller,bivak) VALUES ('" . $spelerInfo->id . "','" . $slachtofferInfo->id . "',NOW(),2,'" . $aanvalGeluktArray[$berichtPoging] . "','" . $bericht . "','" . $bivakMuts . "')");
		$cash = number_format($slachtofferInfo->cash,0,",",".");
		mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('0','" . $spelerInfo->id . "','Moord geslaagd','Mooi gedaan " . $spelerInfo->login . ", je hebt zijn kavel en het cash geld van je slachtoffer gekregen, dit was € " . $cash . "!',NOW())");
		print "<table width='550px'><tr><td class='table_subTitle'>MOORDPOGING GELUKT</td></tr><tr>
		<td class='table_mainTxt'>" . $aanvalGeluktArray[$berichtPoging] . "</td></tr><tr><td class='table_mainTxt'><a href='moorden.php' target='main'>Klik hier om terug te gaan</a></td></tr></table>";
	}
	exit;
}
?>
<form method="post" action="moorden.php">
<tr>
	<td class="center">
		<tr>
			<td class="subTitle">Slachoffer opgeven</td>
		</tr>
		<tr>
			<td class="table_mainTxt">Slachtoffer naam</td>
		</tr>
		<tr>
			<td class="table_mainTxt"><input class="input_form" type="text" maxlength="20" size="30" name="slachtoffer" /></td>
		</tr>
		<tr>
			<td class="table_mainTxt">Aantal kogels</td>
		</tr>
		<tr>
			<td class="table_mainTxt"><input class="input_form" type="text" maxlength="10" size="30" name="kogels" /></td>
		</tr>
		<tr>
			<td class="table_mainTxt">Bericht voor je slachtoffer (Max 500 tekens)</td>
		</tr>
		<tr>
			<td class="table_mainTxt"><input class="input_form" type="text" maxlength="500" size="30" name="bericht" /></td>
		</tr>
		<tr>
			<td class="table_mainTxt">Bivakmuts opzetten?</td></tr>
		<tr>
			<td class="table_mainTxt">Nee <input type="radio" name="bivak" value="0" /> Ja <input type="radio" name="bivak" value="1" Checked /></td>
		</tr>
		<tr>
			<td class="table_mainTxt"><input type="submit" class="button_form" name="kill" value="maak hem af!" /></td>
		</tr>
	</td>
</tr>
</form>
<td class="center">
		<tr>
			<td class="subTitle">Moorden informatie</td>
		</tr>
<tr>
	<td class="table_mainTxt">Ben jij iemand beu? of wil je meer macht? vermoord hem/haar gewoon... Opgepast er is een kans dat je slachtoffer terugschiet en dat kan wel is zuur opbreken.<br>
	Wanneer je iemand vermoord wordt er een getuige verklaring aan een andere speler afgeleverd. Deze speler is dan getuige dat jij het slachtoffer omlegde. Als de moord succesvol is krijg jij het slachtoffer zijn cash geld.</td>
</tr>
</table>
</table>