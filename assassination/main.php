<?php
	include("config.php");
	include("SYSTEM_CONFIGURATION.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php if(!isset($_SESSION["id"])){ ?>
	<table width="550px" colspan="1">
		<tr>
			<td class='table_subTitle' colspan='1'>Welkom bij <?php echo $WEBSITE_TITLE; ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt outline padding_5" width="100%" colspan="1">
				<p class="padding_5"><?php echo $WEBSITE_TITLE; ?> is een text-based gangster spel waar jij vecht voor dominantie in de onderwereld.</p>
				<p class="padding_5">Door middel van misdaden en intimidatie werk je je naar de top van de meest machtigste gangsters. Handel in illegale goederen en gebruik het geld om je eigen familie op te zetten.</p>
				<p class="padding_5">Met je eigen mafia familie kan je spelers uitnodigen en samen domineren om de machtigste familie in de onderwereld te worden!</p>
				<p class="padding_5">Ben jij klaar om deze uitdaging aan te gaan? <a href="registreren.php">Maak dan hier een account, als je durft...</a></p>
			</td>
		</tr>
	</table>
<?php } else { ?>
	<table width="550px" colspan="1">
		<tr>
			<td class='table_subTitle' colspan='1'>Welkom terug</td>
		</tr>
		<tr>
			<td class="table_mainTxt outline padding_5" width="100%" colspan="1">
				<p class="padding_5">Vergeet niet om op de promoot pagina te stemmen voor onze website.</p>
			</td>
		</tr>
	</table>
<?php } ?>