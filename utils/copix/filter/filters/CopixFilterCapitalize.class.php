<?php
/**
 * @package		copix
 * @subpackage	filter
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link 		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Filtres pour récupérer des données capitalisées
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterCapitalize extends CopixAbstractFilter {
	/**
	 * Construction et initialisation des options
	 *
	 * @param array $pParams le tableau d'options
	 */
	public function __construct ($pParams = array ()){
		if (!isset ($pParams['lowerCaseWords'])){
			$pParams['lowerCaseWords'] = array ();
		}
		if (!isset ($pParams['upperCaseWords'])){
			$pParams['upperCaseWords'] = array ();
		}
		foreach ($pParams['upperCaseWords'] as $key=>$upperCaseElement){
			$pParams['upperCaseWords'][$key] = strtolower ($upperCaseElement);
		}
		foreach ($pParams['lowerCaseWords'] as $key=>$lowerCaseElement){
			$pParams['lowerCaseWords'][$key] = strtolower ($lowerCaseElement);
		}
		parent::__construct ($pParams);
	}
	
	/**
	 * Récupération de la chaîne capiotalisée
	 *
	 * @param mixed $pValue La chaine de caractère (mais peut être un autre type qui sera capitalisée)
	 * @return string
	 */
	public function get ($pValue){
		$final = array ();
		$spacesPart = explode (' ', $pValue);
		foreach ($spacesPart as $spacePart){
			$tiretParts = explode ('-', $spacePart);
			$finalTiret = array ();
			foreach ($tiretParts as $tiretPart){
				$finalTiret[] = $this->_capitalizeString ($tiretPart);				
			}
			$final[] = implode ('-', $finalTiret);
		}
		return implode (' ', $final);
	}

	/**
	 * Capitalisation d'une chaine sans tenir compte des espaces ou autres élements de séparation
	 * 
	 * @param string $pPart l'élément a capitaliser
	 * @return string
	 */
	private function _capitalizeString ($pPart){
		$lowerPart = strtolower ($pPart);
		if (in_array ($lowerPart, $this->getParam ('lowerCaseWords', array ()), true)){
			return $lowerPart;
		}elseif (in_array ($lowerPart, $this->getParam ('upperCaseWords', array ()), true)){
			return strtoupper ($pPart);
		}else{
			return ucfirst ($lowerPart);
		}
	}	
}