<?php

class XmlFunctions {
	
	/**
	 * Remplace les caractères interdits par leur code HTML
	 * 
	 * @param string $pValue Valeur à transformer
	 * @return string
	 */
	public function xmlValue ($pValue) {
		//$toReturn = str_replace ('&', '&amp;', $toReturn);
		$toReturn = str_replace ('<', '&lt;', $pValue);
		$toReturn = str_replace ('>', '&gt;', $toReturn);
		$toReturn = str_replace ("'", '&apos;', $toReturn);
		$toReturn = str_replace ('"', '&quot;', $toReturn);
		
		return $toReturn;
	}
}
?>