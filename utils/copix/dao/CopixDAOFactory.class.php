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
Modifications à faire sur les DAO pour la version 3.1
Nom de fichier dépendant du driver
Nom de classe dépendant du driver
Ajouter une interface dans le "sans driver"

un fichier .dao.php qui contient l'objet manipulé.
un fichier .dao.driver.php qui contient l'objet spécifique au driver et porte comme nom de classe DAODriverTable
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
	 * Type de record que l'on décide de parcourir
	 * 
	 * @var string
	 */
	private $_recordId = null;
	
	/**
	 * Résultats de la requête
	 *
	 * @var CopixDBResultSet
	 */
	private $_resultSet;
	
	/**
	 * Offset courant
	 *
	 * @var int
	 */
	private $_currentOffset = 0;
	
	/**
	 * Construction en indiquant le type de DAO en paramètre
	 * 
	 * @param array $pArray Résultats de la requête
	 * @param string $pRecordId Type de record que l'on décide de parcourir
	 */
	public function __construct ($pArray, $pRecordId) {
		$this->_resultSet = $pArray;
		$this->_recordId = $pRecordId;
	}

	/**
	 * Retourne l'élément d'indice donné
	 * 
	 * @param string $pOffset
	 * @return object
	 */
	public function offsetGet ($pOffset) {
		return $this->_makeRecordIfNot ($pOffset);
	}

	/**
	 * Retourne l'élément courant
	 * 
	 * @return object 
	 */
	public function current () {
		return $this->_makeRecordIfNot ($this->_currentOffset);
	}
	
	/**
	 * Création d'un enregistrement a partir d'un élément
	 * 
	 * @param	mixed	offset (integer)	
	 * @return ICopixDAORecord 
	 */
	private function _makeRecordIfNot ($pOffset){
		if (isset ($this->_resultSet[$pOffset])){
			if ($this->_resultSet[$pOffset] instanceof ICopixDAORecord){
				return $this->_resultSet[$pOffset];
			}else{
				return $this->_resultSet[$pOffset] = _record ($this->_recordId)->initFromDBObject ($this->_resultSet[$pOffset]);				
			}
		}else{
			return null;
		}
	}

	/**
	 * Passe à l'enregistrement suivant
	 */
	public function next () {
		$this->_currentOffset++;
	}

	/**
	 * Retourne la clef courante
	 * 
	 * @return int
	 */
	public function key () {
		return $this->_currentOffset;
	}
	
	/**
	 * Indique si l'élément courant est valide
	 * 
	 * @return boolean 
	 */
	public function valid () {
		return isset ($this->_resultSet[$this->_currentOffset]);
	}
	
	/**
	 * Réinitialisation du parcours des éléments au premier indice
	 */
	public function rewind () {
		$this->_currentOffset = 0;
	}
	
	/**
	 * Blocage de la possibilité de définir un enregistrement. Déclenche une exception.
	 * 
	 * @param mixed $pKey Clef à modifier, type string ou int
	 * @param mixed $pValue Nouvelle valeur pour la clef $pKey
	 * @throws Exception 
	 */
	public function offsetSet ($pKey, $pValue) {
		throw new Exception (_i18n ('copix:dao.error.offsetSet'));
	}
	
	/**
	 * Blocage de la possibilité de supprimer un enregistrement. Déclenche une exception.
	 * 
	 * @param mixed $pKey Clef à supprimer, type string ou int
	 * @throws Exception
	 */
	public function offsetUnset ($pKey) {
		throw new Exception (_i18n ('copix:dao.error.offsetUnset'));
	}
	
	/**
	 * Indique si $pOffset existe
	 * 
	 * @param mixed $pOffset Clef dont on veut vérifier l'existance
	 * @return boolean
	 */
	public function offsetExists ($pOffset) {
		return isset ($this->_resultSet[$pOffset]);
	}
	 
	/**
	 * Retourne le nombre d'éléments qui existent dans le résulat
	 * 
	 * @return int
	 */
	public function count () {
		return count ($this->_resultSet);
	}
	 
	/**
	 * Récupère l'ensemble des enregistrements dans un tableau
	 * 
	 * @return array
	 */
	public function fetchAll () {
		$results = array ();
		foreach ($this->_resultSet as $key => $element) {
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