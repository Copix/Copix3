<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Nicolas Bastien
 */

/**
 * Route_Dynamique Permet de définir la route à utiliser en fonction de la valeur d'un champs du formulaire
 * 
 * @package copix
 * @subpackage forms
 * @author Nicolas Bastien
 */
class Route_Dynamique extends CopixAbstractFormRoute {

    private $_form;

    public function __construct($pForm) {
		parent::__construct($pForm);
        //on mémorise le formulaire car l'on en aura besoin pour initialiser la route à la validation
        $this->_form = $pForm;
    }

    public static function getFormParams($arCfRouteParams = array()){
        
        $form = new CopixFormLight('form_route_params');
		$service = new Form_Service();

		$form->setTitle('Paramètrage du routage dynamique');

		$form->attachField ('route_class', _field ('hidden'), array ())
			 ->attachField ('editId', _field ('hidden'), array ())
             ->attachField ('route_field_id', _field ('select', array('values'=>$service->getFormFieldByType('select'),'extra'=>'style="width:100%" onchange="updateRouteField();"')),
							array ('label'=>'Champs de base :'));

        //Ajout de la liste des valeurs du champs
        $route_field_id = (isset($arCfRouteParams['route_field_id'])) ? $arCfRouteParams['route_field_id'] : _request('route_field_id');

        if ($route_field_id != null ) {
            $arValues = DAOcms_form_element_values::instance ()->findByElement($route_field_id);
            $formConfig = new Form_config();
            $arRoutes = $formConfig->getRoutes();

            foreach($arValues as $value) {
                $form->attachField ('route_field_value_'.$value->cfev_id, _field ('select', array('values'=>$arRoutes,'extra'=>'style="width:100%;" onchange="updateRouteField();"')),
							array ('label'=>'>&nbsp;' . $value->cfev_value));
                        
                if (isset($arCfRouteParams['route_field_value_'.$value->cfev_id])) {
                    $route_field_value = $arCfRouteParams['route_field_value_'.$value->cfev_id];
                    $routeParams = $arCfRouteParams;
                } else {
                    $route_field_value = _request('route_field_value_'.$value->cfev_id);
                    $routeParams = CopixRequest::asArray();
                }
                if ($route_field_value != null) {
                    //Récupération et formatage du formulaire du route choisi
                    $routeClass = 'Route_' . ucfirst($route_field_value);
                    if (class_exists($routeClass)) {
                        $tmpForm =  call_user_func(array($routeClass, 'getFormParams'), $routeParams);
                        foreach ($tmpForm->getFields() as $field) {
                            //Ajout de la valeur dans l'identifiant pour discerner les champs
                            $field->setName($field->getName() . '_' . $value->cfev_id);
                            $form->addField($field);
                        }
                    }
                }
            }
        }


        $url = _url('form|adminajax|getRouteParams', array('cf_route'=>'dynamique', 'editId'=>_request('editId')));

        $jsCode = <<<JS_CODE
function updateRouteField () {
    
    var myRequest = new Request.HTML({
url:'$url' + '&' + $('form_route_params').toQueryString(),
method: 'get', evalScripts: true, update: 'route_params_div'});
		myRequest.send();
}
JS_CODE;

        $form->addJSCode($jsCode);


        $helpImageSrc = _resource('form|img/help.png');
        $legend = <<<STR_LEGEND
<p class="copix_help">
<img src="$helpImageSrc" class="p_icon"/>
&nbsp;&nbsp;Le routage dynamique vous permet de sélectionner un champ à choix multiple de votre formulaire et d'affecter une route spécifique à chacune des valeurs de ce champs.
<br/><br/>
Une fois ce champs choisi, vous devez configurez une route pour chaque valeur, par défaut les informations seront simplement sauvegardées en base.
</p>
STR_LEGEND;

		$form->setLegend($legend);

        $form->populate(array_merge(array('route_class'=>'Route_Dynamique', 'editId'=>_request('editId')), CopixRequest::asArray()));

        $form->populate($arCfRouteParams);

		return $form;
    }


    public static function formatParams() {
		$arParams = CopixRequest::asArray();
        //On supprime les paramètres inutiles
        unset($arParams['module']);
        unset($arParams['group']);
        unset($arParams['action']);
        unset($arParams['Copix']);
        unset($arParams['route_class']);
        unset($arParams['editId']);

		array_map('trim', $arParams);

		return serialize($arParams);
	}

    public function checkParams(){}

    /**
	 * (non-PHPdoc)
	 * @see forms/CopixAbstractFormRoute#_process()
	 */
	protected function _process($arData) {

        //Récupération du champs servant au routage
        $field = DAOcms_form_element::instance ()->get($this->_params['route_field_id']);
        $arValues = DAOcms_form_element_values::instance ()->findByElement($this->_params['route_field_id']);

        $valueId = null;
        foreach ($arValues as $value) {
            if ($value->cfev_value == $arData[$field->cfe_label]) {
                $valueId = $value->cfev_id;
                break;
            }
        }

        if ($valueId != null && isset($this->_params['route_field_value_' . $valueId])) {
            //Ici on doit reconstituer les paramètres de routage
            $routeClassName = 'route_' . $this->_params['route_field_value_' . $valueId];
            if (class_exists($routeClassName)) {
                $routeParams = array();
                $suffixeLength = strlen($valueId) + 1;
                foreach ($this->_params as $paramId => $paramValue) {
                    if (substr($paramId,$suffixeLength *-1) == '_' . $valueId) {
                        $routeParams[str_replace('_' . $valueId, '', $paramId)] = $paramValue;
                    }
                }
                $this->_form->cf_route_params = serialize($routeParams);

                $formRoute = new $routeClassName ($this->_form);
                //Envoie au routage
                $formRoute->process($this->_form->getValues());
            }
        }
	}

}