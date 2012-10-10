<?php
/**
 * @package		copix
 * @subpackage	cache
 * @author		Croes Gérald, Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Permet de gérer un cache mémoire APC
 * 
 * @package		copix
 * @subpackage	cache
 */
class CopixCacheApcStrategy implements ICopixCacheStrategy {
	/**
	 * Elements déjà en mémoire
	 * 
	 * @var array
	 */
	private $_memory = array ();
	
	/**
	 * Paramètres supplémentaires de configruation
	 */
	private $_extra;
	
	/**
	 * Construction et configruation de l'objet de cache.
	 * @param array $pExtra
	 */
	public function __construct ($pExtra){
		$this->_extra = $pExtra;
	}
	
	/**
	 * Destruction de l'objet, sauvegarde l'état des caches dans APC
	 */
	function __destruct () {
		foreach ($this->_memory as $type => $content) {
			apc_store ("CopixCacheTree|" . $type, $content);
		}
	}
	
	/**
	 * Indique si la stratégie peut fonctionner
	 * 
	 * @return boolean
	 */
	public function isEnabled () {
		return function_exists ('apc_fetch');
	}
	
	/**
	 * Lecture du cache de type donné si ce n'est pas déja fait
	 * 
	 * @param string $pType Type de cache à lire
	 */
	private function _loadIfNotReady ($pType) {
		if (!array_key_exists (self::_getMain ($pType), $this->_memory)) {
			$this->_memory[self::_getMain ($pType)] = apc_fetch ('CopixCacheMemory|' . self::_getMain ($pType));
		}
	} 

	/**
	 * Enregistrement des éléments dans le cache
	 *
	 * @param string $pId Identifiant de l'élméent à écrire dans le cache 
	 * @param string $pType Type de cache ou écrire
	 * @param mixed $pContent Contenu
	 */	
	public function write ($pId, $pContent, $pType) {
		$this->_loadIfNotReady ($pType);
		$pContent = serialize ($pContent);

		$elems = explode ('|', $pType);
		$currentNode = & $this->_memory[self::_getMain ($pType)];
		foreach ($elems as $elem) {
			if (!isset ($currentNode[$elem])) {
				$currentNode[$elem] = array();
			}
			$currentNode = & $currentNode[$elem];
		}
		if (!isset ($currentNode[$pId])){
			$currentNode[$pId] = new StdClass ();
		}
		$currentNode[$pId]->content = $pContent;
		$currentNode[$pId]->time = time();
	}

	/**
	 * Lecture de données depuis le cache
	 *
	 * @param string $pId Identifiant de l'élément à récupérer 
	 * @param string $pType Type de cache ou récupérer les données
	 * @return string
	 * 
	 * @throws CopixCacheException
	 */	
	public function read ($pId, $pType) {
		$this->_loadIfNotReady ($pType);

		$elems = explode ('|', $pType);
		$currentNode = &$this->_memory[self::_getMain ($pType)];
		foreach ($elems as $elem) {
			if (!isset ($currentNode[$elem])) {
				throw new CopixCacheException (_i18n ('copix:copixcache.error.contentNotFound', $pId));
			}
			$currentNode = &$currentNode[$elem];
		}
		if (isset ($currentNode[$pId])) {
			if ((!isset ($this->_extra['duration'])) || (time () - $currentNode[$pId]->time) < $this->_extra['duration'] || $this->_extra['duration'] == 0) {
				if ($return = $currentNode[$pId]->content) {
					return unserialize ($return);
				}
				throw new CopixCacheException (_i18n ('copix:copixcache.error.invalidContent', array ($currentNode[$pId]->content)));
			} else {
				$this->clear ($pType, $pId);
			}
		}
		throw new CopixCacheException (_i18n ('copix:copixcache.error.contentNotFound', $pId));
	}
	
	/**
	 * Supression des éléments du cache
	 * Si $pId = null tout le type (ou sous-type) passé en paramètre du constructeur est vidé
	 *
	 * @param string $pId Identifiant de l'élément à supprimer
	 * @param string $pType Type de cache
	 */
	public function clear ($pId, $pType) {
		$this->_loadIfNotReady ($pType);

		$elems = explode ('|', $pType);
		$currentNode = & $this->_memory[self::_getMain ($pType)];
		$currentTempNode = null;
		foreach ($elems as $elem) {
			if ($currentTempNode != null) {
				$currentNode = & $currentTempNode;
			}
			if (!isset ($currentNode[$elem])) {
				return '';
			}
			$currentTempNode = & $currentNode[$elem];
			$lastElem = $elem;
		}
		if ($pId !== null) {
			$currentNode = & $currentTempNode;
			unset ($currentNode[$pId]);
		} else {
			unset ($currentNode[$lastElem]);
		}
	}

	/**
	 * Teste l'existence d'un élément dans le cache
	 *
	 * @param string $pId Identifiant du cache
	 * @param string $pType Type de cache
	 * @return boolean
	 */	
	public function exists ($pId, $pType) {
		$this->_loadIfNotReady ($pType);

		$elems = explode ('|', $pType);
		$currentNode = & $this->_memory[self::_getMain ($pType)];
		foreach ($elems as $elem) {
			if (!isset ($currentNode[$elem])) {
				return false;
			}
			$currentNode = & $currentNode[$elem];
		}
		if (isset ($currentNode[$pId])) {
			if ((!isset ($this->_extra['duration'])) || (time () - $currentNode[$pId]->time) < $this->_extra['duration'] || $this->_extra['duration'] == 0) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Récupère le type principal de l'élément passé en paramètre
	 * 
	 * @param string $pType Type de cache duquel on souhaite extraire l'élément principal
	 * @return string
	 */
	private static function _getMain ($pType) {
		$parts = explode ('|', $pType);
		return $parts[0];
	}
}