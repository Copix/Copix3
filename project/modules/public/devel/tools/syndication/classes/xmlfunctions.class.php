<?php

class XmlFunctions {
	// caractère de fin de ligne (si on ne veut pas compresser)
	private $_endLine = "\r\n";
	
	// caractère tabulation (si on ne veut pas compresser)
	private $_tab = "\t";
	
	// nombre de tabulation pour la node en cours
	private $_nbrTab = 0;
	
	// chaine contenant le xml
	private $_content = null;
	
	/**
	 * Constructeur
	 * 
	 * @param bool $pCompress Compression du retour (pas de tabulations et pas de retour à la ligne)
	 * @param string $pEncoding Encodage
	 */
	public function __construct ($pCompress = false, $pEncoding = 'UTF-8') {
		if ($pCompress) {
			$this->_endLine = '';
			$this->_tab = '';
		}
		
		$this->_content = '<?xml version="1.0" encoding="' . $pEncoding . '" ?>' . $this->_endLine;
	}
	
	/**
	 * Retourne une valeur valide pour une valeur d'une node
	 * 
	 * @param string $pValue Valeur de la node
	 * @return string
	 */
	private function _getValue ($pValue) {
		//$toReturn = str_replace ('&', '&amp;', $toReturn);
		$toReturn = str_replace ('<', '&lt;', $pValue);
		$toReturn = str_replace ('>', '&gt;', $toReturn);
		$toReturn = str_replace ("'", '&apos;', $toReturn);
		$toReturn = str_replace ('"', '&quot;', $toReturn);
		
		return $toReturn;
	}
	
	/**
	 * Retourne une valeur valide pour un attribut
	 * 
	 * @param string $pValue Valeur de l'attribut
	 * @return string
	 */
	private function _getAttValue ($pValue) {
		$toReturn = str_replace ('<', '&lt;', $pValue);
		$toReturn = str_replace ('>', '&gt;', $toReturn);
		$toReturn = str_replace ("'", '&apos;', $toReturn);
		$toReturn = str_replace ('"', '&quot;', $toReturn);
		
		return $toReturn;
	}
	
	/**
	 * Ajoute une node
	 * 
	 * @param string $pNodeName Nom de la node
	 * @param string $pValue Valeur de la node
	 * @param array $pAttributes Attributs (clef = nom, valeur = valeur)
	 * @param bool $closeNode Si on n'a pas de valeur (null ou vide), fermer la node ou pas avec  />
	 * @param bool $pForceIfEmpty Force l'écriture de la node même si il n'y a aucune valeur et aucun attribut, et qu'on veut la fermer (node vide)
	 * @return string
	 */
	public function addNode ($pNodeName, $pValue = null, $pAttributes = null, $closeNode = true, $pForceIfEmpty = false) {
		if (
			((!is_null ($pValue) || (is_array ($pAttributes) && count ($pAttributes) > 0)) && $closeNode)
			|| !$closeNode
			|| $pForceIfEmpty
		) {		
			// création de la chaine des attributs
			$attStr = '';
			if (!is_null ($pAttributes)) {
				foreach ($pAttributes as $attName => $attValue) {
					if (!is_null ($attValue)) {
						$hasAtt = true;
						$attStr .= ' ' . $attName . '="' . $this->_getAttValue ($attValue) . '"';
					}
				}
			}
	
			// si on a une valeur
			if (strlen (trim ($pValue)) > 0) {
				$this->_content .= $this->_getTabStr () . '<' . $pNodeName . $attStr . '>' . $this->_getValue ($pValue) . '</' . $pNodeName . '>' . $this->_endLine;
				
			// si on n'a pas de valeur
			} else {
				$endStr = ($closeNode) ? ' />' : '>';
				$this->_content .= $this->_getTabStr () . '<' . $pNodeName . $attStr . $endStr . $this->_endLine;
			}
			
			if (!$closeNode) {
				$this->_nbrTab++;
			}
		}
	}
	
	/**
	 * Ouvre une node
	 * 
	 * @param string $pNodeName
	 */
	public function openNode ($pNodeName, $pAttributes = null) {
		$this->addNode ($pNodeName, null, $pAttributes, false);
	}
	
	/**
	 * Ferme une node
	 * 
	 * @param string $pNodeName Nom de la node
	 * @return string
	 */
	public function closeNode ($pNodeName) {
		$this->_nbrTab--;
		$this->_content .= $this->_getTabStr () . '</' . $pNodeName . '>' . $this->_endLine;
	}
	
	/**
	 * Retourne le contenu XML
	 * 
	 * @return string
	 */
	public function getContent () {
		return $this->_content;
	}
	
	/**
	 * Retourne une chaine avec le nombre de tabulations nécessaires
	 * 
	 * @return string
	 */
	private function _getTabStr () {
		$tabStr = '';
		for ($boucle = 0; $boucle < $this->_nbrTab; $boucle++) {
			$tabStr .= $this->_tab;
		}
		return $tabStr;
	}		
}
?>