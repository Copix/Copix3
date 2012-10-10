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
 * Filtres pour récupérer des données flottantes
 * 
 * @package		copix
 * @subpackage	filter
 */
class CopixFilterFloat extends CopixAbstractFilter {
	/**
	 * Filtre numérique utilisé en interne
	 *
	 * @var CopixFilterNumeric
	 */
	private $_numericFilter;
	
	/**
	 * Constructeur
	 *
	 * @param array $pParams paramètre passés au filtre
	 */
	public function __construct ($pParams = array ()){
		$this->_numericFilter = new CopixFilterNumeric (array ('withComma'=>true));
		parent::__construct ($pParams);
	}

	/**
	 * Filtre les données numériques flottantes
	 */
	public function get ($pValue){
		$decimal = (($decimal = $this->getParam ('decimal', null, _validator ('numeric', array ('min'=>0))))!==null) ? '.'.$decimal : null;
		//On passe par sprintf pour éviter les bugs existants dans PHP 5.2.2+ à 5.3-
		//http://bugs.php.net/43053 
		return sprintf ('%'.$decimal.'F', floatval ($this->_numericFilter->get ($pValue)));
	}
}