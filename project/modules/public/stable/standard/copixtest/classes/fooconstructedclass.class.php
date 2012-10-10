<?php
/**
 * @package		standard
 * @subpackage	copixtest
 * 
 */

/**
 * @package		standard
 * @subpackage	copixtest
 * Classe pour tester les constructeurs avec paramètre.
 */
class FooConstructedClass {
	/**
	 * Variable privée
	 * @var string	
	 */
	private $_var = null;
	
	/**
	 * Deuxième variable  
	 * @var string 
	 */
	private $_var2 = null;

	/**
	 * Constructeur avec une valeur par défaut.
	 */
	public function __construct ($pParam = 'default', $pParam2 = 'default2'){
		$this->_var = $pParam;
		$this->_var2 = $pParam2;
	}
	
	/**
	 * Retourne la valeur qui a été passée au constructeur.
	 * @return mixed 
	 */
	public function getVar (){
		return $this->_var;		
	}
	
	/**
	 * Retourne la deuxième variable qui a été passée au constructeur
	 * @return mixed
	 */
	public function getVar2 (){
		return $this->_var2;
	}
}

?>