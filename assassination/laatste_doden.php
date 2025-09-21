<?php
include("config.php");
include("SYSTEM_CONFIGURATION.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	if(isset($_GET['x']) && isset($_GET['y'])){
		$x = mysqli_real_escape_string($con,test_input($_GET['x']));
		$y = mysqli_real_escape_string($con,test_input($_GET['y']));
		if(!is_numeric($x) || !is_numeric($y)){
			print "";
			exit;
		}
	} else {
		$x = 0; 
		$y=10;
	}
	$speler1 = mysqli_query($con,"SELECT * FROM leden WHERE status='dood' ORDER BY datumdood DESC, rangvordering DESC,id LIMIT " . $x . "," . $y);
	
?>
<table width="550px" colspan="4" class="inhoud_table">
<?php 
	if(isset($_SESSION['id'])){
		if($spelerInfo->mini_banner == 1){
			echo "
			<tr>
				<td colspan='4'><img src='images/graveyard.jpg' width='550px' height='120px' alt='kerkhof' /></td>
			</tr>";
		}
	} else {
		echo "
		<tr>
			<td colspan='4'><img src='images/graveyard.jpg' width='550px' height='120px' alt='kerkhof' /></td>
		</tr>";
	}
	if(mysqli_num_rows($speler1) == 0)
	{
		echo "
		<tr>
			<td colspan='4' class='table_mainTxt outline padding_5'>Er zijn momenteel nog geen vermoorde spelers.</td>
		</tr>";
		exit;
	}
?>

<tr>
	<td class='table_subTitle' colspan='4'>SPELERS</td>
</tr>
<tr>
	<td class="table_mainTxt outline padding_5" width="10%" colspan="1"></td>
	<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Login</td>
	<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Rang</td>
	<td class="table_mainTxt outline padding_5" width="50%" colspan="1">Datum</td>
</tr>
<?php
	if($x == 0){
		$i = 1;
	}else{
		$i = $x+1;
	}
	
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
			<td class='table_mainTxt'>" . $spelerList->datumdood . "</td>
		</tr>
		";
		$i++;
	}
	if($x == 0 && $i > 10){
		$volgende = 10;
		$y = $volgende+10;
		print "<tr>
			<td class='table_mainTxt' colspan='4'><a href='laatste_doden.php?x=" . $volgende . "&y=" . $y . "'>Volgende pagina</td>
		</tr>";
	} elseif($x == 0 && $i < 10){

	} else if($x != 0 && ($i-1) <= $x){
		// volgende pagina
		$p = mysqli_real_escape_string($con,test_input($_GET['x']));
		$l = mysqli_real_escape_string($con,test_input($_GET['y']));
		if(!is_numeric($p) || !is_numeric($x)){
			print"";
			exit;
		}
		$x = ($p+10);
		$y = ($l+10);
		// vorige pagina
		$vorige =($p-10);
		$vorige2 = ($l-10);
		print "<tr>
			<td class='table_mainTxt' colspan='4'><a href='laatste_doden.php?x=" . $vorige . "&y=" . $vorige2 . "'>Vorige Pagina</a> | <a href='laatste_doden.php?x=" . $x . "&y=" . $y . "'>Volgende pagina</a></td>
		</tr>";
	} else if($x != 0 && $x < $i){
	$p = mysqli_real_escape_string($con,test_input($_GET['x']));
		$l = mysqli_real_escape_string($con,test_input($_GET['y']));
		if(!is_numeric($p) || !is_numeric($x)){
			print"";
			exit;
		}
		$vorige =($p-10);
		$vorige2 = ($l-10);
		print "<tr>
			<td class='table_mainTxt' colspan='4'><a href='laatste_doden.php?x=" . $vorige . "&y=" . $vorige2 . "'>Vorige pagina</td>
		</tr>";
	}
?>
</table>