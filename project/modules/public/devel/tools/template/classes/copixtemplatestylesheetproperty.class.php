<?php
/**
* Représente une propriété feuille de style
*/
CopixClassesFactory::fileInclude ('template|copixtemplatesimpleproperty');
class CopixTemplateStyleSheetProperty extends CopixTemplateSimpleProperty {
	/**
	* Liste des classes définies dans la feuille de style
	* @var array
	* @access private
	*/
	var $_classes = null;

	/**
	* Liste des identifiants dans la feuille de style
	* @var array
	* @access private
	*/
	var $_id = null;

	/**
	* Constructeur
	* 
	* @param string $pName le nom
	* @param string $pCaption le libellé
	* @param mixed $pValue la valeur
	*/
	function CopixTemplateStyleSheetProperty ($pName, $pCaption, $pValue){
		parent::CopixTemplateSimpleProperty ($pName, $pCaption, $pValue);
	}

	/**
	* Recherche l'ensemble des identifiants déclarés dans la feuille de style
	* @return array tableau des identifiants déclarés dans la feuille de style
	* @access private
	*/
	function _parseId (){
		$results = array (''=>'');
		foreach ($this->_getCssContent () as $line){
			preg_match_all ('|^#[_a-zA-Z0-9-]*|', $line, $sub);
			if (count ($sub[0])){
				$results[] = substr ($sub[0][0], 1);
			}
		}
		return $results;
	}

	/**
	* Recherche l'ensemble des classes déclarées dans la feuille de style
	* @return array tableau des noms de classe déclarés dans le fichier
	* @access private
	*/
	function _parseClasses (){
		$results = array (''=>'');
		foreach ($this->_getCssContent () as $line){
			preg_match_all ('|^\.[_a-zA-Z0-9-]*|', $line, $sub);
			if (count ($sub[0])){
				$results[] = substr ($sub[0][0], 1);
			}
		}
		return $results;
	}

	/**
	* Récupère le contenu de la feuille de style au format texte
	* @return array le contenu de la feuille CSS sous la forme d'un tableau. 
	* @access private
	*/
	function _getCSSContent (){
		if (is_readable ($this->getValue ())){
			return file ($this->getValue ());
		}else{
			return array ();
		}
	}

	/**
	* Code HTML de la propriété
	* @return string
	*/
	function getHtml (){
		return parent::getHtml ();
	}

	/**
	* Récupère la liste des identifiants trouvés dans la feuille de style
	* @return array
	*/
	function getPossibleId (){
		if ($this->_id === null){
			$this->_id = $this->_parseId ();
		}
		return $this->_id;
	}

	/**
	* Récupère la liste des classes trouvées dans la feuille de style
	* @return array
	*/
	function getPossibleClasses (){
		if ($this->_classes === null){
			$this->_classes = $this->_parseClasses ();
		}
		return $this->_classes;
	}
}
?>