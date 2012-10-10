<?php
/**
* Représente une propriété éditable sous la forme d'une liste déroulante
*/
class CopixTemplateComboProperty extends CopixTemplateProperty {
	/**
	* Valeurs possibles
	* @var array
	* @access private
	*/
	var $_possibleValues = array ();
	
	/**
	* Constructeur
	* @param string $pName Le nom de la propriété
	* @param string $pCaption Le libellé de la propriété
	* @param string $pValue la valeur de la propriété
	* @param array $pPossibleValues la liste des valeurs possibles pour la propriété (tableau associatif valeur > libellé)
	*/
	function CopixTemplateComboProperty ($pName, $pCaption, $pValue, $pPossibleValues){
		parent::CopixTemplateProperty ($pName, $pCaption, $pValue);
		$this->setPossibleValues ($pPossibleValues);
	}

	/**
	* Retourne la liste des valeurs possibles
	* @return array
	*/
	function getPossibleValues (){
		return $this->_possibleValues;
	}
	
	/**
	* Retourne le code nécessaire à l'édition de la propriété
	* @return string le code HTML capable d'éditer la propriété combo
	*/
	function getHtml (){
		$code  = '<label for="'.$this->_name.'">'.$this->_caption.'</label>';
		$code .= '<select name="'.$this->getName().'">';
		foreach ($this->_possibleValues as $value=>$caption){
			if ($value == $this->_value){
				$selectedCode = ' selected="selected" ';
			}else{
				$selectedCode = '';
			}
			$code .= '<option value="'.$value.'" '.$selectedCode.'>'.$caption.'</option>';
		}
		$code .= '</select>';
		return $code;
	}
	
	/**
	* Définition de la liste des valeurs possible pour la combo
	* @param array $pPossibleValues la liste des valeurs possible pour la propriété
	*/
	function setPossibleValues ($pPossibleValues){
		$this->_possibleValues = $pPossibleValues;
	}
}
?>