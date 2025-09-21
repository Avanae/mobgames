<?php
	include("config.php");
	include("SYSTEM_CONFIGURATION.php");
	include("include/functions.php");

?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
if(isset($_POST['login'])){
	$pasGeenSpatie = mysqli_real_escape_string($con,test_input($_POST['pass']));
	$pas = sha1(md5($pasGeenSpatie));
	$userGeenSpatie = trim($_POST['user']);
	$userNaam = mysqli_real_escape_string($con,test_input($userGeenSpatie));
	if(preg_match('/^[a-zA-Z0-9_\-]+$/',$userNaam) == 0){
		$bericht = "<p class='red'>Dit is geen geldige invoer gelieve opnieuw te proberen!</p>";
		exit;
	}
	$acc = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $userNaam . "' AND wachtwoord='" . $pas . "'");
	$acco = mysqli_fetch_object($acc);
	if(mysqli_num_rows($acc) > 0){
		// Controleren of het account is geactiveerd
		if(!empty($acco->activated)){
			echo print_bericht("Account Activatie","Je moet eerst je account activeren!");
			exit;
		}
		//checken of je nog leeft
		if($acco->status == "dood" && $acco->ban == '0'){
			$moordpogingInfo2 = mysqli_query($con,"SELECT * FROM moordpogingen WHERE slachtoffer='" . $acco->id . "' AND status='2'");
			$moordpogingInfo = mysqli_fetch_object($moordpogingInfo2);
		
			if($moordpogingInfo->bivak == 0){
				$killerQ = mysqli_query($con,"SELECT * FROM leden WHERE id=" . $moordpogingInfo->schutter . "");
				$killer = mysqli_fetch_object($killerQ);
				$bivakKill = "<a class='bold' href='speler_profiel.php?x=" . $killer->id . "'>" . $killer->login . "</a>";
			}else {
				$bivakKill = "Anoniem";
			}
			$berichtKiller = ($moordpogingInfo->woordenkiller) ? $moordpogingInfo->woordenkiller : "<font class='bold' color='red'>Geen</font>";
			$datumVermoord = $moordpogingInfo->datum;
			$bericht = "
			<p><img src='images/dood.jpg' width='100%' alt='Je bent vermoord' /></td>
			<p class='table_subTitle'>Assassination Bericht</p>
			<p class='table_mainTxt center'><font size='3' color='red'>Helaas, je bent vermoord!</font></p>
			<p class='table_mainTxt'>Datum: " . $datumVermoord . "</p>
			<p class='table_mainTxt'>Dader: " . $bivakKill . "</p>
			<p class='table_mainTxt'>Woorden van je killer: </p>
			<p class='table_mainTxt'>" . $berichtKiller . "</p>
			<p class='table_mainTxt'><a href='index.php' target='main'>Klik hier om een nieuw account te maken</a></p>
			";
		} else {
			if($acco->ban == 1 && $acco->status == 'dood'){
				$schQuery = mysqli_query($con,"SELECT * FROM schandpaal WHERE speler='" . $acco->id . "'");
				$schandpaal = mysqli_fetch_object($schQuery);
				$bericht = "
				<p><img src='images/dood.jpg' width='100%' alt='Je bent vermoord' /></td>
				<p class='table_subTitle'>Assassination Bericht</p>
				<p class='table_mainTxt center'><font size='3' color='red'>Helaas, je bent dood!</font></p>
				<p class='table_mainTxt'>Datum: " . $datumVermoord . "</p>
				<p class='table_mainTxt'>Dader: ONBEKEND</p>
				<p class='table_mainTxt'>Woorden van je killer: </p>
				<p class='table_mainTxt'>Je bent vermoord door een steen van een speler omdat je op de schandpaal stond.</p>
				<p class='table_mainTxt'><a href='index.php' target='main'>Klik hier om een nieuw account te maken</a></p>
				";
			} else if($acco->ban == 1 && $acco->status == 'levend'){
				$schQuery = mysqli_query($con,"SELECT * FROM schandpaal WHERE speler='" . $acco->id . "'");
				$schandpaal = mysqli_fetch_object($schQuery);
				$bericht = "
				<p><img src='images/schandpaal.jpg' width='100%' alt='Schandpaal' /></td>
				<p class='table_subTitle'>Assassination Bericht</p>
				<p class='table_mainTxt'><font size='3' color='red'>Je staat op de schandpaal!</font></p>
				<p class='table_mainTxt'>Datum: " . $schandpaal->datum . "</p>
				<p class='table_mainTxt'>Reden: " . $schandpaal->reden . "</p>
				";
			} else {
				$gn2 = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(online) AS online,0 FROM leden WHERE id='" . $acco->id . "' AND wachtwoord='" . $pas . "'");
				$gn4 = mysqli_fetch_object($gn2); 
				if(($gn4->online+3) < time()){
					// hoort 300 te zijn
					$_SESSION['id'] = $acco->id;
					$_SESSION['pass'] = $pas;
					$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
					$bericht = "<p class='table_mainTxt'>Welkom " . $acco->login . "!</p><p class='table_mainTxt'><a href='index.php' target='_parent'>Klik hier om verder te gaan</a></p>";
					mysqli_query($con,"UPDATE leden SET online=NOW(), ip='" . $_SESSION['ip'] . "' WHERE id='" . $acco->id . "' AND wachtwoord='" . $pas . "'");	
					
				} else {
					$bericht = "<p class='table_mainTxt'>Je moet minimum 5 minuten wachten voor je weer kan inloggen!</p>";
				}
			}
		}
	} else {
		$bericht = "<p class='table_mainTxt'>Er is geen account met deze gegevens!</p>";
	}
	print "<base target='main' /><p class='table_subTitle'>Inloggen</p><p class='table_mainTxt'>" . $bericht . "</p>";

}
if(isset($_GET['x'])=='loguit'){
	unset($_SESSION['id']);
	unset($_SESSION['pass']);
	unset($_SESSION['ip']);
	session_unset();
	session_destroy();
	print "<p class='table_subTitle'>Uitloggen</p><p class='table_mainTxt'>je bent uitgelogt</p><p class='table_mainTxt'><a href='index.php' target='_parent'>Klik hier om verder te gaan</a> </p>";
	exit();
}
?>