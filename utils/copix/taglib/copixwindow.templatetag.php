<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Pour afficher une fenetre
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCopixWindow extends CopixTemplateTag  {

	public function process ($pContent=null){

		$jsParams = array ('dragSelector', 'clicker', 'zone', 'url', 'onComplete', 'onDisplay', 'onFocus', 'onUnfocus', 'fixed', 'center', 'modal', 'modalclose');

		// Récupère les paramètres
		$zone     = $this->getParam('zone');
		$id       = $this->getParam('id', uniqid());
		$class    = $this->getParam('class', 'copixwindow');
		$template = $this->getParam('template', 'generictools|window.tpl');
		$url      = $this->getParam('url');
        $fixed    = $this->getParam('fixed');
        $domready = $this->getParam('domready', false);

		foreach ($jsParams as $key=>$param) {
			unset ($jsParams[$key]);
			$jsParams[$param] = $this->getParam($param);
		}
		$jsParams['canDrag'] = $this->getParam ('canDrag', true);
		$jsParams['modalclose'] = $this->getParam ('modalclose', true);
		
		// Valide les paramètres
		//$this->validateParams();

		$extraParams    = $this->getExtraParams();
		$zoneParams     = array ();
		$templateParams = array ();
		// Supprime le préfixe "zoneParams_" des paramètres de la zone
		// Supprime le préfixe "templateParams_" des paramètres du template
		// Cela peut servir à passer des paramètres supplémentaires au niveau du tag
		// qui rentrent en conflit avec les noms des paramètres standard.
		foreach($templateParams as $key=>$value) {
			if(preg_match('/^zoneParams_(.+)$/i', $key, $parts)) {
				unset($extraParams[$key]);
				$zoneParams[$parts[1]] = $value;
			}
			if(preg_match('/^templateParams_(.+)$/i', $key, $parts)) {
				unset($extraParams[$key]);
				$templateParams[$parts[1]] = $value;
			}
		}

		//On merge les paramètres avec les paramètres supplémentaire du tag d'origine
		$zoneParams     = array_merge ($extraParams, $zoneParams);
		$templateParams = new CopixPPO (array_merge ($extraParams, $templateParams));

		if (!isset ($zoneParams['id'])) {
			$zoneParams['id'] = uniqid();
		}

		$zoneParams['onComplete'] = "
			$('".$zoneParams['id']."').fireEvent('sync');
			".(isset($zoneParams['onComplete']) ? $zoneParams['onComplete'] : '');
	  
		if ($zone != null) {
			$zoneParams['process'] = $zone;
			$zoneParams['ajax']=true;
			$pContent = _tag ('copixzone', $zoneParams);
			$jsParams['zone'] = $zoneParams['id'];
		}


		$templateParams->class = $class;
		$templateParams->id = $id;

		if (isset ($templateParams->width)) {
			$templateParams->width = 'auto';
		}
		//On gère le template
		$tpl = new CopixTPL ();
		$tpl->assign ('MAIN', $pContent);
		$tpl->assign ('ppo', $templateParams);
		$toReturn = $tpl->fetch ($template);


		if (!isset ($jsParams['dragSelector'])) {
			$jsParams['dragSelector'] = '.copixwindow_title';
		}

		$js = new CopixJSWidget();
		$js->Copix->register_copixwindow ($id, $jsParams, $domready);//->display ();
		_tag ('mootools', array ('plugin' => array ('overlayfix')));
		CopixHTMLHeader::addJSLink(_resource ('js/taglib/copixwindow.js'));
		CopixHTMLHeader::addJSDOMReadyCode($js);

		return $toReturn;
	}
}