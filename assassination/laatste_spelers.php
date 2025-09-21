<?php
include("config.php");
include("SYSTEM_CONFIGURATION.php");
$speler1 = mysqli_query($con,"SELECT * FROM leden WHERE ban='0' AND status='levend' ORDER BY geregistreerd DESC LIMIT 0,10");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<table width="550px" colspan="4" class="inhoud_table">

<?php 
if(isset($_SESSION['id'])){
	if($spelerInfo->mini_banner == 1){
	echo "
	<tr>
		<td colspan='4' class='table_mainTxt'><img src='images/headers/noobs.jpg' width='550px' height='120px' alt='nieuwelingen pic' /></td>
	</tr>";
	}
} else {
	echo "
	<tr>
		<td colspan='4' class='table_mainTxt'><img src='images/headers/noobs.jpg' width='550px' height='120px' alt='nieuwelingen pic' /></td>
	</tr>";
}
?>
	<tr>
			<td class='table_subTitle' colspan='4'>Laatste nieuwe spelers</td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="10%" colspan="1">id</td>
		<td class="table_mainTxt outline padding_5" width="25%" colspan="1">Login</td>
		<td class="table_mainTxt outline padding_5" width="25%" colspan="1">Rang</td>
		<td class="table_mainTxt outline padding_5" width="40%" colspan="1">Datum</td>
	</tr>
<?php
	$i = 1;
	while($spelerList = mysqli_fetch_object($speler1)){
		$rang = $famRangen[$spelerList->familierang];
		$spelerrang = "";
		if($spelerList->rang > count($gameRangen)-1){
			$spelerrang = $gameRangen[count($gameRangen)-1] . " (LVL: " . $spelerList->rang . " )";
		} else {
			$spelerrang = $gameRangen[$spelerList->rang];
		}
		print"
		<tr>
			<td class='table_mainTxt'>" . $i . "</td>
			<td class='table_mainTxt'><a href='speler_profiel.php?x=" . $spelerList->id . "'>" . $spelerList->login . "</a></td>
			<td class='table_mainTxt'>" . $spelerrang . "</td>
			<td class='table_mainTxt'>" . $spelerList->geregistreerd . "</td>
		</tr>
		";
		$i++;
	}
?>
</table>