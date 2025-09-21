<?php
	include("config.php");
	include("include/functions.php");
	
//------------------------------------------------------------
// nog te doen aan speler profiel:
// 1) speler informatie 
// 2) Capo regime (capo status)
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
include("check_login.php");
if(isset($_GET['x'])){
	$spelerId = trim(mysqli_real_escape_string($con,$_GET['x']));
	if(!is_numeric($spelerId)){
		print"
		<table width='550px'>
			<tr>
				<td class='table_subTitle'>Speler profiel</td>
			</tr>
			<tr>
				<td class='table_mainTxt padding_left'>Er is geen speler met deze gegevens</td>
			</tr>
		</table>
		";
		exit;
	} else {
		$speler = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $spelerId . "'");
		$profielInfo = mysqli_fetch_object($speler);
		$aant = mysqli_query($con,"SELECT eigenaar FROM kavels WHERE eigenaar='" . $profielInfo->id . "'");
		$aantalKavels = mysqli_num_rows($aant);
		
		$rang = formatDecimaal($profielInfo->rang);
		if($profielInfo->rang >= count($gameRangen)){
			$rang = $gameRangen[count($gameRangen)-1] . " ( LVL: " . $profielInfo->rang . ")";
		} else {
			$rang = $gameRangen[$profielInfo->rang];
		}
		
		if($profielInfo->familie == "Geen"){
			$familie = "<font color='red'><b>Geen</b></font>";
		} else {
			$famQuery = mysqli_query($con,"SELECT * FROM families WHERE id='" . $profielInfo->familie . "'");
			if(mysqli_num_rows($famQuery) == 1)
			{
				$fam = mysqli_fetch_object($famQuery);
				$familie="<font color='limegreen'><b><a href='familie.php?pagina=" . $profielInfo->familie . "'>" . $fam->naam . "</a></b></font>";
			} else {
				$familie = "UNKNOWN";
			}
		}
		if($profielInfo->status == "dood"){
			$status = "<font color='red'><b>DOOD</b></font> | <a href='forum.php?pagina=rip&x=" . $profielInfo->id . "'>Rip forum";
		} else {
			$status="<font color='limegreen'><b>LEVEND</b></font>";
		}
		$famrang = $famRangen[$profielInfo->familierang];
		if($spelerInfo->profiel_pic == ""){
			$profielPic = "/images/system/noimage.jpeg";
		} else {
			$profielPic = $profielInfo->profiel_pic;
		}
		if($profielInfo->profiel_info == ""){
			$profielnfo = "<span class='red bold'>Geen informatie opgegeven.</span>"; 
		} else {
			$profielnfo = $profielInfo->profiel_info;
		}
		?>
		<table width="550px" colspan="3">
			
			<tr>
				<td class="table_subTitle" width="100%" colspan="3">Profiel van <?php echo $profielInfo->login; ?></td>
			</tr>
			<tr>
				<td class="table_mainTxt" width="20%" colspan="1"></td>
				<td class="table_mainTxt" width="60%" colspan="1"></td>
				<td class="table_mainTxt" width="20%" colspan="1" rowspan="10"><img src="<?php echo $profielPic; ?>" width="200px" height="200px" alt="Profiel foto"/></td>
			</tr>
			<tr>
				<td class="table_mainTxt padding_left" width="20%" colspan="1">Login</td>
				<td class="table_mainTxt" width="60%" colspan="1"><?php echo $profielInfo->login; ?></td>
				<td class="table_mainTxt" width="20%" colspan="1"></td>
			</tr>
			<tr>
				<td class="table_mainTxt padding_left" width="20%" colspan="1">Rang</td>
				<td class="table_mainTxt" width="60%" colspan="1"><?php echo $rang; ?></td>
				<td class="table_mainTxt" width="20%" colspan="1"></td>
			</tr>
			<tr>
				<td class="table_mainTxt padding_left" width="20%" colspan="1">Kavels</td>
				<td class="table_mainTxt" width="60%" colspan="1"><?php echo $aantalKavels; ?></td>
				<td class="table_mainTxt" width="20%" colspan="1"></td>
			</tr>
			<tr>
				<td class="table_mainTxt padding_left" width="20%" colspan="1">Geregistreerd</td>
				<td class="table_mainTxt" width="60%" colspan="1"><?php echo $profielInfo->geregistreerd; ?></td>
				<td class="table_mainTxt" width="20%" colspan="1"></td>
			</tr>
			<tr>
				<td class="table_mainTxt padding_left" width="20%" colspan="1">Familie</td>
				<td class="table_mainTxt" width="60%" colspan="1"><?php echo $familie; ?></td>
				<td class="table_mainTxt" width="20%" colspan="1"></td>
			</tr>
			<tr>
				<td class="table_mainTxt padding_left" width="20%" colspan="1">Familie rang</td>
				<td class="table_mainTxt" width="60%" colspan="1"><?php echo $famrang; ?></td>
				<td class="table_mainTxt" width="20%" colspan="1"></td>
			</tr>
			<tr>
				<td class="table_mainTxt padding_left" width="20%" colspan="1">Status</td>
				<td class="table_mainTxt" width="60%" colspan="1"><?php echo $status; ?></td>
				<td class="table_mainTxt" width="20%" colspan="1"></td>
			</tr>
			
		</table>
		<table width="550px" colspan="3">
			<tr>
				<td colspan="1" width="34%"><a href="berichten.php?b=nieuw&x=<?php echo $profielInfo->login; ?>"><div class="hyperlink_knop unselectable">Stuur bericht</div></a></td>
				<td colspan="1" width="33%"><a href="kavel.php?kavels=<?php echo $profielInfo->id; ?>"><div class="hyperlink_knop unselectable">Kavel lijst</div></a></td>
				<td colspan="1" width="33%"><a href="magazijn.php?m=magazijn"><div class="hyperlink_knop unselectable">Vriend toevoegen</div></a></td>
			</tr>
			<tr>
				<td class="subTitle" width="100%" colspan="4">Speler informatie</td>
			</tr>
			<tr>
				<td class="table_mainTxt outline padding_left" width="100%" colspan="4"><?php echo $profielnfo; ?></td>
			</tr>
		</table>

		<?php
	}
}
?>