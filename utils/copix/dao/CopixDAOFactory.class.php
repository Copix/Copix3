<?php
/**
* @package		copix
* @subpackage	dao
* @author		Croës Gérald , Jouanneau Laurent
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Définition de l'interface CopixDAO
 * @package copix
 * @subpackage dao
 */
interface ICopixDAO {
    //on ne met pas get ni delete car les paramètres varient en fonction du nombre de clef
    public function findBy ($pSp, $leftjoin=array());
    public function countBy ($pSp);
    public function deleteby ($pSp);
    public function update ($pRecord);
    public function insert ($pRecord);
    public function findAll ();
}

/**
 * Définition de l'interface CopixDAORecord
 * @package copix
 * @subpackage dao 
 */
interface ICopixDAORecord {}

/**
 * Classe qui permet de parcourir un ensemble de résultat "standards" sous la forme d'un tableau de DAORecord
 */
class CopixDAORecordIterator implements Iterator, ArrayAccess, Countable {
	/**
	 * Quel est le type de record que l'on décide de parcourir.
	 * @var string
	 */
	private $_recordId = null;
	
	/**
	 * Résultats de la requêtes
	 *
	 * @var CopixDBResultSet
	 */
	private $_resultSet;
	
	/**
	 * L'offset courant
	 *
	 * @var int
	 */
	private $_currentOffset = 0;
	
	/**
	 * Construction en indiquant le type de DAO en paramètre
	 */
	function __construct ($pArray, $pRecordId){
		$this->_resultSet = $pArray;
		$this->_recordId = $pRecordId;
	}

	/**
	 * Retourne l'élément d'indice donné.
	 */
	function offsetGet ($pOffset){
		return _record ($this->_recordId)->initFromDBObject ($this->_resultSet[$pOffset]);
	}

	/**
	 * Retourne l'élément courant
	 * @return StdClass 
	 */
	function current (){
		return $this->_resultSet[$this->_currentOffset];
	}

	/**
	 * Mise à jour du compteur de position
	 * @return void
	 */
	public function next () {
		$this->_currentOffset++;
	}

	/**
	 * Retourne la clef courante
	 * @return int
	 */
	public function key (){
		return $this->_currentOffset;
    }
    
    /**
     * Indique si l'élément courant est valide.
     * @return boolean 
     */
    public function valid (){
    	return isset ($this->_resultSet[$this->_currentOffset]);
    }
    
    /**
     * Réinitialisation du parcours des éléments au premier indice 
     * @return void
     */
    public function rewind (){
    	$this->_currentOffset = 0;
    }
    
     /**
	 * Impossibilité de définir des valeurs dans un resultset
	 */
	 function offsetSet ($key, $value) {
	 	throw new Exception ('Cannot set directly in a result set');
	 }
	
	 /**
  	  * Defined by ArrayAccess interface
	  * Unset a value by it's key e.g. unset($A['title']);
	  * @param mixed key (string or integer)
	  * @return void
 	  */
	 function offsetUnset ($key) {
	 	throw new Exception ('Impossible de supprimer un élément de l ensemble de résultat');
	 }
	
	 /**
	 * Defined by ArrayAccess interface
	 * Check value exists, given it's key e.g. isset($A['title'])
	 * @param mixed key (string or integer)
	 * @return boolean
	 */
	 function offsetExists ($pOffset) {
	 	return isset ($this->_resultSet[$pOffset]);
	 }
	 
	 /**
	  * Indique le nombre d'éléments qu'il existe dans le résulat
	  */
	 public function count (){
	 	return count ($this->_resultSet);
	 }
	 
	 /**
	  * Récupère l'ensemble des enregistrements dans un tableau
	  * @return array
	  */
	 public function fetchAll (){
	 	$results = array ();
	 	foreach ($this->_resultSet as $key=>$element){
	 		$results[$key] = _record ($this->_recordId)->initFromDBObject ($element); 
	 	}
	 	return $results;
	 }
}

/**
 * Classe de base lorsqu'une mise à jour est demandée alors que le record a été modifié entre temps.  
 */
class CopixDAOVersionException extends Exception{
	/**
	 * L'élément qui n'est pas à jour.
	 */
	protected $_record = null;
	
	/**
	 * Retourne l'enregistrement dont la mise à jour à échouée.
	 * @return CopixDAORecord 
	 */
	public function getRecord (){
		return $this->_record;
	}
	
	/**
	 * Constructeur
	 */
	public function __construct ($pRecord){
		$this->_record = $pRecord;
	}
}

/**
 * Classe de base pour les erreurs de vérification des données sur les DAO
 * @package copix
 * @subpackage dao
 */
class CopixDAOCheckException extends Exception {
	/** 
	 * tableau des erreurs de validation
	 * @var array
	 */
	protected $_errors = array ();
	
	/**
	 * L'élément de données sur lequel l'erreur est survenue
	 */
	protected $_record = null;
	
	/**
	 * Constructeur
	 */
	public function __construct ($arrayOfErrors = array (), $record = null){
		$this->_errors = $arrayOfErrors;
		$this->_record = $record;
		parent::__construct ($this->getErrorMessage ());
	}
	
	/**
	 * Retourne les messages d'erreurs sous la forme d'une chaine de caractère
	 */
	public function getErrorMessage (){
		return implode ("\n\r *", $this->_errors);
	}
	
	/**
	 * Récupération du tableau d'erreur utilisé lors de l'exception
	 * @return array
	 */
	public function getErrors (){
		return $this->_errors;
	}
	
	/**
	 * Récupération du record
	 * @return DAORecordAdapter
	 */
	public function getRecord (){
		return $this->_record;
	}
}

/**
* Factory de DAO
* @package copix
* @subpackage dao
*/
class CopixDAOFactory {
	/**
	 * Instances uniques des DAO
	 * @var array
	 */
	private static $_daoSingleton = array ();
	
	/**
	 * Tableau qui nous sert à nous souvenir des vérifications déja effectuées sur 
	 * la compilation de certaines classes
	 */
	private static $_compilationChecked = array ();

    /**
    * Création du DAO à partir de son identifiant.
    * 
    * @param string $DAOid l'identifiant Copix du DAO
    * @param string $pConnectionName le nom de la connexion à utiliser pour la DAO
    * @return DAO
    */
    public static function create ($DAOid, $pConnectionName = null){
        $DAOid    = self::_fullQualifier ($DAOid);
        self::_fileInclude ($DAOid, $pConnectionName);
        $className = self::getDAOName ($DAOid);
        return new $className($pConnectionName);
    }

    /**
    * Création ou récupération d'une instance unique de DAO
    * 
    * @param string $DAOid l'identifiant Copix du DAO
    * @param string $pConnectionName le nom de la connection que l'on souhaites utiliser pour la DAO
    * @return DAO
    */
    public static function getInstanceOf ($DAOid, $pConnectionName = null) {
        $DAOid    = self::_fullQualifier ($DAOid);
        if (! isset (self::$_daoSingleton[$DAOid][$pConnectionName])){
            self::$_daoSingleton[$DAOid][$pConnectionName === null ? '' : $pConnectionName] = self::create ($DAOid, $pConnectionName);
        }
        return self::$_daoSingleton[$DAOid][$pConnectionName === null ? '' : $pConnectionName];
    }

    /**
    * Création d'un objet enregistrement
    * 
    * @param string $DAOId l'identifiant du DAO à créer
    */
    public static function createRecord ($DAOid, $pConnectionName = null){
        $DAOid    = self::_fullQualifier ($DAOid);
        self::_fileInclude ($DAOid, $pConnectionName);
        $className = self::getDAORecordName ($DAOid);
        return new $className ();
    }

    /**
    * Demande l'inclusion du fichier de définition du DAO 
    * @param string $pDAOid identifiant Copix du DAO
    */
    public static function fileInclude ($pDAOid, $pConnectionName=null){
        self::_fileInclude (self::_fullQualifier ($pDAOid), $pConnectionName);
    }
    
    /**
    * Inclusion de la définition du DAO
    * @param string $pFullQualifiedDAOId l'identifiant Copix complet (avec le module) du DAO
    */
    private static function _fileInclude ($pFullQualifiedDAOId, $pConnectionName){
        if (self::_needsCompilation ($pFullQualifiedDAOId, $pConnectionName)){
        	self::_generateDAO ($pFullQualifiedDAOId, $pConnectionName);
        }
        Copix::RequireOnce (self::_getCompiledPath ($pFullQualifiedDAOId));
    }

    /**
    * Création d'un objet de type CopixDAOSearchParams pour effectuer des requêtes type 
    *  findby avec les dao. 
    * @param string $kind le type par défaut des conditions de l'objet (AND ou OR)
    *   par défaut AND
    * @return object
    */
    public static function createSearchParams ($kind = 'AND'){
        return new CopixDAOSearchParams ($kind);
    }

    /**
    * Récupération du chemin ou le DAO sera compilé en PHP
    * @param string $DAOid l'identifiant complètement qualifié du DAO
    * @todo ne pas avoir cette méthode public ici.
    */
    public static function _getCompiledPath ($pFullQualifiedDAOid){
    	return COPIX_CACHE_PATH.'php/dao/'.str_replace (array ('|', ':'), array ('_', '_S_'), $pFullQualifiedDAOid).'.dao.php';
    }

    /**
    * Récupération du qualificateur complet du DAO
    * @param string $DAOId identifiant du DAO 
    */
    private static function _fullQualifier ($pDAOid){
        $selector = CopixSelectorFactory::create ($pDAOid);
        $fileName = $selector->getPath (COPIX_RESOURCES_DIR).strtolower ($selector->fileName.'.dao.xml');
        $fileClassName = $selector->getPath (COPIX_CLASSES_DIR).strtolower ($selector->fileName.'.dao.php');
        if (is_readable ($fileName) || (count (explode ('|', $pDAOid)) > 1) || (count (explode (':', $pDAOid)) > 1)){
        	return $selector->getSelector (); 
        }else{
        	return $pDAOid;//peut être une DAO automatique ?
        }
    }

    /**
    * Indique si le DAO à besoin d'être regénéré
    * @param string $DAOid l'identifiant (complet) du DAO à tester 
    */
    private static function _needsCompilation ($pFullQualifiedDAOid, $pConnectionName){
        if (isset (self::$_compilationChecked[$pFullQualifiedDAOid])){
        	return false;
        }

    	$config = CopixConfig::instance ();
    	if ($config->force_compile){
            return true;
        }

        //regarde s'il existe la classe compilée.
        $compiledPath = self::_getCompiledPath ($pFullQualifiedDAOid);
        if ( file_exists($compiledPath)===false){
            //compiled file does not exists.....
            return true;
        }

        //On effectue la vérification uniquement si demandé dans le fichier de configuration
        if ($config->compile_check){
            $compiledTime = filemtime ($compiledPath);
            foreach (self::_getUsersFilesPath ($pFullQualifiedDAOid) as $name){
                //Regarde la date de dernière modification du fichier
                if ( file_exists($name) === true && ($fileTime = filemtime ($name)) !== false){
                	//Si les fichiers "sources" n'existent pas, ce n'est pas important
                        if ($compiledTime < $fileTime){
                        //Le fichier à été modifié depuis la date de génération, il faut recompiler
                        return true;
                    }
                }
            }
        }

        //Rien n'a été détecté, le fichier est à jour
        self::$_compilationChecked[$pFullQualifiedDAOid] = true;
        return false;
    }

    /**
    * Récupération des fichiers de définition de DAO (classe surchargée et xml)
    * @param tring $DAOid l'identifiant du DAO
    */
    private static function _getUsersFilesPath ($DAOid){
        try {
           $selector = CopixSelectorFactory::create ($DAOid);
           $fileName = strtolower($selector->fileName);
           return array ($selector->getPath (COPIX_CLASSES_DIR).$fileName.'.dao.php',
                      $selector->getPath (COPIX_RESOURCES_DIR).$fileName.'.dao.xml');
        }catch (Exception $e){
        	return array ();
        }
    }
    
    /**
     * Génération du DAO
     * @param string $pFullyQualifiedDAO l'identifiant du DAO complet 
     */
    private static function _generateDAO ($pFullyQualifiedDAO, $pConnectionName){
        Copix::RequireOnce (COPIX_PATH.'dao/CopixDAOGenerator.class.php');
        $generator = new CopixDAOGenerator (self::_getDAODefinitionBuilder ($pFullyQualifiedDAO, $pConnectionName)->getDefinition ());

        // génération des classes PHP correspondant à la définition de la DAO
        CopixFile::write (CopixDAOFactory::_getCompiledPath ($pFullyQualifiedDAO), $generator->getPHPCode ());
    }

    /**
     * Création d'un objet capable de créer la définition d'une DAO en fonction de son identifiant.
     * 
     * @param string $pFullyQualifiedDAO l'identifiant du DAO dont on souhaites récupérer la définition
     * @param string $pConnectionName le nom de la connexion à utiliser pour créer le DAO
     */
    private static function _getDAODefinitionBuilder ($pFullyQualifiedDAO, $pConnectionName){
    	Copix::RequireOnce (COPIX_PATH.'dao/CopixDAODefinitionBuilder.class.php');
        $selector = CopixSelectorFactory::create ($pFullyQualifiedDAO);
        $fileName = $selector->getPath (COPIX_RESOURCES_DIR).strtolower ($selector->fileName.'.dao.xml');
        $fileClassName = $selector->getPath (COPIX_CLASSES_DIR).strtolower ($selector->fileName.'.dao.php');
        if (is_readable ($fileName)  || (count (explode ('|', $pFullyQualifiedDAO)) > 1) || (count (explode (':', $pFullyQualifiedDAO)) > 1)){
    		//On lit si il existe un element parameterdans la definition du xml
        	if (! ($parsedFile = @simplexml_load_file ($fileName))){
				throw new Exception ('Impossible d\'analyser le fichier XML pour le DAO '.$fileName);    			
    		}
	    	if (isset ($parsedFile->parameter) && ($parsedFile->parameter['value'] == 'auto') ){
	    		return new CopixDAODefinitionXmlAutoBuilder ($pFullyQualifiedDAO, array ('xmlFilePath'=>$fileName, 'phpClassFilePath'=>$fileClassName, 'connection'=>$pConnectionName));
	    	}
	    	
        	return new CopixDAODefinitionXmlBuilder ($pFullyQualifiedDAO, array ('xmlFilePath'=>$fileName, 'phpClassFilePath'=>$fileClassName, 'connection'=>$pConnectionName));
        }else{
        	return new CopixDAODefinitionDBBuilder ($pFullyQualifiedDAO, array ('tableName'=>$pFullyQualifiedDAO, 'phpClassFilePath'=>$fileClassName, 'connection'=>$pConnectionName));
        }
    }

    /**
    * Récupération du nom du DAO à partir de son identifiant
    * @param string $DAOid l'identifiant du DAO
    * @return string
    */
    public static function getDAOName ($DAOid, $pGenerated = true){
        return ($pGenerated === true ? 'Compiled' : '').'DAO'.CopixSelectorFactory::create ($DAOid)->fileName;
    }

    /**
    * Récupération du nom du DAO record à partir de son identifiant
    * @param string $DAOid l'identifiant du DAO
    * @return string
    */
    public static function getDAORecordName ($DAOid, $pGenerated = true){
        return ($pGenerated === true ? 'Compiled' : '').'DAORecord'.CopixSelectorFactory::create ($DAOid)->fileName;
    }
}
?>