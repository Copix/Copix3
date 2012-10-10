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
	 * Filtre minuscules
	 *
	 * @var CopixFilterLowerCase
	 */
	private $_lowerCaseFilter;
	
	/**
	 * Construction et initialisation des options
	 *
	 * @param array $pParams le tableau d'options
	 */
	public function __construct ($pParams = array ()){
		$this->_lowerCaseFilter = new CopixFilterLowerCase ();

		if (!isset ($pParams['lowerCaseWords'])){
			$pParams['lowerCaseWords'] = array ();
		}
		if (!isset ($pParams['upperCaseWords'])){
			$pParams['upperCaseWords'] = array ();
		}
		foreach ($pParams['upperCaseWords'] as $key=>$upperCaseElement){
			$pParams['upperCaseWords'][$key] = $this->_lowerCaseFilter->get ($upperCaseElement);
		}
		foreach ($pParams['lowerCaseWords'] as $key=>$lowerCaseElement){
			$pParams['lowerCaseWords'][$key] = $this->_lowerCaseFilter->get ($lowerCaseElement);
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
		$lowerPart = $this->_lowerCaseFilter->get ($pPart);
		if (in_array ($lowerPart, $this->getParam ('lowerCaseWords', array ()), true)){
			return $lowerPart;
		}elseif (in_array ($lowerPart, $this->getParam ('upperCaseWords', array ()), true)){
			return _filter ('UpperCase')->get ($pPart);
		}else{
			if (extension_loaded ('mbstring')){
				return mb_convert_case ($lowerPart, MB_CASE_TITLE, CopixI18N::getCharset ());
			}
			return ucfirst ($lowerPart);
		}
	}	
}