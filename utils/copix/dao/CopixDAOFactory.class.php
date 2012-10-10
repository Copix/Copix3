<?php
/**
 * @package	copix
 * @subpackage	dao
 * @author	Croës Gérald , Jouanneau Laurent
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Factory de DAO
 *
 * @package		copix
 * @subpackage	dao
 */
class CopixDAOFactory {
    /**
     * Instances uniques des DAO
     *
     * @var array
     */
    private static $_daoSingleton = array ();

    /**
     * Tableau qui nous sert à nous souvenir des vérifications déja effectuées sur la compilation de certaines classes
     *
     * @var array
     */
    private static $_compilationChecked = array ();

    /**
     * Création du DAO à partir de son identifiant.
     *
     * @param string $pDAOid Identifiant Copix du DAO
     * @param string $pConnectionName Nom de la connexion à utiliser
     * @return ICopixDAO
     */
    public static function create ($pDAOid, $pConnectionName = null) {
        $pDAOid = CopixSelectorFactory::purge ($pDAOid);
        self::_fileInclude ($pDAOid, $pConnectionName);

        $className = 'DAO'.$pDAOid;
        // on gère avec un class exist pour avoir une exception en cas de DAO Invalide, car CopixModule utilise un try catch
        if (!class_exists($className)) {
            throw new CopixException (_i18n ('copix:copix.error.class.couldNotLoadClass', $className));
        }
        return new $className ($pConnectionName);
    }

    /**
     * Création ou récupération d'une instance unique de DAO
     *
     * @param string $pDAOid Identifiant Copix du DAO
     * @param string $pConnectionName Nom de la connexion à utiliser
     * @return ICopixDAO
     */
    public static function getInstanceOf ($pDAOid, $pConnectionName = null) {
        $pDAOid = CopixSelectorFactory::purge ($pDAOid);
        self::_fileInclude ($pDAOid, $pConnectionName);
        $className = 'DAO'.$pDAOid;
		$dao = new $className ($pConnectionName);
		return $dao->instance ($pConnectionName);
    }

    /**
     * Création d'un objet enregistrement
     *
     * @param string $pDAOid Identifiant du DAO dont l'enregistrement sera créé
     * @param string $pConnectionName Nom de la connexion à utiliser
     * @return ICopixDAORecord
     */
    public static function createRecord ($pDAOid, $pConnectionName = null) {
        $pDAOid = CopixSelectorFactory::purge ($pDAOid);
        self::_fileInclude ($pDAOid, $pConnectionName);

        $className = 'DAORecord'.$pDAOid;
        return new $className ();
    }

    /**
     * Demande l'inclusion du fichier de définition du DAO
     *
     * @param string $pDAOid identifiant Copix du DAO
     * @param string $pConnectionName Nom de la connexion à utiliser
     */
    public static function fileInclude ($pDAOid, $pConnectionName = null) {
        self::_fileInclude (CopixSelectorFactory::purge ($pDAOid), $pConnectionName);
    }

    /**
     * Cache de tests d'inclusions.
     */
    private static $_included = array ();

    /**
     * Inclusion de la définition du DAO
     *
     * @param string $pFullQualifiedDAOId Identifiant Copix complet (avec le module) du DAO
     * @param string $pConnectionName Nom de la connexion à utiliser
     */
    private static function _fileInclude ($pFullQualifiedDAOId, $pConnectionName) {
        if (array_key_exists ($pFullQualifiedDAOId, self::$_included)){
            return;
        }
        if (self::_needsCompilation ($pFullQualifiedDAOId, $pConnectionName)) {
            self::_generateDAO ($pFullQualifiedDAOId, $pConnectionName);
        }
        foreach (self::_getFilePath ($pFullQualifiedDAOId, $pConnectionName) as $path) {
            Copix::RequireOnce ($path);
        }
        self::$_included[$pFullQualifiedDAOId] = true;
    }


    /**
     * Création d'un objet CopixDAOSearchParams pour effectuer des requêtes type findby avec les DAO
     *
     * @param string $pKind Type par défaut des conditions de l'objet (AND ou OR)
     * @return CopixDAOSearchParams
     */
    public static function createSearchParams ($pKind = 'AND') {
        return new CopixDAOSearchParams ($pKind);
    }

    /**
     * Récupération du chemin ou le DAO sera compilé en PHP
     *
     * @param string $pConnectionName Nom de la connexion utilisée, null pour celle par défaut
     */
    private static function _getCacheDriverPath ($pConnectionName) {
		return self::getCacheBasePath () . CopixConfig::instance ()->copixdb_getProfile ($pConnectionName)->getDriverName () . '/';
    }

	/**
	 * Retourne le répertoire où seront stockés les caches des DAO
	 *
	 * @return string
	 */
	public static function getCacheBasePath () {
		return COPIX_CACHE_PATH . 'dao/';
	}

    /**
     * Indique si le DAO à besoin d'être regénéré
     *
     * @param string $DAOid Identifiant (complet) du DAO à tester
     * @return boolean
     */
    private static function _needsCompilation ($pFullQualifiedDAOid, $pConnectionName) {
        if (isset (self::$_compilationChecked[$pFullQualifiedDAOid])) {
            return false;
        }

        $config = CopixConfig::instance ();
        if ($config->force_compile) {
            return true;
        }

        //regarde s'il existe la classe compilée.
        foreach ($filePath = self::_getFilePath ($pFullQualifiedDAOid, $pConnectionName) as $path) {
            if (!is_readable ($path)) {
                return true;
            }
        }

        //Calcul de la date du fichier le plus ancien
        $compiledTime = false;
        foreach ($filePath as $path) {
            $time = filemtime ($path);
            if ($time < $compiledTime || $compiledTime === false) {
                $compiledTime = $time;
            }
        }

        //Rien n'a été détecté, le fichier est à jour
        self::$_compilationChecked[$pFullQualifiedDAOid] = true;
        return false;
    }

    /**
     * Génération du DAO
     *
     * @param string $pFullyQualifiedDAO l'identifiant du DAO complet
     * @param string $pConnectionName Nom de la connexion à utiliser
     */
    private static function _generateDAO ($pFullyQualifiedDAO, $pConnectionName) {
        $definition = self::_getDAODefinitionBuilder ($pFullyQualifiedDAO, $pConnectionName)->getDefinition ();
        $generator = new CopixDAOGenerator ($definition);
        $path = self::_getFilePath ($pFullyQualifiedDAO, $pConnectionName);
        //génération des 4 fichiers de DAO
        $dao = $generator->getPHPCode4DAO ();
        CopixFile::write ($path['compileddao'], $dao);
        $record = $generator->getPHPCode4DAORecord ();
        CopixFile::write ($path['compiledrecord'], $record);
        $dao = $generator->getPHPCode4UserDAO ();
        CopixFile::write ($path['dao'], $dao);
        $record = $generator->getPHPCode4UserDaoRecord ();
        CopixFile::write ($path['daorecord'], $record);
    }

    /**
     * Création d'un objet capable de créer la définition d'un DAO en fonction de son identifiant
     *
     * @param string $pFullyQualifiedDAO l'identifiant du DAO dont on souhaites récupérer la définition
     * @param string $pConnectionName Nom de la connexion à utiliser
     * @return CopixDAODefinitionBuilder
     */
    private static function _getDAODefinitionBuilder ($pFullyQualifiedDAO, $pConnectionName) {
        //La table copixmodule est un cas particulier, on ne peut lui rajouter des méthodes
        if ($pFullyQualifiedDAO === 'copixmodule') {
            return new CopixDAODefinitionDBBuilder (
                    $pFullyQualifiedDAO,
                    array (
                            'tableName' => $pFullyQualifiedDAO,
                            'connection' => $pConnectionName
                    )
            );
        }
        //recherche d'un fichier .dao.xml et d'un fichier .dao.php qui pourrait correspondre a notre DAO
        $xmlFile = false;
        $phpFile = false;
        $phpRecordFile = false;
        foreach (CopixModule::getList () as $moduleName) {
            $basePath = CopixModule::getPath ($moduleName);
            if ($phpFile === false && is_readable ($tmpPath = $basePath.DIRECTORY_SEPARATOR.COPIX_CLASSES_DIR.DIRECTORY_SEPARATOR.$pFullyQualifiedDAO.'.dao.php')) {
                $phpFile = $tmpPath;
            }
            if ($phpRecordFile === false && is_readable ($tmpPath = $basePath.DIRECTORY_SEPARATOR.COPIX_CLASSES_DIR.DIRECTORY_SEPARATOR.$pFullyQualifiedDAO.'.record.php')) {
                $phpRecordFile = $tmpPath;
            }
            if ($xmlFile === false && is_readable ($tmpPath = $basePath.DIRECTORY_SEPARATOR.COPIX_RESOURCES_DIR.DIRECTORY_SEPARATOR.$pFullyQualifiedDAO.'.dao.xml')) {
                $xmlFile = $tmpPath;
            }
        }

        if (is_readable ($xmlFile)) {
            //On lit si il existe un element parameterdans la definition du xml
            if (!($parsedFile = @simplexml_load_file ($xmlFile))) {
                throw new CopixDAODefinitionException ($pFullyQualifiedDAO, _i18n ('copix:dao.error.badXMLFile', $xmlFile));
            }

            // si c'est un DAO semi automatique
            if (isset ($parsedFile->parameter) && ($parsedFile->parameter['value'] == 'auto')) {
                return new CopixDAODefinitionXmlAutoBuilder (
                        $pFullyQualifiedDAO,
                        array (
                                'UserDAOFilePath' => $phpFile,
                                'UserDAORecordFilePath' => $phpRecordFile,
                                'xmlFilePath' => $xmlFile,
                                'connection' => $pConnectionName
                        )
                );
            }
            // si c'est un DAO entièrement définit via le XML
            return new CopixDAODefinitionXmlBuilder (
                    $pFullyQualifiedDAO,
                    array (
                            'UserDAOFilePath' => $phpFile,
                            'UserDAORecordFilePath' => $phpRecordFile,
                            'xmlFilePath' => $xmlFile,
                            'connection' => $pConnectionName
                    )
            );
        } else {
            //si c'est un DAO entièrement automatique
            try {
                return new CopixDAODefinitionDBBuilder (
                        $pFullyQualifiedDAO,
                        array (
                                'UserDAOFilePath' => $phpFile,
                                'UserDAORecordFilePath' => $phpRecordFile,
                                'tableName' => $pFullyQualifiedDAO,
                                'connection' => $pConnectionName
                        )
                );
            }catch (CopixDAODefinitionNoTableException $e) {
                return new CopixDAODefinitionPHPBuilder (
                        $pFullyQualifiedDAO,
                        array (
                                'UserDAOFilePath' => $phpFile,
                                'tableName' => $pFullyQualifiedDAO,
                                'connection' => $pConnectionName
                        )
                );
            }
        }
    }

    /**
     * retourne les chemins de fichiers compilés
     *
     * @return array of string
     */
    private static function _getFilePath ($pFullyQualifiedDAO, $pConnectionName) {
        $basePath = self::_getCacheDriverPath ($pConnectionName);
        return array (
                'compileddao'=>$basePath.'compiled'.DIRECTORY_SEPARATOR.strtolower ($pFullyQualifiedDAO).'.dao.php',
                'compiledrecord'=>$basePath.'compiled'.DIRECTORY_SEPARATOR.strtolower ($pFullyQualifiedDAO).'.record.php',
                'dao'=>$basePath.'user'.DIRECTORY_SEPARATOR.strtolower ($pFullyQualifiedDAO).'.dao.php',
                'daorecord'=>$basePath.'user'.DIRECTORY_SEPARATOR.strtolower ($pFullyQualifiedDAO).'.record.php',
        );
    }
}