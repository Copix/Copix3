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
 * Permet de gérer un cache mémoire
 * @package copix
 * @subpackage cache
 */
class CopixCacheSystemStrategy implements ICopixCacheStrategy {
	/**
	 * Méthode d'enregistrement dans le cache
	 *
	 * @param string $pType le type de cache
	 * @param string $pId (serialize) Id
	 * @param string $pContent le contenu (serialize)
	 */
	public function write ($pId, $pContent, $pType, $pExtra){
	    $elems = explode ('|', $pType);
	    $currentNode = & $this->_memory;
	    foreach ($elems as $elem) {
	        if (!isset($currentNode[$elem])) {
	            $currentNode[$elem] = array();
	        }
	        $currentNode = & $currentNode[$elem];
	    }
	    $currentNode[$pId]->content = $pContent;
	    $currentNode[$pId]->time    = time();
	}

	/**
	 * Méthode read
	 *
	 * @param string $pType le type de cache
	 * @param string $pId id (serialize) des donnée en cache a retourné
	 * @return string Les données serialize (si pas de donnée renvoi false)
	 */
	public function read ($pId, $pType, $pExtra){
	    $elems = explode ('|', $pType);
	    $currentNode = & $this->_memory;
	    foreach ($elems as $elem) {
	        if (!isset ($currentNode[$elem])) {
	        	throw new CopixCacheException ($pId.'-'.$pType);
	        }
	        $currentNode = & $currentNode[$elem];
	    }
	    if (isset($currentNode[$pId])) {
	        if ((time() - $currentNode[$pId]->time) < $pExtra['duration'] || $pExtra['duration'] == 0) {
	            return $currentNode[$pId]->content;
	        } else {
	            $this->clear($pType, $pId, $pExtra);
	        }
	    }
	    throw new CopixCacheException ($pId.'-'.$pType);
	}

	/**
	 * Méthode qui supprime le cache
	 * Si le $pId = null tout le type (ou soustype) passé en paramètre du constructeur est vidé
	 *
	 * @param string $pType le type de cache
	 * @param string $pId serialize Id
	 */
	public function clear ($pId, $pType, $pExtra){
	    $elems = explode ('|', $pType);
	    $currentNode = & $this->_memory;
	    $currentTempNode = null;
	    foreach ($elems as $elem) {
	        if ($currentTempNode != null) $currentNode = & $currentTempNode;
	        if (!isset($currentNode[$elem])) {
	            return '';
	        }
	        $currentTempNode = & $currentNode[$elem];
	        $lastElem = $elem; 
	    }
	    if ($pId != null) {
	        $currentNode = & $currentTempNode;
	        unset($currentNode[$pId]);
	    } else {
	        unset($currentNode[$lastElem]);
	    }
	}

	/**
	 * Méthode qui test si ce cache existe
	 *
	 * @param string $pType le type de cache
	 * @param string $pId serialize Id
	 * @return bool true si existe false sinon
	 */
	public function exists($pId, $pType, $pExtra) {
	    $elems = explode ('|', $pType);
	    $currentNode=&$this->_memory;
	    foreach ($elems as $elem) {
	        if (!isset($currentNode[$elem])) {
	            return false;
	        }
	        $currentNode=&$currentNode[$elem];
	    }
	    if (isset($currentNode[$pId])) {
	        if ((time() - $currentNode[$pId]->time)<$pExtra['duration'] || $pExtra['duration']==0) {
	            return true;
	        }
	    }
	    return false;
	}
	
	/**
	 * Indique si cette stratégie est active (ce qui est toujours le cas)
	 */
	public function isEnabled ($pExtra){
		return true;
	}
}
?>