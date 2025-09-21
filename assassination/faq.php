<?php
include("config.php");
include("SYSTEM_CONFIGURATION.php");
include("include/functions.php");
$faqAant = mysqli_query($con,"SELECT DISTINCT category FROM faq ORDER BY category"); 
// aantal categorien tellen
$faqAantal = mysqli_num_rows($faqAant);
// $gg = 1;
?>
<link href="stijl<?php echo $SYSTEM_STYLE; ?>.css" type="text/css" rel="stylesheet" />
<?php
if(isset($_POST['x'])){
	if(!isset($_POST['y'])){
		$category = "algemeen"; 
	} else {
		$category = mysqli_real_escape_string($con,$_POST['y']);
	}
	$aantalOnderwerpen1 = mysqli_query($con,"SELECT * FROM faq WHERE category='" . $category . "'");
	// aantal onderwerpen per category tellen
	$aantalOnderwerpen = mysqli_num_rows($aantalOnderwerpen1);
			print"<table width='550px' colspan='1'>
				<tr>
				<td class='table_subTitle' colspan='1' width='550px'>" . $category . "</td>
				</tr>";
			while($faqTopic = mysqli_fetch_object($aantalOnderwerpen1)){
				print"
				<tr>
					<td class='table_mainTxt padding_left' width='550px'><a href='faq.php?x=" . $faqTopic->x . "'>" . $faqTopic->onderwerp . "</a></td>
				</tr>";
			}	
			print"	<tr>
						<td class='table_mainTxt padding_left outline'><a href='faq.php' target='main'>Klik hier om terug te gaan naar de zoek optie</a></td>
					</tr></table>";
} else {
	?>
	<form method="post" action="faq.php">
		<table width="550px" colspan="2">
			<tr>
				<td class="table_subTitle" colspan="2">F.A.Q Zoeken</td>
			</tr>
			<tr>
				<td class="table_mainTxt padding_left" width="80%" colspan="1">
				<label for="cat">Kies een category</label>
				
				</td>
				<td class="table_mainTxt" width="20%" colspan="1"><select id="cat" name="y" size="<?php echo $faqAantal;  ?>">
				<?php $gg=0; while($faqCat = mysqli_fetch_object($faqAant)){
					print "<option value='" . $faqCat->category . "'>" . $faqCat->category . "</option>";
				} $gg++; ?>
				</select></td>
			</tr>
			<tr>
				<td class="table_mainTxt" align="right" width="100%" colspan="2"><input type="submit" class="button_form" value="Zoek categorie" name="x" /></td>
			</tr>
		</table>
	</form>
	<?php
}
?>
<?php
if(isset($_GET['x'])){
	$onderwerpFaq = mysqli_real_escape_string($con,$_GET['x']);
	$onderwerpOpzoeken2 = mysqli_query($con,"SELECT * FROM faq WHERE x='" . $onderwerpFaq . "'");
	$onderwerpOpzoeken = mysqli_fetch_object($onderwerpOpzoeken2);
	if(!$onderwerpOpzoeken){
		print_error("FAQ - fout","Dit topic bestaat niet, gelieve een admin te contacteren via de helpdesk.");
		exit();
	} else {
		print "<table width='550px'>
					<tr>
						<td class='table_subTitle' width='550px'>" . $onderwerpOpzoeken->onderwerp . "</td> 
					</tr>
					<tr>
						<td class='table_mainTxt outline padding_5' width='550px'>" . $onderwerpOpzoeken->uitleg . "</td>
					</tr>
		</table>
		<table width='550px'><tr><td class='table_mainTxt'><a href=\"javascript:history.go(-1);\">Ga terug</a></td></tr></table>
		";
	}
}
?>
