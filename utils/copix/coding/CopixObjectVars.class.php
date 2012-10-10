<?php
/**
 * @package  	copix
 * @subpackage	coding
 * @author		Steevan BARBOYON, Guillaume Perréal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet de récupérer des propriétés privées et protégées d'un objet, avec var_export
 * 
 * @package		copix
 * @subpackage	coding
 */
class CopixObjectVars {
	/**
	 * Nom de l'objet de base que l'on a transformé en CopixReflectObject
	 *
	 * @var string
	 */
	private $_reflectedObjectName = null;
	
	/**
	 * Propriétés de l'objet de base
	 *
	 * @var array
	 */
	private $_vars = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pObjectName Nom de l'objet de base que l'on a transformé en CopixObjectVars
	 * @param array $pVars Propriétés de l'objet de base, tout accès confondu (public, protected et private)
	 */
	public function __construct ($pObjectName, $pVars) {
		$this->_reflectedObjectName = $pObjectName;
		$this->_vars = $pVars;
	}
	
	/**
	 * Retourne les variables de l'objet de base
	 *
	 * @return array
	 */
	public function getVars () {
		return $this->_vars;
	}
	
	/**
	 * Retourne le nom de l'objet de base
	 *
	 * @return string
	 */
	public function getObjectName () {
		return $this->_reflectedObjectName;
	}
}