<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * Interface des FormDataClass (décrivant les données pour l'initialisation des CMS Forms)
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */
interface ICMSFormData {
	
	/**
	 * Renvoit la liste des données contenu dans le CopixUser
	 * @return array
	 */
	public function getUserInfos();
}