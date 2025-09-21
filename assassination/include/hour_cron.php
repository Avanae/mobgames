<?php
	$con = mysqli_connect("localhost","assterror637_fritZakske","w00Q0cTUvS","assterror637_assassingame");
	
	// Update aandeel prijzen
	$aandelenQuery = mysqli_query($con,"SELECT * FROM beurs");
	if(mysqli_num_rows($aandelenQuery) >= 1)
	{
		while($aandeel = mysqli_fetch_object($aandelenQuery)){
			$newValue = rand($aandeel->minPrijs,$aandeel->maxPrijs);
			mysqli_query($con,"UPDATE beurs SET prijs='" . $newValue . "',vorigeprijs='" . $aandeel->prijs . "' WHERE id='" . $aandeel->id . "'");
		}
	}
	
	// Update kavel inkomsten
	$kavelsQuery = mysqli_query($con,"SELECT * FROM kavels WHERE eigenaar !='0'");
	if(mysqli_num_rows($kavelsQuery) >= 1){
		while($kavel = mysqli_fetch_object($kavelsQuery)){
			$kavelOwnerQuery = mysqli_query($con,"SELECT * FROM leden WHERE id='" . $kavel->eigenaar . "' AND status='levend' AND ban='0'");
			if(mysqli_num_rows($kavelOwnerQuery) == 1){
				$kavelOwner = mysqli_fetch_object($kavelOwnerQuery);
				$profit = $kavel->opbrengst;
				if($kavelOwner->familie != 0){
					$famQuery = mysqli_query($con,"SELECT * FROM families_belasting WHERE familieid='" . $kavelOwner->familie . "'");
					if(mysqli_num_rows($famQuery) == 1){
						$fam = mysqli_fetch_object($famQuery);
						$tax = floor(($profit/100)*$fam->kavelTax);
						$profit = $profit-$tax;
						mysqli_query($con,"UPDATE families SET geld=geld+'" . $tax . "' WHERE id='" . $kavelOwner->familie . "'");
					}
				}
				mysqli_query($con,"UPDATE leden SET cash=cash+'" . $profit . "' WHERE id='" . $kavel->eigenaar . "'");
			} else {
				mysqli_query($con,"UPDATE kavels SET eigenaar='0' WHERE id='" . $kavel->id . "'");
			}
		}
	}
?>
