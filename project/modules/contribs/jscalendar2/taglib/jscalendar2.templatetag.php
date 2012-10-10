<?php
/**
 * @package		jscalendar2
 * @author		Duboeuf Damien
 */

/**
 * Pour afficher un calendier
 * 
 * Les parametre sonts :
 * 
 * id=uniqid()                               : Identifian du calendrier
 * name=id                                   : name du champs input l'acompagnant autogénéré
 * class                                     : class du champs input l'acompagnant autogénéré
 * style                                     : style du champs input l'acompagnant autogénéré
 * tabindex                                  : tabeIndex du champs input l'acompagnant autogénéré
 * extra                                     : ajout de paramettre exotique aux champs input l'acompagnant autogénéré
 * lock                                      : Vérouille l'input automatique si el javascripte est activé
 * trigger                                   : tableu de paramettre ciblant le déclancheur du calendrier
 *                                              - id    : identifiant du trigger
 *                                              - class : class du trigger
 *                                              - extra : parametre exotique du trigger
 *                                              - style : style exotique du trigger
 * interne=false                             : Applique le trigger d'une manière interne
 * template='jscalendar2|calendar.tpl'       : template par default du calendrier
 * weekNumbers=true                          : Affichage des nom de la semaine
 * theme=TemplateTagJSCalendar2::CSS_COMPACT : Theme associé au calendare
 *                                             la liste des themes sont :
 *                                              - CSS_COMPACT
 *                                              - CSS_WIN2K
 *                                              - CSS_GOLD
 *                                              - CSS_STEEL
 *                                              - CSS_MATRIX
 * 
 * @package		jscalendar2
 */
class TemplateTagJSCalendar2 extends CopixTemplateTag  {
	
	const CSS_COMPACT = 'jscalendar2|js/jscal2/src/css/reduce-spacing.css';
	const CSS_WIN2K   = 'jscalendar2|js/jscal2/src/css/win2k/win2k.css';
	const CSS_GOLD    = 'jscalendar2|js/jscal2/src/css/gold/gold.css';
	const CSS_STEEL   = 'jscalendar2|js/jscal2/src/css/steel/steel.css';
	const CSS_MATRIX  = 'jscalendar2|js/jscal2/src/css/matrix/matrix.css';
	
	public function process ($pContent = null){
		
		$jsParams = array ('weekNumbers'=>true, 'dateFormat'=>'%d/%m/%Y');
		
		$id       = $this->getParam('id'      ,  uniqid());
		$interne  = $this->getParam('interne' ,  false);
		$value    = $this->getParam('value');
		$template = $this->getParam('template', 'jscalendar2|calendar.tpl');
		$theme    = $this->getParam('theme', self::CSS_COMPACT);
		
		//paramettre du trigger
		if ($interne) {
			$trigger['id'] = $id;
		} else {
			$trigger = $this->getParam('trigger', array());
			$trigger['id'] = (isset ($trigger['id'])) ? $trigger['id'] : 'trigger_'.$id;
		}
		$trigger = (object)$trigger;
		
		//paramettre du javascript
		foreach ($jsParams as $key=>$default) {
			$jsParams[$key] = $this->getParam($key, $default);
		}
		
		// Ajout des CSS
		_tag ('mootools');
		CopixHTMLHeader::addCSSLink (_resource ('jscalendar2|js/jscal2/src/css/jscal2.css'));
		CopixHTMLHeader::addCSSLink (_resource ('jscalendar2|js/jscal2/src/css/border-radius.css'));
		CopixHTMLHeader::addCSSLink (_resource ($theme));
		
		
		// Ajout des javascripts
		CopixHTMLHeader::addJSLink  (_resource ('jscalendar2|js/jscal2/src/js/jscal2.js'));
		
		$ressource = 'jscalendar2|js/jscal2/src/js/lang/'.strtolower(CopixI18N::getLang()).'.js';
		if (file_exists(_resourcePath($ressource))) {
			CopixHTMLHeader::addJSLink  (_resource ($ressource));
		} else {
			CopixHTMLHeader::addJSLink  (_resource ('jscalendar2|js/jscal2/src/js/lang/en.js'));
		}
		CopixHTMLHeader::addJSLink  (_resource ('jscalendar2|js/copix_jscal2.js'));
		
		
		// Création js du calendar
		$jsParams = array_merge($jsParams,
		                        array ('inputField' => $id,
		                               'trigger'    => $trigger->id));
		
		$js = new CopixJSWidget();
		$js->Copix->register_jsCalendar2 ($id, $jsParams);
		CopixHTMLHeader::addJSDOMReadyCode($js);
		
		
		// Ajout du template
		$ppo            = new CopixPPO ();
		$tpl            = new CopixTpl ();
		$ppo->id        = $id;
		$ppo->trigger   = $trigger;
		$ppo->interne   = $interne;;
		$ppo->name      = $this->getParam('name', $id);
		$ppo->class     = $this->getParam('class');
		$ppo->style     = $this->getParam('style');
		$ppo->extra     = $this->getParam('extra');
		$ppo->tabindex  = $this->getParam('tabindex');
		$ppo->lock      = $this->getParam('lock');
		$ppo->value     = $value;
		$ppo->size      =$this->getParam('size', 10);
		$ppo->maxlength =$this->getParam('maxlength', 10);
		/*
		$ppo->width    = $width;
		$ppo->title    = $title;
		*/
		$tpl->assign ('ppo', $ppo);
		$pContent = $tpl->fetch ($template);
		
		return $pContent;
	}
	
	
}