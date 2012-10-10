<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * Gère la soumission des formulaires du CMS
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */
class ActionGroupDefault extends CopixActionGroup{
	
	/**
	 * Soumission d'un formulaire du CMS
	 * @return CopixActionReturn
	 */
	public function processSubmit (){
		// récupération du theme d'affichage de la page precedente identifiée par le public_id
		if (_request('public_id') && $theme = _ioClass('heading|headingElementInformationServices')->getTheme (_request('public_id'), $fooParameterIn)){
			CopixTpl::setTheme ($theme);
		}
		
		$formBuilder = new Form_Builder();
		
		//Récupération du formulaire
		//Ici le thème sert lors du routage
		$cmsForm = $formBuilder->get(_request('cf_id'), _request('cf_theme'));
		
		if ($cmsForm == null) {
			//On renvoit directement sur la page appelante
			return _arRedirect($_SERVER['HTTP_REFERER']);
		}
		
		//Gestion des blocs dynamiques
		//Listage des blocs utilisés
		$arBlocs = array();
		foreach (CopixRequest::asArray() as $key => $values) {
			if (strpos($key, 'cfbc_') === 0) {
				$arBlocs[] = substr($key, 5 ,strpos($key, '_', 5) - 5);
			}
		}
		$arBlocs = array_unique($arBlocs);
		foreach ($arBlocs as $idBloc) {
			$cmsForm->attachBloc($formBuilder->getBloc($idBloc, _request('cf_theme')));
		}
		
		$cmsForm->populate(CopixRequest::asArray());
		
		if ($cmsForm->check() === false) {
			//Mise en session des valeurs saisies pour afficher les erreurs sur la page appelante
			CopixSession::set ('form|current|values', $cmsForm->getValues(), 'form_values_' . _request('cf_id'));
			return _arRedirect($_SERVER['HTTP_REFERER']);
		}
		//Suppression de l'éventuelle session
		CopixSession::destroyNamespace('form_values_' . _request('cf_id'));
		
		//Formatage des données
		$routeClass = 'Route_' . ucfirst($cmsForm->cf_route);
		$formRoute = new $routeClass ($cmsForm);
		
		//Envoie au routage
		$formRoute->process($cmsForm->getValues());
		
		$ppo = _ppo();
		$ppo->TITLE_PAGE = "Votre demande vient d'être envoyée";
		$ppo->urlRetour = $_SERVER['HTTP_REFERER'];
		
		_notify ('cms_formsubmit', array ('public_id' => _request('public_id')));

		if(_request('public_id_confirmation')){
			return _arRedirect(_url('heading||', array('public_id'=>_request('public_id_confirmation'))));
		}
		return _arPPO($ppo, 'submit.success.tpl');
	}
	
	/**
	 * Aperçu d'un formulaire
	 * @return CopixActionReturn
	 */
	public function processPreview() {
		
		$formBuilder = new Form_Builder();
		$config = new Form_Config();
		$arThemes = $config->getThemes();
		
		$ppo = _ppo();
		$ppo->TITLE_PAGE = 'Aperçu du formulaire';
		$ppo->arForms = array();
		
		foreach ($arThemes as $theme => $description) {
			$form = $formBuilder->get(_request('id'));
			$ppo->arForms[$description] = $form;
		}
		
		CopixHTMLHeader::addCSSLink(_resource ('form|css/cmsform-content.css'));
		$tpl = new CopixTpl();
		$tpl->assign('ppo', $ppo);
		$ppo->MAIN = $tpl->fetch('form.display.tpl');
		return _arPPO($ppo, 'generictools|blanknohead.tpl');
	}

    /**
     * Récupération d'un bloc dynamique
     * @return CopixActionReturn
     */
    public function processGetBlocDynamique() {
        $builder = new Form_Builder();
        $bloc = $builder->getBloc(_request('id'), _request('theme'));

        $ppo = _ppo();
        $ppo->idBloc = $bloc->getId();
        $ppo->fields = $bloc->getFields();

        return _arDirectPPO($ppo, 'bloc.display.tpl');
    }
}