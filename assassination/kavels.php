<?php
	include("config.php");
	include("include/functions.php");
	
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	function showKavelInfo(id,eigenaar,eId,land,lId,status,opbrengst,prijs){
		document.getElementById("kavelInfo").style.display = 'block';
		
		var eigenaarLink = document.createElement("a");
		if(eigenaar === ''){
			eigenaarLink.href = "kavels.php?buy=" + id;
			eigenaarLink.innerHTML = "Koop voor &euro;" + prijs;
		} else {
			eigenaarLink.href = "speler_profiel.php?x=" + eId;
			eigenaarLink.innerHTML = eigenaar;
		}
		document.getElementById("kavelEigenaar").innerHTML = "";
		document.getElementById("kavelEigenaar").appendChild(eigenaarLink);
		
		var landLink = document.createElement("a");
		landLink.href = "kavels.php?overview=" + lId;
		landLink.innerHTML = land;

		document.getElementById("kavelLand").innerHTML = "";
		document.getElementById("kavelLand").appendChild(landLink);
		
		document.getElementById("kavelStatus").innerHTML = status + " %";
		document.getElementById("kavelOpbrengst").innerHTML = opbrengst;
	}

</script>
<?php
	include("check_login.php");
	include("check_jail.php");
	
	if(isset($_GET["admin"])){
		mysqli_query($con,"UPDATE kavels SET buyable='0' WHERE xPos='14'");
		mysqli_query($con,"UPDATE kavels SET buyable='0' WHERE yPos='14'");
	}	
	if(isset($_GET["overview"])){
		$id = mysqli_real_escape_string($con,$_GET["overview"]);
		if($id == null){ 
			$id = $spelerInfo->land;
		}
		
		if(is_numeric($id))
		{
			$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $id . "'");
			if(mysqli_num_rows($landQuery) == 0)
			{
				print_bericht("Kavels","Deze kavel bestaat niet.");
				exit;
			}
			$land = mysqli_fetch_object($landQuery);
			$mapTxt = "<div id=\"kavelMap\">";
			$mapTxt = $mapTxt ."<h1 class=\"subTitle\">Kavels in " . $land->land . "</h1>";
			for($x = 0; $x < 15; $x++)
			{
				$mapTxt = $mapTxt . "<div id=\"float_left\">";
				for($y=0; $y < 15; $y++)
				{
					$kavelQuery = mysqli_query($con,"SELECT * FROM kavels WHERE land='" . $spelerInfo->land . "' AND xPos='" . $x . "' AND yPos='" . $y . "'");
					if(mysqli_num_rows($kavelQuery) == 1)
					{
						$kavel = mysqli_fetch_object($kavelQuery);
						
						$prijs = $kavel->opbrengst*10;
						$eigenaarTxt = "";
						if($kavel->eigenaar != 0)
						{
							$eigenaarQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $kavel->eigenaar . "'");
							$eigenaar = mysqli_fetch_object($eigenaarQuery);
							$eigenaarTxt = $eigenaar->login;
						}
						
						$landTxt = "<a href=\"kavels.php?overview=" . $land->id . "\">" . $land->land . "</a>";
						$kavelPic = "defaultkavel.jpg";
						if($kavel->eigenaar == 0)
						{
							if($kavel->buyable != 1)
							{
								$kavelPic = "notbuyable.jpg";
							}
						} else {
							$kavelPic = "sold.jpg";
						}
						if($kavel->buyable){
							$mapTxt = $mapTxt . "<img src=\"images/" . $kavelPic . "\" width=\"25px\" height=\"25px\" class=\"float_left\" onclick=\"showKavelInfo('" . $kavel->id . "','" . $eigenaarTxt . "','" . $kavel->eigenaar . "','" . $land->land . "','" . $land->id . "','" . $kavel->status . "','&euro; " . formatDecimaal($kavel->opbrengst) . "','" . formatDecimaal($prijs) . "')\"/>";
						} else {
							$mapTxt = $mapTxt . "<img src=\"images/" . $kavelPic . "\" width=\"25px\" height=\"25px\" class=\"float_left\" />";
						}
					}
				}
				$mapTxt = $mapTxt . "</div>";
			}
			$mapTxt = $mapTxt . "</div>";
			$mapTxt = $mapTxt . "<div id=\"kavelInfo\" style=\"display:none\">";
			$mapTxt = $mapTxt . "<table width='175x' class=\"inhoud_table\" colspan='1'>";
			$mapTxt = $mapTxt . "<tr>
						<td class='table_subTitle' colspan='1'>Kavel Info</td>
					</tr>
					<tr>
						<td class='table_mainTxt bold outline' colspan='1' width='100%'>Eigenaar</td>
					</tr>
					<tr>
						<td class='table_mainTxt' colspan='1' width='100%'><div id=\"kavelEigenaar\"></div></td>
					</tr>
					<tr>
						<td class='table_mainTxt bold outline' colspan='1' width='100%'>Land</td>
					</tr>
					<tr>
						<td class='table_mainTxt' colspan='1' width='100%'><div id=\"kavelLand\"></div></td>
					</tr>
					<tr>
						<td class='table_mainTxt bold outline' colspan='1' width='100%'>Status</td>
					</tr>
					<tr>
						<td class='table_mainTxt' colspan='1' width='100%'><div id=\"kavelStatus\"></div></td>
					</tr>
					<tr>
						<td class='table_mainTxt bold outline' colspan='1' width='100%'>Opbrengst</td>
					</tr>
					<tr>
						<td class='table_mainTxt' colspan='1' width='100%'><div id=\"kavelOpbrengst\"></div></td>
					</tr>
				";
				print "</table>";
			$mapTxt = $mapTxt ."</div>";
			echo $mapTxt;
		} else {
			print_bericht("Kavels","Er werden geen kavels gevonden.");
		}
	}
	if(isset($_GET["id"])){
		$id = mysqli_real_escape_string($con,$_GET["id"]);
		if(is_numeric($id))
		{
			$kavelQuery = mysqli_query($con,"SELECT * FROM kavels WHERE id='" . $id . "'");
			if(mysqli_num_rows($kavelQuery))
			{
				$kavel = mysqli_fetch_object($kavelQuery);
				if(!$kavel->buyable)
				{
					echo "<p>Deze kavel kan niet gekocht worden.</p>";
					exit;
				}
				$prijs = $kavel->opbrengst*10;
				$eigenaarTxt = "<a href=\"kavels.php?buy=" . $kavel->id . "\" class=\"limegreen\">Kopen voor &euro; " . formatDecimaal($prijs) . "!</a>";
				if($kavel->eigenaar != 0)
				{
					$eigenaarQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $kavel->eigenaar . "'");
					$eigenaar = mysqli_fetch_object($eigenaarQuery);
					$eigenaarTxt = "<a href=\"speler_profiel.php?id=" . $eigenaar->id . "\">" . $eigenaar->login . "</a>";
				}
				
				
				$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $kavel->land . "'");
				$land = mysqli_fetch_object($landQuery);
				$landTxt = "<a href=\"kavels.php?overview=" . $land->id . "\">" . $land->land . "</a>";
				
				print "<table width='550px' class=\"inhoud_table\" colspan='5'>";
				
				if($spelerInfo->mini_banner == 1){
					print"<tr>
						<td colspan='4'><img src='images/headers/bezitting.jpg' width='550px' height='120px' alt='bezittingen pic' /></td>
					</tr>";
				}
				print "
					<tr>
						<td class='table_subTitle' colspan='4'>Kavel</td>
					</tr>
					<tr>
						<td class='table_mainTxt bold outline' colspan='1' width='20%'>Eigenaar</td>
						<td class='table_mainTxt' colspan='1' width='30%'>" . $eigenaarTxt . "</td>
						<td class='table_mainTxt bold outline' colspan='1' width='20%'>Land</td>
						<td class='table_mainTxt' colspan='1' width='30%'>" . $landTxt . "</td>
					</tr>
					<tr>
						<td class='table_mainTxt bold outline' colspan='1' width='20%'>Status</td>
						<td class='table_mainTxt' colspan='1' width='30%'>" . $kavel->status . " %</td>
						<td class='table_mainTxt bold outline' colspan='1' width='20%'>Opbrengst</td>
						<td class='table_mainTxt' colspan='1' width='30%'>&euro; " . formatDecimaal($kavel->opbrengst) . "</td>
					</tr>
				";
				print "</table>";
			} else {
				print_bericht("Kavels","Er ging iets mis met het laden van deze kavel.");
			}
		} else {
			print_bericht("Kavels","Er ging iets mis met het laden van deze kavel.");
		}
	}
	if(isset($_GET["buy"])){
		$id = mysqli_real_escape_string($con,$_GET["buy"]);
		if(is_numeric($id))
		{
			$kavelQuery = mysqli_query($con,"SELECT * FROM kavels WHERE id='" . $id . "'");
			if(mysqli_num_rows($kavelQuery) == 1)
			{
				$kavel = mysqli_fetch_object($kavelQuery);
				$prijs = $kavel->opbrengst*10;
				if($spelerInfo->cash >= $prijs)
				{
					echo print_bericht("Kavel","Je hebt deze kavel gekocht voor &euro; " . formatDecimaal($prijs) . "!");
					mysqli_query($con,"UPDATE leden SET cash=cash-'" . $prijs . "' WHERE id='" . $spelerInfo->id . "'");
					mysqli_query($con,"UPDATE kavels SET eigenaar='" . $spelerInfo->id . "' WHERE id='" . $kavel->id . "'");
				} else {
					print_bericht("Kavel","Je hebt niet genoeg geld op zak om deze kavel te kopen.");
				}
			} else {
				print_bericht("Kavel","Er ging iets mis tijdens het laden van deze kavel.");
			}
		}
	}
	if(isset($_GET["owner"])){
		$kavelQuery = mysqli_query($con,"SELECT * FROM kavels WHERE eigenaar='" . $spelerInfo->id . "' ORDER by land desc");
		print "<table width='550px' class=\"inhoud_table\" colspan='5'>";
				
			if($spelerInfo->mini_banner == 1){
				print"<tr>
					<td colspan='4' class='table_mainTxt'><img src='images/headers/bezitting.jpg' width='535px' height='120px' alt='bezittingen pic' /></td>
				</tr>";
			}
			print "
				<tr>
					<td class='table_subTitle' colspan='3'>Kavels</td>
				</tr>
				<tr>
					<td class='table_mainTxt bold outline' colspan='1' width='40%'>Land</td>
					<td class='table_mainTxt bold outline' colspan='1' width='20%'>Status</td>
					<td class='table_mainTxt bold outline' colspan='1' width='40%'>Opbrengst</td>
				</tr>";
			if(mysqli_num_rows($kavelQuery) >= 1)
			{
				while($kavel = mysqli_fetch_object($kavelQuery))
				{
					$landQuery = mysqli_query($con,"SELECT * FROM landen WHERE id='" . $kavel->land . "'");
					$land = mysqli_fetch_object($landQuery);
					$landTxt = "<a href=\"kavels.php?overview=" . $land->id . "\">" . $land->land . "</a>";
					print "
						
						<tr>
							<td class='table_mainTxt' colspan='1' width='40%'>" . $landTxt . "</td>
							<td class='table_mainTxt' colspan='1' width='20%'>" . $kavel->status . " %</td>
							<td class='table_mainTxt' colspan='1' width='40%'>&euro; " . formatDecimaal($kavel->opbrengst) . "</td>
						</tr>
					";
				}
			} else {
				print "
					<tr>
						<td class='table_mainTxt' colspan='3' width='100%'>Je bent nog niet in het bezit van een of meerdere kavels.</td>
					</tr>
				";
			}
		print "</table>";
		
	}
?>