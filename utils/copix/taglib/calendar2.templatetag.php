<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Champenois Goulven
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @see			http://electricprism.com/aeron/calendar
 */

/**
 * Génération d'une boite de saisie pour les dates
 * @package copix
 * @subpackage taglib
 * @example {calendar name="maDate"	value="31/12/1970"}
 * 
 * Paramètre requis
 * 	- name : nom et identifiant de l'input qui sera créé et qui contiendra la date
 * Paramètres facultatifs 
 *  - css : le nom du fichier css à utiliser. Par défaut, mootools/css/calendars/default.css
 * Paramètres HTML
 *  - tous les paramètres HTML autorisés pour un input (value, class, style, title, etc.)
 * Paramètres pour JS :
 *  - direction : -1 vers le passé uniquement, 1 vers le futur uniquement, 0 les deux (valeur par défaut)
 *  - navigation : 0 seul le mois en cours, 1 navigation mois par mois, 2 navigation par mois et années
 *  - blocked : tableau de dates non sélectionnables (au format cron : 31 12 2009 = 31 décembre 2009, * * * 0,6 = samedi et dimanche de chaque semaine) 
 *  - dayTitle : le format de date du title pour chaque date valide (format date PHP)
 *  - draggable : si le calendrier est déplaçable (oui/1 par défaut)
 *  - etc.
 */
class TemplateTagCalendar2 extends CopixTemplateTag {

	public function process($pParams = null) {
		$this->assertParams ('name');
		$params = $this->getParams();
		if($this->getParam('id')){
			$id = $this->getParam('id');
		} else {
			$id = $this->getParam('name');
			$params['id'] = $id;
		}
		
		// Choix de la CSS
		$css = 'default';
		if($this->getParam('css', 'default') != 'default'){
			$css = $this->getParam('css');
			unset( $params['css'] );
			$params['classes'][0] = $css;
		}
		
		// event handlers
		$handlers = array( 'onHideStart', 'onHideComplete', 'onShowStart', 'onShowComplete');
		$handlersJS = array(); 
		foreach ( $handlers as $handler ){
			if ( $this->getParam($handler, false ) ){
				$handlersJS[$handler] = $this->getParam($handler);
				unset( $params[$handler] );
			}
		}
		
		
		$attributes = array ();
		// Tableau des paramètres utilisés en attributs 
		$attributsHTML = array('accesskey', 'align', 'class', 'dir', 'disabled', 'id', 'lang', 'maxlength', 'name', 'readonly', 'size', 'style', 'tabindex', 'title', 'value');
		foreach ($attributsHTML as $attribut) {
			if (isset ($params[$attribut])) {
				$attributes[$attribut] = $params[$attribut];
			}
		}

		// attribut error
		if (isset ($params['error']) && $params['error']) {
			$attributes['error'] = true;
		}

        if(!isset ($params['style'])){
            $attributes['extra'] = 'style="width: 90px"';
        }
        
		$params['img'] = (isset ($params['img'])) ? $params['img'] : _resource ('img/tools/calendar.png');

		// Convertit pour JS les paramètres qui ne correspondent pas à des attributs HTML
		$options = CopixJSON::encode( $params );
		// Force l'ajout du fichier JS du plugin
		_tag ('mootools', array ('plugins'=>'calendar2'));
		// Déclenche l'initialisation du script avec les paramètres qui le concerne
		
		$jsDomReady = " 
var calendar2_$id = new Calendar ({'$id':'d/m/Y'}, $options);
";
		foreach( $handlersJS as $handler => $value ){
			$jsDomReady .= "
calendar2_$id.addEvent('$handler', $value );";
		}
		CopixHTMLHeader::addJSDOMReadyCode( $jsDomReady );
		CopixHTMLHeader::addCSSLink(_resource('js/mootools/css/calendar2/'.$css.'.css'));
		return _tag ('inputtext', $attributes);
	}
}