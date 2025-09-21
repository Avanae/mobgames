<?php
	include("config.php");
	
	$totalPlayersQuery = mysqli_query($con,"SELECT * FROM leden WHERE status='levend'");
	$totalPlayers = mysqli_num_rows($totalPlayersQuery);
	
	$totalDeadPlayersQuery = mysqli_query($con,"SELECT * FROM leden WHERE status='dood'");
	$totalDeadPlayers = mysqli_num_rows($totalDeadPlayersQuery);
	
	$totalVipsQuery = mysqli_query($con,"SELECT * FROM leden WHERE vip='1'");
	$totalVips = mysqli_num_rows($totalVipsQuery);
	
	$totalBanQuery = mysqli_query($con,"SELECT * FROM leden WHERE ban='1'");
	$totalBan = mysqli_num_rows($totalBanQuery);
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<table width="550px" class="inhoud_table" colspan="4">
	<tr>
		<td class='table_subTitle' colspan='4'>Algemene Statistieken</td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="30%" colspan="1">Spelers</td>
		<td class="table_mainTxt padding_5" width="20%" colspan="1"><?php echo $totalPlayers; ?></td>
		<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Doden</td>
		<td class="table_mainTxt padding_5" width="20%" colspan="1"><?php echo $totalDeadPlayers; ?></td>
	</tr>
	<tr>
		<td class="table_mainTxt outline padding_5" width="30%" colspan="1">Vips</td>
		<td class="table_mainTxt padding_5" width="20%" colspan="1"><?php echo $totalVips; ?></td>
		<td class="table_mainTxt outline padding_5" width="20%" colspan="1">Verbannen</td>
		<td class="table_mainTxt padding_5" width="20%" colspan="1"><?php echo $totalBan; ?></td>
	</tr>
</table>
<table width="550px" class="inhoud_table" colspan="4">
	<tr>
		<td class='table_subTitle' colspan='4'>Rangen Statistieken</td>
	</tr>
	<?php 
		$index = 0;
		foreach($gameRangen as $gameRang){ 
			$res = mysqli_query($con,"SELECT * FROM leden WHERE rang='" . ($index) . "'");
			$rang = $gameRangen[$index];
			print"
			<tr>
				<td class=\"table_mainTxt outline padding_5\" width=\"30%\" colspan=\"1\">" . $rang . "</td>
				<td class=\"table_mainTxt padding_5\" width=\"70%\" colspan=\"1\">" . mysqli_num_rows($res) . "</td>
			</tr>";
			$index++; 
		}
		$res = mysqli_query($con,"SELECT * FROM leden WHERE rang>='11'");
		
	?>
	<tr>
		<td class="table_mainTxt outline padding_5" width="30%" colspan="1">Baas Lvl 11+</td>
		<td class="table_mainTxt padding_5" width="70%" colspan="1"><?php echo mysqli_num_rows($res); ?></td>
	</tr>
</table>