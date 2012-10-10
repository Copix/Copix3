<?php
/**
* @package   copix
* @subpackage db
* @author   Croës Gérald
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Exceptions pour les bases de données
 * @package copix
 * @subpackage db
 */
class CopixDBConnectionException extends CopixDBException {}	

/**
 * Classe de base pour représenter des connections
 * @package   copix
 * @subpackage db
 */
abstract class CopixDBConnection {
	/**
	* Le profil de connexion utilisé pour la connexion
	* @var CopixDBProfil
	*/
	protected $_profil;

	/**
	* La chaine qui marque le début d'un commentaire dans un script SQL
	* @var string
	*/
    protected $_scriptComment = '/^\s*#/';
  
	/**
    * La chaine qui marque la fin d'un commentaire dans un script SQL
    * @var string
    */
    protected $_scriptEndOfQuery = '/;\s*$/';
    
    /**
     * Constructeur
     * @param	CopixDBProfile	$pProfil	le profil de connexion à utiliser pour se connecter à la base de donées.
     */
    public function __construct ($pProfil){
    	$this->_profil = $pProfil;
    }
    
    /**
     * Retourne le profil de connexion utilisé pour la connexion courante
     * @return CopixDBProfile
     */
    public function getProfile (){
    	return $this->_profil;
    }
    
    /**
     * Analyse de la requête à redéfinir dans les parents
     * @return array [isSelect] => true / false si c'est une sélection
     *         array [isStatement] => true / false si c'est un statement
     *         array [query] => la requête à exécuter
     */
    protected function _parseQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null){
      //On regarde si c'est une manipulation (insert/update/delete) ou une sélection
      $toReturn ['isSelect'] = ((($pos = stripos (trim ($pQueryString), 'select')) === 0) || $pos === 1);
      $toReturn ['query']    = $pQueryString;//non modifiée
      
      if (($pOffset === null || $pOffset === 0) || ($toReturn ['isSelect'] == false)){
         //Aucun offset de donné, on considère que la fonctionnalité est de toute façon prise en charge
         $toReturn ['offset'] = true;      	
      }else{
         $toReturn['offset'] = false;
      }
      if ($pCount === null  || ($toReturn ['isSelect'] == false)){
         //Aucune restriction de nombre de ligne donnée
      	 $toReturn['count'] = true;
      }else{
         $toReturn['count'] = false;      	
      }
      return $toReturn;
    } 
    
    /**
    * Lancement d'une requête SQL
    * @param	string	$pQueryString	la requête à lancer
    * @param	array	$pParameters	tableau de paramètres
    * @param	int		$pOffset		l'offset à partir duquel nous allons lire les résultats => Si null, pas d'offset 
    * @param	int		$pCount			le nombre d'élément que l'on souhaites récupérer depuis la base. Si null => le maximum
    */
    abstract function doQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null);
    
    /**
     * Lancement d'une requête SQL dont les résultats sont retournés sous la forme d'iterateurs.
     *
     * @param	string	$pQueryString	la requête à lancer
     * @param	array	$pParameters	tableau de paramètres
     * @param	int		$pOffset		l'offset à partir duquel nous allons lire les résultats => Si null, pas d'offset 
     * @param	int		$pCount			le nombre d'élément que l'on souhaites récupérer depuis la base. Si null => le maximum
     */
    abstract function iDoQuery ($pQueryString, $pParameters = array (), $pOffset = null, $pCount = null);

    /**
    * Lance un script SQL
    * @param	string	$pFilePath	le chemin du fichier SQL à exécuter
    * @param	boolean	$pRollbackOnFailure	indique si l'on doit réaliser un rollback en cas d'échec d'une requête
    * @return 	integer le nombre de requêtes exécutées avec succès
    */
    public function doSQLScript ($pFilePath, $pRollbackOnFailure = true) {
        $lines = file($pFilePath);
        $cmdSQL = '';
        $nbCmd = 0;
      	if ($pRollbackOnFailure){
			CopixDB::begin ();
      	}
        foreach ((array)$lines as $key=>$line) {
            if ((!preg_match($this->_scriptComment,$line))&&(strlen(trim($line))>0)) { 
               // la ligne n'est ni vide ni commentaire
               if (strlen (trim ($line)) > 0){
	               $cmdSQL.=$line;
	               if (preg_match($this->_scriptEndOfQuery, $line)) {
	                    //Si on est à la ligne de fin de la commande on l'execute
	                    // On nettoie la commande du ";" de fin et on l'execute
	                    $cmdSQL = preg_replace ($this->_scriptEndOfQuery,'',$cmdSQL);
	                    try {
	                       $this->doQuery ($cmdSQL);
	                    }catch (Exception $e){
	                    	if ($pRollbackOnFailure){
	                    		CopixDB::rollback ();
	                    	}
	                    	throw $e;
	                    }
	                    $nbCmd++;
	                    $cmdSQL = '';
	               }
               } 
            }
        }
		if ($pRollbackOnFailure){
        	CopixDB::commit ();
		}
        return $nbCmd;
    }

    /**
    * Renvoi le type de jointure à utiliser dans les requêtes sql.
    * @return	string	le type de jointure (ORACLE ou MYSQL).
    */
    public function joinType () {
        if ($this->_profil->driver == 'oci8') {
            return 'ORACLE';
        }else{
            return 'MYSQL';
        }
    }
    
    /**
    * Dernier identifiant automatique affecté
    * @param	string	$pFromSequence	le nom de la séquence depuis laquelle on veut récupérer le dernier identifiant.
    * 	si null est donné, on suppose que le dernier identifiant provient d'un autoincrément
    * @return	integer
    */
    abstract public function lastId ($pFromSequence = null);
    
    /**
     * Quote un élément pour la base de données.
     * @param	string 	$pString l'élément que l'on souhaites mettre entre quotes
     * @param	boolean	$pCheckNull si l'on souhaite vérifier la valeur nulle ou non (null sera mis au lieu de "null" si true)
     * @return	string	la chaine représentant l'élément mis entre quote pour la base de données 
     */
    public function quote ($pString, $pCheckNull = true){
        if ($pCheckNull && is_null ($pString)){
           return 'NULL';
        }
        return "'".addslashes ($pString)."'";
    }

    /**
     * Fonction qui indique une valeur max de count pour tout récupérer lorsque null est donné avec un offset
     * @return int
     */
    protected function _getMaxCount (){
    	return 500000;
    }
    
    /**
     * Vérifie qu'il existe bien tous les éléments requis pour créer une connexion à la base de données
     * demandée.
     * @param	array	$pArrayToCheck	tableau qui contient la liste des clefs requises dans la base de données
     * @param	array	$pParts			le tableau qui corresponds au driver parsé 
     */
    protected function _assertParts ($pArrayToCheck, $pParts){
    	$toRaise = array ();
    	foreach ($pArrayToCheck as $element){
    		if (!isset ($pParts[$element])){
    			$toRaise[] = $element;
    		}
    	}
    	if (count ($toRaise)){
    		throw new CopixDBException ('[CopixDBConnection] Les éléments '.implode ('-', $toRaise).' sont manquants pour la chaine de connexion '.$this->_profil->getDriverName ());
    	}
    }

    /**
     * Valide une transaction en cours sur la connection
     */
    abstract public function commit ();

    /**
     * Annule une transcation sur la connection 
     */
    abstract public function rollback ();
    
    /**
     * Demarre une transaction sur la connection donnée
     */
    abstract public function begin ();

    /**
     * Récuération de la liste des tables connues depuis la connexion
     * @return array
     */
    abstract public function getTableList ();

    /**
     * Retourne la liste des champs connus dans la table
     * @param	string	$pTableName le nom de la table dont on souhaite connaitre la liste des champs 
     */
    abstract public function getFieldList ($pTableName);
    
    /**
     * Indique si le driver est disponible
     * @return bool
     */
    public static function isAvailable (){return false;}
}
?>