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
	 * On nettoie le cache d'information sur les fichiers pour ne pas avoir de problèmes 
	 * sur l'interrogation des dernières modifications 
	 */
	public function __construct (){
		clearstatcache ();
	}
	
	/**
	 * Indique si le cache est actif
	 * 
	 * @param array $pExtra Paramètres supplémentaires
	 * @return boolean
	 */
	public function isEnabled ($pExtra) {
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
	public function read ($pId, $pType, $pExtra) {
		if ($return = CopixFile::read ($this->_makeFileName ($pId, $pType, $pExtra))) {
			return unserialize ($return);
		}
		throw new CopixCacheException ($pId . '-' . $pType);
	}

	/**
	 * Détermine le nom de fichier du cache
	 * 
	 * @param string $pId Identifiant de l'élément à récupérer 
	 * @param string $pType Type de cache ou récupérer les données
	 * @param array	$pExtra	Paramètres supplémentaires
	 */
	private function _makeFileName ($pId, $pType, $pExtra) {
		$fileMainName = md5 ($pId);
		return COPIX_CACHE_PATH . self::_getDir ($pExtra) . $this->_getDirectory ($pType, $pExtra) . '/' . $fileMainName . '.cache';
	}

	/**
	 * Enregistrement des éléments dans le cache
	 *
	 * @param string $pId Identifiant de l'élméent à écrire dans le cache 
	 * @param string $pType Type de cache ou écrire
	 * @param mixed $pContent Contenu
	 * @param array	$pExtra	Paramètres supplémentaires
	 */	
	public function write ($pId, $pContent, $pType, $pExtra) {
		CopixFile::write ($this->_makeFileName ($pId, $pType, $pExtra), serialize ($pContent));
	}

	/**
	 * Teste l'existence du cache
	 *
	 * @param string $pId Identifiant du cache
	 * @param string $pType Type de cache
	 * @param array	$pExtra	Paramètres supplémentaires
	 * @return boolean
	 */	
	public function exists ($pId, $pType, $pExtra) {
		$fileName = $this->_makeFileName ($pId, $pType, $pExtra);
		if (is_readable ($fileName)) {
			if ($pExtra['duration'] === null || $pExtra['duration'] == 0) {
				return true;
			}
			if ((time () - filemtime ($fileName)) < $pExtra['duration']) {
				return true;
			} else {
				$this->clear ($pId, $pType, $pExtra);
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
	 * @param array	$pExtra	Paramètres supplémentaires
	 */
	public function clear ($pId, $pType, $pExtra) {
		if ($pId !== null) {
			unlink ($this->_makeFileName ($pId, $pType, $pExtra));
		} else {
			if (file_exists (COPIX_CACHE_PATH . self::_getDir ($pExtra) . $this->_getDirectory ($pType, $pExtra))) {
				CopixFile::removeDir (COPIX_CACHE_PATH . self::_getDir ($pExtra) . $this->_getDirectory ($pType, $pExtra) . '/');
			}
		}
	}

	/**
	 * Génère le chemin du répertoire en fonction du type et du sous-type
	 * 
	 * @param string $pType le type de cache
	 * @param array	$pExtra	Paramètres supplémentaires
	 * @return string
	 */
	private function _getDirectory ($pType, $pExtra) {
		if (!isset($this->_arDir[$pType])) {
			$this->_arDir[$pType] = '/' . str_replace ('|', '/', $pType);
		}
		return $this->_arDir[$pType];
	}

	/**
	 * Récupération du répertoire de cache
	 * 
	 * @param array	$pExtra	Paramètres supplémentaires
	 * @return string
	 */
	static private function _getDir ($pExtra) {
		return 'copixcache/' . isset ($pExtra['dir']) ? $pExtra['dir'] : '';
	}
}
?>