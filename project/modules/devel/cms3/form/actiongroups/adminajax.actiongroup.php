<?php 
/**
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * Gestion des action ajax sur l'administration des formulaires du CMS
 * @package cms3
 * @subpackage form
 * @author Nicolas Bastien
 */
class ActionGroupAdminAjax extends CopixActionGroup {
	
	/**
	 * Renvoit la liste des types de champs possible
	 * @return array
	 */
	private function _getTypeElement() {
		$formConfig = new Form_Config();
		return $formConfig->getFields();
	}
	
	/**
	 * Mise à jour de la session depuis l'édition du CmsForm
	 * @return CopixActionReturn
	 */
	public function processUpdateCmsForm() {
		$formService = new Form_Service();
		$element = $formService->getCurrentForm();
		_ppo (CopixRequest::asArray ('caption_hei', 'description_hei', 'cf_route'))->saveIn ($element);
		return _arNone ();
	}
	
	
	/**
	 * Affichage du formulaire de création d'un élément
	 * @return CopixActionReturn
	 */
	public function processNewElement() {
		$ppo = _ppo();
		$ppo->arTypeElement = $this->_getTypeElement();
		
		return _arDirectPPO($ppo, 'form.element.edit.tpl');
	}
	
	/**
	 * Soumission de la création d'élément
	 * @return CopixActionReturn
	 */
	public function processSubmitNewElement() {
		$errors = array();
		if (_validator ('NotEmpty')->check(_request('cfe_label')) !== true
		|| _validator ('string')->check(_request('cfe_label')) !== true) {
			$errors[] = "Vous devez saisir un libellé correct.";
		}
	
		if (count($errors) > 0) {
			$ppo = _ppo();
			$ppo->arTypeElement = $this->_getTypeElement();
			$ppo->errors = $errors;
			return _arDirectPPO($ppo, 'form.element.edit.tpl');
		}
		
		//Enregistrement de l'élément
		$record = DAORecordcms_form_element::create ();
		$record->cfe_label = _request('cfe_label');
		$record->cfe_type = _request('cfe_type');
		
		DAOcms_form_element::instance ()->insert ($record);
		
		$ppo = _ppo($record);
		return _arDirectPPO($ppo, 'form.element.add.tpl');
	}
	
	/**
	 * Affichage du formulaire de mise à jour d'un élement
	 * @return CopixActionReturn
	 */
	public function processUpdateElement() {
		$record = DAOcms_form_element::instance ()->getWithValues(_request('cfe_id'));
		
		if ($record == null) {
			//Si l'on trouve pas l'élément, on n'affiche rien
			return _arNone();
		}
		
		$ppo = _ppo();
		$ppo->record = $record;
		$elements = $this->_getTypeElement();
		$ppo->record->libelle_type = $elements[$ppo->record->cfe_type];
		$config = new Form_Config();
		$ppo->arUserInfos = $config->getFormData();

		return _arDirectPPO($ppo, 'form.element.update.tpl');
	}
	
	public function processGetFieldType() {
		$ppo = _ppo();
		$ppo->arTypeElement = $this->_getTypeElement();
		$ppo->selectedType = _request('cfe_id');
		return _arDirectPPO($ppo, 'element.type.update.php');
	}
	
	/**
	 * Soumission de la mise à jour d'élément
	 * @return CopixActionReturn
	 */
	public function processSubmitUpdateElement() {
		$errors = array();
		if (_validator ('NotEmpty')->check(_request('cfe_label')) !== true
		|| _validator ('string')->check(_request('cfe_label')) !== true) {
			$errors[] = "Vous devez saisir un libellé correct.";
		}
		
		if (count($errors) > 0) {
			$ppo = _ppo();
			$ppo->errors = $errors;
			return _arDirectPPO($ppo, 'form.element.update.tpl');
		}
		
		//Enregistrement de l'élément
		$record = DAOcms_form_element::instance ()->get(_request('cfe_id'));
		
		if ($record === false) {
			//L'identifiant est invalide
			return _arContent('');
		}
		
		$record->cfe_label = _request('cfe_label');
		$record->cfe_aide = _request('cfe_aide');
		if (_request('cfe_type')){
			$record->cfe_type = _request('cfe_type');
		}
		$record->cfe_orientation = (_request('cfe_orientation') != null) ? _request('cfe_orientation') : 0;
		$record->cfe_columns = (_request('cfe_columns') != null) ? _request('cfe_columns') : 0;
		$record->cfe_default = _request('cfe_default');
		$record->cfe_default_data = _request('cfe_default_data');
		
		DAOcms_form_element::instance ()->update ($record);
		
		//Mise à jour du libellé + suppression du bloc de mise à jour
		return _arContent("<script>$('cfe_label_$record->cfe_id').innerHTML = '" . $record->cfe_label . "';myUpdateElementSlide.slideOut(); $('update_element_div').innerHTML = '';</script>");
	}
	
	/**
	 * Suppression d'un élément
	 * @return CopixActionReturn
	 */
	public function processDeleteElement() {
		$record = DAOcms_form_element::instance ()->get(_request('cfe_id'));
		if ($record === false) {
			return _arContent('');
		}
		//Suppression de l'élément du formulaire en cours
		DAOcms_form_element::instance ()->delete($record);
		
		//Code JS de suppression de la ligne
		return _arContent("<script>$('form_field_line_{$record->cfe_id}').remove();myUpdateElementSlide.slideOut();$('update_element_div').innerHTML='';</script>");
	}
	
	/**
	 * Ajout / Suppression d'un champs du Formulaire courant
	 * @return CopixActionReturn
	 */
	public function processAddRemoveField() {
		
		$idField = _request('cfe_id');
		
		$formService = new Form_Service();
		$cmsForm = $formService->getCurrentForm();

		if (_request('do') == 'add') {
			$record = DAORecordcms_form_content::create ();
			$record->cfc_id_form = $cmsForm->cf_id;
			$record->cfc_id_element = $idField;
			
			//Calcul de l'ordre (celui du dernier + l'écart)
			$lastRecord = end($cmsForm->content);
			$record->cfc_order = Form_Service::ORDER_STEP;
			if ($lastRecord !== false) {
				$record->cfc_order += $lastRecord->cfc_order;
			}
			
			$cmsForm->content[] = $record;
		} else {
			foreach ($cmsForm->content as $key => $record) {
				if ($record->cfc_id_element == $idField) {
					unset($cmsForm->content[$key]);
				}
			}
		}
		
		//Mise en session
		CopixSession::set (Form_Service::STR_SESSION, $cmsForm, _request ('editId'));
		
		if (_request('do') == 'add') {
			$element = DAOcms_form_element::instance ()->get($record->cfc_id_element);
			$record->cfe_label = $element->cfe_label;
			
			$formConfig = new Form_Config();
			$fields = $formConfig->getFields();
			$record->cfe_type = $element->cfe_type;
			$record->cfe_type_label = $fields[$element->cfe_type];
			$record->cfe_columns = $element->cfe_columns;
			$record->cfc_orientation = $element->cfe_orientation;
			$record->cfe_default = $element->cfe_default;
			$record->cfe_default_data = $element->cfe_default_data;
			
			return _arDirectPPO(_ppo($record), 'form.parametrage.add.tpl');
		}
		
		//Code JS de suppression de la ligne
		return _arContent("$('form_tr_$idField').remove();");
	}
	
	/**
	 * Création d'un cms_form_element_value
	 * @return CopixActionReturn
	 */
	public function processSubmitElementValue() {
		
		$ppo = _ppo();
		
		$record = DAORecordcms_form_element_values::create ();
		$record->cfev_id = _request('cfev_id');
		$record->cfev_id_element = _request('cfe_id');
		$record->cfev_value = _request('cfev_value');
		
		$action = 'insert';
		if ($record->cfev_id != null){$action = 'update';} 
		
		DAOcms_form_element_values::instance ()->{$action}($record);
		
		$ppo = _ppo($record);
		return _arDirectPPO($ppo, 'element.value.add.tpl');
	}
	
	/**
	 * Suppression d'un cms_form_element_value
	 * @return CopixActionReturn
	 */
	public function processDeleteElementValue() {
		$record = DAOcms_form_element_values::instance ()->get(_request('cfev_id'));
		if ($record === false) {
			return _arContent('');
		}
		DAOcms_form_element_values::instance ()->delete($record);
		
		//Code JS de suppression de la ligne
		return _arContent("<script>$('element_value_line_{$record->cfev_id}').remove();myUpdateElementSlide.slideIn();</script>");
	}
	
	/**
	 * Mise à jour d'un cms_form_element_value
	 * @return CopixActionReturn
	 */
	public function processSubmitUpdateElementValue() {
		$record = DAOcms_form_element_values::instance ()->get(_request('cfev_id'));
		if ($record === false) {
			return _arContent('');
		}
		$record->cfev_value = _request('cfev_value');
		DAOcms_form_element_values::instance ()->update($record);
		
		//Code JS de suppression de la ligne
		return _arContent("<script>switchDisplayElementValue($record->cfev_id, '$record->cfev_value');</script>");
	}
	
	
	/* *** Gestion de la portlet cms form *** */
	/**
	 * Récupération de la protlet courante
	 * @return Portlet
	 */
	protected function _getEditedElement (){
		CopixRequest::assert ('editId');
		if (!$element =	CopixSession::get ('portlet|edit|record', _request ('editId'))){
			throw new CopixException ('Page en cours de modification perdu');
		}
		return $element;
	}
	
	/**
	 * Enregistrement du formulaire courant
	 * @return void
	 */
	public function processSetCMSForm (){
		$ppo = new CopixPPO ();
		$toReturn = '';
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);
		
		$html_code = '';
		if ($portlet->getOption ('cmsform') != _request('cmsform')) {
			//Si l'on vient de changer de formulaire, on met à jour la liste des champs
			$content = DAOcms_form::instance ()->getContent(_request('cmsform'));
			$fields = array();
			foreach ($content as $field) {
				$fields[$field->cfc_id_element] = $field->cfe_label;
			}
			$fields['submit'] = 'Bouton valider';
			$tpl = new CopixTpl();
			$tpl->assign('fields', $fields);
			$tpl->assign('identifiantFormulaire', _request ('portletId'));
			$tpl->assign('target_id', 'option_div_' . _request ('portletId'));
			$tpl->assign('name', 'form_fields_' . _request ('portletId'));
			$html_code = str_replace(array("\r\n", "\n", "\r"), '', $tpl->fetch('form|portlet.form.field.tpl'));
		}
		
		
		$options = CopixRequest::asArray('cmsform', 'cf_theme', 'cf_public_id_confirmation', 'cf_confirmation');
		$options['cf_confirmation'] = $options['cf_confirmation'] == 'true' ? true : false;
		$selectedFields = _request('selectedFields');
		if (!empty($selectedFields)) {
			//Suppression du dernier '.'
			$selectedFields = substr($selectedFields, 0, -1);
			$options['selectedFields'] = explode('.', $selectedFields);
		} else {
			$options['selectedFields'] = null;//Permet de remettre à zéro
		}
		$portlet->setOptions ($options);
		
		$form = DAOcms_form::instance ()->get(_request('cmsform'));
		
		$portlet->attach ($form->public_id_hei, 0);
		
		return _arContent($html_code);
	}
	/* *** Fin - Gestion de la portlet cms form *** */
	
	/**
	 * Gestion de la checkbox obligatoire pour le contenu du formulaire
	 * @return void
	 */
	public function processAddRemoveRequiredContent() {
		$idField = _request('cfc_id_element');
		
		$formService = new Form_Service();
		$cmsForm = $formService->getCurrentForm();

		$newRequiredValue = 0;
		if (_request('do') == 'add') {$newRequiredValue = 1;}
		
		foreach ($cmsForm->content as $key => $record) {
			if ($record->cfc_id_element == $idField) {
				$record->cfc_required = $newRequiredValue;
				break;
			}
		}
		return _arContent('');
	}

    /**
     * Mise à jour de l'orientation du élément dans le formulaire
     * @return void
     */
    public function processSetOrientationContent() {

        $idElement = _request('idElement');
        $orientation = _request('orientation');

        $formService = new Form_Service();
        foreach ($formService->getCurrentForm()->content as $key => $content) {
            if ($content->cfc_id_element == $idElement) {
                $content->cfc_orientation = $orientation;
                break;
            }
        }
        return _arContent('');
    }


	/* *** Gestion des paramètres de routage du formulaire *** */
	public function processGetRouteParams() {
		$routeClass = 'Route_' . ucfirst(_request('cf_route'));
		if (!class_exists($routeClass)) {
			return _arNone ();
		}
		
		$ppo = _ppo();
		$ppo->form = call_user_func(array($routeClass, 'getFormParams'));
		
		if ($ppo->form == false) {
			return _arNone ();
		}
		
		return _arDirectPPO ($ppo, 'route.params.edit.tpl');
	}
	
	public function processUpdateRouteParams() {
		$formService = new Form_Service();
		$element = $formService->getCurrentForm();
		
		$routeClass = _request('route_class');
		if (!class_exists($routeClass)) {
			return _arNone ();
		}
		
		$strCfRouteParams = call_user_func(array($routeClass, 'formatParams'));
		
		$element->cf_route_params = $strCfRouteParams;
		
		return _arNone ();
	}
	
	/* *** Fin - Gestion des paramètres de routage du formulaire *** */

	/**
	 * Réordonne la liste des éléments du formulaire
	 * @return CopixActionReturn
	 */
	public function processMoveUpElement() {
		$id_element = _request('id_element');
		$service = new Form_Service();
		$content = $service->moveUpElement($id_element);
		
		//On renvoit la zone
		$ppo = _ppo();
		$ppo->MAIN = CopixZone::process ('form|formparametrage');
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * Réordonne la liste des éléments du formulaire
	 * @return CopixActionReturn
	 */
	public function processMoveDownElement() {
		$id_element = _request('id_element');
		$service = new Form_Service();
		$content = $service->moveDownElement($id_element);
		
		//On renvoit la zone
		$ppo = _ppo();
		$ppo->MAIN = CopixZone::process ('form|formparametrage');
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * Mise à jour de l'ordre du contenu du formulaire (via drag & drop)
	 * @return CopixActionReturn
	 */
	public function processUpdateContentOrder() {
		$service = new Form_Service();
		$service->setContentOrder(_request('arElementOrder'));
		return _arNone();
	}

    /**
     * Affichage du bloc d'ajout de bloc
     * @return CopixActionReturn
     */
    public function processGetNewBlocDiv() {
        $tpl = new CopixTpl();
        $tpl->assign('arFormElement', iterator_to_array(DAOcms_form_element::instance ()->getAll()));
        $tpl->assign('arFormElementSelected', array());
        return _arContent($tpl->fetch('bloc.edit.tpl'));
    }

    /**
     * Ajout ou modification d'un bloc
     * @return CopixActionReturn
     */
    public function processSubmitBloc() {

        $idBloc = _request('cbf_id');

        if ($idBloc != null) {
            $record = DAOcms_form_bloc::instance ()->get($idBloc);
            $isNew = false;
        } else {
            $record = DAORecordcms_form_bloc::create ();
            $isNew = true;
        }

        _ppo (CopixRequest::asArray ('cbf_id', 'cfb_nom', 'cfb_description', 'form_field'))->saveIn($record);
        
        $isOk = DAOcms_form_bloc::instance ()->save($record);

        if ($isOk) {
            if ($isNew) {
                //On ferme le formulaire et on insère le novueau bloc dans la liste
                $jsCode = <<<JS_CODE
<script type='text/javascript'>
    hideNewDiv();
    $('blocs_list').innerHTML = $('blocs_list').innerHTML + '<div class="form_bloc" id="form_bloc_line_{$record->cfb_id}">
        <span class="form_field_chk">
            <input type="radio" value="{$record->cfb_id}" name="form_bloc[]" id="cb_form_bloc_{$record->cfb_id}" />
        </span>
        <div onclick="updateBloc({$record->cfb_id})" class="form_bloc_label" id="cfb_label_{$record->cfb_id}">{$record->cfb_nom}</div>
    </div>';
    $('cb_form_bloc_{$record->cfb_id}').addEvent('click', function () {\$('disable_form_bloc').checked = false;});
</script>
JS_CODE;
            } else {
               //On ferme le formulaire et on met à jour le div
                $jsCode = <<<JS_CODE
<script type='text/javascript'>
    hideUpdateDiv();
    $('cfb_label_{$record->cfb_id}').innerHTML='{$record->cfb_nom}';
</script>
JS_CODE;
            }
        } else {
            $divErrorId = ($isNew) ? 'form_error_new_bloc_form' : 'form_error_update_bloc_form';

            //Afifchage des erreurs
            $jsCode = <<<JS_CODE
<script type='text/javascript'>
    $('$divErrorId').innerHTML = "Vous devez remplir toutes les données.";
</script>
JS_CODE;
        }

        //On compresse pour ne pas avoir d'erreur JS
        $jsCode = str_replace(array("\r\n", "\n", "\r", "\t"), '', $jsCode);

        return _arContent($jsCode);
    }

    /**
     * Affichage du bloc de mise à jour de bloc
     * @return CopixActionReturn
     */
    public function processGetUpdateBlocDiv() {
        $tpl = new CopixTpl();
        $bloc = DAOcms_form_bloc::instance ()->getWithContent(_request('id_bloc'));
        if ($bloc === false) {return _arContent('Le bloc vient d\'être supprimé ou n\'existe pas.');}

        $arFormElementSelected = array();
        foreach ($bloc->content as $content) {
            $arFormElementSelected[] = $content->cfbc_id_element;
        }

        $tpl->assign('bloc', $bloc);
        $tpl->assign('idBloc', $bloc->cfb_id);
        $tpl->assign('arFormElement', iterator_to_array(DAOcms_form_element::instance ()->getAll()));
        $tpl->assign('arFormElementSelected', $arFormElementSelected);
        return _arContent($tpl->fetch('bloc.edit.tpl'));
    }
    
    public function processDeleteBloc() {
    	DAOcms_form_bloc_content::instance ()->deleteBy(_daoSP()->addCondition('cfbc_id_bloc', '=', _request('id_bloc')));
        DAOcms_form_bloc::instance ()->delete(_request('id_bloc'));
        return _arNone();
    }
}
