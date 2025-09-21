<?php
	$con = mysqli_connect("localhost","assterror637_fritZakske","w00Q0cTUvS","assterror637_assassingame");
	
	$landenQuery = mysqli_query($con,"SELECT * FROM landen");
	if(mysqli_num_rows($landenQuery) >= 1)
	{
		while($land = mysqli_fetch_object($landenQuery)){
			$wiet = rand(400,650);
			$hasj = rand(500,750);
			$xtc = rand(700,1000);
			$coke = rand(1500,2000);
			$kogels = rand(900,2000);
			$kogelAmount = rand(1000,2000);
			mysqli_query($con,"UPDATE landen SET wietPrijs='" . $wiet . "',hasjPrijs='" . $hasj . "',xtcPrijs='" . $xtc . "',cokePrijs='" . $coke . "',kogelPrijs='" . $kogels . "',kogels='" . $kogelAmount . "' WHERE id='" . $land->id . "'");
		}
	}
?>
