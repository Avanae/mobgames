<?php
	include('config.php');
	include('include/functions.php');

	if(isset($_GET["start"])){
		$camelId = $_GET["start"];
		$spelerid = $spelerInfo->id;
		$inzet = $_GET["inzet"];
		
		$speeds = range(2,8);
		shuffle($speeds);
		$speeds = array_slice($speeds, 0, 6);
		
		$won = true;
		for($i = 0; $i < 6; $i++){
			if($speeds[$camelId] < $speeds[$i]){
				$won = false;
			}
		}
		// Als de speler gewonnen heeft winst berekenen
		if($won){
			$profitMultipliers = [2,3,4,5,7,8];
			$profit = $inzet*$profitMultipliers[$camelId];
			mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "' WHERE id='" . $spelerInfo->id . "'");	
		} else {
			mysqli_query($con,"UPDATE leden SET cash=cash-'" . $inzet . "' WHERE id='" . $spelerInfo->id . "'");
		}
		echo json_encode($speeds);
		exit;
	}
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php	
	include("check_login.php");
	include("check_jail.php");
	
?>
<script type="application/javascript">
	var gainAr = [2,3,4,5,7,8];
	var gameTimer = '';
	var playerMoney = <?php echo $spelerInfo->cash; ?>;
	var kameel = 0;
	var inzet = 0;
	var winnaar = 0;
	var speeds = [];
	function setStage(){
		document.getElementById('racetrack').style.display = 'none';
		document.getElementById('resultBox').style.display = 'none';
		document.getElementById('kamelenKeuzeMenu').style.display = 'block';
		document.getElementById('kameleninformatie').style.display = 'block';
		kameel = -1;
		inzet = 0;
		winnaar = 0;
		loadGameObjects();
		unloadRadio();
	}
	function unloadRadio(){
		for(var l = 0; l < 6; l++){
			var r = document.getElementById('kameel' + l);
			r.checked = false;
		}
	}
	function loadGameObjects(){
		for(var i = 0; i < 6; i++){
			document.getElementById("rider" + i).innerHTML = '<p><img id="rider_image' + i + '" src="images/icons/camelrider.gif" height="22px" width="20px" title="' + i + '" /></p>';
		}
		setRider('rider_image0');
		setRider('rider_image1');
		setRider('rider_image2');
		setRider('rider_image3');
		setRider('rider_image4');
		setRider('rider_image5');
	}
	function setRider(ridername){
	   riderOb = document.getElementById(ridername);
	   riderOb.style.position= 'relative'; 
	   riderOb.style.left = '0px'; 
	}
	function chooseCamel(el){
		kameel = el.value;
	}
	function doGamble(){
		// De inzet ophalen
		var inz = document.getElementById('inzet').value;
		if(inz === "" || typeof inz == 'undefined' || isNaN(inz)){
			alert('Je moet een geldig bedrag opgeven.');
		} else if(parseInt(inz) < 0){
			alert('De minimum inzet is &euro; 1.');	
		} else if(parseInt(inz) > 10000){
			alert('De maximum inzet is &euro; 10.000.');	
		} else {
			if(parseInt(inz) > playerMoney){
				alert('Je hebt zoveel geld niet op zak. Ga eerst langs de bank of zet een lager bedrag in.');
			} else {
				if(kameel == -1){
					alert('Kies een geldige kameel');
				} else {
					setPlayerTicket(inz,kameel);
				}
			}
		}
	}
	function setPlayerTicket(inzetje,camel){
		document.getElementById('resultBox').style.display = 'block';
		var input = document.getElementById('userInput');
		input.innerHTML = "&euro; " + inzetje;
		inzet = inzetje;
		var cam = document.getElementById('userCamel');
		cam.innerHTML = parseInt(camel)+1;
		document.getElementById('racetrack').style.display = 'block';
		document.getElementById('kamelenKeuzeMenu').style.display = 'none';
		document.getElementById('kameleninformatie').style.display = 'none';
		loadDoc(inzet,kameel);
		gameTimer = setInterval(function(){engine()},100);
	}
	function engine(){
		moveCamel('rider_image0',0);
		moveCamel('rider_image1',1);
		moveCamel('rider_image2',2);
		moveCamel('rider_image3',3);
		moveCamel('rider_image4',4);
		moveCamel('rider_image5',5);
	}
	//----------------------------------------------------
	function moveCamel(camel,id){
		//var speed = Math.floor((Math.random()*10)+3);
		var speed = speeds[id];
		riderOb = document.getElementById(camel);
		if(parseInt(riderOb.style.left) <= 530 && winnaar === 0){
			riderOb.style.left = parseInt(riderOb.style.left) + speed + 'px';  
			if(parseInt(riderOb.style.left) > 530){
				// winnar
				clearInterval(gameTimer);
				var win = parseInt(riderOb.title);
				if(win == kameel){
					var price = inzet*parseInt(gainAr[kameel]);
					//alert('Jou kameel heeft de race gewonnen en je wint ' + price);
					document.getElementById("raceResult").innerHTML = 'Jou kameel heeft de race gewonnen en je wint ' + price;
				} else {
					//alert('Kameel ' + (win+1) + ' heeft gewonnen dus je bent je inzet kwijt');
					document.getElementById("raceResult").innerHTML = 'Kameel ' + (win+1) + ' heeft gewonnen dus je bent je inzet kwijt';
				}
				playerMoney = <?php echo $spelerInfo->cash; ?>;
				winnaar = riderOb.title;
				
				setTimeout(function(){reload()},5000);
			}
		}
	}
	//-----------------------------------------------------
	function reload(){
		kameel = -1;
		inset = 0;
		location.reload();
	}

	function loadDoc(inzet,camelid){
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var res = this.responseText;
				speeds = JSON.parse(res);
		   }
		};
		xhttp.open("GET", "kamelenrace.php?start=" + camelid + "&inzet=" + inzet, false);
		xhttp.send();
	}
</script>
<style>
	#track {
		width:550px;
		line-height:20px;
		display:block;
		background-color:#6F4A00;
		background-size:100% 100%;
		border-top:1px solid #333;
		border-left:1px solid #D99300;
		border-right:1px solid #D99300;
		padding:5px;
	}
	#racetrack {
		display:block;
		width:545px;
	}
</style>
<table colspan="1" class="inhoud_table" id="kameleninformatie" width="550px">
	<tr>
		<td class="table_subTitle"><h2 class="table_title">Kamelen race</h2></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5">Denk jij de bovenhand te hebben op de racebaan? Probeer je geluk en zet geld in op een van onze kamelen. Als je wint krijg je het bedrag dubbel en dik terug. Als de kansen 7:1 zijn en je zou &euro; 20 inzetten. Dan zou je 7 keer &euro; 20 winnen wat neerkomt op &euro; 140 in totaal.</td>
	</tr>
		
</table>
<table colspan="1" class="inhoud_table" id="resultBox" width="550px">
	<tr>
		<td class="table_subTitle" width="550px">Jou ticketje</td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5">Jou inzet: <div id="userInput" class="padding_left_5"></div></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5">Jou kameel: <div id="userCamel" class="padding_left_5"></div></td>
	</tr>
	<tr>
		<td class="table_mainTxt padding_5">Resultaat: <div id="raceResult" class="padding_left_5">Onbeslist</div></td>
	</tr>
</table>
<div class="margin_bottom_5" id="kamelenKeuzeMenu">
	<table colspan="3" class="inhoud_table" width="550px">
		<tr>
			<td class="table_subTitle" colspan="3" width="100%">Onze kamelen:</td>
		</tr>
		<tr>
			<td class="subTitle" colspan="1" width="25%">Kameel</td>
			<td class="subTitle" colspan="1" width="35%">Winst ratio</td>
			<td class="subTitle" colspan="1" width="40%"></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" colspan="1" width="25%"><input type="radio" name="kameel" id="kameel0" value="0" checked onclick="chooseCamel(this)" /><span class="padding_left_5"><label for="kameel0">Kameel 1</label></span></td>
			<td class="table_mainTxt padding_5" colspan="1" width="35%">2:1</td>
			<td class="table_mainTxt padding_5" colspan="1" width="40%"></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" colspan="1" width="25%"><input type="radio" name="kameel" id="kameel1" value="1" onclick="chooseCamel(this)" /><span class="padding_left_5"><label for="kameel1">Kameel 2</label></span></td>
			<td class="table_mainTxt padding_5" colspan="1" width="35%">3:1</td>
			<td class="table_mainTxt padding_5" colspan="1" width="40%"></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" colspan="1" width="25%"><input type="radio" name="kameel" id="kameel2" value="2" onclick="chooseCamel(this)" /><span class="padding_left_5"><label for="kameel2">Kameel 3</label></span></td>
			<td class="table_mainTxt padding_5" colspan="1" width="35%">4:1</td>
			<td class="table_mainTxt padding_5" colspan="1" width="40%"></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" colspan="1" width="25%"><input type="radio" name="kameel" id="kameel3" value="3" onclick="chooseCamel(this)" /><span class="padding_left_5"><label for="kameel3">Kameel 4</label></span></td>
			<td class="table_mainTxt padding_5" colspan="1" width="35%">5:1</td>
			<td class="table_mainTxt padding_5" colspan="1" width="40%"></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" colspan="1" width="25%"><input type="radio" name="kameel" id="kameel4" value="4" onclick="chooseCamel(this)" /><span class="padding_left_5"><label for="kameel4">Kameel 5</label></span></td>
			<td class="table_mainTxt padding_5" colspan="1" width="35%">7:1</td>
			<td class="table_mainTxt padding_5" colspan="1" width="40%"></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" colspan="1" width="25%"><input type="radio" name="kameel" id="kameel5" value="5" onclick="chooseCamel(this)" /><span class="padding_left_5"><label for="kameel5">Kameel 6</label></span></td>
			<td class="table_mainTxt padding_5" colspan="1" width="35%">8:1</td>
			<td class="table_mainTxt padding_5" colspan="1" width="40%"></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" colspan="1" width="25%"><label for="inzet">Cash:</label></td>
			<td class="table_mainTxt padding_5" colspan="2" width="75%">&euro; <?php echo formatDecimaal($spelerInfo->cash); ?></td>
		</tr>
		<tr>
			<td class="table_mainTxt padding_5" colspan="1" width="25%"><label for="inzet">Inzet:</label></td>
			<td class="table_mainTxt padding_5" colspan="2" width="75%"><input type="text" name="inzet" id="inzet" class="input_form padding_5" maxlength="9" size="30" /> <input type="button" onclick="doGamble()" value="Gok!" class="button_form" /></td>
		</tr>
	</table>
</div>
<div id="racetrack" width="550px">
	<div id="track"><div id="rider0" class="rider"></div></div>
	<div id="track"><div id="rider1" class="rider"></div></div>
	<div id="track"><div id="rider2" class="rider"></div></div>
	<div id="track"><div id="rider3" class="rider"></div></div>
	<div id="track"><div id="rider4" class="rider"></div></div>
	<div id="track"><div id="rider5" class="rider"></div></div>
</div>
<script>setStage(); </script>