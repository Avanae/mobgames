<?php
	$con = mysqli_connect("localhost","assterror637_fritZakske","w00Q0cTUvS","assterror637_assassingame");
	
	// Tabellen leegmaken
	mysqli_query($con,"TRUNCATE TABLE autos_leden");
	mysqli_query($con,"TRUNCATE TABLE berichten");
	mysqli_query($con,"TRUNCATE TABLE donatie_logs");
	mysqli_query($con,"TRUNCATE TABLE casino_objecten");
	mysqli_query($con,"TRUNCATE TABLE families");
	mysqli_query($con,"TRUNCATE TABLE families_belasting");
	mysqli_query($con,"TRUNCATE TABLE families_invite");
	
	mysqli_query($con,"TRUNCATE TABLE leden");
	mysqli_query($con,"TRUNCATE TABLE leden_beurs");
	mysqli_query($con,"TRUNCATE TABLE leden_huizen");
	mysqli_query($con,"TRUNCATE TABLE leden_refferals");
	mysqli_query($con,"TRUNCATE TABLE leden_timers");
	
	mysqli_query($con,"TRUNCATE TABLE moordpogingen");
	mysqli_query($con,"TRUNCATE TABLE schandpaal");
	mysqli_query($con,"TRUNCATE TABLE villa_plantages");
	mysqli_query($con,"TRUNCATE TABLE nieuws");
	// Objecten en kavels vrij maken
	mysqli_query($con,"UPDATE kavels SET eigenaar='0' WHERE eigenaar !='0'");
	mysqli_query($con,"UPDATE landen_vliegveld SET speler='0',bom='0',omzet='0'");
	
	mysqli_query($con,"INSERT INTO leden (login,wachtwoord,email,ip,activated,level) VALUES ('admin','b4bd64873e45fe82ee093436c430805c4f0d676c','admin@assassination.be','','','255')");
	$id = mysqli_insert_id($con);
	mysqli_query($con,"INSERT into leden_timers (speler) VALUES ('" . $id . "')");
?>
