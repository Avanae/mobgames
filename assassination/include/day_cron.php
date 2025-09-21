<?php
	$con = mysqli_connect("localhost","assterror637_fritZakske","w00Q0cTUvS","assterror637_assassingame");
	
	// Update rente
	$usersQuery = mysqli_query($con,"SELECT * FROM leden WHERE bank >='1'");
	if(mysqli_num_rows($usersQuery) >= 1){
		while($user = mysqli_fetch_object($usersQuery)){
			$rente = round(($user->bank/100)*5);
			mysqli_query($con,"UPDATE leden SET cash=cash+'" . $rente . "'");
		}
	}
?>
