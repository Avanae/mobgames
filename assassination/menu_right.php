<?php
	if(!isset($_SESSION['id']) && !isset($_SESSION['pass'])){
?>
	<p class="subTitle">Inloggen</p>
	<form method="post" action="login.php">
		<table colspan="2" width="200px">
			<tr>
				<td class="mainTxt padding_5" colspan="1" width="50%"><label for="login_user">Gebruikersnaam</label</td>
			</tr>
			<tr>
				<td class="mainTxt" colspan="1" width="50%"><input type="text" id="login_user" class="input_form padding_5" size="25" maxlength="20" name="user" value=""/></td>
			</tr>
			<tr>
				<td class="mainTxt padding_5" colspan="1" width="50%"><label for="login_pass">Wachtwoord</label></td>
			</tr>
			<tr>
				<td class="mainTxt" colspan="1" width="50%"><input type="password" id="login_pass" class="input_form padding_5" size="25" maxlength="20" name="pass" value="" /></td>
			</tr>
			<tr>
				<td class="mainTxt" colspan="2" width="100%"><input type="submit" class="button_form" name="login" value="Inloggen" /></td>
			</tr>
		</table>
	</form>
<?php
}else {
	include("javascript/framewerk.php");
	$landensql = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $spelerInfo->land . "'");
	$locatie = mysqli_fetch_object($landensql);
?>
<script>
	var timer = setInterval(UpdateGps,60000);
	
	function UpdateGps(){
		ExeGetReq("JsCalls.php?PlayerGpsStats",UpdateGpsUi);
	}
	function UpdateGpsUi(res){
		var details = JSON.parse(res);
		
		document.getElementById("gpsCash").innerHTML = details[0];
		document.getElementById("gpsBank").innerHTML = details[1];
		document.getElementById("gpsLand").innerHTML = details[3];
		CreateStatusBar(details[2]);
	}
	
	
</script>
<link href="stylesheets/gps_stijl.css" type="text/css" rel="stylesheet" />
<div id="gps" style="background-image: url(images/system/gps.gif); height: 155px; width: 200px; background-size:100%; background-repeat:no-repeat;">
	<table class="padding_25" colspan="2" width="100%">
		<tr>
			<td class="" colspan="2" width="100%">
				<a href="airport.php">
					<img src="images/system/icons/global_icon.gif" width="20px" alt="wereldbol" class="unselectable align_middle" /><span id="gpsLand"><?php print utf8_encode($locatie->land); ?></span>	
				</a>
			</td>
		</tr>
		<tr>
			<td class="padding_left_25 bold unselectable" colspan="1" width="30%"><span class="red">CASH</span></td>
			<td class="" colspan="1" width="70%"><span id="gpsCash" class="limegreen">&euro; <?php $cashGeld = formatDecimaal($spelerInfo->cash); echo $cashGeld;  ?></span></td>
		</tr>
		<tr>
			<td class="bold  unselectable" colspan="1" width="30%"><span class="red">BANK</span></td>
			<td class="" colspan="1" width="70%"><span id="gpsBank" class="limegreen">&euro; <?php $bankGeld = formatDecimaal($spelerInfo->bank); echo $bankGeld;  ?></span></td>
		</tr>
		<tr>
			<td class="bold unselectable" colspan="1" width="30%"><span class="red">LEVEN</span></td>
			<td class="" colspan="1" width="70%"><span id="gpsLeven">
				<?php printStatusBar($spelerInfo->leven);  ?></span>
					
			</td>
		</tr>
	</table>
	
</div>

<div id="menu_left">
	
	<table colspan="2" width="200px">
		<tr>
			<td class="subTitle unselectable" colspan="1" width="50%">Eigendommen<div id="float_right"><INPUT TYPE="image" src="images/nopic.gif" HEIGHT="20px" WIDTH="20px" BORDER="0px" onclick="toggleview('eigendommen','eigendommenbutton')" id="eigendommenbutton" title="Verberg dit menu item"/></div></td>
		</tr>
	</table>
	<table id="eigendommen" colspan="1" width="200px">
		<tr>
			<td class="mainTxt padding_left" colspan="1" width="50%"><a href="villa.php" target="main">Villa</a></td>
		</tr>
		<tr>
			<td class="mainTxt padding_left" colspan="1" width="50%"><a href="kavels.php?overview" target="main">Kavels</a></td>
		</tr>
		<tr>
			<td class="mainTxt padding_left" colspan="1" width="50%"><a href="kavels.php?owner" target="main">Jou kavels</a></td>
		</tr>
	</table>
	<?php 
	/*
	<table colspan="2" width="200px">
		<tr>
			<td class="subTitle unselectable" colspan="1" width="50%">Capo regimes<div id="float_right"><INPUT TYPE="image" src="images/nopic.gif" HEIGHT="20px" WIDTH="20px" BORDER="0px" onclick="toggleview('capoeregime','capoeregime')" id="capobutton" title="Verberg dit menu item"/></div></td>
		</tr>
	</table>
	
	<table id="capoeregime" colspan="1" width="200px">
		<tr>
			<td class="mainTxt padding_left" colspan="1" width="50%"><a href="capo.php?overzicht" target="main">Overzicht</a></td>
		</tr>

		<tr>
			<td class="mainTxt padding_left" colspan="1" width="50%"><a href="capo.php?beheren" target="main">Beheren</a></td>
		</tr>
	</table>
	*/
	?>
	<div id="nieuws_menu">
		<?php
		$sql = "SELECT * FROM nieuws WHERE type='1' ORDER BY datum DESC limit 0,10";
		$ttt = mysqli_query($con,$sql);
		$nn = mysqli_num_rows($ttt);
		?>
		<table colspan="2" width="200px">
			<tr>
				<td class="subTitle unselectable" colspan="1" width="200px">NIEUWS<div id="float_right"><INPUT TYPE="image" src="images/nopic.gif" HEIGHT="20px" WIDTH="20px" BORDER="0px" onclick="toggleview('nieuwscasino','nieuwsbutton')" id="nieuwsbutton" title="Verberg dit menu item"/></div></td>
			</tr>
		</table>
		<table id="nieuwscasino" colspan="2" width="200px">
			<?php
			if($nn <= 0){
			echo "<tr>
					<td class='mainTxt red' colspan='1' width='100%'>Geen onderwerpen</td>
				</tr>";
			} else{
				while($topicInfo = mysqli_fetch_object($ttt)){
				$date = date_create($topicInfo->datum);
				$datum = date_format($date, 'd/m/y');
				echo "<tr>
					<td class='bg_black' colspan='1' width='100%'><a href='nieuws.php?x=" . $topicInfo->id . "' target='main' class='bold'>" . $datum . " " .  $topicInfo->onderwerp . "</a></td>
				</tr>";
				}
			}
			?>

		</table>
	</div>
</div>
<?php
}
?>