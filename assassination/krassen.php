<?php
	include("config.php");
	include("include/functions.php");
	
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php 
	include("check_login.php");
	include("check_jail.php");	
if($spelerInfo->mini_banner == 1){ ?>
<tr>
	<td colspan="4" class="table_mainTxt"><img src="images/headers/krasloten.gif" width="550px" height="120px" alt="moord pic" /></td>
</tr>

<?php } ?>
<?php
	$gn1 = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(krassen) AS krassen,0 FROM leden_timers WHERE id='" . $spelerInfo->id . "'");
	$gn = mysqli_fetch_object($gn1);  
if($gn->krassen+86400 < time()){
	mysqli_query($con,"UPDATE leden SET krassen='50' WHERE id='" . $spelerInfo->id . "'");
}
if($spelerInfo->krassen <= 0){
	print"<p class='table_subTitle'>Lotje krassen</p><p class='table_mainTxt padding_left'><font color='red'>Je kan maar 50X per dag krassen!</font></p>"; 
	exit;
}
	
if(isset($_POST['kras']) || isset($_GET['kras'])){
	if($spelerInfo->cash < 10000){print"<p class='table_subTitle'>Lotje krassen</p><p class='table_mainTxt padding_left'><font color='red'>Je hebt niet genoeg geld om te krassen!</font></p>"; exit;}
	
	for($tt=1; $tt < 9; $tt++){
		$t[$tt]=0;
	}
	$gewonnen = 0;
	$prijzenArray = array("","&euro; 100.000","50 kogels","&euro; 10.000","250 kogels","&euro; 500.000","500 kogels","&euro; 1.000.000","1.000 kogels");
	for($i=1; $i <= 6; $i++){
		$prijs1 = rand(1,8);
		$prijs[$i] = $prijzenArray[$prijs1];
		$t[$prijs1] = ($t[$prijs1]+1);	
	}

	$cash = formatDecimaal($spelerInfo->cash);
	echo "
	<table class=\"padding_5\" border=\"1\" width=\"550px\" cellspacing=\"0\" cellpadding=\"0\" bordercolor=\"#000000\" bgcolor=\"#333\" colspan='3'>
	<tr>
		<td class=\"table_subTitle\" colspan='3'>Lotje krassen</td>
		</tr>
		<tr>
		<td class='table_mainTxt center padding_5' colspan='3'><font color='limegreen'>Je hebt nog &euro;" . $cash . "</font></td>
		</tr>
		<tr>
			<td class='table_mainTxt outline center' width='100px' colspan='1'><font color='red'>" . $prijs[1] . "</font></td>
			<td class='table_mainTxt outline center' width='100px' colspan='1'><font color='red'>" . $prijs[2] . "</font></td>
			<td class='table_mainTxt outline center' width='100px' colspan='1'><font color='red'>" . $prijs[3] . "</font></td>
		</tr>
		<tr>
			<td class='table_mainTxt outline center' width='100px' colspan='1'><font color='red'>" . $prijs[4] . "</font></td>
			<td class='table_mainTxt outline center' width='100px' colspan='1'><font color='red'>" . $prijs[5] . "</font></td>
			<td class='table_mainTxt outline center' width='100px' colspan='1'><font color='red'>" . $prijs[6] . "</font></td>
		</tr>";
	if($t[1] > 2){print "<tr><td class='table_mainTxt center bold padding_5' colspan='3'><font color='limegreen'>&euro; 100.000</font></td></tr>"; mysqli_query($con,"UPDATE leden SET cash=cash+'100000' where id='" . $spelerInfo->id . "'"); $gewonnen = 1;};
	if($t[2] > 2){print "<tr><td class='table_mainTxt center bold padding_5' colspan='3'><font color='limegreen'>50 kogels</font></td></tr>"; mysqli_query($con,"UPDATE leden SET kogels=kogels+'50' where id='" . $spelerInfo->id . "'"); $gewonnen = 1;};
	if($t[3] > 2){print "<tr><td class='table_mainTxt center bold padding_5' colspan='3'><font color='limegreen'>&euro; 10.000</font></td></tr>";  mysqli_query($con,"UPDATE leden SET cash=cash+'10000' where id='" . $spelerInfo->id . "'"); $gewonnen = 1;};
	if($t[4] > 2){print "<tr><td class='table_mainTxt center bold padding_5' colspan='3'><font color='limegreen'>250 kogels</font></td></tr>"; mysqli_query($con,"UPDATE leden SET kogels=kogels+'250' where id='" . $spelerInfo->id . "'"); $gewonnen = 1;};
	if($t[5] > 2){print "<tr><td class='table_mainTxt center bold padding_5' colspan='3'><font color='limegreen'>&euro; 500.000</font></td></tr>";  mysqli_query($con,"UPDATE leden SET cash=cash+'500000' where id='" . $spelerInfo->id . "'"); $gewonnen = 1;};
	if($t[6] > 2){print "<tr><td class='table_mainTxt center bold padding_5' colspan='3'><font color='limegreen'>500 kogels</font></td></tr>"; mysqli_query($con,"UPDATE leden SET kogels=kogels+'500' where id='" . $spelerInfo->id . "'"); $gewonnen = 1;};
	if($t[7] > 2){print "<tr><td class='table_mainTxt center bold padding_5' colspan='3'><font color='limegreen'>&euro; 1.000.000</font></td></tr>";  mysqli_query($con,"UPDATE leden SET cash=cash+'1000000' where id='" . $spelerInfo->id . "'"); $gewonnen = 1;};
	if($t[8] > 2){print "<tr><td class='table_mainTxt center bold padding_5' colspan='3'><font color='limegreen'>1.000 kogels</font></td></tr>"; mysqli_query($con,"UPDATE leden SET kogels=kogels+'1000' where id='" . $spelerInfo->id . "'"); $gewonnen = 1;};
	if($gewonnen == 0){ print "<tr><td class='table_mainTxt center bold padding_5' colspan='3'><font color='red'>Sorry je hebt niets gewonnen.</font></td></tr>"; }
	mysqli_query($con,"UPDATE leden SET krassen=krassen-'1', cash=cash-'10000' WHERE id='" . $spelerInfo->id . "'");
	mysqli_query($con,"UPDATE leden_timers SET krassen=NOW() WHERE speler='" . $spelerInfo->id . "'");
	print "<tr><form method='post' action='krassen.php'><td class='table_mainTxt center' colspan='3'><input type='submit' name='kras' value='Koop nog een lotje voor &euro; 10.000' class='button_form' /></td></form></tr></table>";
	exit;
	
} else {
?>

<form method='post' action='krassen.php'>
	<table width="550px">
		<tr>
			<td class="table_subTitle">Lotje krassen</td>
		</tr>
		<tr>
			<td class="table_mainTxt outline padding_5"><p>Met lotje krassen kan je enkele mooie prijzen winnen. Je kan maximum 50 lotjes krassen per dag.</p><p>Probeer je geluk en kras een lotje!</p><p>Wilt u een lotje kopen voor &euro; 10.000?</p></td>
		</tr>

		<tr>
			<td class="table_mainTxt" width="40%"><input class="button_form" type="submit" value="kopen" name="kras" /></td>
		</tr>
		
	</table>
</form>
<?php
}
?>