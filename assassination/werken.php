<?php
	include("config.php");
	include("include/functions.php");
	
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
	include("check_login.php");
	include("check_jail.php");

	if(isset($_POST['werk'])){
		$bedrag = rand(500,5000);
		$waitTime = date("Y-m-d H:i:s",(time()+900));
		mysqli_query($con,"UPDATE leden SET cash=cash+'" . $bedrag . "' WHERE id='" . $spelerInfo->id . "'");
		mysqli_query($con,"UPDATE leden_timers SET werken='" . $waitTime . "' WHERE speler='" . $spelerInfo->id . "'");
		
		print_bericht("Werken","U bent 15 minuten gaan werken en heeft &euro; " . formatDecimaal($bedrag) . " verdient.");
		exit;
	}

	$gn1 = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(werken) AS werken,0 FROM leden_timers WHERE speler='" . $spelerInfo->id . "'");
	$gn = mysqli_fetch_object($gn1);  

	if($gn->werken > time()){
		$res = GetWaitTime(time(),$gn->werken);
		print print_bericht("Werken","U bent nog " . $res . " minuten aan het wachten.");
		exit;
	}  else { 
?>
		<form method='post' action='werken.php'>
		<?php if($spelerInfo->mini_banner == 1){ print"<tr><td class='table_mainTxt'><img src='images/headers/werken.jpg' width='550px' height='120px' alt='werken pic' /></td></tr>"; } ?>
			<table class="inhoud_table" width="550px">
				<tr>
					<td class="table_subTitle">Werken</td>
				</tr>
				<tr>
					<td class="table_mainTxt padding_left_5">Verdien tussen de &euro; 500 en de &euro; 5.000 door te werken als freelancer.</td>
				</tr>
				<tr>
					<td class="table_mainTxt padding_left_5">Wilt u 15 minuten gaan werken?</td>
				</tr>
				<tr>
					<td class="table_mainTxt"><input type="submit" class="button_form" value="Ga werken" name="werk" /></td>
				</tr>
			</table>
		</form>
	<?php } ?>