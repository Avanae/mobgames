<?php
	include("config.php");
	include("include/functions.php");
	include("SYSTEM_CONFIGURATION.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php 
	if(isset($_SESSION['id'])){
		if($spelerInfo->mini_banner == 1){
		echo "<table width=\"550px\" colspan=\"5\">
		<tr>
			<td colspan='6'><img src='images/headers/stats.jpg' width='550px' height='120px' alt='kerkhof' /></td>
		</tr></table>";
		}
	}
?>
<table width="550px" class="inhoud_table" colspan="6">
	<tr>
		<td class='table_subTitle' colspan='6'>Drugsprijs per land</td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Land</td>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">Wiet</td>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">Hasj</td>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">XTC</td>
		<td class="table_mainTxt outline padding_5" width="15%" colspan="1">Cocaïne</td>
		<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Kogels</td>
	</tr>
	<?php
	$drugsSql = mysqli_query($con,"SELECT * FROM landen");
	while($drugs = mysqli_fetch_object($drugsSql)){
		// De kogelfabriek laden
		$kogelfabriekQuery = mysqli_query($con,"SELECT * FROM casino_objecten WHERE type='4' AND land='" . $drugs->id . "'");
		// Controleren of de kogelfabriek bestaat, zoniet aanmaken en opnieuw ophalen
		if(mysqli_num_rows($kogelfabriekQuery) <= 0){
			mysqli_query($con,"INSERT INTO casino_objecten (type,land) VALUES ('4','" . $drugs->id . "')");
			$kogelfabriekQuery = mysqli_query($con,"SELECT * FROM casino_objecten WHERE type='4' AND land='" . $drugs->id . "'");
		}
		$kogelfabriek = mysqli_fetch_object($kogelfabriekQuery);
		print"
		<tr>
			<td class='table_mainTxt'>" . utf8_encode($drugs->land) . "</td>
			<td class='table_mainTxt'>&euro; " . formatDecimaal($drugs->wietPrijs) . "</td>
			<td class='table_mainTxt'>&euro; " . formatDecimaal($drugs->hasjPrijs) . "</td>
			<td class='table_mainTxt'>&euro; " . formatDecimaal($drugs->xtcPrijs) . "</td>
			<td class='table_mainTxt'>&euro; " . formatDecimaal($drugs->cokePrijs) . "</td>
			<td class='table_mainTxt'>&euro; " . formatDecimaal($kogelfabriek->maxinzet) . "</td>
		</tr>
		";
	}
	?>
</table>