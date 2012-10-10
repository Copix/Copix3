<?php
/**
 * 
 *
 */

/**
 * Interface de base pour les comportements sur donn�es
 * 
 * @package copix
 * @subpackage core
 */
interface ICopixDataBehaviour {
	/**
	 * R�cup�ration d'une donn�e apr�s application de traitements
	 *
	 * @param mixed $pValue
	 */
	public function get ($pValue); 
}