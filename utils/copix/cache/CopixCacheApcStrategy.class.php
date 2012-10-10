<?php
/**
* @package		copix
* @subpackage	cache
* @author		Croes Gérald, Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Permet de gérer un cache mémoire APC
 * @package copix
 * @subpackage cache
 */
class CopixCacheApcStrategy implements ICopixCacheStrategy {
	/**
	 * Elements déjà en mémoire
	 * @var array
	 */
	private $_memory = array ();
	
	/**
	 * Sauvegarde l'état des caches dans APC
	 */
	function __destruct (){
		foreach ($this->_memory as $type=>$content){
			apc_store("CopixCacheTree|".$type, $content);
		}
	}
	
	/**
	 * Indique si la stratégie peut fonctionner
	 * @param 	array	$pExtra	tableau des paramètres du cache
	 * @return boolean
	 */
	public function isEnabled($pExtra) {
	    return (CopixConfig::instance ()->apcEnabled && function_exists('apc_fetch'));
	}
	
	/**
	 * Lecture du cache de type donné si ce n'est pas déja fait
	 * @param 	string	$pType	le type de cache à lire
	 */
	private function _loadIfNotReady ($pType){
		if (!array_key_exists (self::_getMain ($pType), $this->_memory)){
			$this->_memory[self::_getMain ($pType)]  = apc_fetch("CopixCacheMemory|".self::_getMain ($pType));
		}
	} 

	/**
	 * Enregistrement des éléments dans le cache
	 *
	 * @param string $pId identifiant de l'élméent à écrire dans le cache 
	 * @param string $pType le type de cache ou écrire
	 * @param string $pContent le contenu
	 * @param array	$pExtra	tableau des paramètres du cache 
	 */	
	public function write ($pId, $pContent, $pType, $pExtra){
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
	    $currentNode[$pId]->content = $pContent;
	    $currentNode[$pId]->time    = time();
	}

	/**
	 * Lecture de données depuis le cache
	 *
	 * @param string $pId identifiant de l'élément à récupérer 
	 * @param string $pType le type de cache ou récupérer les données
	 * @param array	$pExtra	tableau des paramètres du cache 
	 * @return string Les données serialize (si pas de donnée renvoi false)
	 */	
	public function read ($pId, $pType, $pExtra){
		$this->_loadIfNotReady ($pType);

	    $elems = explode ('|', $pType);
	    $currentNode = & $this->_memory[self::_getMain ($pType)];
	    foreach ($elems as $elem) {
	        if (!isset ($currentNode[$elem])) {
	        	throw new CopixCacheException ($pId.'-'.$pType);
	        }
	        $currentNode = & $currentNode[$elem];
	    }
	    if (isset($currentNode[$pId])) {
	    	if ((!isset ($pExtra['duration'])) || (time() - $currentNode[$pId]->time) < $pExtra['duration'] || $pExtra['duration'] == 0) {
	            if ($return = $currentNode[$pId]->content){
	            	return unserialize ($return);
	            }
	            throw new CopixCacheException ($pId.'-'.$pType);
	        } else {
	            $this->clear ($pType, $pId, $pExtra);
	        }
	    }
	    throw new CopixCacheException ($pId.'-'.$pType);
	}
	
	/**
	 * Supression d'éléments du cache
	 * Si le $pId = null tout le type (ou soustype) passé en paramètre du constructeur est vidé
	 *
	 * @param string $pId l'identifiant de l'élément à supprimer
	 * @param string $pType le type de cache
	 * @param array	$pExtra	tableau des paramètres du cache 
	 */
	public function clear ($pId, $pType, $pExtra){
		$this->_loadIfNotReady ($pType);

	    $elems = explode ('|', $pType);
	    $currentNode = & $this->_memory[self::_getMain ($pType)];
	    $currentTempNode = null;
	    foreach ($elems as $elem) {
	        if ($currentTempNode != null) {
	           $currentNode = & $currentTempNode;
	        }
	        if (!isset($currentNode[$elem])) {
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
	 * Test l'existance d'un élément dans le cache
	 *
	 * @param string $pId l'identifiant du cache
	 * @param string $pType le type de cache
	 * @param array	$pExtra	tableau des paramètres du cache 
	 * @return bool true si existe false sinon
	 */	
	public function exists ($pId, $pType, $pExtra) {
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
	    	if ((!isset ($pExtra['duration'])) || (time() - $currentNode[$pId]->time) < $pExtra['duration'] || $pExtra['duration']==0) {
	            return true;
	        }
	    }
	    return false;
	}
	
	/**
	 * Récupère le type principal de l'élément passé en paramètre
	 * @param 	string	$pType	le type de cache duquel on souhaite extraire l'élément principal
	 * @return string
	 */
	private static function _getMain ($pType){
		$parts = explode ('|', $pType);
		return $parts[0];
	}
}
?>