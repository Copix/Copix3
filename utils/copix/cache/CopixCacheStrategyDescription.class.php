<?php
/**
 * @package copix
 * @subpackage log
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Informations sur une stratégie de cache
 *
 * @package copix
 * @subpackage log
 */
class CopixCacheStrategyDescription {
	/**
	 * Pour la compatibilité avec Copix 3.0.x, on autorise l'accès aux propriétés suivantes
	 *
	 * @var array
	 */
	private $_allowGet = array ('id' => 'getId', 'caption' => 'getCaption');
	
	/**
	 * Identifiant de la stratégie (file, apc etc si copix, ou strtolower (module|nomClass) si provient d'un module)
	 *
	 * @var string
	 */
	private $_id = null;
	
	/**
	 * Classe à appeler, contenant le selecteur (copix: ou nomModule|)
	 *
	 * @var string
	 */
	private $_class = null;
	
	/**
	 * Nom de la classe uniquement
	 *
	 * @var string
	 */
	private $_className = null;
	
	/**
	 * Nom de la stratégie
	 *
	 * @var string
	 */
	private $_caption = null;
	
	/**
	 * Description de la stratégie
	 * 
	 * @var string
	 */
	private $_description = null;
	
	/**
	 * Module qui contient cette stratégie
	 *
	 * @var string
	 */
	private $_module = null;
	
	/**
	 * Indique si la stratégie provient de Copix ou d'un module
	 *
	 * @var boolean
	 */
	private $_isFromCopix = false;
	
	/**
	 * Constructeur
	 *
	 * @param string $pClass Nom de la classe, préfixée de copix: si elle provient de Copix ou de module|
	 */
	public function __construct ($pClass, $pCaption, $pDescription) {
		// stratégie dans un module
		if (substr ($pClass, 0, 6) != 'copix:') {
			$this->_isFromCopix = false;
			$this->_class = $pClass;
			list ($this->_module, $this->_className) = explode ('|', $pClass);
			$this->_id = strtolower ($this->_class);
			
		// stratégie dans Copix
		} else {
			// dans les versions de Copix 3.0.x, on avait stocké en dur dans CopixLog les noms des stratégies en minuscule
			// pour garder la compatibilité, on fait pareil, sauf pour Apc qui était écrit avec une majuscule
			$this->_id = substr ($pClass, 16, -8);
			$this->_id = (strtolower ($this->_id) == 'apc') ? 'Apc' : strtolower ($this->_id);
			$this->_isFromCopix = true;
			$this->_class = $pClass;
			$this->_className = substr ($pClass, 6);
		}
		
		$this->_caption = $pCaption;
		$this->_description = $pDescription;
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
	 * Retourne l'identifiant (file, apc etc si copix, ou strtolower (module|nomClass) si provient d'un module)
	 *
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}
	
	/**
	 * Retourne le nom
	 *
	 * @return string
	 */
	public function getCaption () {
		return $this->_caption;
	}
	
	/**
	 * Retourne la desription
	 * 
	 * @return string
	 */
	public function getDescription () {
		return $this->_description;
	}
	
	/**
	 * Retourne le nom de la classe avec le sélecteur (copix: ou module|)
	 *
	 * @return string
	 */
	public function getClass () {
		return $this->_class;
	}
	
	/**
	 * Retourne le nom de la classe uniquement
	 *
	 * @return string
	 */
	public function getClassName () {
		return $this->_className;
	}
	
	/**
	 * Indique si la stratégie provient de Copix ou d'un module
	 *
	 * @return boolean
	 */
	public function isFromCopix () {
		return $this->_isFromCopix;
	}
	
	/**
	 * Retourne le nom du module qui contient cette stratégie
	 *
	 * @return string
	 */
	public function getModule () {
		return $this->_module;
	}
}