<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * Portlet pour l'intégration des Formulaires dans les pages
 * 
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */
class PortletCMSForm extends Portlet {
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les titles sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletCMSForm";
	}
	
	/**
	 * rendu du contenu du title
	 *
	 * @param string $pRendererContext le contexte de rendu (Modification, Moteur de recherche, affichage, ....)
	 * @param string $pRendererMode    le mode de rendu demandé (généralement le format de sortie attendu)
	 * @return string
	 */
	protected function _renderContent ($pRendererMode, $pRendererContext){
		if ($pRendererMode == RendererMode::HTML){
			return $this->_renderHTML ($pRendererContext);
		}
		throw new CopixException ('Mode de rendu non pris en charge');
	}

	/**
	 * Rendu pour le mode HTML
	 *
	 * @param string $pRendererContext le contexte de rendu
	 * @return string
	 */
	private function _renderHTML ($pRendererContext){
		if ($pRendererContext == RendererContext::DISPLAYED || $this->getEtat () == self::DISPLAYED){
			return $this->_renderHTMLDisplay ();
		}else{
			return $this->_renderHTMLUpdate ();
		}
	}
	
	/**
	 * Retourne le contenu du template de la portlet en rendu update
	 * @return String
	 */
	private function _renderHTMLUpdate (){
		$tpl = new CopixTpl ();
		
		$tpl->assign('identifiantFormulaire', $this->getRandomId ());
		
		$params = new CopixParameterHandler();
		$options = $this->getOptions ();	
		$params->setParams($options);
		$tpl->assign('params', $params);
		
		$tpl->assign('arCMSForms', DAOcms_form::instance ()->getList());
		
		$tpl->assign('selectId', 'cmsform_' . $this->getRandomId ());
		$tpl->assign('selectedForm', $params->getParam('cmsform'));
		
		$formConfig = new Form_Config();
		$tpl->assign('arThemes', $formConfig->getThemes());
		$tpl->assign('selectedTheme', $params->getParam('cf_theme'));
		$tpl->assign('selectedConfirmationTheme', $params->getParam('cf_public_id_confirmation'));
		$tpl->assign('confirmation', $params->getParam('cf_confirmation'));
		
		$content = DAOcms_form::instance ()->getContent($params->getParam('cmsform'));
		$fields = array();
		foreach ($content as $field) {
			$fields[$field->cfc_id_element] = $field->cfe_label;
		}
		$fields['submit'] = 'Bouton valider';
		$tpl->assign('fields', $fields);
		$tpl->assign('selectedFields', $this->getOption('selectedFields'));
		
		$toReturn = $tpl->fetch ('form|portlet.form.edit.php');
		return $toReturn;
	}
	
	/**
	 * Retourne le contenu du template de la portlet en rendu display
	 * @return String
	 */
	private function _renderHTMLDisplay (){
		
		CopixHTMLHeader::addCSSLink(_resource('heading|css/cms.css'));
		CopixHTMLHeader::addCSSLink(_resource ('heading|css/mycmscssconfig.css'));
		//Push du context pour que la surcharge de la dao fonctionne (appel à la génération des pages)
		CopixContext::push('form');
		$params = new CopixParameterHandler ();
		$params->setParams($this->_moreData);
		$idCmsForm = $params->getParam('cmsform');
		$formTheme = $params->getParam('cf_theme');
		
		$formBuilder = new Form_Builder();
		$form = $formBuilder->get($idCmsForm, $formTheme);
		
		//on transmet le public_id  de la page en cours
		$form->attachField ('public_id', _field ('hidden'), array());
		$form->populate(array('public_id' => _request('public_id')));	

		if($params->getParam('cf_confirmation')){
			$form->attachField ('public_id_confirmation', _field ('hidden'), array());
			$form->populate(array('public_id_confirmation' => $params->getParam('cf_public_id_confirmation')));
		}
		
		//Cas du formulaire supprimer ou invalide
		if ($form === false) {
			CopixContext::pop();
			return '';
		}
		
		//Gestion des erreurs
		//Si l'on revient d'un validation qui à échoué, alors on récupère les valeurs saisies en session.
		if (!!$arValues = CopixSession::get ('form|current|values', 'form_values_' . $idCmsForm)) {
			$form->populate($arValues);
			$form->check();
		}
		$selectedFields = $params->getParam('selectedFields');
		if (!empty($selectedFields)) {
			//Appel au registre pour signaler le mode de rendu partiel
			CopixRegistry::instance()->set('partialMode', true, 'cms|form');
			CopixRegistry::instance()->set('idCmsForm', $idCmsForm, 'cms|form');
			//Traitement de l'affichage partiel
			$toReturn = $form->getPartialHTML($selectedFields);
		} else {
			$toReturn = $form->getAllHTML();
		}
		CopixContext::pop();
		
		return $toReturn;
	}
	
}