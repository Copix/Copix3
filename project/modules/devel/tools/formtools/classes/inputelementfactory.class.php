<?php

/**
 * Classe de fabrique des éléments Inout du formulaire
 *  
 * @author bfavre
 *
 */
class InputElementFactory {
	
	/**
	 * Function de récupération de l'élément Input
	 * 
	 * @param $pTypeElement
	 * @param $pIdElement
	 * @param $pFieldName
	 * @param $pDefaultValues
	 * @return abstractInputElement
	 */
	static function get ($pTypeElement, $pIdElement, $pFieldName, $pDefaultValues = null){
		
		$className = 'InputElement'.$pTypeElement;
		_classInclude ('formtools|inputs/'.$className);
		
		
		return new $className ($pIdElement, $pFieldName, $pDefaultValues);
	}
}