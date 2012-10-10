<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */
 
/**
 * Interface pour les routes des formulaires CopixFormLight
 * @package copix
 * @subpackage forms
 * @author Nicolas Bastien
 */
interface ICopixFormRoute {
	/**
	 * Renvoit le formualire de saisie des paramètres du route
	 * @param $arCfRouteParams
	 * @return CoppixFormLight
	 */
	public static function getFormParams($arCfRouteParams = array());
	/**
	 * Vérification des paramètres saisie à la création du formulaire
	 * (ex: vérififcation du mail si route_mail)
	 * @return boolean
	 */
	public function checkParams();
	
	/**
	 * En registrement des données saisies en base
	 * @param $arData
	 * @return boolean
	 */
	public function saveData($arData);
	
	/**
	 * Action à effectuer
	 * @param $arData
	 * @return boolean
	 */
	public function process($arData);
}
?>