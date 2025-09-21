<?php
	include("config.php");
	include("SYSTEM_CONFIGURATION.php");
	include("include/functions.php");
	include("check_login.php");
//------------------------------------------------------------
// nog te doen aan speler lijst:
// 1) speler zoeken
// 2) bazen en admins andere kleur weergeven
// 3) geld status weergeven
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
$sort = "";

if(isset($_GET['x']) && isset($_GET['y'])){
	$x = mysqli_real_escape_string($con,test_input($_GET['x']));
	$y = mysqli_real_escape_string($con,test_input($_GET['y']));
	if(!is_numeric($x) || !is_numeric($y)){
		exit;
	}
} else {
	$x = 0; 
	$y=10;
}
if(isset($_GET["sort"])){
	$sorteren = mysqli_real_escape_string($con,test_input($_GET["sort"]));
	
	if($sorteren == "rang" || $sorteren == "login" || $sorteren == "familierang" || $sorteren == "familie"){
		switch($sorteren){
			case "login":
				$speler1 = mysqli_query($con,"SELECT * FROM leden WHERE leven>='1' AND status='levend' AND ban='0' ORDER BY " . $sorteren . " ASC LIMIT " . $x . "," . $y . " ");
				$sort = "&sort=login";
			break;
			case "familierang":
				$speler1 = mysqli_query($con,"SELECT * FROM leden WHERE leven>='1' AND status='levend' AND ban='0' ORDER BY " . $sorteren . " DESC LIMIT " . $x . "," . $y . " ");
				$sort = "&sort=familierang";
			break;
			
			default:
				$speler1 = mysqli_query($con,"SELECT * FROM leden WHERE leven>='1' AND status='levend' AND ban='0' ORDER BY " . $sorteren . " DESC LIMIT " . $x . "," . $y . " ");
				$sort = "&sort=" . $sorteren;
			break;
		}
		
	} else {
		$speler1 = mysqli_query($con,"SELECT * FROM leden WHERE leven>='1' AND status='levend' AND ban='0' ORDER BY rang DESC, rangvordering DESC,id LIMIT " . $x . "," . $y);
	}
} else{
	$speler1 = mysqli_query($con,"SELECT * FROM leden WHERE leven>='1' AND status='levend' AND ban='0' ORDER BY rang DESC, rangvordering DESC,id LIMIT " . $x . "," . $y);
}
?>
<table width="550px" class="inhoud_table" colspan="5">
<tr>
		<td class='table_subTitle' colspan='5'>Speler Lijst</td>
</tr>
<tr>
		<td class='table_mainTxt padding_5' colspan='5'>Hier kan je de gegevens zien van de spelers die op assassination geregistreerd zijn.</td>
</tr>
<tr>
		<td class='table_subTitle' colspan='5'>SPELERS</td>
</tr>
<tr>
	<td class="table_mainTxt center bold outline padding_5" width="10%" colspan="1"><a href="speler_lijst.php?x=<?php echo $x ?>&y=<?php echo $y ?>&sort=plaats">Plaats</a></td>
	<td class="table_mainTxt center bold outline padding_5" width="20%" colspan="1"><a href="speler_lijst.php?x=<?php echo $x ?>&y=<?php echo $y ?>&sort=login">Login</a></td>
	<td class="table_mainTxt center bold outline padding_5" width="25%" colspan="1"><a href="speler_lijst.php?x=<?php echo $x ?>&y=<?php echo $y ?>&sort=rang">Rang</a></td>
	<td class="table_mainTxt center bold outline padding_5" width="20%" colspan="1"><a href="speler_lijst.php?x=<?php echo $x ?>&y=<?php echo $y ?>&sort=familie">Familie</a></td>
	<td class="table_mainTxt center bold outline padding_5" width="25%" colspan="1"><a href="speler_lijst.php?x=<?php echo $x ?>&y=<?php echo $y ?>&sort=familierang">Familie status</a></td>
</tr>
<?php
if($x == 0){
	$i = 1;
}else{
	$i = $x+1;
}
while($spelerList = mysqli_fetch_object($speler1)){
	$rang = $famRangen[$spelerList->familierang];
	
	//$spelerrang = $gameRangen[$spelerList->rang];
	
	$spelerrang = formatDecimaal($spelerList->rang);
	if($spelerList->rang >= count($gameRangen)){
		$spelerrang = $gameRangen[count($gameRangen)-1] . " ( LVL: " . $spelerList->rang . ")";
	} else {
		$spelerrang = $gameRangen[$spelerList->rang];
	}
	if($spelerList->familie == 0){
		$familie = "Geen";
	} else {
		$famQuery = mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerList->familie . "'");
		if(mysqli_num_rows($famQuery) == 1){
			$fam = mysqli_fetch_object($famQuery);
			$familie = "<a href='familie.php?pagina=" . $spelerList->familie . "'>" . $fam->naam . "</a>";
		} else {
			$familie = "UNKNOWN";
		}
	}
	print"
	<tr>
		<td class='table_mainTxt center outline'>" . $i . "</td>
		<td class='table_mainTxt padding_left'><a href='speler_profiel.php?x=" . $spelerList->id . "'>" . $spelerList->login . "</a></td>
		<td class='table_mainTxt padding_left'>" . $spelerrang . "</td>
		<td class='table_mainTxt padding_left'>" . $familie . "</td>
		<td class='table_mainTxt padding_left'>" . $rang . "</td>
	</tr>
	";
	$i++;
}
if($x == 0 && $i > 10){
	$volgende = 10;
	$y = $volgende+10;
	print "<tr>
		<td class='table_mainTxt center outline bold' colspan='5'><a href='speler_lijst.php?x=" . $volgende . "&y=" . $y . $sort . "'>Volgende pagina</td>
	</tr>";
} elseif($x == 0 && $i < 10){

}else if($x != 0 && ($i-1) <= $x){
	// volgende pagina
	$p = mysqli_real_escape_string($con,test_input($_GET['x']));
	$l = mysqli_real_escape_string($con,test_input($_GET['y']));
	if(!is_numeric($p) || !is_numeric($x)){
		
		exit;
	}
	$x = ($p+10);
	$y = ($l+10);
	// vorige pagina
	$vorige =($p-10);
	$vorige2 = ($l-10);
	print "<tr>
		<td class='table_mainTxt center outline bold' colspan='5'><a href='speler_lijst.php?x=" . $vorige . "&y=" . $vorige2 . "'>Vorige Pagina</a> | <a href='speler_lijst.php?x=" . $x . "&y=" . $y . "'>Volgende pagina</a></td>
	</tr>";
} else if($x != 0 && $x < $i){
	$p = mysqli_real_escape_string($con,test_input($_GET['x']));
	$l = mysqli_real_escape_string($con,test_input($_GET['y']));
	if(!is_numeric($p) || !is_numeric($x)){
		
		exit;
	}
	$vorige =($p-10);
	$vorige2 = ($l-10);
	print "<tr>
		<td class='table_mainTxt center outline bold' colspan='5'><a href='speler_lijst.php?x=" . $vorige . "&y=" . $vorige2 . "'>Vorige pagina</td>
	</tr>";
}
?>
</table>