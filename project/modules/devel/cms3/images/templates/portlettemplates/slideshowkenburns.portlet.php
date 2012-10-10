<?php
// fichier js spécifique à ce gabarit
$aExtraJs = array('slideshow.kenburns');

// fichier css spécifique à ce gabarit
$aExtraCss = array();

$aOption = $portlet->getOptions();

$arOptionsKenburns = array('slideZoom', 'slidePan');

foreach ($arOptionsKenburns as $option){
	if (array_key_exists($option, $aOption)){
		$elements = preg_split('/,/', $aOption[$option]);
		$toApply = '';
		foreach ($elements as $element){
			if(is_numeric($element) && $element >= 0 && $element <= 100){
				$toApply.= 	$element.',';	
			}
		}
		// suppression de la dernière vigule
		if(substr($toApply, -1, 1) == ','){
			$toApply = substr($toApply, 0,-1);
		}
		if(strlen($toApply) == 0){
			$aOption[$option] = 100;
		}else{
			// s'il y a des ',' on  a plusieurs éléments, on le met sous forme de tableau
			if(strrchr($toApply, ',')){
				$aOption[$option] = '['.$toApply.']';
			}else{
				$aOption[$option] = $toApply;
			}
		}
	}else{
		$aOption[$option] = 100;
	}
}

// paramètres du slideshow spécifiques à ce gabarit
$aExtraParams = array(
	// pour le KenBurns, pourcentage de zoom à appliquer sur l'image
	'zoom' => $aOption['slideZoom'],
	// offset en pourcentage sur le quel on va se déplacer
	'pan' => $aOption['slidePan'],
	'duration' => 0
);
include(dirname(__FILE__).'/slideshow.common.php');
?>