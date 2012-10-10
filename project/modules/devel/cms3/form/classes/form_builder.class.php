<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * Construction les formulaires
 * 
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */
class Form_Builder {

	const URL_SUBMIT = 'form|default|submit';
	
	/**
	 * Construit un objet formulaire
	 * @param $pFormData CompiledDAORecordcms_headingelementinformations amélioré avec le contenu du formulaire
	 * @return CopixForm
	 */
	public function make($pFormData, $theme = null) {
		
		if (empty($theme)) {
			$theme = 'CopixFormLight';
		}
		
		$form = new $theme('cms_form_' . $pFormData->cf_id);
		
		$form->setTitle($pFormData->caption_hei);
		$form->setSubmitUrl(self::URL_SUBMIT);
		
		$form->attachField ('cf_id', _field ('hidden'), array ());
		$form->attachField ('cf_theme', _field ('hidden'), array ());
		
		$arDefaultData = array();
		
		foreach($pFormData->content as $form_element) {
			$arParams = array ('columns'=>$form_element->cfe_columns, 
								'label'=>$form_element->cfe_label,
                                'orientation'=>$form_element->cfc_orientation,
                                'help'=>isset($form_element->cfe_aide) ? $form_element->cfe_aide : "");
			if ($form_element->isRequired()) {
				$arParams['require'] = true;
			}
			
			$arFieldParams = $form->getFieldExtra($form_element->cfe_type);
			$arFieldParams['separator'] = array_key_exists('separator', $arFieldParams) && $arFieldParams['separator'] ? $arFieldParams['separator'] : $form->getValueSeparator($form_element->cfc_orientation);
			
			$fieldName = 'cfc_' . $pFormData->cf_id . '_' . $form_element->cfc_id_element;

            //Ajout du thème pour la création des blocs dynamiques
            $arFieldParams['theme'] = $theme;
            //Ajout du nombre de colonnes
            $arFieldParams['columns'] = $arParams['columns'];
            $field = $this->_getField($fieldName, $form_element, $arFieldParams);

			$form->attachField($fieldName, $field, $arParams);
            //Gestion des blocs dynamiques
            if ($field->getParam('jsCode') != null) {
                $form->addJSCode($field->getParam('jsCode'));	
            }
			
			//Ajout des valeurs par défaut
			if (isset($form_element->cfe_default_data) && !empty($form_element->cfe_default_data)) {
				//Premièrement on initialise avec les données de l'utilisateur
				$arUserFields = explode('#', $form_element->cfe_default_data);
				$user = _currentUser();
				$arDefaultData[$fieldName] = null;
				foreach ($arUserFields as $userField) {
					if ($user->getExtra($userField) != null) {
						$arDefaultData[$fieldName] .= $user->getExtra($userField) . ' ';
					}
				}
			}
			
			if (!isset($arDefaultData[$fieldName])
			&& isset($form_element->cfe_default) && !empty($form_element->cfe_default)) {
				//Si aucune donnée utilisateur, on met les éventuelles données par défaut
				$arDefaultData[$fieldName] = $form_element->cfe_default;
			}
		}
	
		$form->attachField ('submit', _field ('submit'), $form->getFieldExtra('submit')); 
		
		$form->populate($arDefaultData);
		
		return $form;
	}
	
	/**
	 * Construction d'un formulaire depuis son identifiant
	 * @param $pIdCmsForm
	 * @return CopixForm
	 */
	public function get($pIdCmsForm, $formTheme = null) {
		$form = DAOcms_form::instance ()->get($pIdCmsForm);
		
		if ($form === false) {
			return false;
		}
		
		$element = _ioClass ('heading|HeadingElementInformationServices')->getById ($pIdCmsForm, Form_Service::FORM_TYPE);
		//fusion des informations communes et spécifiques
		_ppo ($form)->saveIn ($element);
		
		$element->content = DAOcms_form::instance ()->getContent($pIdCmsForm);
		
		$toReturn = $this->make($element, $formTheme);
		
		//Ajout de l'identifiant
		$toReturn->populate(array('cf_id' => $pIdCmsForm, 'cf_theme' => $formTheme));
		
		//Ajout du route
		$toReturn->cf_route = $form->cf_route;
		$toReturn->cf_route_params = $form->cf_route_params;
		
		return $toReturn;
	}

    public function getBloc($pIdBloc, $theme = null) {

        $bloc = DAOcms_form_bloc::instance ()->getForDisplay($pIdBloc);

        if (empty($theme)) {
			$theme = 'CopixFormLight';
		}

		$form = new $theme('cms_form_bloc_' . $pIdBloc);
        $arDefaultData = array();
		foreach($bloc->content as $form_element) {
			$arParams = array ('label'=>$form_element->cfe_label, 'orientation'=>$form_element->cfe_orientation);
			if ($form_element->isRequired()) {
				$arParams['require'] = true;
			}

			$arFieldParams = $form->getFieldExtra($form_element->cfe_type);

			$arFieldParams['separator'] = $form->getValueSeparator($form_element->cfe_orientation);

			$fieldName = 'cfbc_' . $pIdBloc . '_' . $form_element->cfbc_id_element;
			$form_element->cfc_id_element = $form_element->cfbc_id_element; //pour _getField
			
            $field = $this->_getField($fieldName, $form_element, $arFieldParams);
			$form->attachField($fieldName, $field, $arParams);
            
			//Ajout des valeurs par défaut
			if (isset($form_element->cfe_default_data) && !empty($form_element->cfe_default_data)) {
				//Premièrement on initialise avec les données de l'utilisateur
				$arUserFields = explode('#', $form_element->cfe_default_data);
				$user = _currentUser();
				$arDefaultData[$fieldName] = null;
				foreach ($arUserFields as $userField) {
					if ($user->getExtra($userField) != null) {
						$arDefaultData[$fieldName] .= $user->getExtra($userField) . ' ';
					}
				}
			}

			if (!isset($arDefaultData[$fieldName])
			&& isset($form_element->cfe_default) && !empty($form_element->cfe_default)) {
				//Si aucune donnée utilisateur, on met les éventuelles données par défaut
				$arDefaultData[$fieldName] = $form_element->cfe_default;
			}
		}

		$form->populate($arDefaultData);

        return $form;
    }
	
	
	/**
	 * Renvoit le CopixField correspondant
     * @param $pFieldName string
	 * @param $pFormElement
	 * @param $pParams extra pour l'affichage
	 * @return CopixField
	 */
	private function _getField($pFieldName, $pFormElement, $pParams) {
		$arTypeWithValues = array('select','checkbox','radio');
		if (in_array($pFormElement->cfe_type,  $arTypeWithValues))  {
			//Récupération des valeurs possibles
			$arValues = array();
            $arBlocs = array();
			foreach (DAOcms_form_element_values::instance ()->findByElement($pFormElement->cfc_id_element) as $value) {
				$arValues[$value->cfev_value] = $value->cfev_value;
                //Gestion de l'affichage de bloc en dynamique
                if ($value->cfev_id_bloc_to_display != null) {
                    $arBlocs[$value->cfev_value] = $value;
                }
			}

			$pParams['values'] = $arValues;
            if (count($arBlocs) > 0) {
                //Gestion du code d'affichage			
                if ($pFormElement->cfe_type == 'select') {
                    if (!isset($pParams['extra'])){$pParams['extra']='';}
                    $pParams['extra'] .= ' onchange="jsFunc_' . $pFieldName . '();"';
                    $pParams['jsCode'] = $this->_getFieldJSCode($pFieldName, $arBlocs, $pParams['theme']);
                } else if($pFormElement->cfe_type == 'checkbox'){
                	if (!isset($pParams['extra'])){$pParams['extra']='';}
                    $pParams['extra'] .= ' onchange="jsCheckBoxFunc_' . $pFieldName . '(this);"';
                    $pParams['jsCode'] = $this->_getCheckBoxFieldJSCode($pFieldName, $arBlocs, $pParams['theme']);
                }
				//Ajout des infos pour la reconstruction à la validation
				$pParams['arBlocs'] = $arBlocs;
            }
			//Si le champs est obligatoire, on supprimer la valeur vide
			$pParams['emptyShow'] = !$pFormElement->isRequired();
		}
		
		return _field($pFormElement->cfe_type, $pParams);
	}

	/**
	 * Création du code javascript pour la gestion des blocs dynamiques
	 * 
	 * @param object $pFieldName
	 * @param object $pArBlocs
	 * @param object $pTheme
	 * @return string
	 */
    private function _getFieldJSCode($pFieldName, $pArBlocs, $pTheme){

        $toReturn = 'function jsFunc_' . $pFieldName . '() {';
        //Ajout d'un conteneur pour ajouter le bloc dynamique
        //ou suppression des anciens éléments
        $toReturn .= "
        if ($('dynbloc_$pFieldName') == null) {
            var dynbloc  = new Element('div', {id: 'dynbloc_$pFieldName'});
            dynbloc.inject($('row_$pFieldName'), 'after');
        } else {
            $('dynbloc_$pFieldName').innerHTML = '';
        }";

        $toReturn .= "switch ($('$pFieldName').value){";
        foreach ($pArBlocs as $fieldValue => $block) {
            $toReturn .= "case '$fieldValue':";
			
            $toReturn .= "var myRequest = new Request.HTML({
url:'". _url('form|default|getBlocDynamique', array('id'=>$block->cfev_id_bloc_to_display, 'theme'=>$pTheme)) ."',
method: 'get', evalScripts: true, update: 'dynbloc_$pFieldName'});
		myRequest.send();";
			
            $toReturn .= 'break;';
        }
        $toReturn .= '}';
        $toReturn .= '};';

        return $toReturn;
    }
    
	private function _getCheckBoxFieldJSCode($pFieldName, $pArBlocs, $pTheme){

        $toReturn = 'function jsCheckBoxFunc_' . $pFieldName . '(element) {';
        //Ajout d'un conteneur pour ajouter le bloc dynamique
        //ou suppression des anciens éléments


        $toReturn .= "switch (element.value){";
        foreach ($pArBlocs as $fieldValue => $block) {
        	$id = uniqid();
            $toReturn .= "case \"".addslashes($fieldValue)."\":";
			$toReturn .= "
	        if ($('dynbloc_".$pFieldName.$id."') == null) {
	            var dynbloc  = new Element('div', {id: 'dynbloc_".$pFieldName.$id."'});";
	        if ($block->cfev_parent_adopt){  
	            $toReturn .= "element.getParent().adopt(dynbloc);";
	        } else {
	        	$toReturn .= "dynbloc.inject($('row_$pFieldName'), 'after');";
	        }
	        
			$toReturn .= " }\n if (element.checked){
				var myRequest = new Request.HTML({
					url:'". _url('form|default|getBlocDynamique', array('id'=>$block->cfev_id_bloc_to_display, 'theme'=>$pTheme)) ."',
					method: 'get', evalScripts: true, update: 'dynbloc_".$pFieldName.$id."'
	        	});
				myRequest.send();
			} else { 
				$('dynbloc_".$pFieldName.$id."').innerHTML = ''; 
        	}
        	break;";
        }
        $toReturn .= '}';
        $toReturn .= '};';

        return $toReturn;
    }
}
