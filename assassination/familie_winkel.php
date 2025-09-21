<?php
	include("config.php");
	include("include/functions.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php	
	include("check_login.php");
	include("check_jail.php");
	$crushers = array (
		array(0,0,0),
		array(250000,50,25),
		array(500000,100,25),
		array(2250000,500,25),
		array(3500000,500,40),
		array(5000000,1000,30)
	);
	
	if($spelerInfo->familie == 0 || $spelerInfo->rang <= 1){
		print_bericht("Familie","U bent niet bevoegd om deze pagina te bezoeken!");
		exit;
	}
	$gn1 = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(crusherTime) AS werken,0 FROM families WHERE id='" . $spelerInfo->familie . "'");
	$gn = mysqli_fetch_object($gn1);  
	
	if($gn->werken > time()){
		$w = GetWaitTime(time(),$gn->werken);
		print_bericht("Familie","Je moet nog " . $w . " wachten voor je opnieuw een crusher kan huren.");
		exit;
	}
	
	if(isset($_POST["crusher"])){
		$crusher = mysqli_real_escape_string($con,test_input($_POST["crusher"]));
		
		if(!is_numeric($crusher) || empty($crusher)){
			print_bericht("Familie","Je moet een crusher selecteren.");
			exit;
		}
		
		$familieQuery = mysqli_query($con,"SELECT * FROM families WHERE id='" . $spelerInfo->familie . "'");
		if(mysqli_num_rows($familieQuery) == 1){
			$familie = mysqli_fetch_object($familieQuery);
			
			if($familie->geld >= $crushers[$crusher][0]){
				$crushExistQuery = mysqli_query($con,"SELECT * FROM families_crusher WHERE familie='" . $spelerInfo->familie . "'");
				if(mysqli_num_rows($crushExistQuery) == 1){
					mysqli_query($con,"UPDATE families_crusher SET maxAutos='" . $crushers[$crusher][1] . "',kogels='" . $crushers[$crusher][2] . "',crushed='0' WHERE familie='" . $spelerInfo->familie . "'");
				} else {
					mysqli_query($con,"INSERT INTO families_crusher (familie,maxAutos,kogels) VALUES ('" . $spelerInfo->familie . "','" . $crushers[$crusher][1] . "','" . $crushers[$crusher][2] . "')");
				}
				$time = date("Y-m-d H:i:s",(time()+86400));
				mysqli_query($con,"UPDATE families SET geld=geld-'" . $crushers[$crusher][0] . "',crusherTime='" . $time . "' WHERE id='" . $spelerInfo->familie . "'");
				print_bericht("Familie","Je hebt met succes een auto crusher gehuurt.");
				exit;
			} else {
				print_bericht("Familie","Je hebt niet genoeg geld op de familie bank om deze crusher te huren!");
				exit;
			}
		} else {
			print_bericht("Familie","Jou familie bestaat niet meer.");
			exit;
		}
	}
?>
<form method="post" action="familie_winkel.php">
	<table width="550px" class="inhoud_table" colspan="4">
		<tr>
			<td class="table_subTitle center" width="100%" colspan="4">Familie Crusher</td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" width="5%" colspan="1"></td>
			<td class="table_mainTxt outline padding_5" width="30%" colspan="1">Prijs</td>
			<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Max auto's</td>
			<td class="table_mainTxt outline padding_5" width="45%" colspan="1">Kogels/auto</td>
			
		</tr>
		<?php for($i=1; $i < count($crushers); $i++){ ?>
			<tr>
				<td class="table_mainTxt" colspan="1"><input type="radio" name="crusher" id="label_<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i == 1 ? "checked" : ""; ?> /></td>
				<td class="table_mainTxt" colspan="1"><label for="label_<?php echo $i; ?>">&euro; <?php echo formatDecimaal($crushers[$i][0]); ?></label></td>
				<td class="table_mainTxt" colspan="1"><?php echo formatDecimaal($crushers[$i][1]); ?></td>
				<td class="table_mainTxt" colspan="1"><?php echo formatDecimaal($crushers[$i][2]); ?></td>
			</tr>
		<?php } ?>
		<tr>
			<td class="table_mainTxt padding_5" width="100%" colspan="4"><input type="submit" class="button_form" value="Crusher huren" name="hire" /></td>
		</tr>
	</table>
</form>