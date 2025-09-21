<?php
	include("config.php");
	
	if(isset($_SESSION['id'])){
		$gn1 = mysqli_query($con,"SELECT *,UNIX_TIMESTAMP(gevangenis) AS gevangenis,0 FROM leden_timers WHERE speler='" . $spelerInfo->id . "'");
		$gn = mysqli_fetch_object($gn1);
		
		if($gn->gevangenis > time()){
			$res = GetWaitTime(time(),$gn->gevangenis);
			print print_bericht("Gevangenis","U zit nog " . $res . " in de gevangenis.");
			exit;
		}			
	}
?>