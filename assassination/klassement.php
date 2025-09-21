<?php
include("config.php");
include("SYSTEM_CONFIGURATION.php");
$speler1 = mysqli_query($con,"SELECT * FROM leden WHERE leven>='1' AND status='levend' ORDER BY bank DESC LIMIT 0,10 ");
$speler2 = mysqli_query($con,"SELECT * FROM leden WHERE leven>='1' AND status='levend' ORDER BY kogels DESC LIMIT 0,10 ");
$speler3 = mysqli_query($con,"SELECT * FROM leden WHERE leven>='1' AND status='levend' ORDER BY kills DESC LIMIT 0,10 ");
$speler4 = mysqli_query($con,"SELECT DISTINCT  eigenaar FROM kavels WHERE eigenaar!='Geen' ORDER BY eigenaar DESC LIMIT 0,10");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<table width="550px">
	<?php if(!isset($_SESSION["id"]) || $spelerInfo->mini_banner == 1){ ?>
		<tr>
			<td><img src="images/headers/stats.jpg" width='550px' height='120px' alt="statistieken pic" /></td>
		</tr>
	<?php } ?>
</table>
<?php
$titelArray = array("Top 10 rijkste spelers","Top 10 meeste kogels","Top 10 moordenaars");
$inhoudArray = array("Bank geld","Kogels","Moorden");
$varArray = array("bank","kogels","kills");
$queryArray = array($speler1,$speler2,$speler3);
for($xxx=0; $xxx < count($titelArray); $xxx++){
	?>
	<table id="float_left" class="inhoud_table" width="275px" colspan="5">
	<tr>
	<td class="table_subTitle_mini" width="100%" colspan="3"><?php echo $titelArray[$xxx]; ?></td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="5%" colspan="1">Plaats</td>
		<td class="table_mainTxt outline padding_5" width="30%" colspan="1">Login</td>
		<td class="table_mainTxt outline padding_5" width="65%" colspan="1"><?php echo $inhoudArray[$xxx]; ?></td>
	</tr>
	<?php
	$i = 1;
	while($spelerList = mysqli_fetch_object($queryArray[$xxx])){
		if($xxx == 0){ $eurote = "&euro;"; } else { $eurote = ""; }
		$bank = number_format($spelerList->{$varArray[$xxx]},0,",",".");
		echo "
		<tr>
			<td class='table_mainTxt outline center'>" . $i . "</td>
			<td class='table_mainTxt'><a href='speler_profiel.php?x=" . $spelerList->id . "'>" . $spelerList->login . "</a></td>
			<td class='table_mainTxt'>" . ($xxx >= 1 ? "" : "&euro;") . " " . $bank . "</td>
		</tr>";
		$i++;
	}
}
?>
</table>