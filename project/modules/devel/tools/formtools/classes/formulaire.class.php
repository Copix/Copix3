<?php

_classInclude ('formtools|formulaireexception');

class Formulaire {
	
	/**
	 * Action du formulaire
	 *
	 * @var string
	 */
	public $action;
	
	/**
	 * Méthode du formulaire
	 * 
	 * @var string
	 */
	public $method;
	
	/**
	 * Options du formulaire : schéma d'encodage
	 * 
	 * @var string
	 */
	public $options;
	
	/**
	 * Liste des éléments du formulaire
	 *
	 * @var array
	 */
	private $_inputElement = array();
	
	/**
	 * Objet de routage
	 * 
	 * @var object
	 */
	public $route;
	
	/**
	 *  
	 * Passage des actions et methode via des setter
	 * @param $pAction 
	 * @param $pMethod
	 * @param $pOptions
	 * @return unknown_type
	 */
	public function __construct (){
		$this->action = _url (CopixConfig::get ('formtools|defaultvalidurl'));
		// Méthode par défaut 
		$this->method = 'POST';
	}
	
	public function setAction ($pAction){
		$this->action = $pAction;
	}
	
	public function setMethod ($pMethod){
		$this->method = $pMethod;
	}
	
	public function setOption ($pOption){
		$this->options = $pOption;
	}
	
	/**
	 * 
	 */
	public function addInputElement (abstractInputElement $pInputElement){
		if (isset ($this->_inputElement [$pInputElement->idElement])) {
			throw new FormulaireException (_i18n ('formtools.error.addedfield', array ($pInputElement->idElement)));
		}
		$this->_inputElement [$pInputElement->idElement] = $pInputElement;
	}
	
	/**
	 * 
	 */
	public function showForm ($pValues = null, $pValidateForm = false ){
		$res = '<form action="'.$this->action.'" method="'.$this->method.'" '.$this->options.'>';
		foreach ($this->_inputElement as $idElement => $elem) {
			$class = null;
			if ($pValidateForm !== false) {
				$errors = _ctValidator ()->attachTo ($elem->getValidator(), $idElement)->check ($pValues);
				if ($errors !== true) {
					$res .= CopixZone::process ('generictools|notification', $errors);
					$class = 'error';
				} 
			}
			
			$res .= $elem->getForm ($pValues, $class);
			$res .= '<br/>';
		}
		// Pour le moment on ajoute le champ simplement.
		$res .= '<input type="submit" value="send"/>';
		$res .= '</form>';
		return $res;
	}
	
	/**
	 * 
	 */
	public function getFieldList (){
		$arResult = array ();
		foreach ($this->_inputElement as $elem) {
			$arResult = array_merge ($arResult, $elem->getFields ());
		}
		return $arResult;
		
	}
	
	/**
	 * Récupération 
	 * 
	 * @return array 
	 */
	public function getInputElement (){
		return $this->_inputElement;
	}
	
	/**
	 * 
	 */
	public function showResult ($pArrayResult){
		$res = '';
		foreach ($this->_inputElement as $idElement => $elem) {
			$res .= $elem->getDisplay ($pArrayResult [$idElement]).'<br/>';
		}
		return $res;
	}  
	

	


	/**
	 * 
	 */
	private function _applyCondition ($pCondition, $pArrayValues){
		list ($way, $param) =  split (':', $pCondition);
		switch ($way) {
			case 'mailto':
				$pMessage = '';
				foreach ($pArrayValues as $libelle => $value) {
					$pMessage .= $libelle.': '.$value;
				}
				$mail = new CopixHTMLEmail ($param, null, null, 'Infos du formulaire', $pMessage);
				$mail->send();
			default: 
				break;
		}
	}
	/**
	 * Fonction de validation des formulaires
	 * 
	 * @param $pValue Valeurs à vérifier (objet ou tableau) 
	 */
	public function validateForm ($pValue){
		$validator = _ctValidator ();
		foreach ($this->_inputElement as $idElement => $elem) {
			if ($elem->getValidator () != null){
				$validator->attachTo ($elem->getValidator (), $idElement);
			}
		}

		return $validator->check ($pValue);
	} 
	

	/**
	 * Mise en session des valeurs du formulaire
	 * 
	 */
	public function setSessionValues (){
		$arValues = array ();
		foreach ($this->_inputElement as $pIdElement => $elem) {
			$arValues[$pIdElement] = _request ($pIdElement);
		}
		CopixSession::set ('values_form', $arValues, 'formtools');
		return $arValues;
	}
	
	

	/**
	 * Récupération des valeurs en session
	 */
	public function getSessionValues (){
		return CopixSession::get ('values_form', 'formtools');
	}
	
	public function sessionSetErrors (){
		
	}


}