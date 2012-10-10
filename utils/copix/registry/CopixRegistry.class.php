<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Gérald Croës
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe permettant de sauvegarder des variables dans un registre
 * @package    copix
 * @subpackage utils
 */
class CopixRegistry {
	/**
	 * Les instances des éléments sauvegardés dans le registre
	 *
	 * @var array
	 */
	private $_instances = array ();
	
	/**
	 * Le singleton
	 *
	 * @var CopixRegistry
	 */
	private static $_singleton = false;

	/**
	 * Récupération de l'instance de l'élément
	 * @return CopixRegistry
	 */
	public static function instance (){
		if (self::$_singleton === false){
			self::$_singleton = new CopixRegistry ();
		}
        return self::$_singleton;
	}
	
	/**
	 * Indique si l'élément $pName existe dans $pNamespace
	 * 
	 * @param string $pName      le nom de l'élément
	 * @param string $pNamespace l'espace de nom dans lequel on veut tester l'existance de l'élément
	 * 
	 * @return boolean
	 */
	public function exists ($pName, $pNamespace = 'default'){
		if (isset ($this->_instances[$pNamespace])){
			return array_key_exists ($pName, $this->_instances[$pNamespace]);
		}
		return false;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pName
	 * @param unknown_type $pNamespace
	 */
	public function get ($pName, $pNamespace = 'default'){
		if ($this->exists ($pName, $pNamespace)){
			return $this->_instances[$pNamespace][$pName];
		}
		throw new CopixRegistryException (_i18n ('copix:copixregistry.error.notFound', array ($pName, $pNamespace)));
	}

	/**
	 * Définition de l'élément $pElement de nom $pName dans le namespace $pNamespace
	 *
	 * @param string $pName      le nom de l'élément
	 * @param mixed  $pValue     l'élément a définir
	 * @param string $pNamespace le nom de l'espace de nom
	 */
	public function set ($pName, $pValue, $pNamespace = 'default'){
		if (! $this->namespaceExists ($pNamespace)){
			$this->_instances[$pNamespace] = array ();
		}
		$this->_instances[$pNamespace][$pName] = $pValue; 
	}
	
	/**
	 * Supression de l'élément d'un nom donné, dans un namespace donné 
	 */
	public function remove ($pName, $pNamespace = 'default'){
		if ($this->exists ($pName, $pNamespace)){
			$this->_instance[$pNamespace][$pName] = null;
			return true;
		}
		return false;
	}
	
	/**
	 * Supression du namespace complet
	 * 
	 * @param string $pNamespace l'espace de nom a supprimer
	 */
	public function removeNamespace ($pNamespace){
		$this->_instances[$pNamespace] = array ();
	}
	
	/**
	 * Retourne le nombre d'éléments dans le namespace voulu (si null, tous les namespaces)
	 *
	 * @param string $pNamespace
	 * @return int
	 */
	public function count ($pNamespace = null){
		if ($pNamespace === null){
			$count = 0;
			foreach ($this->_instances as $key=>$elements){
				$count += count ($elements);
			}
			return $count;
		}

		if ($this->namespaceExists ($pNamespace)){
			return count ($this->_instances[$pNamespace]);
		}
	}

    /**
	 * Vérifie l'existence d'un name space
	 * @param $pNamespace
	 * @return boolean
	 */
	public function namespaceExists ($pNamespace) {
		return isset ($this->_instances[$pNamespace]);
	}
}