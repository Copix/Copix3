<?php

abstract class abstractInputElement {

		
	/**
	 * 
	 * @param string $idElement identifiant de l'élément, sera réutilisé pour les 
	 */
	public $idElement;
	
	/**
	 * @param string $fieldName Nom du champ utilisé pour l'affichage
	 */
	public $fieldName;
	
	/**
	 * Valeurs par défaut de l'élement input
	 *
	 * @var ppo contenant les valeurs par défaut
	 */
	public $defaultValues;
	
	/**
	 * Type du Input Element
	 * 
	 * @param string Type du champ 
	 */
	public $kind;

	/**
	 * Liste des champs fournis par l'inputElement
	 *
	 * @var array
	 */
	protected $_fields = array (); 
	
	/**
	 * Fonction 
	 *
	 * @param unknown_type $pIdElement
	 * @param unknown_type $pFieldName
	 * @param unknown_type $pDefaultValues
	 */
	public function __construct ($pIdElement, $pFieldName){
		$this->idElement = $pIdElement;
		$this->fieldName = $pFieldName;
	}
	
	/**
	 * 
	 * @param $pIdInput
	 * @param $pDataSource
	 * @return unknown_type
	 */
	abstract public function getForm ($pDefaultValues = null);

	/**
	 * 
	 * @param $pValue
	 * @return unknown_type
	 */
	abstract public function getDisplay ($pValue);

	/**
	 * 
	 * @return unknown_type
	 */
	abstract public function getValidator ();

	/**
	 * Récupération des champs fournis par l'entrée de formulaire
	 *
	 */
	public function getFields (){
		if (count ($this->_fields) == 0){
			return (array)$this->idElement;
		} else {
			$rArray = array ();
			foreach ($this->_fields as $field) {
				$rArray[] = $this->idElement.'-'.$field; 
			}
			return $rArray;
		}
		
	}
}