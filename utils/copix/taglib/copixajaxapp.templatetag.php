<?php
/**
 * @package    copix
 * @subpackage taglib
 * @author     Gérald Croës, Salleyron Julien, Guillaume Perréal
 * @copyright  CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Pour afficher une zone Copix
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCopixAjaxApp extends CopixTemplateTag  {
	public function process ($pContent=null){
		$pParams = $this->getParams ();
		
		// Récupère les paramètres
		$zone       = $this->requireParam ('process');
		$required   = $this->getParam     ('required');
		$id         = $this->getParam     ('id');
		
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
		
		// Initialise le Javascript
		CopixHTMLHeader::addJSFramework();
		CopixHTMLHeader::addJSLink(_resource('js/taglib/copixapp.js'));

		$js = new CopixJSWidget();
			
		// Options de la zone
		$options = array('zoneId' => $id);	
		$options['url'] = _url ($zone, $this->getExtraParams());
		// Met en session AJAX les paramètres de la zone
		$options['instanceId'] = $instanceId = uniqid();
		CopixAJAX::getSession()->set($instanceId, array($id, _url ('#'), CopixRequest::asArray ()));
			
		// Initialise la zone 
		$js->Copix->registerApp($options);
			
		// Ajoute le code
		CopixHTMLHeader::addJSDOMReadyCode($js, "tag_copixapp_".$id);
		
		return '<div id="'.$id.'"></div>';
	}
}