<?php
/**
 * @package standard
 * @subpackage test
 * @author	Gérald Croës
 * @copyright 2001-2007 CopixTeam
 * @link      http://copix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe foo pour vérifier les fonctionnement de CopixClassesFactory, ainsi 
 * que CopixSessionObject qui utilise massivement les méthodes magiques.
 * 
 * @package standard
 * @subpackage test
 */
class FooClass {
	/**
	 * Variable publique test
	 *
	 * @var mixed
	 */
	public $test = null;
	
	/**
	 * Variable privée _test
	 *
	 * @var mixed
	 */
	private $_test = null;
	
	/**
	 * retourne le paramètre qui a été donné
	 *
	 * @param mixed $param	paramètre qui sera retourné
	 * @return mixed
	 */
	public function getParam ($param){
		return $param;
	}
	
	/**
	 * Retourne les deux paramètres donnés sous la forme d'un tableau
	 * @param	mixed	$pParam1	Premier paramètre
	 * @param	mixed	$pParam2	Second paramètre
	 * @return array
	 */
	public function getArrayWith ($pParam1, $pParam2){
		return array ($pParam1, $pParam2);
	}
	
	/**
	 * Défini la valeur de la propriété publique test
	 *
	 * @param mixed $pValue	valeur à assigner à test
	 */
	public function setPublicPropertyTest ($pValue){
		$this->test = $pValue;
	}
	
	/**
	 * Retourne la valeur de la propriété publique test
	 *
	 * @return mixed
	 */
	public function getPublicPropertyTest (){
		return $this->test;
	}
	
	/**
	 * Valeur à assigner à une propriété qui n'existe pas dans l'objet
	 *
	 * @param string $pName	le nom de la propriété
	 * @param mixed $pValue	la valeur de la propriété
	 */
	public function setUnknownProperty ($pName, $pValue){
		$this->$pName = $pValue;
	}
	
	/**
	 * Récupère la valeur d'une propriété inexistante de l'objet qui a été 
	 * définie au préalable par setUnknownProperty
	 *
	 * @param string $pName	le nom de la propriété à récupérer
	 * @return mixed
	 */
	public function getUnknownProperty ($pName){
		return $this->$pName;
	}
	
	/**
	 * Définition de la propriété privée _test
	 *
	 * @param mixed $pValue	valeur à définir
	 */
	public function setPrivatePropertyTest ($pValue){
		$this->_test= $pValue;
	}
	
	/**
	 * Récupération de la propriété privée _test
	 *
	 * @return mixed
	 */
	public function getPrivatePropertyTest (){
		return $this->_test;
	}
}