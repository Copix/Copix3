<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * Route_Db
 * 
 * @package copix
 * @subpackage forms
 * @author Nicolas Bastien
 */
class Route_Db extends CopixAbstractFormRoute {
	
	/**
	 * (non-PHPdoc)
	 * @see forms/ICopixFormRoute#getFormParams()
	 */
	public static function getFormParams($strCfRouteParams = '') {
		$form = new CopixFormLight('form_route_params');
		$form->setTitle('Enregistrement en base de donnée');
		
		$helpImageSrc = _resource('form|img/help.png');

$legend = <<<STR_LEGEND
<p class="copix_help">
<img src="$helpImageSrc" class="p_icon"/>
&nbsp;&nbsp;Enregistrement simple en base de données.<br/>
Les réponses seront simplement enregistrées en base afin d'être prise en compte dans les statistiques.<br/>
Aucun autre traitement ne sera effectué.
</p>
STR_LEGEND;

		$form->setLegend($legend);	
		
		return $form;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see forms/ICopixFormRoute#checkParams()
	 */
	public function checkParams() {
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see forms/CopixAbstractFormRoute#_process()
	 */
	protected function _process($arData) {
		return;
	}
}