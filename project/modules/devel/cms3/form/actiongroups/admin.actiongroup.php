<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * Administration des formulaires du CMS
 *
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */
class ActionGroupAdmin extends ActionGroupAbstractAdminHeadingElement {

	/**
	 * Ajout des styles et JS
	 *
	 * @param string $pActionName le nom de l'action demandée
	 *
	 * @return void
	 */
	protected function _beforeAction ($pActionName){
		if ($pActionName != 'setValueOption' && $pActionName != 'getValueOption'){
			parent::_beforeAction($pActionName);
		}
		CopixHTMLHeader::addCSSLink (_resource ('form|css/cmsform-content.css'));
		CopixHTMLHeader::addCSSLink (_resource ('heading|css/cms.css'));
		CopixHTMLHeader::addJsLink (_resource ('form|js/cmsform-content.js'));
		CopixHTMLHeader::addCSSLink (_resource ('heading|css/mycmscssconfig.css'));
	}

	/**
	 * Enregistrement en session et redirection vers l'edition
	 *
	 * @return CopixActionReturn
	 */
	public function processPrepareEdit (){
		$service = new Form_Service();
		$toEdit = $service->getOrCreateCmsForm();
		//redirection vers l'écran de modification
		return _arRedirect (_url ('admin|edit', array ('editId'=>$toEdit->editId)));
	}

	/**
	 * Edition d'un formulaire
	 *
	 * @return CopixActionReturn
	 */
	public function processEdit (){
		$service = new Form_Service();
		$form = $service->getCmsFormForm();

		$ppo = _ppo ();
		$ppo->editedElement = $service->getCurrentForm();

		$form->populate(get_object_vars($ppo->editedElement));
		$ppo->TITLE_PAGE = $ppo->editedElement->public_id_hei ? 'Modification d\'un formulaire' : 'Création d\'un formulaire';
		$ppo->form = $form;

		//Gestion des paramètres du routage
		if (!empty($ppo->editedElement->cf_route)) {
			$routeClass = 'Route_' . ucfirst($ppo->editedElement->cf_route);
			if (class_exists($routeClass)) {
				$ppo->route_form = call_user_func(array($routeClass, 'getFormParams'), unserialize($ppo->editedElement->cf_route_params));
			}
		}
		return _arPpo ($ppo, 'form.edit.tpl');
	}

	/**
	 * Edition du contenu du formulaire
	 *
	 * @return CopixActionReturn
	 */
    public function processEditContent (){
		$ppo = _ppo ();
		return _arPpo ($ppo, 'form.editcontent.tpl');
	}

	/**
	 * Enregistrement du formulaire
	 *
	 * @return CopixActionReturn
	 */
	public function processValid () {
		//Appeler depuis l'édition du contenu, à ce stade tout le contenu du formulaire ce trouve en session
		//Il suffit donc de sauver la session
		$service = new Form_Service();
		$form = $service->getCmsFormForm();

		$element = $service->getCurrentForm();

		//On attribut l'objet courant au formulaire pour validation
		$form->populate(get_object_vars($element));

		if ($form->check() === false) {
			//Retour sur le formulaire
			$ppo = _ppo ();
			$ppo->form = $form;
			return _arPpo ($ppo, 'form.edit.tpl');
		}

		//On réordonne les champs (drag&drop)
		$service->updateContentOrder();

		if ($element->cf_id === null) {
			$service->insert ($element);
		} else {
			//On modifie directement l'objet si
			//le statut de l'élément est brouillon
			$service->update ($element);
		}

		//retour sur l'écran d'admin générale
		return _arRedirect (_url ('heading|element|finalizeEdit', array ('editId' => _request ('editId'), 'result'=>'saved', 'selected'=>array($element->id_helt . '|' . $element->type_hei))));
	}

	/**
	 * Affichage du formulaire actuel
	 *
	 * @return CopixActionReturn
	 */
	public function processDisplay() {
		$formService = new Form_Service();
		$formService->updateContentOrder();
		$cmsForm = $formService->getCurrentForm();

		$formBuilder = new Form_Builder();
		$config = new Form_Config();
		$arThemes = $config->getThemes();

		$ppo = _ppo();
		$ppo->TITLE_PAGE = 'Aperçu du formulaire';
		$ppo->arForms = array();

		foreach ($arThemes as $theme => $description) {
			$form = $formBuilder->make($cmsForm, $theme);
			$form->populate($cmsForm);//Pour gérer, les hidden, les valeurs par défaut
			$ppo->arForms[$description] = $form;
		}

		return _arPPO($ppo, 'form.display.tpl');
	}

	/**
	 * Affichage des options avancées pour les valeurs d'un composant à choix multiple
	 *
	 * @return CopixActionReturn
	 */
	public function processGetValueOption() {
		$element_value = DAOcms_form_element_values::instance ()->get(_request('id'));

		$ppo = _ppo($element_value);
		$ppo->arFormBlocs = iterator_to_array(DAOcms_form_bloc::instance ()->findAll());

		$tpl = new CopixTpl();
		$tpl->arFormElement = iterator_to_array(DAOcms_form_element::instance ()->getAll());

		return _arPPO($ppo, array('template' => 'element.value.options.tpl', 'mainTemplate' => 'default|popup.php'));
	}

	/**
	 * Enregistrement des options pour un element_value
	 *
	 * @return CopixActionReturn
	 */
	public function processSetValueOption() {

		$idBloc = (_request('disable_form_bloc')!=null) ? null : _request('form_bloc');
		$parentAdopt = _request('parent_adopt',  0);
		$elementValue = DAOcms_form_element_values::instance ()->get(_request('cfev_id'));
		if ($elementValue != false) {
			$elementValue->cfev_id_bloc_to_display = is_array($idBloc) ? $idBloc[0] : $idBloc;
			$elementValue->cfev_parent_adopt = $parentAdopt;
			DAOcms_form_element_values::instance ()->update($elementValue);
		}

		$ppo = _ppo();
		$ppo->MAIN = "OK";
		return _arDirectPPO($ppo, 'generictools|blank.tpl');
	}
}