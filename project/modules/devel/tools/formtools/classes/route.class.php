<?php

_classInclude ('formtools|routingrule');
class Route {
	
	/**
	 * Liste des routes enregistrés pour la route associé
	 *
	 * @var unknown_type
	 */
	private $_rules;
	
	private $_url_success;
	
	private $_url_fail;
	
	private $_url_form;

	/**
	 * Route par défaut, celle qui sera appliquée si aucune condition ne va
	 *
	 * @var RoutingRule
	 */
	private $_default_rule;
	
	/**
	 * On peut construire une route à partir d'un fichier XML
	 * <?xml version="1.0" encoding="UTF-8"?>
	 * <route default="">
	 * 	<rule way="" field="" condition="" values=""/>
	 * 	<rule way="" field="" condition="" values=""/>
	 * </route> 
	 * 
	 * @param $pXML
	 */
	public function __construct ($pXML = null){
		if ($pXML != null){
			$oRoute = simplexml_load_string ($pXML);
			foreach ($oRoute->rule as $rule) {
				$this->addRule(new RoutingRule ((string)$rule['way'], (string)$rule['field'], (string)$rule['condition'], explode (";", (string)$rule['values'])));
			}
		}
	}
	
	
	/**
	 * Ajout d'une règle de routage
	 *
	 * @param RoutingRule $ppRoutingrule
	 */
	public function addRule (RoutingRule $pRoutingrule) {
		 $this->_rules [] = $pRoutingrule;
	}
	
	/**
	 * Récupération des règles
	 *
	 * @return array
	 */
	public function getRules (){
		return $this->_rules;
	}
	
	/**
	 * 
	 * @param $pRuleCondition
	 * @return unknown_type
	 */
	public function getRule ($pRuleCondition){
		foreach ($this->_rules as $rule) {
			if ($rule->condition == $pRuleCondition){
				return $rule; 
			}
		}
		return $rule;
	}
	
	/**
	 * Ajoute la route par défaut 
	 * @return void
	 */
	public function addDefaultRule (RoutingRule $rule){
		$this->_default_rule = $rule;
	}
	
	/**
	 * Récupère la route par défaut
	 * 
	 * @return Routing Rule
	 */
	public function getDefaultRule (){
		return $this->_default_rule;
	}
	
	/**
	 * Mise en place d'une URL de redirection en cas de succès 
	 *
	 * @param string $pUrl
	 */
	public function setUrlSuccess ($pUrl) {
		 $this->_url_success = $pUrl;
	}
	
	public function getUrlSuccess () {
		return $this->_url_success;
		 
	}
	
	public function setUrlFail ($pUrl) {
		 $this->_url_fail = $pUrl;
	}
	
	public function getUrlFail () {
		return $this->_url_fail; 
	}
	
	public function setUrlForm ($pUrl) {
		 $this->_url_form = $pUrl;
	}
	
	public function getUrlForm () {
		return $this->_url_form; 
	}
	
	public function asXML (){
		$res ='<?xml version="1.0" encoding="UTF-8"?>'."\n";
		if (isset ($this->_default_rule)) {
			$res .= '<route default="'.$this->_default_rule->way.'">'."\n"; 
		} else {
			$res = '<route>'."\n";
		}
		foreach ($this->_rules as $rule) {			
			$res .= '<rule way="'.$rule->way.'" condition="'.$rule->condition.'" ';
			if ($rule->field != null)  {
				$res .= 'field="'.$rule->field .'" values="'.implode (';',(array)$rule->values).'"'; 
			}
			$res .= '/>'."\n";
		}
		$res .= '</route>';
		return $res;
	}

	
	/**
	 * On applique la route
	 * 
	 * @param 	$pValues 	tableau des résultats
	 * @return 	string		règle appliquée
	 */
	public function apply ($pValues) {
		// Par soucis on effectue toujours un enregistrement des données.
		
		$oInfo = _ppo ($pValues); 
		// Condition qui va s'appliquer 
		$applyingCondition = '';
		if (count ($this->_rules) > 0)
		foreach ($this->_rules() as $rule){
			switch ($rule->condition) {
				case 'BEGINWITH':
					foreach ($rule->values as $value){
						if (preg_match ('/^'.$value.'/', $this->_getElem ($rule->field, $pValues))) {
							$applyingCondition = $rule->way;
							break;
						}
					}
					break;
				case 'EQUALS':
					if ($rule->values == $this->_getElem ($rule->field, $pValues)) {
						$applyingCondition = $rule->way;
					}
					break;
			}
			// Si la condition qui s'applique est différente de chaine vide alors on sort
			if ($applyingCondition != '') {
				break;
			}
		}		
		if ($applyingCondition == ''){
			if ($this->_default_rule != null) {
				$applyingCondition = $this->_default_rule->way;
			} else {
				throw new RouteException ('Pas de règle par défaut définie');
			}
			
		}

		$this->_applyCondition ($applyingCondition, $pValues);
		return $applyingCondition;
	}
	/**
	 * 
	 */
	private function _applyCondition ($pCondition, $pValues){
		list ($way, $param) =  split (':', $pCondition);
		switch ($way) {
			case 'mailto':
				$pMessage = '';
				foreach ($pValues as $libelle => $value) {
					$pMessage .= $libelle.': '.$value;
				}
				$mail = new CopixHTMLEmail ($param, null, null, 'Infos du formulaire', $pMessage);
				$mail->send();
			case 'db':
				// Param contient le nom de la table
				$record = _record ($param);
				$ppo = _ppo ($pValues);
				$record->objet_result = CopixXMLSerializer::serialize ($ppo);
				_ioDao ($param)->insert ($record);
			default: 
				break;
		}
	}
	
	/**
	 * On récupérer l'élément en fonction de l'id
	 *  
	 */
	private function _getElem ($pIdElem, $pValues){
		$copyValue = $pValues;
		foreach (explode ('-', $pIdElem) as $name){
			if (isset ($copyValue[$name])){
				$copyValue = $copyValue[$name];
			}else{
				return null;
			}
		}
		return $copyValue;
	}
	
}