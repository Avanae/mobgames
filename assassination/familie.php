<?php
	include("config.php");
	include("include/functions.php");
	
//-------------------------------------------------
//- Familie instellingen: Top veranderen toevoegen, belastingen POST toevoegen
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	if($spelerInfo->mini_banner == 1){
		print"<table width='550px' colspan='2'><tr>
				<td colspan='5' class='table_mainTxt'><img src='images/headers/familie.png' width='550px' height='120px' alt='bezittingen pic' /></td>
			</tr></table>";
	}
	//----------------------------------------------
	//- fam Instellingen (geupdated)
	//----------------------------------------------
	if(isset($_POST["changeResourceTax"])){
		include("check_jail.php");
		if($spelerInfo->familierang <= 1){
			print_bericht("Familie Instellingen","Je hebt niet genoeg macht om de instellingen van je familie aan te passen.");
			exit;
		} else {
			$smokkel = mysqli_real_escape_string($con,test_input($_POST["taxSmokkel"]));
			$kogels = mysqli_real_escape_string($con,test_input($_POST["taxKogels"]));
			$crime = mysqli_real_escape_string($con,test_input($_POST["taxCrime"]));
			$auto = mysqli_real_escape_string($con,test_input($_POST["taxAuto"]));
			$og = mysqli_real_escape_string($con,test_input($_POST["taxOg"]));
			$kavel = mysqli_real_escape_string($con,test_input($_POST["taxKavel"]));
						
			if(!is_numeric($smokkel) || !is_numeric($kogels) || !is_numeric($crime) || !is_numeric($auto) || !is_numeric($og) || !is_numeric($kavel)){
				print_bericht("Familie Instellingen","Je gaf een ongeldig getal op in een van de velden!");
				exit;
			}
			
			if($smokkel < 0){ $smokkel = 0; }
			if($smokkel > 10){ $smokkel = 10; }
			
			if($kogels < 0){ $kogels = 0; }
			if($kogels > 10){ $kogels = 10; }
			
			if($crime < 0){ $crime = 0; }
			if($crime > 10){ $crime = 10; }
			
			if($auto < 0){ $auto = 0; }
			if($auto > 10){ $auto = 10; }
			
			if($og < 0){ $og = 0; }
			if($og > 10){ $og = 10; }
			
			if($kavel < 0){ $kavel = 0; }
			if($kavel > 10){ $kavel = 10; }
			print_bericht("Familie Instellingen","Je hebt de familie belastingen aangepast!");
			mysqli_query($con,"UPDATE families_belasting SET kogelTax='" . $kogels . "',misdaadTax='" . $crime . "',autoTax='" . $auto . "',gmTax='" . $og . "',kavelTax='" . $kavel . "',smokkelTax='" . $smokkel . "' WHERE familieid='" . $spelerInfo->familie . "'");
			exit;
		}		
	}
	if(isset($_POST["changeUnderboss"])){
		include("check_jail.php");
		if($spelerInfo->familierang <= 1){
			print_bericht("Familie Instellingen","Je hebt niet genoeg macht om de instellingen van je familie aan te passen.");
			exit;
		} else {
			$username = mysqli_real_escape_string($con,test_input($_POST["onderbaas"]));
			if(!checkUserName($username)){
				print_bericht("Familie Instellingen","Dit is geen geldige gebruikersnaam.");
				exit;
			}
			
			// Controleren of de gebruiker bestaat
			$userSql = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $username . "'");
			if(mysqli_num_rows($userSql) <= 0){
				print_bericht("Familie Instellingen","Deze speler bestaat niet.");
				exit;
			}
			$user = mysqli_fetch_object($userSql);
			
			// Controleren of de gebruiker nog leeft en niet aan de schandpaal hangt
			if($user->status == "dood" || $user->ban == 1){
				print_bericht("Familie Instellingen","Deze speler leeft niet meer of hangt aan de schandpaal.");
				exit;
			}
			
			// Controleren of de speler in dezelfde familie zit als de baas
			if($user->familie != $spelerInfo->familie){
				print_bericht("Familie Instellingen","Deze speler zit niet in jou familie.");
				exit;
			}
			
			print_bericht("Familie Instellingen","Je hebt jou onderbaas aangepast.");
			mysqli_query($con,"UPDATE leden SET familierang='2' WHERE id='" . $user->id . "'");
			
		}
		exit;		
	}
	if(isset($_GET['instellingen'])){
		include("check_jail.php");
		if($spelerInfo->familierang <= 1){
			print "<p class='table_subTitle'>Familie instellingen</p><p class='table_mainTxt'>Je hebt niet genoeg macht om de instellingen van je familie aan te passen.</p>";
			exit;
		} else {
			// Familie ophalen
			$familieQuery =  mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerInfo->familie . "'");
			$familie = mysqli_fetch_object($familieQuery);
			
			// Onderbaas van de familie ophalen
			$onderB = mysqli_query($con,"SELECT * FROM leden WHERE familierang='2' AND familie='" . $familie->id . "'");
			$onderbaasInfo = mysqli_fetch_object($onderB);
			$onderbaas = "Geen opgegeven";
			if($onderbaasInfo){
				$onderbaas = "<a href='speler_profiel.php?x=" . $onderbaasInfo->id . "'>" . $onderbaasInfo->login . "</a>";
			}
			// De belastingen van de familie laden
			$familieTaxQuery = mysqli_query($con,"SELECT * FROM families_belasting WHERE familieid='" . $familie->id . "'");
			$familieTax = mysqli_fetch_object($familieTaxQuery);
			if(!$familieTax){
				mysqli_query($con,"INSERT INTO families_belasting (familieid) VALUES ('" . $familie->id . "')");
				$familieTaxQuery = mysqli_query($con,"SELECT * FROM families_belasting WHERE familieid='" . $familie->id . "'");
				$familieTax = mysqli_fetch_object($familieTaxQuery);
			}
			print "<form method='post' action='familie.php'>
			<table width='550px' class=\"inhoud_table\" colspan='2'>
				<tr>
					<td class='table_subTitle' width='100%' colspan='2'>Familie Onderbaas</td>
				</tr>
				<tr>
					<td class='table_mainTxt outline padding_5' width='30%' colspan='1'>Huidige onderbaas</td>
					<td class='table_mainTxt' width='70%' colspan='1'>" . $onderbaas . "</td>
				</tr>
				<tr>
					<td class='table_mainTxt outline padding_5' width='30%' colspan='1'><label for='onderbaas'>Onderbaas</label></td>
					<td class='table_mainTxt' width='70%' colspan='1'><input class='input_form' type='text' name='onderbaas' id='onderbaas' maxlength='20'  /></td>
				</tr>
				<tr>
					<td class='table_mainTxt' width='30%' colspan='1'></td>
					<td class='table_mainTxt' width='70%' colspan='1'><input type='submit' class='button_form' name='changeUnderboss' value='Onderbaas veranderen' /></td>
				</tr>
			</table>
			</form>";
			print "<form method='post' action='familie.php'>
			<table width='550px' class=\"inhoud_table\" colspan='2'>
				<tr>
					<td class='table_subTitle' width='100%' colspan='2'>Familie Belastingen</td>
				</tr>
				<tr>
					<td class='table_mainTxt padding_5' width='100%' colspan='2'>Deze belastingen gelden enkel op de spelers die tot jou familie behoren. Dat wil zeggen dat je een bepaald percentage van de winst krijgt. Bij kavels zal de familie een deel van de inkomsten per uur ontvangen</td>
				</tr>
				<tr>
					<td class='table_mainTxt outline' width='30%' colspan='1'><label for='taxSmokkel'>Belasting op smokkelen</label></td>
					<td class='table_mainTxt' width='70%' colspan='1'><input class='input_form' type='text' name='taxSmokkel' id='taxSmokkel' maxlength='2' value='" . $familieTax->smokkelTax . "' /> %</td>
				</tr>
				<tr>
					<td class='table_mainTxt outline' width='30%' colspan='1'><label for='taxKogels'>Belasting op kogels</label></td>
					<td class='table_mainTxt' width='70%' colspan='1'><input class='input_form' type='text' name='taxKogels' id='taxKogels' maxlength='2' value='" . $familieTax->kogelTax . "' /> %</td>
				</tr>
				<tr>
					<td class='table_mainTxt outline' width='30%' colspan='1'><label for='taxCrime'>Kleine misdaad</label></td>
					<td class='table_mainTxt' width='70%' colspan='1'><input class='input_form' type='text' name='taxCrime' id='taxCrime' maxlength='2' value='" . $familieTax->misdaadTax . "' /> %</td>
				</tr>
				<tr>
					<td class='table_mainTxt outline' width='30%' colspan='1'><label for='taxAuto'>Auto verkoop</label></td>
					<td class='table_mainTxt' width='70%' colspan='1'><input class='input_form' type='text' name='taxAuto' id='taxAuto' maxlength='2' value='" . $familieTax->autoTax . "' /> %</td>
				</tr>
				<tr>
					<td class='table_mainTxt outline' width='30%' colspan='1'><label for='taxOg'>Georganiseerde misdaad</label></td>
					<td class='table_mainTxt' width='70%' colspan='1'><input class='input_form' type='text' name='taxOg' id='taxOg' maxlength='2' value='" . $familieTax->gmTax . "' /> %</td>
				</tr>
				<tr>
					<td class='table_mainTxt outline' width='30%' colspan='1'><label for='taxKavel'>Kavel inkomsten</label></td>
					<td class='table_mainTxt' width='70%' colspan='1'><input class='input_form' type='text' name='taxKavel' id='taxKavel' maxlength='2' value='" . $familieTax->kavelTax . "' /> %</td>
				</tr>
				<tr>
					<td class='table_mainTxt' width='30%' colspan='1'></td>
					<td class='table_mainTxt' width='70%' colspan='1'><input type='submit' class='button_form' name='changeResourceTax' value='Belasting veranderen' /></td>
				</tr>
			</table>
			</form>";
			exit;
		}	
	}
	//----------------------------------------------
	//- fam verwijderen (geupdated)
	//----------------------------------------------
	if(isset($_GET['verwijder'])){
		include("check_jail.php");
		if($spelerInfo->familierang != 3){
			print "<p class='table_subTitle'>Familie verwijderen</p><p class='table_mainTxt'>Je hebt niet genoeg rechten op de familile te verwijderen. Je moet de baas zijn om deze actie uit te voeren!</p>";
			exit;
		} else {
			print "<form method='post' action='familie.php'>
			<table width='550px' class=\"inhoud_table\" colspan='2'>
				<tr>
					<td class='table_subTitle' width='100%' colspan='2'>Familie verwijderen</td>
				</tr>
				<tr>
					<td class='table_mainTxt' width='100%' colspan='2'>Weet u zeker dat u de familie wil verwijderen?</td>
				</tr>
				<tr>
					<td class='table_mainTxt center' width='100%' colspan='2'><input type='submit' class='button_form' name='delete' value='Verwijder familie' /></td>
				</tr>
			</table>
			</form>";
			exit;
		}	
	}
	if(isset($_POST['delete'])){
		include("check_jail.php");
		if($spelerInfo->familierang == 3){
			$bericht = "Je hebt de familie verwijderd!";
			mysqli_query($con,"DELETE FROM families WHERE id='" . $spelerInfo->familie . "' AND baas='" . $spelerInfo->id . "'");
			mysqli_query($con,"DELETE FROM families_belasting WHERE familieid='" . $spelerInfo->familie . "'");
			mysqli_query($con,"UPDATE leden SET familie='0', familierang='0' WHERE familie='" . $spelerInfo->familie . "'");
		} else {
			$bericht = "Je moet baas zijn van de familie om deze te verwijderen!";
		}
		print "<table width='550px' class=\"inhoud_table\"><tr><td class='table_subTitle'>Familie verwijderen</td></tr><tr><td class='table_mainTxt'>" . $bericht . "</td>";
		exit;
	}
	//----------------------------------------------
	//- nieuwe familie (geupdated)
	//----------------------------------------------
	if(isset($_GET['nieuw'])){
		include("check_jail.php");
		if($spelerInfo->cash < 200000000){
			print_bericht("Familie","Je hebt niet genoeg geld om een familie op te zetten. De kosten zijn &euro; 200.000.000!");
			exit;
		} else {
			if($spelerInfo->rang < 9){
				print_bericht("Familie","Je moet minimum de rang Lokale baas hebben om een familie op te zetten!");
			}else{
				print "<form method='post' action='familie.php'>
				<table width='550px' class=\"inhoud_table\" colspan='2'>
					<tr>
						<td class='table_subTitle' width='100%' colspan='2'>Familie opzetten</td>
					</tr>
					<tr>
						<td class='table_mainTxt' width='30%' colspan='1'><label for='1'>Naam familie</label></td>
						<td class='table_mainTxt' width='70%' colspan='1'><input class='input_form' type='text' maxlength='30' name='naamfam' id='1' /></td>
					</tr>
					<tr>
						<td class='table_mainTxt' width='30%' colspan='1'><label for='2'>Onderbaas</label></td>
						<td class='table_mainTxt' width='70%' colspan='1'><input class='input_form' type='text' maxlength='20' name='onderbaas' id='2' /></td>
					</tr>
					<tr>
						<td class='table_mainTxt' width='100%' colspan='2'><input type='submit' class='button_form' name='fammaken' value='Maak familie' /></td>
					</tr>
				</table>
				</form>";
			}
			exit;
		}	
	}
	if(isset($_POST['fammaken'])){
		include("check_jail.php");
		$naamFam = mysqli_real_escape_string($con,test_input($_POST['naamfam']));
		$onderbaasFam = mysqli_real_escape_string($con,test_input($_POST['onderbaas']));
		
		if($naamFam == ""){
			print_bericht("Familie","Gelieve alle velden in te vullen.");
			exit;
		} else if(preg_match('/^[a-zA-Z0-9_\-]+$/',$naamFam) == 0){
			print_bericht("Familie","Een familie naam mag alleen A-Z, a-z, 0-9, _ en - hebben!");
			exit;
		} else if(preg_match('/^[a-zA-Z0-9_\-]+$/',$onderbaasFam) == 0){
			print_bericht("Familie","Een naam van een onderbaas mag alleen A-Z, a-z, 0-9, _ en - hebben!");
			exit;
		} elseif($onderbaasFam == ""){
			print_bericht("Familie","Gelieve alle velden in te vullen.");
			exit;		
		}
			
		$onderbaasChecken = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $onderbaasFam . "' AND status='levend' AND ban='0'");
		$onderbaasInfo = mysqli_fetch_object($onderbaasChecken);
		
		$familieChecken1 = mysqli_query($con,"SELECT * FROM families WHERE naam='" . $naamFam . "'");
		$familieExist = mysqli_num_rows($familieChecken1);
		if($spelerInfo->familie != 0)
		{
			$bericht = "Je zit nog in een familie! Vraag aan jou baas om je uit de familie te zetten.";
		} else if($familieExist > 0){
			$bericht = "Er is al een familie met deze naam.";
		} else if(!$onderbaasInfo){
			$bericht = "Er is geen speler met deze naam. Kies een andere onderbaas";
		} else if($onderbaasInfo->familierang >= 1){
			$bericht = "Deze speler zit al in een familie.";
		} else if($onderbaasInfo->rang <= 8){
			$bericht = "Je onderbaas moet minimim de rang " . $gameRangen[9] . " hebben.";
		} else if($spelerInfo->rang < 9){
			$bericht = "Om een familie te stichten moet je minimim de rang " . $gameRangen[9] . " hebben.";
		} else {
			mysqli_query($con,"INSERT INTO families (naam,land, baas,geld,datum) VALUES ('" . $naamFam . "','" . $spelerInfo->land . "','" . $spelerInfo->login . "','50000',NOW())");
			$famId = mysqli_insert_id($con);
			mysqli_query($con,"INSERT INTO families_belasting (familieid) VALUES ('" . $famId . ")");
			mysqli_query($con,"UPDATE leden SET familie='" . $famId . "', familierang='3', cash=cash-'200000000' WHERE id='" . $spelerInfo->id . "'");
			mysqli_query($con,"UPDATE leden SET familie='" . $famId . "', familierang='2' WHERE id='" . $onderbaasInfo->id . "'");
			
			
			$bericht = "Je hebt met succes een nieuwe familie opgezet.";
		}
		print_bericht("Familie",$bericht);
		exit;
	}
	//----------------------------------------------
	//- Familie lijst (geupdated)
	//----------------------------------------------
	if(isset($_GET['lijst'])){
		print"<table width='550px' class=\"inhoud_table\" colspan='6'>
			<tr>	
				<td class='table_subTitle' colspan='6'>Familie lijst</td>
			</tr>
			<tr>
				<td class='table_mainTxt outline' colspan='1' width='15%'>Plaats</td>
				<td class='table_mainTxt outline' colspan='1' width='30%'>Naam</td>
				<td class='table_mainTxt outline' colspan='1' width='15%'>Baas</td>
				<td class='table_mainTxt outline' colspan='1' width='10%'>Leden</td>
				<td class='table_mainTxt outline' colspan='2' width='30%'>Kracht familie</td>
			</tr>";
		$memSql = "SELECT SUM(rang) rang, COUNT(*) as members, familie,land FROM leden WHERE familie !=0 GROUP BY familie ORDER BY rang DESC";
		$memQuery = mysqli_query($con,$memSql);
		while($mem = mysqli_fetch_object($memQuery)){
			$famQuery = mysqli_query($con,"SELECT * FROM families WHERE id='" . $mem->familie . "'");
			$famInfo = mysqli_fetch_object($famQuery);
			
			$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $famInfo->land . "'");
			$land = mysqli_fetch_object($landQuery);
			$baas2 = mysqli_query($con,"SELECT * FROM leden WHERE familie='" . $famInfo->id . "' AND familierang='3'");
			$baas = mysqli_fetch_object($baas2);
			
			print"<tr>
				<td class='table_mainTxt' colspan='1' width='15%'><a href='kavels.php?overview=" . $land->id . "'>" . $land->land . "</a></td>
				<td class='table_mainTxt' colspan='1' width='30%'><a href='familie.php?pagina=" . $famInfo->id . "'>" . $famInfo->naam . "</td>
				<td class='table_mainTxt' colspan='1' width='15%'><a href='speler_profiel.php?x=" . $baas->id . "'>" . $baas->login . "</a></td>
				<td class='table_mainTxt' colspan='1' width='10%'>" . $mem->members . "</td>
				<td class='table_mainTxt' colspan='2' width='30%'>" . $mem->rang . "</td>
			</tr>";
			
			/*
			$fam = mysqli_query($con,"SELECT * FROM families ORDER BY naam DESC");
			$i=1;
			while($famInfo = mysqli_fetch_object($fam)){
				$leden1 = mysqli_query($con,"SELECT * FROM leden WHERE familie='" . $famInfo->id . "'");
				$baas2 = mysqli_query($con,"SELECT * FROM leden WHERE familie='" . $famInfo->id . "' AND familierang='3'");
				$baas = mysqli_fetch_object($baas2);
				$leden = mysqli_num_rows($leden1);
				$krachtLedenTotaal = 0;
				
				$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $famInfo->land . "'");
				$land = mysqli_fetch_object($landQuery);
				while($ledenFam = mysqli_fetch_object($leden1)){
					$krachtLedenTotaal = ($krachtLedenTotaal+($ledenFam->rang+$ledenFam->rangvordering));		
				}
				print"<tr>
					<td class='table_mainTxt' colspan='1' width='15%'><a href='kavels.php?overview=" . $land->id . "'>" . $land->land . "</a></td>
					<td class='table_mainTxt' colspan='1' width='30%'><a href='familie.php?pagina=" . $famInfo->id . "'>" . $famInfo->naam . "</td>
					<td class='table_mainTxt' colspan='1' width='15%'><a href='speler_profiel.php?x=" . $baas->id . "'>" . $baas->login . "</a></td>
					<td class='table_mainTxt' colspan='1' width='10%'>" . $leden . "</td>
					<td class='table_mainTxt' colspan='2' width='30%'>" . $krachtLedenTotaal . "</td>
				</tr>";
				$i++;
			}
			*/
		}
		print"</table>";
		exit;
	}
	//----------------------------------------------
	//- Familie pagina (geupdated)
	//----------------------------------------------
	if(isset($_GET['pagina'])){
		$famPage = mysqli_real_escape_string($con,test_input($_GET['pagina']));
		if(!is_numeric($famPage) && !empty($famPage))
		{
			print_bericht("Familie","Er ging iets mis met het ophalen van deze familie.");
			exit;
		}
		$fam = mysqli_query($con,"SELECT * FROM families WHERE id='" . $famPage . "'");
		if(mysqli_num_rows($fam) == 0){
			print_bericht("Familie","Deze familie bestaat niet.");
			exit;
		}
		
		$famNaam = mysqli_fetch_object($fam);
		$leden1 = mysqli_query($con,"SELECT * FROM leden WHERE familie='" . $famNaam->id . "' AND status='levend' AND ban='0'");
		$leden = mysqli_num_rows($leden1);
		$onderB = mysqli_query($con,"SELECT * FROM leden WHERE familierang='2' AND familie='" . $famNaam->id . "' AND status='levend' AND ban='0'");
		$onderbaasInfo = mysqli_fetch_object($onderB);
		
		$bassC = mysqli_query($con,"SELECT * FROM leden WHERE familierang='3' AND familie='" . $famNaam->id . "' AND status='levend' AND ban='0'");
		$baasInfo = mysqli_fetch_object($bassC);
		$i=1;
		$krachtLedenTotaal = 0;
		
		while($ledenFam = mysqli_fetch_object($leden1)){
			$krachtLedenTotaal = ($krachtLedenTotaal+($ledenFam->rang+$ledenFam->rangvordering));		
		}
		$i++;

		if(!$onderbaasInfo){
			$onderbaas = "Geen";
		} else {
			$onderbaas = "<a href='speler_profiel.php?x=" . $onderbaasInfo->id . "'>" . $onderbaasInfo->login . "</a>";
		}
		
		$crusherTxt = "<span class=\"red\">GEEN</span>";
		$crusherQuery = mysqli_query($con,"SELECT * FROM families_crusher WHERE familie='" . $famNaam->id . "'");
		if(mysqli_num_rows($crusherQuery) == 1){
			$crusher = mysqli_fetch_object($crusherQuery);
			if($crusher->maxAutos > $crusher->crushed){
				$crusherTxt = formatDecimaal($crusher->crushed) . " / " . formatDecimaal($crusher->maxAutos);
			}
		}
		$pppoen = formatDecimaal($famNaam->geld);
		print"
		<table width='550px' class=\"inhoud_table\" colspan='2'>
		<tr>	
			<td class='table_subTitle' colspan='2'>Familie Pagina van " . $famNaam->naam . "</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='1' width='50%'>Don</td>
			<td class='table_mainTxt' colspan='1' width='100%'><a href='speler_profiel.php?x=" . $baasInfo->id . "'>" . $baasInfo->login . "</a></td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='1' width='50%'>Rechterhand</td>
			<td class='table_mainTxt' colspan='1' width='100%'>" . $onderbaas . "</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='1' width='50%'>Leden</td>
			<td class='table_mainTxt' colspan='1' width='50%'>" . $leden . "</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='1' width='50%'>Kracht familie</td>
			<td class='table_mainTxt' colspan='1' width='100%'>" . $krachtLedenTotaal . "</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='1' width='50%'>Geld</td>
			<td class='table_mainTxt' colspan='1' width='100%'>&euro; " . $pppoen . "</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='1' width='50%'>Bestaat sinds</td>
			<td class='table_mainTxt' colspan='1' width='100%'>" . $famNaam->datum . "</td>
		</tr>
		<tr>
			<td class='table_mainTxt outline' colspan='1' width='50%'>Crusher Gehuurd</td>
			<td class='table_mainTxt' colspan='1' width='100%'>" . $crusherTxt . "</td>
		</tr>
		<tr>
			<td class='table_subTitle' width='550px' colspan='2'>Leden</td>
		</tr>
		<tr>
		<td class='table_mainTxt' width='550px' colspan='2'>
		";
		// alle leden weergeven:
		$mem = mysqli_query($con,"SELECT * FROM leden WHERE familie='" . $famNaam->id . "' ORDER BY rang DESC");
		$aantalM = mysqli_num_rows($mem);
		$zit=1;
		while($ledenFam2 = mysqli_fetch_object($mem)){
			if($aantalM == 1 || $aantalM == $zit){
				$lid = "<a href='speler_profiel.php?x=" . $ledenFam2->id . "'>" . $ledenFam2->login . "</a>";
			} else {
				$lid = "<a href='speler_profiel.php?x=" . $ledenFam2->id . "'>" . $ledenFam2->login . "</a> , ";
			}
			echo $lid;
			$zit++;
		}
		print"</td></tr>
		<tr>
			<td class='table_subTitle' width='550px' colspan='2'>Familie informatie</td>
		</tr>
		<tr>
			<td class='table_mainTxt' width='550px' colspan='2'>" . $famNaam->familie_info . "</td>
		</tr>
		</table>";
		exit;
	}
	//----------------------------------------------
	//- Familie donatie (geupdated)
	//----------------------------------------------
	if(isset($_POST['doneer'])){
		include("check_jail.php");
		$bedrag = mysqli_real_escape_string($con,test_input($_POST['bedrag']));
		if(!is_numeric($bedrag) && !empty($bedrag)){
			$bericht = "Dit is geen geldig bedrag.";
		} elseif($spelerInfo->cash < $bedrag){
			$bericht = "Je hebt niet genoeg geld om dat bedrag te doneren.";
		} elseif($bedrag == 0){
			$bericht = "Je moet minimum 1 euro doneren.";
		}else {
			mysqli_query($con,"UPDATE leden SET cash=cash-'" . $bedrag . "' WHERE id='" . $spelerInfo->login . "'");
			mysqli_query($con,"UPDATE families SET geld=geld+'" . $bedrag . "' WHERE id='" . $spelerInfo->familie . "'");
			mysqli_query($con,"INSERT INTO donatie_logs (zender,ontvanger,bedrag,datum,type) VALUES ('" . $spelerInfo->id . "','" . $spelerInfo->familie . "','" . $bedrag . "',NOW(),'familie')");
			$pppoen = number_format($bedrag,0,",",".");
			$bericht = "Je hebt met succes &euro; " . $pppoen . " gedoneert op de familie rekening.";
		}
		print_bericht("Familie",$bericht);
		exit;
	}
	if(isset($_POST['doneerlid'])){
		include("check_jail.php");
		$bedrag = mysqli_real_escape_string($con,test_input($_POST['bedrag']));
		$lid = mysqli_real_escape_string($con,test_input($_POST['naam']));
		if(preg_match('/^[a-zA-Z0-9_\-]+$/',$lid) == 0){
			$bericht = "<font color='red'>Een naam van een onderbaas mag alleen A-Z, a-z, 0-9, _ en - hebben!</font>";
		} else {
			$lidex = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $lid . "' AND familie='" . $spelerInfo->familie . "' AND status='levend' AND ban='0'");
			$lidInfo = mysqli_fetch_object($lidex);
			$famb = mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerInfo->familie . "'");
			$famBank = mysqli_fetch_object($famb);
			if(!$lidInfo){ 
				$bericht = "Er bestaat geen lid in je familie met deze naam!";
			} elseif(!is_numeric($bedrag)){
				$bericht = "Dit is geen geldig bedrag.";
			} elseif($famBank->geld < $bedrag){
				$bericht = "Er staat niet genoeg geld op de familie bank om dit bedrag te doneren.";
			} elseif($bedrag == 0){
				$bericht = "Je moet minimum 1 euro doneren.";
			}else {
				mysqli_query($con,"UPDATE leden SET cash=cash+'" . $bedrag . "' WHERE id='" . $lidInfo->id . "'");
				mysqli_query($con,"UPDATE families SET geld=geld-'" . $bedrag . "' WHERE id='" . $spelerInfo->familie . "'");
				mysqli_query($con,"INSERT INTO donatie_logs (zender,ontvanger,bedrag,datum,type) VALUES ('" . $spelerInfo->familie . "','" . $lidInfo->id . "','" . $bedrag . "',NOW(),'familie')");
				$pppoen = number_format($bedrag,0,",",".");
				$bericht = "Je hebt met succes " . $pppoen . " euro gedoneert aan " . $lidInfo->login . ".";
			}
		}
		print_bericht("Familie",$bericht);
		exit;
	}
	if(isset($_GET['doneren']) && $spelerInfo->familierang >= 1){
		include("check_jail.php");
		$verz = mysqli_query($con,"SELECT * FROM donatie_logs WHERE zender='" . $spelerInfo->familie . "' AND type='familie' ORDER BY datum DESC LIMIT 0,10");
		$ontv = mysqli_query($con,"SELECT * FROM donatie_logs WHERE ontvanger='" . $spelerInfo->familie . "' AND type='familie' ORDER BY datum DESC LIMIT 0,10");
		$famb = mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerInfo->familie . "'");
		$famBank = mysqli_fetch_object($famb);
		print"
		<form method='post' action='familie.php'>
			<table width='550px' class=\"inhoud_table\" colspan='2'>
				<tr>
					<td class='table_subTitle' width='100%' colspan='2'>Familie donatie</td>
				</tr>
				<tr>
					<td class='table_mainTxt' width='20%' colspan='1'>Bedrag</td>
					<td class='table_mainTxt' width='80%' colspan='1'><input class='input_form' type='text' maxlentgh='10' name='bedrag'/></td>
				</tr>
				<tr>
					<td class='table_mainTxt' width='100%' colspan='2'><input type='submit' name='doneer' value='Doneer aan de familie' class='button_form' /></td>
				</tr>
			</table>
		</form>";
		if($spelerInfo->familierang >= 2){
			$poen = formatDecimaal($famBank->geld);
			print"
			<form method='post' action='familie.php'>
				<table width='550px' class=\"inhoud_table\" colspan='2'>
					<tr>
						<td class='table_subTitle' width='100%' colspan='2'>Lid donatie</td>
					</tr>
					<tr>
						<td class='table_mainTxt' width='40%' colspan='1'>Bedrag</td>
						<td class='table_mainTxt' width='60%' colspan='1'><input class='input_form' type='text' maxlentgh='10' name='bedrag'/> Max &euro; " . $poen . "</td>
					</tr>
					<tr>
						<td class='table_mainTxt' width='40%' colspan='1'>Naam van het lid</td>
						<td class='table_mainTxt' width='60%' colspan='1'><input class='input_form' type='text' maxlentgh='10' name='naam'/></td>
					</tr>
					<tr>
						<td class='table_mainTxt' width='100%' colspan='2'><input type='submit' name='doneerlid' value='Doneer aan een lid' class='button_form' /></td>
					</tr>
				</table>
			</form>";
		}
		print"
		<table width='550px' class=\"inhoud_table\" colspan='4'>
		<tr>
			<td class='table_subTitle' colspan='4'>Laatste 10 verzonden naar leden</td>
		</tr><tr>
			<td class='table_mainTxt outline' colspan='1' width='5%'>ID</td>
			<td class='table_mainTxt outline' colspan='1' width='30%'>Login</td>
			<td class='table_mainTxt outline' colspan='1' width='50%'>Datum</td>
			<td class='table_mainTxt outline' colspan='1' width='45%'>Bedrag</td>
		</tr>
		";
		$ida = 1;
		while($verzondenPoen = mysqli_fetch_object($verz)){
			$pppoen = number_format($verzondenPoen->bedrag,0,",",".");
			$usss = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $verzondenPoen->ontvanger . "'");
			$us = mysqli_fetch_object($usss);
			print"<tr>
				<td class='table_mainTxt' colspan='1' width='5%'>" . $ida . "</td>
				<td class='table_mainTxt' colspan='1' width='30%'><a href='speler_profiel.php?x=" . $us->id . "'>" . $us->login . "</a></td>
				<td class='table_mainTxt' colspan='1' width='35%'>" . $verzondenPoen->datum . "</td>
				<td class='table_mainTxt' colspan='1' width='30%'>&euro; " . $pppoen . "</td>
			</tr>
			";
			$ida++;
		}
		print"</table><table width='550px' class=\"inhoud_table\" colspan='4'>
		<tr>
			<td class='table_subTitle' colspan='4'>Laatste 10 ontvangen van leden</td>
		</tr><tr>
			<td class='table_mainTxt outline' colspan='1' width='5%'>ID</td>
			<td class='table_mainTxt outline' colspan='1' width='30%'>Login</td>
			<td class='table_mainTxt outline' colspan='1' width='50%'>Datum</td>
			<td class='table_mainTxt outline' colspan='1' width='45%'>Bedrag</td>
		</tr>";
		$idb = 1;
		while($ontvangenPoen = mysqli_fetch_object($ontv)){
			$uss = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $ontvangenPoen->zender . "'");
			$u = mysqli_fetch_object($uss);
			$pppoen = number_format($ontvangenPoen->bedrag,0,",",".");
			print"<tr>
				<td class='table_mainTxt' colspan='1' width='5%'>" . $idb . "</td>
				<td class='table_mainTxt' colspan='1' width='30%'><a href='speler_profiel.php?x=" . $u->id . "'>" . $u->login . "</a></td>
				<td class='table_mainTxt' colspan='1' width='50%'>" . $ontvangenPoen->datum . "</td>
				<td class='table_mainTxt' colspan='1' width='45%'>&euro; " . $pppoen . "</td>
			</tr>
			";
			$idb++;
		}
		exit;
	}
	//----------------------------------------------
	//- Familie Invite (geupdated)
	//----------------------------------------------
	if(isset($_POST['invite_player'])){
		include("check_jail.php");
		$name = mysqli_real_escape_string($con,test_input($_POST['player_name']));
		if(checkUserName($name)){
			print_bericht("Familie","Ogeldige gebruiker");
			exit;
		}
		
		$sql = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $name . "' AND status='levend' AND ban='0'");
		
		if(mysqli_num_rows($sql) == 0)
		{
			print_bericht("Familie","Deze speler bestaat niet, leeft niet meer of hangt aan de schandpaal.");
			exit;
		}
		$member = mysqli_fetch_object($sql);
		if($member->familie != 0){
			print_bericht("Familie","Deze speler heeft al een familie.");
		} else {
			$famSql = mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerInfo->familie . "'");
			$fam = mysqli_fetch_object($famSql);
			mysqli_query($con,"INSERT INTO families_invite (familie,speler,datum) VALUES ('" . $spelerInfo->familie . "','" . $member->id . "',NOW())");
			$acc = '<a href="familie.php?inviteans=' . $spelerInfo->familie . '&state=1">Klik hier om te accepteren</a>';
			$ref = '<a href="familie.php?inviteans=' . $spelerInfo->familie . '&state=0">Klik hier om te weigeren</a>';
			mysqli_query($con,"INSERT INTO berichten (zender,ontvanger,onderwerp,bericht,datum) VALUES ('" . $spelerInfo->id . "','" . $member->id . "','Familie invite','Je werd door " . $spelerInfo->login . " uitgenodigt om " . $fam->naam . " te betreden als lid. " . $acc . " | " . $ref . "',NOW())");
			
			print_bericht("Familie","Je hebt " . $member->login . " uitgenodigd om in de familie toe te treden. Je moet nu wachten op bevestiging van deze speler.");			
		}
		exit;
	}
	if(isset($_GET['invite'])){
		include("check_jail.php");
		print"
		<table width='550px' class=\"inhoud_table\" colspan='2'>
			<tr>
				<td class='table_subTitle' colspan='2'>Speler uitnodigen</td>
			</tr>
			<tr>
				<td class='table_mainTxt' colspan='2'>Hier kan je uw familie uitbreiden. </td>
			</tr>
			<tr>
				<td class='table_mainTxt center' colspan='1' width='50%'><form method='post' action='familie.php'><label for='input_form'>Naam</label> <input type='text' class='input_form' name='player_name' /></td>
				<td class='table_mainTxt' colspan='1' width='50%'><input type='submit' name='invite_player' class='button_form' value='Zend uitnodiging' /></form></td>
			</tr>
		</table>
		";
		$famInfff = mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerInfo->familie . "'");
		$famInf = mysqli_fetch_object($famInfff);
		$invvv = mysqli_query($con,"SELECT * FROM families_invite WHERE familie='" . $famInf->id . "'");
		print "<table width='550px' class=\"inhoud_table\" colspan='3'>
		<tr>
			<td class='table_subTitle' colspan='3'>Spelers uitgenodigd</td>
		</tr>";
		if(mysqli_num_rows($invvv) <= 0){
			print "
					<tr>
						<td class='table_mainTxt' colspan='3'>Er zijn geen spelers uitgenodigd.</td>
					</tr>
				";
		}else{
			while($invite = mysqli_fetch_object($invvv)){
				$spelerInvvv = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $invite->speler . "'");
				$spelerGegevens = mysqli_fetch_object($spelerInvvv);
				print "
					<tr>
						<td class='table_mainTxt' colspan='1'><a href='speler_profiel.php?x=" . $spelerGegevens->id . "'>" . $spelerGegevens->login . "</a></td>
						<td class='table_mainTxt' colspan='1'>" . $gameRangen[$spelerGegevens->rang] . "</td>
						<td class='table_mainTxt' colspan='1'>" . $invite->datum . "</td>
					</tr>
				";
			}
		}
		print "</table>";
		exit;
	}
	if(isset($_GET['inviteans']) && isset($_GET["state"])){
		include("check_jail.php");
		$famId = mysqli_real_escape_string($con,test_input($_GET['inviteans']));
		$answer = mysqli_real_escape_string($con,test_input($_GET['state']));
		
		// Check of het een geldige invoer is via de URL
		if(!is_numeric($answer) || !is_numeric($famId) || !empty($answer) || !empty($famId))
		{
			print_bericht("Familie","<font color='red'>Euh, euh, euh, you didn't guess the magic password</font>");
			exit;
		}
		
		// Check of de speler al in een familie zit
		if($spelerInfo->familie != 0 || $spelerInfo->familierang != 0)
		{
			print_bericht("Familie","<font color='red'>Verlaat eerst je familie voor je een andere familie kan betreden!</font>");
			exit;
		}
		
		// Check if invite exists
		$inviteSql = mysqli_query($con,"SELECT * FROM families_invite WHERE familie='" . $famId . "' AND speler='" . $spelerInfo->id . "'");
		if(mysqli_num_rows($inviteSql) == 0)
		{
			print_bericht("Familie","<font color='red'>Je bent niet uitgenodigd door deze familie!</font>");
			exit;
		}
		
		// Controleer of de familie nog bestaat
		$famSql = mysqli_query($con,"SELECT * FROM families WHERE id='" . $famId . "'");
		if(mysqli_num_rows($famSql) == 0)
		{
			print_bericht("Familie","<font color='red'>Helaas, deze familie bestaat niet meer!</font>");
			mysqli_query($con,"DELETE FROM families_invite WHERE familie='" . $famId . "' AND speler='" . $spelerInfo->id . "'");
			exit;
		}
		
		$invite = mysqli_fetch_object($inviteSql);
		if($answer == 0)
		{
			print_bericht("Familie","<font color='red'>Je hebt de uitnodiging geweigerd!</font>");
		} else if($answer == 1) {
			print_bericht("Familie","<font color='limegreen'>Je bent nu lid van de familie, gefeliciteerd!</font>");
			mysqli_query($con,"UPDATE leden SET familie='" . $famId . "',familierang='1' WHERE id='" . $spelerInfo->id . "'");
		} else {
			print_bericht("Familie","<font color='red'>Euh, euh, euh, you didn't guess the magic password!</font>");
		}
		mysqli_query($con,"DELETE FROM families_invite WHERE familie='" . $famId . "' AND speler='" . $spelerInfo->id . "'");
		exit;
	}
	print_bericht("Familie","<font color='red'>What are you looking for???</font>");
?>