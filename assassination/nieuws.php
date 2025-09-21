<?php
include("config.php");
include("SYSTEM_CONFIGURATION.php");
include("include/functions.php");
include("check_login.php");
//------------------------------------------------------------
// nog te doen aan speler profiel:
// 1) 
if(isset($_GET['h']) && isset($_GET['y'])){
	$h = mysqli_real_escape_string($con,test_input($_GET['h']));
	$y = mysqli_real_escape_string($con,test_input($_GET['y']));
	if(!is_numeric($h) || !is_numeric($y)){
		echo print_bericht("Nieuws","U hebt geen toegang tot deze pagina.");
		exit;
	}
} else {
	$h = 0; 
	$y = 10;
}
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<table width="550px" colspan="4">
<?php
if(isset($_GET['x'])){
	$topic = trim(mysqli_real_escape_string($con,test_input($_GET['x'])));
	if(!is_numeric($topic)){
		$bericht = "<tr><td class='subTitle' colspan='4'>Het nieuws</td></tr><tr><td class='table_mainTxt' colspan='3'>Er ging iets fout</td></tr>";
	} else {
		$ssss = mysqli_query($con,"SELECT * FROM nieuws WHERE id='" . $topic . "'");
		$topicCheck = mysqli_num_rows($ssss);
		if($topicCheck <= 0){
			$bericht = "<tr><td class='subTitle' colspan='4'>Het nieuws</td></tr><tr><td class='table_mainTxt' colspan='3'>Dit is geen geldig topic</td></tr>";
		}else{
			$topicInfo = mysqli_fetch_object($ssss);
			$bericht = "
			<tr><td class='table_subTitle' colspan='4'>" . $topicInfo->onderwerp . "</td></tr>
			<tr>
				<td class='table_mainTxt' colspan='1' width='10%'>Datum</td>
				<td class='table_mainTxt' colspan='1' width='40%'>" . $topicInfo->datum . "</td>
				<td class='table_mainTxt' colspan='1' width='10%'>Uitgever</td>
				<td class='table_mainTxt' colspan='1' width='40%'>" . $topicInfo->admin . "</td>
			</tr>
			<tr>
				<td class='subTitle' colspan='4' width='100%'>Het artikel</td>
			</tr>
			<tr>
				<td class='table_mainTxt' colspan='4' width='100%'>" . $topicInfo->bericht . "</td>
			</tr>
			";
		}
	}
	echo $bericht;
}
?>
</table>
<?php
$nieuws1 = mysqli_query($con,"SELECT * FROM nieuws ORDER BY datum DESC LIMIT " . $h . "," . $y);
?>
<table width="550px" colspan="3">
<tr>
	<td class="table_subTitle" colspan="3">Het nieuws</td>
</tr>
<tr>
	<td class="subTitle" colspan="1" width="34%">Datum</td>
	<td class="subTitle" colspan="1" width="33%">Onderwerp</td>
	<td class="subTitle" colspan="1" width="33%">Uitgever</td>
</tr>
<?php
if($h == 0){
	$i = 1;
}else{
	$i = $h+1;
}
while($nieuwsInfo = mysqli_fetch_object($nieuws1)){
	print"<tr>
		<td class='table_mainTxt center' colspan='1' width='34%'>" . $nieuwsInfo->datum . "</td>
		<td class='table_mainTxt' colspan='1' width='33%'><a href='nieuws.php?x=" . $nieuwsInfo->id . "'>" . $nieuwsInfo->onderwerp . "</td>
		<td class='table_mainTxt center' colspan='1' width='33%'>" . $nieuwsInfo->admin . "</td>
	</tr>";
	$i++;
}
if($h == 0 || $h == 1 && $i = 10){
	$volgende = 10;
	$y = $volgende+10;
	print "<tr>
		<td class='table_mainTxt' colspan='5'><a href='nieuws.php?h=" . $volgende . "&y=" . $y . "'>Volgende pagina</td>
	</tr>";
} elseif($h == 1 && $i < 10){

}else if($h != 1 && ($i-1) <= $h){
	// volgende pagina
	$p = mysqli_real_escape_string($con,test_input($_GET['h']));
	$l = mysqli_real_escape_string($con,test_input($_GET['y']));
	if(!is_numeric($p) || !is_numeric($h)){
		echo print_bericht("Nieuws","Je hebt geen toegang tot deze pagina.");
		exit;
	}
	$h = ($p+10);
	$y = ($l+10);
	// vorige pagina
	$vorige =($p-10);
	$vorige2 = ($l-10);
	print "<tr>
		<td class='table_mainTxt' colspan='5'><a href='nieuws.php?h=" . $vorige . "&y=" . $vorige2 . "'>Vorige Pagina</a> | <a href='nieuws.php?h=" . $h . "&y=" . $y . "'>Volgende pagina</a></td>
	</tr>";
} else if($h != 0 && $h < $i){
	$p = mysqli_real_escape_string($con,test_input($_GET['h']));
	$l = mysqli_real_escape_string($con,test_input($_GET['y']));
	if(!is_numeric($p) || !is_numeric($h)){
		echo print_bericht("Nieuws","Je hebt geen toegang tot deze pagina.");
		exit;
	}
	$vorige =($p-10);
	
	$vorige2 = ($l-10);
	print "<tr>
		<td class='table_mainTxt' colspan='5'><a href='nieuws.php?h=" . $vorige . "&y=" . $vorige2 . "'>Vorige pagina</td>
	</tr>";
}
?>
</table>