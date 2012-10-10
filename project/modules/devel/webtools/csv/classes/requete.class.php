<?php

class requete {
	
	//Requete a executer
	private $_requete = null;
	//Nombre de pages
	private $nbPages;
	//Nombre d'enregistrement
	private $nbRecord;
	//Connexion utilisée
	private $_connection;
	
	/**
	 * Constructeur pour créer une CopixList
	 *
	 * @param tableau $pParams tableau de paramètres
	 */
	public function __construct ($pParams) {
		$this->_requete = $pParams['requete'];
		$this->_max = $pParams['max'];
		$this->_connection = $pParams['ct'];
	}
	
	/**
	 * Fonction permettant d'ajouter des conditions sur les champs
	 *
	 */
	public function addCondition ($pField, $pCond, $pValue, $pType) {
		
	}
	
	/**
	 * Fonction permettant d'executer la requete et d'initialiser les différentes valeurs
	 *
	 */
	public function find ($page=0, $pOrder=null, $pSens='ASC') {
		
		//Execution de la requête en fonction de la connexion choisie par l'utilisateur
		$results = CopixDb::getConnection ($this->_connection)->doQuery($this->_requete);
		//$results = _doQuery($this->_requete);
		$this->_nbRecord = count($results);
		$results = array_chunk($results,$this->_max);
		$this->_nbPages = count($results);
		return isset($results[$page]) ? $results[$page] : null; 
		
	}
	
	/**
	 * Fonction retournant le nombre de page
	 */
 	public function getNbPage () {
    	return $this->_nbPages;
    }
    
    /**
     * Fonction permettant de retourner le nombre d'enregistrement
     */
    public function getNbRecord () {
         return $this->_nbRecord;
    }
}

?>