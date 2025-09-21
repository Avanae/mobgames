<?php
	include("config.php");
	include("SYSTEM_CONFIGURATION.php");
	include("include/functions.php");
	
	$landenQuery = mysqli_query($con,"SELECT * FROM landen");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />

<?php while($land = mysqli_fetch_object($landenQuery)){ ?>
<table width="550px" colspan="4" class="inhoud_table margin_bottom">
	<?php
		$totalMembersQuery = mysqli_query($con,"SELECT COUNT(*) as total FROM leden WHERE land='" . $land->id . "' AND status='levend' AND ban='0'");
		$totalMembers = mysqli_fetch_object($totalMembersQuery);
		
		$totalFamiliesQuery = mysqli_query($con,"SELECT COUNT(*) as total FROM families WHERE land='" . $land->id . "'");
		$totalFamilies = mysqli_fetch_object($totalFamiliesQuery);
	
		$rijksteQuery = mysqli_query($con,"SELECT * FROM leden WHERE status='levend' AND ban='0' AND land='" . $land->id . "' ORDER BY cash,bank DESC LIMIT 0,1");
		$rijksteTxt = "Geen";
		if(mysqli_num_rows($rijksteQuery) == 1){
			$speler = mysqli_fetch_object($rijksteQuery);
			$rijksteTxt = "<a href=\"speler_profiel.php?x=" . $speler->id . "\">" . $speler->login . "</a>";
		}
		
		$kogelsQuery = mysqli_query($con,"SELECT * FROM leden WHERE status='levend' AND ban='0' AND land='" . $land->id . "' ORDER BY kogels DESC LIMIT 0,1");
		$kogelsTxt = "Geen";
		if(mysqli_num_rows($kogelsQuery) == 1){
			$kogel = mysqli_fetch_object($kogelsQuery);
			$kogelsTxt = "<a href=\"speler_profiel.php?x=" . $kogel->id . "\">" . $kogel->login . "</a>";
		}
		
		$machtigsteQuery = mysqli_query($con,"SELECT * FROM leden WHERE status='levend' AND ban='0' AND land='" . $land->id . "' ORDER BY rang DESC,rangvordering ASC,kogels DESC LIMIT 0,1");
		$machtigsteTxt = "Geen";
		if(mysqli_num_rows($machtigsteQuery) == 1){
			$speler = mysqli_fetch_object($machtigsteQuery);
			$machtigsteTxt = "<a href=\"speler_profiel.php?x=" . $speler->id . "\">" . $speler->login . "</a>";
		}
		
		
		$famsSql = mysqli_query($con,"SELECT * FROM families WHERE land='" . $land->id . "'");
		$machtigsteFamilieTxt = "Geen";
		
		if(mysqli_num_rows($famsSql) >= 1){
			$mostPower = 0;
			while($fa = mysqli_fetch_object($famsSql)){		
				$memQuery = mysqli_query($con,"SELECT * FROM leden WHERE familie='" . $fa->id . "' AND status='levend' AND ban='0'");
				if(mysqli_num_rows($memQuery) >= 1){
					$famPower = 0;
					while($me = mysqli_fetch_object($memQuery)){
						$famPower += $me->rang;
					}
					if($famPower > $mostPower){
						$mostPower = $famPower;
						$machtigsteFamilieTxt = "<a href=\"familie.php?pagina=" . $fa->id . "\">" . $fa->naam . "</a>";
					}
				}
			}
		}
	?>
		<tr>
			<td class='table_subTitle' colspan='4'><?php echo formatCountry($land->land); ?> Overzicht</td>
		</tr>
		<tr>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1">Spelers</td>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1"><?php echo formatDecimaal($totalMembers->total); ?></td>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1">Families</td>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1"><?php echo formatDecimaal($totalFamilies->total); ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1">Rijkste Speler</td>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1"><?php echo $rijksteTxt; ?></td>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1">Meeste Kogels</td>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1"><?php echo $kogelsTxt; ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1">Machtigste Speler</td>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1"><?php echo $machtigsteTxt; ?></td>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1">Machtigste Familie</td>
			<td class="table_mainTxt outline padding_5" width="25%" colspan="1"><?php echo $machtigsteFamilieTxt; ?></td>
		</tr>
	</table>
<?php } ?>