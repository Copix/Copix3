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
 * Permet de gérer un cache mémoire
 * 
 * @package		copix
 * @subpackage	cache
 */
class CopixCacheSystemStrategy implements ICopixCacheStrategy {
	/**
	 * Enregistrement des éléments dans le cache
	 *
	 * @param string $pId Identifiant de l'élméent à écrire dans le cache 
	 * @param string $pType Type de cache ou écrire
	 * @param mixed $pContent Contenu
	 * @param array	$pExtra	Paramètres du cache 
	 */	
	public function write ($pId, $pContent, $pType, $pExtra) {
		$elems = explode ('|', $pType);
		$currentNode = &$this->_memory;
		foreach ($elems as $elem) {
			if (!isset ($currentNode[$elem])) {
				$currentNode[$elem] = array ();
			}
			$currentNode = & $currentNode[$elem];
		}
		$currentNode[$pId]->content = $pContent;
		$currentNode[$pId]->time = time();
	}

	/**
	 * Lecture de données depuis le cache
	 *
	 * @param string $pId Identifiant de l'élément à récupérer 
	 * @param string $pType Type de cache ou récupérer les données
	 * @param array	$pExtra	Paramètres du cache 
	 * @return string
	 * @throws CopixCacheException
	 */	
	public function read ($pId, $pType, $pExtra) {
		$elems = explode ('|', $pType);
		$currentNode = &$this->_memory;
		foreach ($elems as $elem) {
			if (!isset ($currentNode[$elem])) {
				throw new CopixCacheException ($pId . '-' . $pType);
			}
			$currentNode = &$currentNode[$elem];
		}
		if (isset ($currentNode[$pId])) {
			if ((time () - $currentNode[$pId]->time) < $pExtra['duration'] || $pExtra['duration'] == 0) {
				return $currentNode[$pId]->content;
			} else {
				$this->clear ($pType, $pId, $pExtra);
			}
		}
		throw new CopixCacheException ($pId . '-' . $pType);
	}

	/**
	 * Supression des éléments du cache
	 * Si $pId = null tout le type (ou sous-type) passé en paramètre du constructeur est vidé
	 *
	 * @param string $pId Identifiant de l'élément à supprimer
	 * @param string $pType Type de cache
	 * @param array	$pExtra	Paramètres du cache
	 */
	public function clear ($pId, $pType, $pExtra){
		$elems = explode ('|', $pType);
		$currentNode = &$this->_memory;
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
		if ($pId != null) {
			$currentNode = &$currentTempNode;
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
	 * @param array	$pExtra	Paramètres du cache 
	 * @return boolean
	 */	
	public function exists ($pId, $pType, $pExtra) {
		$elems = explode ('|', $pType);
		$currentNode = &$this->_memory;
		foreach ($elems as $elem) {
			if (!isset ($currentNode[$elem])) {
				return false;
			}
			$currentNode = &$currentNode[$elem];
		}
		if (isset ($currentNode[$pId])) {
			if ((time () - $currentNode[$pId]->time) < $pExtra['duration'] || $pExtra['duration'] == 0) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Indique si cette stratégie est active
	 * 
	 * @param array	$pExtra	Paramètres du cache 
	 * @return boolean
	 */
	public function isEnabled ($pExtra) {
		// méthode toujours active
		return true;
	}
}
?>