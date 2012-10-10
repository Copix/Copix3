<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Proxy générique sur une classe
 * @package copix
 * @subpackage core
 */
abstract class CopixClassProxy {
	/**
	 * L'objet encapsulé
	 * @var object
	 */
	protected $_object = false;

	/**
   	 * Constructeur, l'objet et sa définition s'il y a lieu
   	 * @param	object	$pObject	l'objet à placer dans la session
   	 * @param	string	$pFileName	le chemin de la définition du fichier
   	 */
	public function __construct ($pObject){
		$this->_object = $pObject;
   	}

   	/**
   	 * Encapsulation de l'appel des fonctions pour les transmettre directement à l'objet
   	 * @param	string	$pName	nom de la fonction
   	 * @param	array	$pArgs	arguments passés à la fonction
   	 * @return mixed
   	 */
   	public function __call ($pName, $pArgs){
   		return call_user_func_array (array ($this->_object, $pName), $pArgs);   		
   	}
   	
   	/**
   	 * Récupération des propriétés dans l'objet cible
   	 * @param	string	$pName	Le nom de la propriété à récupérer
   	 * @return 	mixed	valeur de la propriété
   	 */
   	public function __get ($pName){
   		return $this->_object->$pName;
   	}
   	
   	/**
   	 * Définition d'une propriété dans l'objet cible
   	 * @param	string	$pName	le nom de la propriété
   	 * @param	mixed	$pValue	la valeur de l'objet
   	 */
   	public function __set ($pName, $pValue){
   		$this->_object->$pName = $pValue;
   	}
   	
   	/**
   	 * Indique si une propriété existe sur l'objet
   	 *
   	 * @param string $pName	la propriété à tester
   	 * @return boolean
   	 */
   	public function __isset ($pName){
   		return isset ($this->_object->$pName);
   	}
   	
   	/**
   	 * Retourne l'objet qui fait l'objet du proxy
   	 * @return object
   	 */
   	public function getRemoteObject (){
   		return $this->_object;
   	}
}
?>