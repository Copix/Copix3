<?php
/**
 * @package		copix
 * @subpackage	cache
 * @author		Croës Gérald, Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Gère le cache en mode File
 * 
 * @package		copix
 * @subpackage	cache
 */
class CopixCacheFileStrategy implements ICopixCacheStrategy {
	/**
	 * Tableau des sous répertoires
	 * @var array
	 */
	private $_arDir = array ();
	
	/**
	 * Paramètres de configuration du cache.
	 * 
	 * @var array
	 */
	private $_extra;
	
	/**
	 * Construction et configuration de l'objet de cache.
	 * 
	 * @param $pExtra
	 */
	public function __construct ($pExtra){
		$this->_extra = $pExtra;
	}

	/**
	 * Indique si le cache est actif
	 * 
	 * @param array $pExtra Paramètres supplémentaires
	 * @return boolean
	 */
	public function isEnabled () {
		// ce système de cache sera toujours disponible, puisqu'il ne necessite rien de particulier pour fonctionner
		return true;
	}

	/**
	 * Lecture de données depuis le cache
	 *
	 * @param string $pId Identifiant de l'élément à récupérer 
	 * @param string $pType Type de cache ou récupérer les données
	 * @param array	$pExtra	Paramètres supplémentaires
	 * @return string
	 * @throws CopixCacheException
	 */	
	public function read ($pId, $pType) {
		if ($return = CopixFile::read ($this->_makeFileName (self::_safeId ($pId), $pType))) {
			$serializableObject = unserialize ($return);
			return $serializableObject->getRemoteObject();
		}
		throw new CopixCacheException (_i18n ('copix:copixcache.error.contentNotFound', self::_safeId ($pId)));
	}

	/**
	 * Détermine le nom de fichier du cache
	 * 
	 * @param string $pId Identifiant de l'élément à récupérer 
	 * @param string $pType Type de cache ou récupérer les données
	 */
	private function _makeFileName ($pId, $pType) {
		$fileMainName = md5 (self::_safeId ($pId));
		return COPIX_CACHE_PATH . $this->_getDir () . $this->_getDirectory ($pType) . '/' . $fileMainName . '.cache';
	}

	/**
	 * Enregistrement des éléments dans le cache
	 *
	 * @param string $pId Identifiant de l'élméent à écrire dans le cache 
	 * @param string $pType Type de cache ou écrire
	 * @param mixed $pContent Contenu
	 */	
	public function write ($pId, $pContent, $pType) {
		CopixFile::write ($this->_makeFileName (self::_safeId ($pId), $pType), serialize (new CopixSerializableObject($pContent)));
	}

	/**
	 * Teste l'existence du cache
	 *
	 * @param string $pId Identifiant du cache
	 * @param string $pType Type de cache
	 * @return boolean
	 */	
	public function exists ($pId, $pType) {
		clearstatcache ();	
		$fileName = $this->_makeFileName (self::_safeId ($pId), $pType);
		if (is_readable ($fileName)) {
			if ($this->_extra['duration'] === null || $this->_extra['duration'] == 0) {
				return true;
			}
			if ((time () - filemtime ($fileName)) < $this->_extra['duration']) {
				return true;
			} else {
				$this->clear (self::_safeId ($pId), $pType);
			}
		}
		return false;
	}

	/**
	 * Supression des éléments du cache
	 * Si $pId = null tout le type (ou sous-type) passé en paramètre du constructeur est vidé
	 *
	 * @param string $pId Identifiant de l'élément à supprimer
	 * @param string $pType Type de cache
	 */
	public function clear ($pId, $pType) {
		if ($pId !== null) {
		    if (file_exists ($this->_makeFileName (self::_safeId ($pId), $pType))) {
			    unlink ($this->_makeFileName (self::_safeId ($pId), $pType));
		    }
		} else {
			if (file_exists (COPIX_CACHE_PATH . $this->_getDir () . $this->_getDirectory ($pType))) {
				CopixFile::removeDir (COPIX_CACHE_PATH . $this->_getDir () . $this->_getDirectory ($pType) . '/');
			}
		}
	}

	/**
	 * Génère le chemin du répertoire en fonction du type et du sous-type
	 * 
	 * @param string $pType le type de cache
	 * @return string
	 */
	private function _getDirectory ($pType) {
		if (!isset($this->_arDir[$pType])) {
			$this->_arDir[$pType] = '/' . str_replace ('|', '/', $pType);
		}
		return $this->_arDir[$pType];
	}

	/**
	 * Récupération du répertoire de cache
	 * 
	 * @return string
	 */
	private function _getDir () {
		return 'copixcache/' . (isset ($this->_extra['dir']) ? $this->_extra['dir'] : '');
	}
	
	/**
	 * On vérifie que l'id soit d'un type correct avant de l'utiliser, sinon on le serialise (compatibilité avec l'existant)
	 * 
	 * @param mixed $pId
	 */
	private static function _safeId ($pId){
		return (is_string ($pId) || is_int ($pId)) ? $pId : serialize ($pId);
	}
}