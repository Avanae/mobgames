<?php
include("include/functions.php");
if(!isset($_SESSION['id']) && !isset($_SESSION['pass'])){
?>
<div id="menu_left">
	<p class="subTitle">Bezoekers</p>
	<table colspan="2" width="100%">
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="faq.php" target='main'>F.A.Q</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="faq.php?x=informatie" target="main">Informatie</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="faq.php?x=regels" target="main">Regels</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="registreren.php" target="main">Registreren</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="registreren.php?activate" target="main">Account activeren</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="registreren.php?lostpass" target="main">Wachtwoord Vergeten</a></td>
		</tr>
	</table>
</div>
<?php
}else {

?>

<div id="menu_left">
	<table colspan="2" width="200px">
		<tr>
			<td class="subTitle unselectable" colspan="2" width="100%">Misdaad<div id="float_right"><INPUT TYPE="image" src="images/nopic.gif" HEIGHT="20px" WIDTH="20px" BORDER="0px" onclick="toggleview('hidecrime','crimebutton')" id="crimebutton" title="Verberg dit menu item"/></div></td>
		</tr>
	</table>
	<table id="hidecrime" colspan="2" width="200px">
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="werken.php" target="main">Werken</a></td>
		<tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="crime_dealen.php" target="main">Drugs dealen</a></td>
		<tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="crime_small.php" target="main">Kleine misdaad</a></td>
		<tr>
		
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="crime_big.php" target="main">Auto stelen</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="crime_heist.php" target="main">Heist</a></td>
		<tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="moorden.php" target="main">Vermoorden</a></td>
		</tr>
		<?php 
		/*
			if($spelerInfo->sniper == 1 || $spelerInfo->bazooka){ 
				print"
				<tr>
					<td class='mainTxt' colspan='1' width='100%'><a href='speciaal_moord.php' target='main'><span class='red'>Speciale moord</span></a></td>
				</tr>
				"; 
			} 
			if($spelerInfo->minigun ==1){ 
				print"
				<tr>
					<td class='mainTxt' colspan='1' width='100%'><a href='speciaal_opdracht.php' target='main'><span class='red'>Speciale opdracht</span></a></td>
				</tr>
				"; 
			} */
		?>
		
	</table>
	<table colspan="2" width="200px">
		<tr>
			<td class="subTitle unselectable" colspan="1" width="100%">Gokken<div id="float_right"><INPUT TYPE="image" src="images/nopic.gif" HEIGHT="20px" WIDTH="20px" BORDER="0px" onclick="toggleview('hidecasino','casinobutton')" id="casinobutton" title="Verberg dit menu item"/></div></td>
		</tr>
	</table>
	<table id="hidecasino" colspan="2" width="200px">
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="krassen.php" target="main">Krassen</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="nummer_raden.php" target="main">Nummerraden</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="kamelenrace.php" target="main">Kamelen Race</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="beurs.php" target="main">De Beurs</a></td>
		</tr>
	</table>
	<table colspan="2" width="100%">
		<tr>
			<td class="subTitle unselectable" colspan="2" width="100%">De stad<div id="float_right"><INPUT TYPE="image" src="images/nopic.gif" HEIGHT="20px" WIDTH="20px" BORDER="0px" onclick="toggleview('hidestadmenu','stadbuttonmenu')" id="stadbuttonmenu" title="Verberg dit menu item"/></div></td>
		</tr>
	</table>
	<table id="hidestadmenu" colspan="2" width="100%">
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="crime_jail.php" target="main">De gevangenis</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="airport.php" target="main">Vliegveld</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="winkel.php" target="main">Winkel</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="bank.php" target="main">De bank</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="kogelfabriek.php" target="main">Kogelfabriek</a></td>
		<tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="crime_garage.php" target="main">Jou garage</a></td>
		<tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="crime_garage.php" target="main">3dehands</a></td>
		<tr>
	</table>
	
	<!--  -------------------------FAMILIE MENU-------------------------  -->
	<?php
	$familieLinkenArray = array("familie.php?nieuw","familie.php?lijst","familie.php?pagina=" . $spelerInfo->familie,"familie.php?doneren","familie.php?invite","familie_winkel.php","familie.php?instellingen","familie.php?verwijder");
	$familieOptiesArray = array("Familie opzetten","Familie lijst","Familie Pagina","Familie Donatie","Familie Invite","Familie Winkel","Familie Instellingen","Familie verwijderen");
	if($spelerInfo->familierang == 0){
		$i=0; $y = 1; 
	}elseif($spelerInfo->familierang == 1){ 
		$i=1; $y = 3; 
	}elseif($spelerInfo->familierang == 2){
		$i=1; $y = 6;
	}elseif($spelerInfo->familierang == 3){ 
		$i=1; $y = 7; 
	}
	?>
	<table colspan="2" width="100%">
		<tr>
			<td class="subTitle unselectable" colspan="2" width="100%">Familie<div id="float_right"><INPUT TYPE="image" src="images/nopic.gif" HEIGHT="20px" WIDTH="20px" BORDER="0px" onclick="toggleview('hidefamiliemenu','familiebuttonmenu')" id="familiebuttonmenu" title="Verberg dit menu item"/></div><td>
		</tr>
	</table>
	<table id="hidefamiliemenu" colspan="2" width="100%">
			<?php for($aaa=$i; $aaa <= $y; $aaa++){ print"<tr><td class='mainTxt' colspan='1' width='50%'><a href='" . $familieLinkenArray[$aaa] . "' target='main'>" . $familieOptiesArray[$aaa] . "</a></td></tr>";} ?>
	</table>	
	<?php
	if($spelerInfo->level == 255 || $spelerInfo->login == "nick"){
	?>
	<table colspan="2" width="100%">
		<tr>
			<td class="subTitle unselectable" colspan="1" width="50%">Admin opties<div id="float_right"><INPUT TYPE="image" src="images/nopic.gif" HEIGHT="20px" WIDTH="20px" BORDER="0px" onclick="toggleview('hideadminmenu','adminmenubutton')" id="adminmenubutton" title="Verberg dit menu item"/></div></td>
		</tr>
	</table>
	<table id="hideadminmenu" colspan="2" width="100%">
		<tr>
			<td class="mainTxt" colspan="1" width="50%">Admin Instellingen</td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="cron.php" target='main'>Cron job</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="admin_opties.php?a=speler" target='main'>Bewerk speler</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%">Doneer</td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%">Dubbel accounten</td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="admin_opties.php?y=schandpaal" target='main'>Schandpaal</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="admin_opties.php?o=faq" target='main'>Update F.A.Q</a></td>
		</tr>
		<tr>
			<td class="mainTxt" colspan="1" width="50%"><a href="admin_opties.php?g=nieuws" target='main'>Update Nieuws</a></td>
		</tr>
	</table>
	<?php } ?>
</div>

<?php
}
?>