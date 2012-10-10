<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës, Salleyron Julien, Guillaume Perréal
 * @copyright	CopixTeam
 * @link			http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Pour afficher une zone Copix
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCopixZone extends CopixTemplateTag  {

	public function process ($pContent=null){
		$pParams = $this->getParams ();

		// Récupère les paramètres
		$zone       = $this->requireParam ('process');
		$required   = $this->getParam ('required');
		$ajax       = $this->getParam ('ajax', false);
		$id         = $this->getParam ('id');
		$idClick    = $this->getParam ('idClick');
		$text       = $this->getParam ('text', '');
		$extra      = $this->getParam ('extra', '');
		$handlers   = array_filter ($this->getParam (array ('onDisplay', 'onHide', 'onComplete'), null, 'string'));
		$auto       = $this->getParam ('auto', false);
		$zoneParams = array_merge(
			$this->getParam ('zoneParams', array ()),
			$this->getParams ()
		);

		// Supprime le préfixe "zoneParams_" des paramètres de la zone
		// Cela peut servir à passer des paramètres supplémentaires au niveau du tag
		// qui rentrent en conflit avec les noms des paramètres standard.
		foreach($zoneParams as $key=>$value) {
			if(preg_match('/^zoneParams_(.+)$/i', $key, $parts)) {
				unset($zoneParams[$key]);
				$zoneParams[$parts[1]] = $value;
			}
		}

		// Vérifie l'existence du module
		$fileInfo = new CopixModuleFileSelector ($zone);
		if (! CopixModule::isEnabled ($fileInfo->module) && ($required === false)) {
			return "";
		}

		// Génère un identifiant si nécessaire
		$idProvided = ($id !== null);
		if(!$idProvided) {
			$id = uniqid('copixzone');
		}

		$toReturn = array();

		// On a spécifié un texte : on l'ajoute comme trigger
		if($text) {
			if($idClick === null) {
				$idClick = $id.'_trigger';
			}
			$toReturn[] = '<span id="'.$idClick.'">'.$text.'</span>';
		}

		// Zone javascript si on a un clicker, de l'AJAX ou des gestionnaires d'événéments
		if($idProvided || $ajax || count($handlers) || $idClick) {
				
			// Initialise le Javascript
			CopixHTMLHeader::addJSFramework();
			CopixHTMLHeader::addJSLink(_resource('js/taglib/copixzone.js'), array('id' => 'taglib_copixzone_js'));

			$js = new CopixJSWidget();
				
			// Options de la zone
			$options = array('zoneId' => $id);

			// Met en session AJAX les paramètres de la zone
			if($ajax) {
				$options['instanceId'] = $instanceId = uniqid();
				CopixAJAX::getSession()->set($instanceId, array($zone, $zoneParams, _url('#'), CopixRequest::asArray ()), $id);
			}
				
			if($auto) {
				$options['auto'] = true;
			}

			// Ajoute les handlers
			foreach($handlers as $name=>$code) {
				$options[$name] = $js->function_(null,"div,trigger",$code);
			}
				
			// Identifiant du trigger
			if($idClick) {
				$options['triggerId'] = $idClick;
			}
				
			// Initialise la zone
			$js->Copix->registerZone($options);
				
			// Ajoute le code
			CopixHTMLHeader::addJSDOMReadyCode($js, "tag_copixzone_".$id);
		}

		// Contenu de la zone
		if($ajax) {
			$zoneContent = '';
			$style = 'style="display:none;" ';
		} else {
			$zoneContent = CopixZone::process ($zone, $zoneParams);
			$style = '';
		}

		if($idProvided || $style || $extra || $ajax || count($handlers) || $idClick) {
			$toReturn[] = '<div id="'.$id.'" '.trim($style.$extra).'>'.$zoneContent.'</div>';
		} else {
			$toReturn[] = $zoneContent;
		}

		return join('', $toReturn);
	}
}