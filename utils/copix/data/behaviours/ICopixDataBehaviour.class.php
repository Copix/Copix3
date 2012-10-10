<?php
/**
 * 
 *
 */

/**
 * Interface de base pour les comportements sur données
 * 
 * @package copix
 * @subpackage core
 */
interface ICopixDataBehaviour {
	/**
	 * Récupèration d'une donnée après application de traitements
	 *
	 * @param mixed $pValue
	 */
	public function get ($pValue); 
}