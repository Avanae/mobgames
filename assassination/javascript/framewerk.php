<script type="text/javascript">
	function toggleview(element1,button) {  
		element1 = document.getElementById(element1);  
		if (element1.style.display == 'block' || element1.style.display == ''){
			element1.style.display = 'none';  
		} else {
			element1.style.display = 'block';  
		}
		return;  
	} 
	function changeTextButton(button){
		var btn = document.getElementById(button);
		if(btn.src == "images/nopic.gif"){
			btn.src = "images/system/icons/vinkje.png";
		} else if(button.src == "images/system/icons/vinkje.png"){
			btn.src = "images/nopic.gif";
		}
	}
	function GetStatusColor(leven){
		var status = "limegreen";
		leven = parseInt(leven, 10);
		if(leven >= 1 && leven <= 25){
			
			status = "red";
		} else if(leven >= 26 && leven <= 50){
			status = "orange";
		} else if(leven >= 51 && leven <= 100){
			status = "limegreen";
		} else {
			status = "white";
		}
		return status;
	}
	function CreateStatusBar(leven){
		var statusColor = GetStatusColor(leven);
		
		var div = document.createElement("div");
		div.className = "rank_box";
		
		var divBar = document.createElement("div");
		divBar.className = "rank_bar";
		divBar.setAttribute("style","background-color:" + statusColor + "; width:" + leven + "px;");
		
		var span = document.createElement("p");
		span.className="bold unselectable";
		span.innerHTML = leven + " %";
		
		divBar.appendChild(span);
		div.appendChild(divBar);
		
		document.getElementById("gpsLeven").innerHTML = "";
		document.getElementById("gpsLeven").appendChild(div);
		
	}
	function changeIframe(framename,newsource){
		iframe = document.getElementById(framename);
		iframe.src = newsource;
	}
	function ExeGetReq(url,callback){
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var res = this.responseText;
				callback(res);
		   }
		};
		xhttp.open("GET", url, false);
		xhttp.send();
	}
	function ExeGetReq(url){
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				var res = this.responseText;
				
		   }
		};
		xhttp.open("GET", url, false);
		xhttp.send();
	}
</script>