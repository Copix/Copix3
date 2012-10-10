<?php
/**
* @package		copix
* @subpackage	cache
* @author		Croës Gérald, Salleyron Julien
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Gère le cache en mode File
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
	 * Indique si le cache est actif (oui tout le temps)
	 * @params 	array	$pExtra	paramètres supplémentaires éventuellement nécessaires
	 * @return boolean
	 */
	public function isEnabled ($pExtra){
		return true;
	}

	/**
	 * Méthode read
	 *
	 * @param string $pType le type de cache
	 * @param string $pId id (serialize) des donnée en cache a retourné
	 * @return string Les données serialize (si pas de donnée renvoi false)
	 */
	public function read ($pId, $pType, $pExtra) {
		if ($return =  CopixFile::read ($this->_makeFileName($pId, $pType, $pExtra))){
			return unserialize ($return);
		}
		throw new CopixCacheException ($pId.'-'.$pType);
     }

	/**
	 * Détermine le nom de fichier du cache.
	 * @access private
     */
	private function _makeFileName ($pId, $pType, $pExtra){
		$fileMainName = md5 ($pId);
		return COPIX_CACHE_PATH.self::_getDir($pExtra).$this->_getDirectory($pType, $pExtra).'/'.$fileMainName.'.cache';
	}

	/**
	 * Méthode d'enregistrement dans le cache
	 * 
	 * @param string $pType le type de cache
	 * @param string $pId (serialize) Id
	 * @param string $pContent le contenu (serialize)
	 */
	public function write ($pId, $pContent, $pType, $pExtra) {
		CopixFile::write ($this->_makeFileName ($pId, $pType, $pExtra), serialize ($pContent));
    }

    /**
     * Méthode qui test si ce cache existe
     * 
	 * @param string $pType le type de cache
     * @param string $pId serialize Id
     * @return bool true si existe false sinon
     */
    public function exists ($pId, $pType, $pExtra) {
    	$fileName = $this->_makeFileName($pId, $pType, $pExtra);
        if (is_readable ($fileName)){
            if ($pExtra['duration'] === null || $pExtra['duration']==0){
                return true;
            }
			if ((time () - filemtime ($fileName)) < $pExtra['duration']){
				return true;
            } else {
            	$this->clear ($pId, $pType, $pExtra);
            }
        }
        return false;
    }
    
    /**
     * Méthode qui supprime le cache
     * Si le $pId = null tout le type (ou soustype) passé en paramètre du constructeur est vidé
     * 
	 * @param string $pType le type de cache
     * @param string $pId serialize Id
     */
    public function clear ($pId, $pType, $pExtra) {
        if ($pId !== null) {
        	unlink ($this->_makeFileName($pId, $pType, $pExtra));
	   } else {
		   if (file_exists (COPIX_CACHE_PATH.self::_getDir($pExtra).$this->_getDirectory($pType, $pExtra))) {
		   	CopixFile::removeDir(COPIX_CACHE_PATH.self::_getDir($pExtra).$this->_getDirectory($pType, $pExtra).'/');
		   }
	   }
    }
    

    /**
     * Génére le chemin du répertoire en fonction du type, sous-type
     * 
	 * @param string $pType le type de cache
     */
    private function _getDirectory ($pType, $pExtra) {
        if (!isset($this->_arDir[$pType])) {
            $this->_arDir[$pType]='/'.str_replace('|','/',$pType);
        }
        return $this->_arDir[$pType];
    }
    
    /**
     * Récupération du répertoire de cache
     */
    private static function _getDir ($pExtra){
    	return 'copixcache/'.isset ($pExtra['dir']) ? $pExtra['dir'] : '';
    }
}
?>