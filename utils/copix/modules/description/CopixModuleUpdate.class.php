<?php
/**
 * @package copix
 * @subpackage modules
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Informations sur la mise à jour d'une version précise d'un module
 * 
 * @package copix
 * @subpackage modules
 */
class CopixModuleUpdate {
	/**
	 * Dans Copix 3.0.x, CopixModule::getInformations renvoyait un stdClass avec des propriétés
	 * Pour conserver cette compatibilité, ce tableau indique les liens entre les anciennes propriétés et les nouvelles méthodes
	 *
	 * @var array
	 */
	private $_allowGet = array ('script' => 'getScript', 'from' => 'getFrom', 'to' => 'getTo');
	
	/**
	 * Nom du script à executer
	 *
	 * @var string
	 */
	private $_script = null;
	
	/**
	 * Version avant la mise à jour
	 *
	 * @var string
	 */
	private $_from = null;
	
	/**
	 * Version après la mise à jour
	 *
	 * @var string
	 */
	private $_to = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $pScript Nom du script PHP à executer
	 * @param string $pFrom Version avant la mise à jour
	 * @param string $pTo Version après la mise à jour
	 */
	public function __construct ($pScript, $pFrom, $pTo) {
		$this->_script = $pScript;
		$this->_from = $pFrom;
		$this->_to = $pTo;
	}
	
	/**
	 * Pour la compatibilité avec Copix 3.0.x
	 *
	 * @param string $pName Propriété dont on veut la valeur
	 * @return mixed
	 */
	public function __get ($pName) {
		if (array_key_exists ($pName, $this->_allowGet)) {
			$method = $this->_allowGet[$pName];
			return $this->$method ();
		}
	}
	
	/**
	 * Retourne le nom du script PHP à executer
	 *
	 * @return string
	 */
	public function getScript () {
		return $this->_script;
	}
	
	/**
	 * Retourne le numéro de version avant la mise à jour
	 *
	 * @return string
	 */
	public function getFrom () {
		return $this->_from;
	}
	
	/**
	 * Retourne le numéro de version après la mise à jour
	 *
	 * @return stirng
	 */
	public function getTo () {
		return $this->_to;
	}
}