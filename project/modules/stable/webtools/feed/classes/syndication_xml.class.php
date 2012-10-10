<?php
/**
 * Permet de faciliter la génération de XML
 * Par exemple on peut compresser le retour, en ne mettant pas de tabulations / retours à la ligne, ou avoir une indentation
 */
class SyndicationXML {
	/**
	 * Caractère de fin de ligne (si on ne veut pas compresser)
	 *
	 * @var string
	 */
	private $_endLine = "\r\n";
	
	/**
	 * Caractère tabulation (si on ne veut pas compresser)
	 *
	 * @var string
	 */
	private $_tab = "\t";
	
	/**
	 * Nombre de tabulation pour la node en cours
	 *
	 * @var int
	 */
	private $_nbrTab = 0;
	
	/**
	 * Le XML en cours
	 *
	 * @var string
	 */
	private $_content = null;
	
	/**
	 * Namespcaces
	 *
	 * @var array
	 */
	private $_namespaces = array ();
	
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
		
		$this->_content = '<?xml version="1.0" encoding="' . $pEncoding . '" --ADDNAMESPACES--?>' . $this->_endLine;
	}
	
	/**
	 * Retourne une valeur valide pour une valeur d'une node
	 * 
	 * @param string $pValue Valeur de la node
	 * @return string
	 */
	private function _getValue ($pValue) {
		$toReturn = $pValue;
		if(!preg_match(';^<\!\[CDATA\[;',$pValue)){
			$toReturn = str_replace ('<', '&lt;', $pValue);
			$toReturn = str_replace ('>', '&gt;', $toReturn);
			$toReturn = str_replace ("'", '&apos;', $toReturn);
			$toReturn = str_replace ('"', '&quot;', $toReturn);
		}
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
	 * @param boolean $pCloseNode Si on n'a pas de valeur (null ou vide), fermer la node ou pas avec  />
	 * @param boolean $pForceIfEmpty Force l'écriture de la node même si il n'y a aucune valeur et aucun attribut, et qu'on veut la fermer (node vide)
	 * @return string
	 */
	public function addNode ($pNodeName, $pValue = null, $pAttributes = null, $pCloseNode = true, $pForceIfEmpty = false) {
		if (
			(($pValue != null || (is_array ($pAttributes) && count ($pAttributes) > 0)) && $pCloseNode)
			|| !$pCloseNode
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
				$endStr = ($pCloseNode) ? ' />' : '>';
				$this->_content .= $this->_getTabStr () . '<' . $pNodeName . $attStr . $endStr . $this->_endLine;
			}
			
			if (!$pCloseNode) {
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
	 * Ajoute un espace de nom
	 *
	 * @param string $name
	 * @param string $uri
	 */
	public function addNamespace($pName, $pURI) {
		$this->_namespaces[$pName] = $pURI;
	}
	
	/**
	 * Retourne le contenu XML
	 * 
	 * @return string
	 */
	public function getContent () {
		$add = null;
		foreach ($this->_namespaces as $name => $uri) {
			$add .= $name . '="' . $uri .'" ';
		}
		
		return str_replace ('--ADDNAMESPACES--', $add, $this->_content);
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