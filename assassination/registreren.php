<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	$errorUsername = "";
	$errorRefferal = "";
	$errorPassword = "";
	$errorPassword2 = "";
	$errorEmail = "";
	$errorSex = "";
	$errorRules = "";
	
	$usernameHtml = "";
	$emailHtml = "";
	
	$ref = "";
	$refferalFieldHtml = "";
	$refUserName = "";
	if(isset($_GET["ref"])){
		$user = mysqli_real_escape_string($con,test_input($_GET["ref"]));
		if (ctype_alnum($user) && !empty($user)){
			$refferalFieldHtml = "<input type=\"hidden\" name=\"ref\" value=\"" . $user . "\" />";
		}
	}
	if(isset($_POST["refferal"])){
		$refferal = mysqli_real_escape_string($con,test_input($_POST["refferal"]));
		if (ctype_alnum($refferal) && !empty($refferal)){
			$refUserName = $refferal;
		}
	}
	if(isset($_POST["register"])){
		$username = mysqli_real_escape_string($con,test_input($_POST["username"]));
		$refferal = "";	
		$email = mysqli_real_escape_string($con,test_input($_POST["email"]));
		$pass = mysqli_real_escape_string($con,test_input($_POST["pass"]));
		$pass2 = mysqli_real_escape_string($con,test_input($_POST["pass2"]));
		$sex = mysqli_real_escape_string($con,test_input($_POST["sex"]));
		
		$ref = "";
		if(isset($_POST["ref"])){
			$refferal = mysqli_real_escape_string($con,test_input($_POST["ref"]));
			if (ctype_alnum($refferal) && !empty($refferal)){
				$refQuery = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $refferal . "'");
				if(mysqli_num_rows($refQuery) == 1){
					$refUser = mysqli_fetch_object($refQuery);
					$ref = $refUser->id;
				}
			}
		}
		if(isset($_POST["refferal"])){
			$refferal = mysqli_real_escape_string($con,test_input($_POST["refferal"]));
			if (!ctype_alnum($refferal) || empty($refferal)){
				$gelukt = false;
				$errorRefferal ="<p class=\"red padding_5\">Je gaf een ongeldige gebruikersnaam als refferal op!</p>";
			} else {
				$usQuery = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $refferal . "' AND status='levend' AND ban='0'");
				if(mysqli_num_rows($usQuery) != 1)
				{
					$gelukt = false;
					$errorRefferal ="<p class=\"red padding_5\">Deze speler bestaat niet en kan je niet opgeven als refferal!</p>";
				} else {
					$tt = mysqli_fetch_object($usQuery);
					$ref = $tt->id;
				}
			}
		}
		
		$gelukt = true;
		if (!ctype_alnum($username) || empty($username)){
			$gelukt = false;
			$errorUsername ="<p class=\"red padding_5\">Je gaf een ongeldige gebruikersnaam op!</p>";
		} else {
			$usQuery = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $username . "'");
			if(mysqli_num_rows($usQuery) != 0)
			{
				$gelukt = false;
				$errorUsername ="<p class=\"red padding_5\">Kies een andere gebruikersnaam!</p>";
			} else {
				$usernameHtml = $username;
			}
		}
		
		if (!ctype_alnum($pass) || empty($pass)){
			$gelukt = false;
			$errorPassword = $errorPassword . "<p class=\"red padding_5\">Je gaf een ongeldig wachtwoord op!</p>";
		} else if (!ctype_alnum($pass2) || empty($pass2)){
			$gelukt = false;
			$errorPassword2 = $errorPassword2 . "<p class=\"red padding_5\">Je gaf een ongeldig wachtwoord op!</p>";
		} else if(strlen($pass) <= 6){
			$gelukt = false;
			$errorPassword = $errorPassword . "<p class=\"red padding_5\">Je wachtwoord is niet lang genoeg!</p>";
		}
		if($gelukt && $pass != $pass2){
			$gelukt = false;
			$errorPassword2 = $errorPassword2 . "<p class=\"red padding_5\">De wachtwoorden komen niet overeen!</p>";
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$gelukt = false;
			$errorEmail = "<p class=\"red padding_5\">Je gaf een ongeldig e-mail adres op!</p>";
		} else {
			$emQuery = mysqli_query($con,"SELECT * FROM leden WHERE email='" . $email . "' AND status='levend'");
			if(mysqli_num_rows($emQuery) != 0)
			{
				$gelukt = false;
				$errorEmail = "<p class=\"red padding_5\">Je mag maar 1 actief account hebben per email adres!</p>";
			} else {
				$emailHtml = $email;
			}
		}
		if(empty($sex)){
			$gelukt = false;
			$errorSex = "<p class=\"red padding_5\">Ongeldig geslacht!</p>";
		} else {
			$sex = $sex == 2 ? 0 : 1;
		}			
		$rules = isset($_POST["rules"]) ? 1 : 0;
		if($rules == 0){
			$gelukt = false;
			$errorRules = "<p class=\"red padding_5\">Je moet akkoord gaan met onze algemene voorwaarden en cookie beleidt!</p>";
		}
		
		if($gelukt){
			$code= RandomGenerateCode();
			mysqli_query($con,"INSERT INTO leden (login,wachtwoord,ip,activated,geslacht) VALUES ('" . $username . "','" . sha1(md5($pass)) . "','" . $_SERVER["REMOTE_ADDR"] . "','" . $code . "','" . $sex . "')");
			$newId = mysqli_insert_id($con);
			if(!empty($ref)){
				
				mysqli_query($con,"INSERT INTO leden_refferals (speler,nieuwespeler,datum) VALUES ('" . $ref . "','" . $newId . "',NOW())");
				$ref ="";
			}
			/*if(!empty($refferal)){
				$usQuery = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $refferal . "' AND status='levend' AND ban='0'");
				$refferalUser = mysqli_fetch_object($usQuery);
				
				mysqli_query($con,"INSERT INTO leden_refferals (speler,nieuwespeler,datum) VALUES ('" . $ref . "','" . $newId . "',NOW())");
			}*/
			$ber = "Beste " . $username . ",\r\n";
			$ber = $ber . "Je hebt met succes een account geregistreerd op Assassination, om jou account te activeren moet je de onderstaande code kopieren en ingeven op onze website.\r\n";
			$ber = $ber . "CODE: " . $code . "\r\n";
			$ber = $ber . "Met vriendelijke groeten,\r\n";
			$ber = $ber . "Het Assassination team";
			$headers = 'From: <mail@assassination.be>' . "\r\n";
			
			$formattedMessage = wordwrap($ber,70);
			if(mail($email,"Assassination Registratie",$formattedMessage,$headers)){
				print_bericht("Registratie voltooid","Je hebt met succes een nieuw account aangemaakt. Ga naar je email account en kopieer de code om jou account te activeren. Ga vervolgens in het linkermenu op onze website naar 'Account Activeren' en plak de code die we naar jou verzonden. Kijk ook in je ongewenste folder moest de email niet in je gewone inbox zitten.");
			} else {
				print_bericht("Wachtwoord Vergeten","Er kon geen e-mail verstuurd worden naar dit e-mail adres. Kies een geldig e-mail adres.");
			}
			
		}
	}
	
	if(isset($_POST["activate"])){
		$code = mysqli_real_escape_string($con,test_input($_POST["code"]));
		
		if(ctype_alnum($code))
		{
			$codeQuery = mysqli_query($con,"SELECT * FROM leden WHERE activated='" . $code . "'");
			if(mysqli_num_rows($codeQuery) != 1)
			{
				print_bericht("Account Activeren","Je gaf een ongeldige code op!");
				exit;
			} else {
				$mem = mysqli_fetch_object($codeQuery);
				// Controleren of de speler via refferal heeft geregistreerd
				$refferalQuery = mysqli_query($con,"SELECT * FROM leden_refferals WHERE nieuwespeler='" . $mem->id . "' AND activated='0'");
				if(mysqli_num_rows($refferalQuery) == 1){
					$refferal = mysqli_fetch_object($refferalQuery);
					
					$targetQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $refferal->speler . "' AND status='levend' AND ban='0'");
					if(mysqli_num_rows($targetQuery) == 1){
						$target = mysqli_fetch_object($targetQuery);
						mysqli_query($con,"UPDATE leden SET vipCoins=vipCoins+'1' WHERE id='" . $target->id . "'");
						mysqli_query($con,"UPDATE leden_refferals SET activated='1' WHERE id='" . $refferal->id . "'");
					}
				}
				print_bericht("Account Activeren","Je hebt je account met succes geactiveerd. Je kan nu inloggen.");
				mysqli_query($con,"INSERT into leden_timers (speler) VALUES ('" . $mem->id . "')");
				mysqli_query($con,"UPDATE leden SET activated='',geregistreerd=NOW() WHERE activated='" . $code . "'");
				exit;
			}
		} else {
			print_bericht("Account Activeren","Je gaf een ongeldige code op!");
			exit;
		}
	}		
	if(isset($_POST["resetpass"])){
		$user = mysqli_real_escape_string($con,test_input($_POST["user"]));
		if (!ctype_alnum($user) || empty($user)){
			print_bericht("Wachtwoord Vergeten","Je gaf een ongeldige gebruikersnaam op.");
			exit;
		}
		$email = mysqli_real_escape_string($con,test_input($_POST["email"]));
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			print_bericht("Wachtwoord Vergeten","Je gaf een ongeldig e-mail adres op.");
			exit;
		}
		// Controleren of de gebruikersnaam gelinkt is aan het e-mail adres
		$checkSql = mysqli_query($con,"SELECT * FROM leden WHERE login='" . $user . "' AND email='" . $email . "' AND status='levend' AND ban='0'");
		if(mysqli_num_rows($checkSql) == 1){
			$player = mysqli_fetch_object($checkSql);
			
			$pass = RandomGenerateCode();
			$ber = "Beste " . $player->login . ",\r\n";
			$ber = $ber . "We hebben jou wachtwoord opnieuw ingesteld. Je kan je nu aanmelden met het onderstaand wachtwoord.\r\n";
			$ber = $ber . "Je kan je wachtwoord veranderen door naar je profielinstellingen te gaan en deze te wijzigen.\r\n";
			$ber = $ber . "WACHTWOORD: " . $pass . "\r\n";
			$ber = $ber . "Met vriendelijke groeten,\r\n";
			$ber = $ber . "Het Assassination team";
			$headers = 'From: <mail@assassination.be>' . "\r\n";
			
			$formattedMessage = wordwrap($ber,70);
			if(mail($email,"Assassination Registratie",$formattedMessage,$headers)){
				print_bericht("Wachtwoord Vergeten","We hebben een nieuw wachtwoord opgestuurd naar jou e-mail adres. Controleer ook je ongewenste e-mails.");
			} else {
				print_bericht("Wachtwoord Vergeten","Ongeldig e-mail adres.");
			}
			
			exit;
		} else {
			print_bericht("Wachtwoord Vergeten","Er ging iets mis met het herstellen van je wachtwoord.");
			exit;
		}
	}
	if(isset($_GET["lostpass"]) && !$WEBSITE_DEBUG){
	?>
	<table width="550px" class="inhoud_table">
		<form method="post" action="registreren.php">
		<tr>
			<td class="center">
				<tr>
					<td class="subTitle">Wachtwoord vergeten</td>
				</tr>
				<tr>
					<td class="table_mainTxt padding_5"><label for="userName">Gebruikersnaam</label></td>
				</tr>
				<tr>
					<td class="table_mainTxt"><input id="userName" class="input_form" type="text" maxlength="20" size="30" name="user" /></td>
				</tr>
				<tr>
					<td class="table_mainTxt padding_5"><label for="emailAddress">E-mail</label></td>
				</tr>
				<tr>
					<td class="table_mainTxt"><input id="emailAddress" class="input_form" type="text" maxlength="70" size="30" name="email" /></td>
				</tr>
				<tr>
					<td class="table_mainTxt padding_5"><input type="submit" class="button_form" name="resetpass" value="Nieuw Wachtwoord" /></td>
				</tr>
			</td>
		</tr>
		</form>
	</table>
	
<?php
	exit;
	}
	if(isset($_GET["activate"]) && !$WEBSITE_DEBUG){
?>
	<table width="550px" class="inhoud_table">>
		<form method="post" action="registreren.php">
		<tr>
			<td class="center">
				<tr>
					<td class="subTitle">Account Activeren</td>
				</tr>
				<tr>
					<td class="table_mainTxt padding_5"><label for="inputActivationCode">Activatie code</label></td>
				</tr>
				<tr>
					<td class="table_mainTxt"><input id="inputActivationCode" class="input_form" type="text" maxlength="20" size="30" name="code" /></td>
				</tr>
				<tr>
					<td class="table_mainTxt padding_5"><input type="submit" class="button_form" name="activate" value="Activeren" /></td>
				</tr>
			</td>
		</tr>
		</form>
	</table>
	
<?php } else if(!$WEBSITE_DEBUG){ ?>
	<form method="post" action="registreren.php">
		<table width="550px" class="inhoud_table">
			<?php if(!empty($refferalFieldHtml)){ echo $refferalFieldHtml; } ?>
			<tr>
				<td class="center">
					<tr>
						<td class="subTitle">Nieuw Account Registreren</td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5"><label for="inputUserName">Gebruikersnaam</label></td>
					</tr>
					<tr>
						<td class="table_mainTxt"><input id="inputUserName" class="input_form" type="text" maxlength="20" size="30" name="username" <?php if(!empty($usernameHtml)){ echo "value=\"" . $usernameHtml . "\" "; } ?>/><?php echo $errorUsername; ?></td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5"><label for="inputRefferal">Refferal</label></td>
					</tr>
					<tr>
						<td class="table_mainTxt"><input id="inputRefferal" class="input_form" type="text" maxlength="20" size="30" name="refferal" <?php if(!empty($refUserName)){ echo "value=\"" . $refUserName . "\" "; } ?>/><?php echo $errorRefferal; ?></td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5"><label for="inputEmail">E-mail adres</label></td>
					</tr>
					<tr>
						<td class="table_mainTxt"><input id="inputEmail" class="input_form" type="email" maxlength="60" size="30" name="email" <?php if(!empty($emailHtml)){ echo "value=\"" . $emailHtml . "\" "; } ?>/><?php echo $errorEmail; ?></td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5"><label for="inputPass">Wachtwoord</label></td>
					</tr>
					<tr>
						<td class="table_mainTxt"><input id="inputPass" class="input_form" type="password" maxlength="20" size="30" name="pass" /><?php echo $errorPassword; ?></td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5"><label for="inputPass2">Wachtwoord herhalen</label></td>
					</tr>
					<tr>
						<td class="table_mainTxt"><input id="inputPass2" class="input_form" type="password" maxlength="20" size="30" name="pass2" /><?php echo $errorPassword2; ?></td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5">Geslacht</td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5"><label for="sexMale">Man</label> <input id="sexMale" type="radio" name="sex" value="2" Checked /> <label for="sexFemale">Vrouw</label> <input id="sexFemale" type="radio" name="sex" value="1" /><?php echo $errorSex; ?></td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5"><input id="rules" type="checkbox" name="rules" /><label class="padding_5" for="rules">Ik ga akkoord met de algemene voorwaarden en cookie beleid van Assassination.</label><?php echo $errorRules; ?></td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5"><a href="policy.php" target="_blank">Algemene voorwaarden</a></td>
					</tr>
					<tr>
						<td class="table_mainTxt padding_5"><input type="submit" class="button_form" name="register" value="Registreren" /></td>
					</tr>
				</td>
			</tr>
		</table>
	</form>
<?php } else { ?>
	<table width="550px">
		<tr>
			<td class="center">
				<tr>
					<td class="subTitle">Account Registreren</td>
				</tr>
				<tr>
					<td class="table_mainTxt padding_5">
						<p class="padding_5">Momenteel zijn we <?php echo $WEBSITE_TITLE; ?> nog grondig aan het testen. Je kan dus nog geen account aanmaken op dit ogenblik. Blijf zeker onze website controleren want in de nabije toekomst veranderd dit.</p>
						<p class="padding_5">Alvast bedankt voor je interesse in ons spel en hopelijk tot binnenkort.</p>
						<p class="padding_5 bold">Het <?php echo $WEBSITE_TITLE; ?> Team</p>
					</td>
				</tr>
			</td>
		</tr>
	</table>
<?php } ?>
