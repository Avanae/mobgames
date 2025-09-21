<?php
	include("config.php");
	include("SYSTEM_CONFIGURATION.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<table width="550px" colspan="1">
	<tr>
		<td class='table_subTitle' colspan='1'>Promoot <?php echo $WEBSITE_TITLE; ?></td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="100%" colspan="1">
			<h2 class="padding_5">Vrienden uitnodigen</h2>
			<p class="padding_5">Vertel je vrienden en familie over <?php echo $WEBSITE_TITLE; ?> en overtuig ze om jou te helpen om je zaken imperium op te bouwen.</p>
			<?php if(isset($_SESSION["id"])){ ?>
				<p class="padding_5">Nodig vrienden uit door ze via volgende link te laten registreren:</p>
				<p class="padding_5"><a href="<?php echo $WEBSITE_URL; ?>registreren.php?ref=<?php echo $spelerInfo->login; ?>"><?php echo $WEBSITE_URL; ?>registreren.php?ref=<?php echo $spelerInfo->login; ?></a></p>
				<p class="padding_5">Eenmaal ze hun account geactiveerd hebben dan ontvang jij 1 VIP coin.</p>
			<?php } else { ?>
				<p class="padding_5">Eenmaal je een account hebt geregistreerd kan je ze via jou persoonlijke link uitnodigen en dan krijg je een bonus per geregistreerd lid.</p>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="100%" colspan="1">	
			<h2 class="padding_5">Stemmen</h2>
			<p class="padding_5">Eenmaal je een account hebt geregistreerd kan je via promoot op onze website stemmen. Je krijgt dan ook een bonus.</p>
		</td>
	</tr>
</table>