<?php
include("config.php");
include("include/functions.php");
include("SYSTEM_CONFIGURATION.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<table width="550px" class="inhoud_table" colspan="5">
<?php 
if(isset($_SESSION['id'])){
	if($spelerInfo->mini_banner == 1){
		echo "
		<tr>
			<td colspan='6'><img src='images/headers/stats.jpg' width='550px' height='120px' alt='statistieken' /></td>
		</tr>";
	}
} else {
	echo "
	<tr>
		<td colspan='6'><img src='images/headers/stats.jpg' width='550px' height='120px' alt='statistieken' /></td>
	</tr>";
}
?>
<?php
	$casinoSql = mysqli_query($con,"SELECT * FROM casino_types");
	while($casinos = mysqli_fetch_object($casinoSql)){
?>
	<tr>
		<td class='table_subTitle' colspan='6'><?php echo $casinos->naam; ?></td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Land</td>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">Eigenaar</td>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">Minimum</td>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">Maximum</td>
		<td class="table_mainTxt outline padding_5" width="25%" colspan="1">Omzet</td>
	</tr>
<?php
	$objectSql = mysqli_query($con,"SELECT * FROM casino_objecten WHERE type='" . $casinos->id . "'");
	while($object = mysqli_fetch_object($objectSql)){
		// Land van het object ophalen
		$landSql = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $object->land . "'");
		$land = mysqli_fetch_object($landSql);
		
		$eigenaarTxt = "Geen";
		// Eigenaar van het object ophalen
		if($object->eigenaar != 0)
		{
			$eigenaarSql = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $object->eigenaar . "'");
			$eigenaar = mysqli_fetch_object($eigenaarSql);
			$eigenaarTxt = "<a href=\"speler_profiel.php?x=" . $eigenaar->id . "\">" . $eigenaar->login . "</a>";
		}
		$omzet = formatDecimaal($object->omzet);
		if($object->omzet < 0){
			$omzet = "<span class='red'>" . formatDecimaal($object->omzet) . "</span>";
		}
		print"
		<tr>
			<td class='table_mainTxt'>" . utf8_encode($land->land) . "</td>
			<td class='table_mainTxt'>" . $eigenaarTxt . "</td>
			<td class='table_mainTxt'>&euro; " . formatDecimaal($object->mininzet) . "</td>
			<td class='table_mainTxt'>&euro; " . formatDecimaal($object->maxinzet) . "</td>
			<td class='table_mainTxt'>&euro; " . $omzet . "</td>
		</tr>
		";
	}
}
?>
</table>