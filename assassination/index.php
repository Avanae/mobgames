<?php
include("config.php");
if(isset($_GET["cookies"])){
	$_SESSION["CookieAgreed"] = true;
}
$banner = "1";
if(isset($_SESSION['id']) && isset($_SESSION["CookieAgreed"])){
	$banner = $spelerInfo->banner;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title><?php echo $WEBSITE_TITLE; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<meta name="author" content="<?php echo $WEBSITE_AUTHOR; ?>">
		<meta name="keywords" content="<?php echo $WEBSITE_KEYWORDS; ?>" />
		<meta name="description" content="<?php echo $WEBSITE_DESCRIPTION; ?>" />
		<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
		<link href="stylesheets/gps_stijl.css" type="text/css" rel="stylesheet" />
		<link rel="icon" type="image/png" href="images/favicon.png">
		<?php include("javascript/framewerk.php"); ?>
		<script>
			function CookieAgree(){
				var cookieBar = document.getElementById("gdprBar");
				cookieBar.remove();
				ExeGetReq("index.php?cookies");
			}
		</script>
	</head>
	<body>
		<?php if(!isset($_SESSION["CookieAgreed"])){ ?>
			<div class="cookie-bar" id="gdprBar">
				<span class="message">Onze website maakt gebruik van cookies om je gebruikerservaring te verbeteren. Waarom deze cookies? <a href="policy.php" target="_blank">Lees hier</a></span>
			 
				<label for="checkbox-cb" class="close-cb"><img src="images/nopic.gif" width="20px" height="20px" onclick="CookieAgree()" /></label>
			 
			</div>
		<?php } ?>
		<div id="body">
 
			
			<div id="container">
				<div id="container_header">
				<?php if($banner == 1){ ?>
					<div id="top_header"><?php include($TOP_HEADER_PAGINA); ?></div>
				<?php } ?>
				</div>
				<div id="nav"><?php include($EXTRA_NAVIGATIE_BALK); ?></div>
				<div id="container_inhoud">
					<div><?php include($INHOUD_MENU_LEFT); ?></div>
					<base target="main" />
					<iframe id="inhoud" src="main.php" name="main" frameborder="0" colspan="1"></iframe>
					<div id="menu_right"><?php include($INHOUD_MENU_RIGHT); ?></div>
				</div>
				<div id="bottom_index"><?php include($BOTTOM_INDEX); ?></div>
			</div>
		</div>
	</body>
</html>
