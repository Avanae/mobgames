<?php
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
function CheckUserName($username){
	$user = test_input($username);
	if (ctype_alnum($user) && !empty($user)){
		if(preg_match('/^[a-zA-Z0-9_\-]+$/',$user) == 1){
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
function formatDecimaal($var){
	return number_format($var,0,",",".");
}
function formatCountry($country){
	return utf8_encode($country);
}
function GetWaitTime($currentTime,$futureTime){
	// Formulate the Difference between two dates 
	$diff = abs($futureTime-$currentTime);  
	$years = floor($diff/(365*60*60*24));  
	$months = floor(($diff-$years*(365*60*60*24))/(30*60*60*24));  
	$days = floor(($diff-$years*(365*60*60*24)-($months*30*60*60*24))/(60*60*24)); 
	$hours = floor(($diff-$years *(365*60*60*24)-($months*30*60*60*24) - ($days*60*60*24))/(60*60));  
	$minutes = floor(($diff - ($years * 365*60*60*24)-($months*30*60*60*24) - ($days*60*60*24)-($hours*60*60))/ 60);  
	$seconds = floor(($diff -($years * 365*60*60*24)-($months*30*60*60*24)-($days*60*60*24)-($hours*60*60)-($minutes*60)));  
	
	$form = date("H:i:s",mktime($hours,$minutes,$seconds));
	return $form; 
	
}
function print_bericht($titel,$bericht){
	print"
		<table width='550px' class=\"inhoud_table\">
			<tr>
				<td class='table_subTitle' width='550px'><span class='padding_left'>" . $titel . "</span></td>
			<tr>
				<td class='table_mainTxt padding_left' width='550px'>" . $bericht . "</td> 
			</tr>
			<tr>
				<td class='table_mainTxt padding_left' width='550px'><a href='javascript:history.go(-1);'>Ga terug</a></td> 
			</tr>
		</table>
	";
}
function print_bericht_link($titel,$bericht,$link){
	print"
		<table width='550px'>
			<tr>
				<td class='table_subTitle' width='550px'><span class='padding_left'>" . $titel . "</span></td>
			<tr>
				<td class='table_mainTxt padding_left' width='550px'>" . $bericht . "</td> 
			</tr>
		</table>
	";
}
function print_error($titel,$bericht){
	print"
		<table width='550px'>
			<tr>
				<td class='table_subTitle' width='550px'>" . $titel . "</td>
			<tr>
				<td class='table_mainTxt padding_left' width='550px'><img class='align_middle' src='images/system/icons/forbidden.gif' alt='verboden' width='20px' height='20px' /><span class='red bold'>" . $bericht . "</span></td> 
			</tr>
			<tr>
				<td class='table_mainTxt padding_left' width='550px'><a href='javascript:history.go(-1);'>Ga terug</a></td> 
			</tr>
		</table>
	";
}
function print_succes($titel,$bericht){
	print"
		<table width='550px'>
			<tr>
				<td class='table_subTitle' width='550px'>" . $titel . "</td>
			<tr>
				<td class='table_mainTxt padding_left' width='550px'><img class='align_middle' src='images/system/icons/vink1.gif' alt='verboden' width='20px' height='20px' /><span class='limegreen bold'>" . $bericht . "</span></td> 
			</tr>
			<tr>
				<td class='table_mainTxt padding_left' width='550px'><a href='javascript:history.go(-1);'>Ga terug</a></td> 
			</tr>
		</table>
	";
}

/* LAY-OUT functies */
function getStatusColor($value){
	if($value <= 25 && $value >= 1){
		$status = "red";
	} else if($value >= 26 && $value <= 50){
		$status = "orange";
	} else if($value >= 51 && $value <= 100){
		$status = "limegreen";
	} else {
		$status = "white";
	}
	return $status;
}
function printStatusBar($value){
	print "
	<div class='rank_box'>
		
		<div class='rank_bar' style='background-color:" . getStatusColor($value) . "; width:" . $value . "px;'><p class='bold unselectable'>" . $value . " %</p></div>
	</div>";
}
function RandomGenerateCode(){

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < 10; $i++) {
		$randstring = $randstring . $characters[rand(0, strlen($characters))];
    }
    return $randstring;
}


/* ------------- DATABASE HELPER FUNCTIES ------------------------ */
function GetAllCountries($connection){
	$landen = [""];
	$landenQuery = mysqli_query($connection,"SELECT * FROM landen");
	$i = 1;
	while($land = mysqli_fetch_object($landenQuery)){
		$landen[$i] = $land->land;
		$i++;
	}
	return $landen;
}
?>
