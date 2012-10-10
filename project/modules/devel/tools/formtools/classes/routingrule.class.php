<?php

class RoutingRule {

	/**
	 * Condition de routage 
	 * Exemple de condition : 
	 * BEGINWITH
	 * EQUALS
	 * ALL
	 * OTHERS
	 *
	 * @var string
	 */
	public $condition;
	
	/**
	 * Tableau des valeurs à vérifier
	 */
	public $values; 
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	public $way;
	
	
	/**
	 * Champ de recherche
	 *
	 * @var unknown_type
	 */
	public $field;
	
	
	
	/**
	 * 
	 *
	 * @param unknown_type $pCondition
	 * @param unknown_type $pWay
	 */
	public function __construct ($pWay, $pField = null, $pCondition = null, $pValues = array ()){
		$this->way = $pWay;
		$this->condition = $pCondition;
		$this->field = $pField;
		$this->values = $pValues;
	}
	
	
 
}