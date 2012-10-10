<?php
/**
 * 
 */


/**
 * Classe de génération de mot de passe alléatoire
 */
class PasswordGenerator {
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pHash
	 * @param unknown_type $pLen
	 */
	private $_hash;
	
	/**
	 * Taille du mot de passe à générer
	 *
	 * @var 
	 */
	private $_len;
    
	/**
	 * Contructeur de l'objet PasswordGenerator
	 *
	 */
	public function __construct (){
	     $this->_len = (int) CopixConfig::get ('generictools|sizeWord');
	     
	     $this->_hash = array ();
	     // Ajout des chiffres
         for ($i = ord('0'); $i <= ord('9'); $i++) {
             $this->_hash [] = chr ($i);
         }
         // Ajout des lettres majuscules
         for ($i = ord('A'); $i <= ord('Z'); $i++) {
             $this->_hash [] = chr ($i);
         }
         // Ajout des lettres minuscules
         for ($i = ord('a'); $i <= ord('z'); $i++) {
             $this->_hash [] = chr ($i);
         }
	}
	
	
	/**
	 * Génération du mot de passe
	 *
	 * @param unknown_type $pLen
	 */
	function generate (){
		$password = '';
		for ($i=0; $i<$this->_len; $i++){
			$password .= $this->_pickLetter ();
		}
		return $password;
	}
	
	/**
	 * Récupère une lettre au hazard
	 *
	 */
	private function _pickLetter (){
		$pick = $this->_hash[rand (0, count ($this->_hash)-1)];
		if (!in_array ($pick, explode (';', CopixConfig::get ('generictools|excludeFromPassword')))){
			return $pick;
		}
		return $this->_pickLetter ();
	}
}
?>