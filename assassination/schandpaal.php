<?php
	include("config.php");
	include("include/functions.php");
	include("check_login.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	if(isset($_GET['x']) && isset($_GET['y'])){
		$x = mysqli_real_escape_string($con,test_input($_GET['x']));
		$y = mysqli_real_escape_string($con,test_input($_GET['y']));
		if(!is_numeric($x) || !is_numeric($y)){
			echo "";
			exit;
		}
	} else {
		$x = 0; 
		$y=10;
	}
	$speler1 = mysqli_query($con,"SELECT * FROM schandpaal ORDER BY datum DESC LIMIT " . $x . "," . $y . "");
?>
<table width="550px" class="inhoud_table" colspan="4">
<?php if($spelerInfo->mini_banner == 1){ ?>
	<tr>
		<td colspan="4"><img src="images/schandpaal.jpg" alt="schandpaal" width="100%" height="200px" /></td>
	</tr>
<?php } ?>
<tr>
	<td class="table_mainTxt outline" colspan="4">Wanneer een speler op de schandpaal staat kan je een steen naar deze speler gooien voor &euro; 10.000. Wanneer deze speler zijn/haar leven op is win je zijn geld, kogels en eventuele eigendommen.</td>
</tr>
<tr>
	<td class='table_subTitle' colspan='4'>Lozers</td>
</tr>
<tr>
	<td class="table_mainTxt outline" width="20%" colspan="1">Login</td>
	<td class="table_mainTxt outline" width="20%" colspan="1">Datum</td>
	<td class="table_mainTxt outline" width="40%" colspan="1">Reden</td>
	<td class="table_mainTxt" width="20%" colspan="1"></td>
</tr>
<?php
if($x == 0){
	$i = 1;
}else{
	$i = $x+1;
}
while($spelerList = mysqli_fetch_object($speler1)){
	$sssspeee = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $spelerList->speler . "'");
	$sssp = mysqli_fetch_object($sssspeee);
	if($sssp->status == "levend"){
		$statu = "<a href='schandpaal.php?x=smijt&naam=" . $spelerList->speler . "'>GOOI STEEN</a>";
	} elseif($sssp->status == "dood"){
		$statu = "<font color='red' class='bold'>DOOD</font>";
	}
	echo "<tr>
		<td class='table_mainTxt'><a href='speler_profiel.php?x=" . $sssp->id . "'>" . $sssp->login . "</a></td>
		<td class='table_mainTxt'>" . date("d-m-Y",strtotime($spelerList->datum)) . "</td>
		<td class='table_mainTxt'>" . $spelerList->reden . "</td>		
		<td class='table_mainTxt'>" . $statu . "</td>
		
	</tr>";
	$i++;
}
if($x == 0 && $i > 10){
	$volgende = 10;
	$y = $volgende+10;
	echo "<tr>
		<td class='table_mainTxt' colspan='4'><a href='schandpaal.php?x=" . $volgende . "&y=" . $y . "'>Volgende pagina</td>
	</tr>";
} elseif($x == 0 && $i < 10){

} else if($x != 0 && ($i-1) <= $x){
	// volgende pagina
	$p = mysqli_real_escape_string($con,test_input($_GET['x']));
	$l = mysqli_real_escape_string($con,test_input($_GET['y']));
	if(!is_numeric($p) || !is_numeric($x)){
		echo"";
		exit;
	}
	$x = ($p+10);
	$y = ($l+10);
	// vorige pagina
	$vorige =($p-10);
	$vorige2 = ($l-10);
	echo "<tr>
		<td class='table_mainTxt' colspan='4'><a href='schandpaal.php?x=" . $vorige . "&y=" . $vorige2 . "'>Vorige Pagina</a> | <a href='schandpaal.php?x=" . $x . "&y=" . $y . "'>Volgende pagina</a></td>
	</tr>";
} else if($x != 0 && $x < $i){
	$p = mysqli_real_escape_string($con,test_input($_GET['x']));
	$l = mysqli_real_escape_string($con,test_input($_GET['y']));
	if(!is_numeric($p) || !is_numeric($x)){
		echo "";
		exit;
	}
	$vorige =($p-10);
	$vorige2 = ($l-10);
	echo "<tr>
		<td class='table_mainTxt' colspan='4'><a href='schandpaal.php?x=" . $vorige . "&y=" . $vorige2 . "'>Vorige pagina</td>
	</tr>";
}
?>
</table>
<?php
if(isset($_GET['x'])=="smijt" && isset($_GET['naam']) ){
	$naamNerd = mysqli_real_escape_String($con,test_input($_GET['naam']));	
	$nneee = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $naamNerd . "' AND ban='1'");
	
	if(mysqli_num_rows($nneee) == 1)
	{
		$nerd = mysqli_fetch_object($nneee);
		$cash = formatDecimaal($nerd->cash);
		if($spelerInfo->cash >= 10000){
			if($nerd->status == "levend"){
				$random = rand(1,5);
				if($nerd->leven-$random <= 0){
					mysqli_query($con,"UPDATE leden SET cash=cash+'" . $nerd->cash . "',kogels=kogels+'" . $nerd->kogels . "' WHERE id='" . $spelerInfo->id . "'");
					mysqli_query($con,"UPDATE leden SET status='dood', leven='0', kogels='0' WHERE id='" . $nerd->id . "'");
					
					print_bericht("Schandpaal","Je hebt een steen gegooid voor &euro; 10.000 naar " . $nerd->login . " en hij ging dood! Je hebt &euro; " . $cash . " gekregen en " . $nerd->kogels . " kogels!");
				} else {
					mysqli_query($con,"UPDATE leden SET leven=leven-'" . $random . "' WHERE id='" . $nerd->id . "'");
					
					print_bericht("Schandpaal","Je hebt een steen gegooid voor &euro; 10.000 naar " . $nerd->login . " maar hij overleefde het. Deze speler geraakte % " . $random . " leven kwijt!");
				}
				mysqli_query($con,"UPDATE leden SET cash=cash-'10000' WHERE id='" . $spelerInfo->id . "'");
			
			} elseif($nerd->status == "dood") {
				print_bericht("Schandpaal",$nerd->login . " is al dood, je bent te laat!");
			}
		} else {
			print_bericht("Schandpaal","Om een steen te gooien heb je &euro; 10.000 nodig.");				
		}
	} else {
		print_bericht("Schandpaal","Deze speler staat niet op de schandpaal.");
	}
}
?>