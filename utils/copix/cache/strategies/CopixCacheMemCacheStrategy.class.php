<?php
/**
 * @package		copix
 * @subpackage	cache
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Permet de gérer un cache mémoire MemCache (extension MemCache)
 * 
 * @package		copix
 * @subpackage	cache
 */
class CopixCacheMemCacheStrategy implements ICopixCacheStrategy {
	/**
	 * l'objet Memcache
	 */
	private $_memcache;
	
	/**
	 * les paramètres de configuration du cache.
	 * 
	 * @var array
	 */
	private $_extra;
	
	/**
	 * Indique si la stratégie peut fonctionner
	 * 
	 * @param array $pExtra Paramètres du cache
	 * @return boolean
	 */
	public function isEnabled () {
		return class_exists ('MemCache');
	}
	
	/**
	 * Connexion au serveur
	 */
	public function __construct ($pExtra){
		$this->_memcache = new MemCache ();
		$this->_memcache->pconnect($pExtra['dir'], 11211);
		$this->_extra = $pExtra;
	}
	
	/**
	 * Enregistrement des éléments dans le cache
	 *
	 * @param string $pId Identifiant de l'élméent à écrire dans le cache 
	 * @param string $pType Type de cache ou écrire
	 * @param mixed $pContent Contenu
	 */	
	public function write ($pId, $pContent, $pType) {
		$this->_memcache->set ($pId.'|'.$pType, $pContent, 0, $this->_extra['duration']);
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
		return $this->_memcache->get ($pId.'|'.$pType);
	}
	
	/**
	 * Supression des éléments du cache
	 * Si $pId = null tout le type (ou sous-type) passé en paramètre du constructeur est vidé
	 *
	 * @param string $pId Identifiant de l'élément à supprimer
	 * @param string $pType Type de cache
	 */
	public function clear ($pId, $pType) {
		$this->_memcache->delete ($pId.'|'.$pType);
	}

	/**
	 * Teste l'existence d'un élément dans le cache
	 *
	 * @param string $pId Identifiant du cache
	 * @param string $pType Type de cache
	 * @return boolean
	 */	
	public function exists ($pId, $pType) {
		return $this->read($pId, $pType) !== false;
	}
}