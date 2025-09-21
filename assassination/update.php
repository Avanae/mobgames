<?php
	include("config.php");
	echo sha1(md5("admin"));
	
	mysqli_query($con,"UPDATE leden SET wachtwoord= '90b9aa7e25f80cf4f64e990b78a9fc5ebd6cecad'");
?>
