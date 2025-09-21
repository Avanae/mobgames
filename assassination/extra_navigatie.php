<?php
	include("config.php");
	include("SYSTEM_CONFIGURATION.php");
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<div id="nav_container">
<?php if(isset($_SESSION['id'])){ ?>
	<div id="nav_links">
		<ul class="l3">
			<li><a href="nieuws.php?h=0&y=10" target="main"><img src="images/icons/nieuws.gif" alt="nieuws" title="Bekijk het nieuws." width="25px" /></a></li>
			<li><a href="berichten.php?x=inbox" target="main"><img src="images/icons/berichten.gif" alt="berichten" title="Beheer hier je berichten." width="25px" /></a></li>	
			<li><a href="landen.php" target="main"><img src="images/icons/wereldmap.png" alt="wereld kaart" title="Bekijk hier de wereldkaart." width="25px" /></a></li>
		</ul>
	</div>
<?php } else {?>
		<div id="nav_bezoeker"><ul class="l1"><li><a href="promoot.php" target="main">Promoot</a></li></ul></div>
<?php } ?>
	<div id="nav">
		<?php if(isset($_SESSION['id'])){ ?>
			<ul class="l1">
				<li><p><a href="vooruitgang.php" target="main" class="unselectable">Hoofdkwartier</a></p>
					<ul class="l2">
						<li><a href="vooruitgang.php" target="main">Speler Informatie</a></li>
						<li><a href="vooruitgang.php?x=bezittingen" target="main">Priv<?php echo utf8_encode('é'); ?> bezittingen</a></li>
						<li><a href="vooruitgang.php?y=moord" target="main">Moord pogingen</a></li>
						<li><a href="vooruitgang.php?z=instellingen" target="main">Profiel Instellingen</a></li>
					</ul>
				</li>
			</ul>
		<?php } ?>
		<ul class="l1">
			<li><p><a href="stats_algemeen.php" class="unselectable">Algemene Statistieken</a></p>
				<ul class="l2">
					<li><a href="stats_prijzen.php" target="main">Prijzen</a></li>
					<li><a href="stats_casino.php" target="main">Casino's</a></li>
					<li><a href="laatste_spelers.php" target="main">10 Nieuwste leden</a></li>
					<li><a href="laatste_doden.php" target="main">Vermoorde spelers</a></li>
					<?php if(isset($_SESSION['id'])){ ?>
						<li><a href="klassement.php" target="main">Klassement</a></li>
						<li><a href="speler_lijst.php" target="main">Speler lijst</a></li>
					<?php } ?>
					
				</ul>
			</li>
		 </ul>
	 
		<?php if(isset($_SESSION['id'])){ ?>
			<ul class="l1">
				<li><p><a href="stats_algemeen.php" class="unselectable">Algemeen</a></p>
					<ul class="l2">
						<li><a href="promoot.php" target="main">Promoot</a></li>
						<li><a href="faq.php" target="main">F.A.Q</a></li>
						<li><a href="schandpaal.php" target="main">Schandpaal</a></li>
						<li><a href="login.php?x=loguit" target="main">UITLOGGEN</a></li>
					</ul>
				</li>
			 </ul>
		<?php } ?>
	 </div>
	<?php
	/*
	<div id="nav_rechts">
	
		<ul class="l3">
			<li><a href="nieuws.php?h=0&y=10" target="main"><img src="images/icons/baksteen.jpg" alt="nieuws" title="Bekijk het nieuws." width="25px" /></a></li>
		</ul>
	</div>
	*/
	?>
	
</div>