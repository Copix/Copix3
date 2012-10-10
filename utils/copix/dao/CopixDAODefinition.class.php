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
 * Représente la définition de DAO qui permettra à un générateur de créer le DAO final
 *
 * @package		copix
 * @subpackage	dao
 */
class CopixDAODefinition {
    /**
     * Liste des propriétés : Clefs => Les noms des champs, Valeurs => des objets de type CopixDAOPropertyDefinition
     *
     * @var array
     */
    private $_properties = array ();

    /**
     * Tableau de toute les tables : keys = Nom de la table, values = array ()
     * 'name' => nom de la table, 'tablename' => 'le nom de la table en base', 'JOIN' => 'type de jointure',
     * 'primary' => 'bool', 'fields' => array (liste des noms de champs)
     *
     * @var array
     */
    private $_tables = array ();

    /**
     * Nom de la table principale
     *
     * @var string
     */
    private $_primaryTableName = null;

    /**
     * Liste des jointures, entre toutes les tables
     * keys = foreign table name
     * values = array('join'=>'type jointure', 'pfield'=>'real field name', 'ffield'=>'real field name');
     *
     * @var array
     */
    private $_joins = array ();

    /**
     * La connection à utiliser pour la génération
     *
     * @var string
     */
    private $_connectionName = null;

    /**
     * Liste des méthodes générées
     *
     * @var array
     */
    private $_methods = array ();

    /**
     * Chemin vers le fichier de définition du DAO
     *
     * @var string
     */
    private $_xmlFilePath = null;

    /**
     * Chemin vers le fichier PHP de DAO écrit par l'utilisateur
     *
     * @var string
     */
    private $_userDAOFilePath = null;

    /**
     * Chemin vers le fichier PHP de Record écrit par l'utilisateur
     *
     * @var string
     */
    private $_userDAORecordFilePath = null;

    /**
     * Identifiant du DAO
     *
     * @var string
     */
    private $_DAOId = null;

    /**
     * Le nom de la base de données associée à la connexion
     *
     * @var string
     */
    private $_database = null;

    /**
     * Définition de l'identifiant de DAO que l'on souhaite générer
     *
     * @param string $pDAOId Identifiant de DAO que l'on souhaites générer
     */
    public function setDAOId ($pDAOId) {
        $this->_DAOId = $pDAOId;
    }

    /**
     * Retourne l'identifiant de DAO que l'on est en train de générer
     *
     * @return string
     */
    public function getDAOId () {
        return $this->_DAOId;
    }

    /**
     * Retourne le nom de la DAO
     */
    public function getDAOName () {
        return 'DAO'.$this->_DAOId;
    }

    /**
     * Retourne le nom du Record pour la DAO
     */
    public function getDAORecordName () {
        return 'DAORecord'.$this->_DAOId;
    }

    /**
     * Ajoute un champ à la liste des champs d'une table
     *
     * @param object $pField Définition du champ à ajouter. Propriétés : table, name, fieldName, fkTable, fkFieldName
     * @throws Exception
     */
    public function addProperty ($pField) {
        $this->_properties[$pField->name] = $pField;
        $this->_tables[$pField->table]['fields'][] = $pField->name;

        if ($pField->fkTable !== null) {
            if (!isset ($this->_joinTypes[$pField->fkTable])) {
                throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.properties.foreign.table.missing', array ($this->_DAOId, $pField->name)));
            }
            $this->_joins[$pField->fkTable][] = array (
                    'join' => $this->_joinTypes[$pField->fkTable],
                    'pfield' => $pField->fieldName,
                    'ffield' => $pField->fkFieldName
            );
        }
        uasort ($this->_joins, array('CopixDAODefinition', '_sortJoins'));
    }

    /**
     * Retourne les jointures définies dans l'objet
     *
     * @return array
     */
    public function getJoins () {
        return $this->_joins;
    }

    /**
     * Récupération de la liste des propriétés
     *
     * @return array
     */
    public function getProperties () {
        return $this->_properties;
    }

    /**
     * Retourne la liste des méthodes inclues dans la définition
     *
     * @return array
     */
    public function getMethods () {
        return $this->_methods;
    }

    /**
     * Définition du nom de la connexion à utiliser
     *
     * @param string $pConnectionName Nom de la connexion à utiliser
     */
    public function setConnectionName ($pConnectionName) {
        $config = CopixConfig::instance ();
        return $this->_database = $config->copixdb_getProfile ($this->_connectionName = $pConnectionName)->getDatabase ();
    }

    /**
     * récupère le nom du driver associé à la connexion
     *
     * @return string
     */
    public function getDatabase (){
        return $this->_database == null ? $this->setConnectionName (CopixConfig::instance ()->copixdb_getDefaultProfileName ()) : $this->_database;
    }

    /**
     * Retourne le nom de la connexion à utiliser
     *
     * @return string
     */
    public function getConnectionName () {
        return $this->_connectionName;
    }

    /**
     * Ajoute une table à la définition de DAO
     *
     * @param array $pTableInfos Tableau contenant les informations de la table à ajouter. Contenu : name => string, tablename => string, primary => bool, join => string
     * @throws Exception
     */
    public function addTable ($pTableInfos) {
        //converting tableinfo into strings
        foreach ($pTableInfos as $key => $name) {
            $newTableInfo[(string) $key] = (string) $name;
        }
        $pTableInfos = $newTableInfo;

        if (!isset ($pTableInfos['name']) || trim ($pTableInfos['name']) == '') {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.table.name'));
        }

        if (!isset ($pTableInfos['tablename']) || $pTableInfos['tablename'] == '') {
            $pTableInfos['tablename'] = $pTableInfos['name'];
        }

        $pTableInfos['fields'] = array ();
        $this->_tables[$pTableInfos['name']] = $pTableInfos;

        if (isset ($pTableInfos['primary']) && $this->_getBool ($pTableInfos['primary'])) {
            if ($this->_primaryTableName !== null) {
                throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.table.primary.duplicate',$this->_primaryTableName));
            }
            $this->_primaryTableName = $pTableInfos['name'];
        } else {
            $join = isset ($pTableInfos['join']) ? strtolower (trim ($pTableInfos['join'])) : '';
            if (!in_array ($join, array ('left', 'right', 'inner', ''))) {
                throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.table.join.invalid', array ($this->_DAOId, $pTableInfos['name'])));
            }

            if ($join == 'inner') {
                $join = '';
            }
            $this->_joinTypes[$pTableInfos['name']] = $join;
        }
    }

    /**
     * Comparaison pour le tri des jointures
     *
     * @param array $join1 Type de jointure 1. Contenu : join => string
     * @param array $join2 Type de jointure 2. Contenu : join => string
     * @return int 1 : $join1 == '' && $join2 != '' ; -1 : $join1 != '' && $join2 == '' ; 0 : tous les autres cas
     */
    private static function _sortJoins ($join1, $join2) {
        $j1 = isset ($join1['join']) ? $join1['join'] : '';
        $j2 = isset ($join2['join']) ? $join2['join'] : '';
        if ($j1 == '' && $j2 != '') {
            return 1;
        } else if ($j1 != '' && $j2 == '') {
            return -1;
        } else {
            return 0;
        }
    }

    /**
     * Retourne des tables
     */
    public function getTables () {
        return $this->_tables;
    }

    /**
     * Retourne des informations sur la table primaire
     *
     * @return string ou null si non défini
     */
    public function getPrimaryTable () {
        return $this->getTable ($this->getPrimaryTableName ());
    }

    /**
     * Retourne le nom de la table primaire (dans le fichier de définition)
     *
     * @return string ou null si non défini
     */
    public function getPrimaryTableName () {
        return $this->_primaryTableName;
    }

    /**
     * Retourne les informations sur une table donnée
     *
     * @param string $pTableName Nom de la table dont on veut récupérer les informations
     * @return array Informations sur la table ou null si la table n'est pas trouvée
     */
    public function getTable ($pTableName) {
        return isset ($this->_tables[$pTableName]) ? $this->_tables[$pTableName] : null;
    }

    /**
     * Donne le nom (celui en base) de la table primaire associée au DAO
     *
     * @return string ou null si non trouvée
     */
    public function getPrimaryTableRealName () {
        if ($primary = $this->getPrimaryTable ()) {
            return $primary['tablename'];
        }
        return null;
    }

    /**
     * Ajout d'une méthode
     *
     * @param object $pMethod Méthode à ajouter
     * @throws Exception
     */
    public function addMethod ($pMethod) {
        if (isset ($this->_methods[$pMethod->name])) {
            throw new CopixDAODefinitionException ($this->_DAOId, _i18n ('copix:dao.error.definitionfile.method.duplicate', $pMethod->name));
        }
        $this->_methods[$pMethod->name] = $pMethod;
    }

    /**
     * Retourne un bool suivant une chaine. true, 1 ou yes renvoie true, le reste, false
     *
     * @param string $pValue Valeur à tester
     * @return bool
     */
    private function _getBool ($pValue) {
        return in_array (trim ($pValue), array ('true', '1', 'yes'));
    }

    /**
     * Assigne le chemin vers le fichier PHP à utiliser et à surcharger pour générer le DAO
     *
     * @param string $pFilePath Chemin absolu vers le fichier PHP à utiliser
     */
    public function setUserDAOFilePath ($pFilePath) {
        $this->_userDAOFilePath = $pFilePath;
    }

    /**
     * Retourne le chemin du fichier PHP assigné à la définition de DAO
     *
     * @return string
     */
    public function getUserDAOFilePath () {
        return $this->_userDAOFilePath;
    }

    /**
     * Assigne le chemin vers le fichier PHP à utiliser et à surcharger pour générer le DAO
     *
     * @param string $pFilePath Chemin absolu vers le fichier PHP à utiliser
     */
    public function setUserDAORecordFilePath ($pFilePath) {
        $this->_userDAORecordFilePath = $pFilePath;
    }

    /**
     * Retourne le chemin du fichier PHP assigné à la définition de DAO
     *
     * @return string
     */
    public function getUserDAORecordFilePath () {
        return $this->_userDAORecordFilePath;
    }

    /**
     * Retourne les champs modifiés par la méthode de capture $pCaptureMethod
     *
     * @param string $pCaptureMethod Nom de la méthode à appeler pour chaque champ (sans _capture)
     * @return array
     */
    public function getPropertiesBy ($pCaptureMethod) {
        $pCaptureMethod = '_capture' . $pCaptureMethod;
        $result = array ();
        $fields = $this->getProperties ();

        foreach ($this->getProperties () as $field) {
            if ($this->$pCaptureMethod ($field)) {
                $result[$field->name] = $fields[$field->name];
            }
        }
        return $result;
    }

    /**
     * Indique si le champ appartient à la clef primaire
     *
     * @param CopixDAOPropertyDefinition $pField Champ à vérifier
     * @return bool
     */
    private function _capturePkFields ($pField) {
        return ($pField->table == $this->getPrimaryTableName ()) && $pField->isPK;
    }

    /**
     * Retourne true si le champ fait partie de la table principale, et qu'il n'est pas auto incrémenté
     *
     * @param CopixDAOPropertyDefinition $pField Champ à vérifier
     * @return bool True : champ de la table principale non auto incrémenté, false sinon
     */
    private function _capturePrimaryFieldsExcludeAutoIncrement ($pField) {
        return
                ($pField->table == $this->getPrimaryTableName ()) &&
                ($pField->type != 'autoincrement') && ($pField->type != 'bigautoincrement');
    }

    /**
     * Retourne true si le champ appartient à la table principale, et qu'il ne fait pas parti de la clef primaire
     *
     * @param CopixDAOPropertyDefinition $pField Champ à vérifier
     * @return bool
     */
    private function _capturePrimaryFieldsExcludePk ($pField) {
        return ($pField->table == $this->getPrimaryTableName ()) && !$pField->isPK;
    }

    /**
     * Retourne true si le champ appartient à la table principale
     *
     * @param CopixDAOPropertyDefinition $pField Champ à vérifier
     * @return bool
     */
    private function _capturePrimaryTable ($pField) {
        return ($pField->table == $this->getPrimaryTableName ());
    }

    /**
     * Récupération de tous les champs
     *
     * @param CopixDAOPropertyDefinition $pField
     * @return true
     */
    private function _captureAll ($pField) {
        return true;
    }

    /**
     * Indique si le champ est de type version
     *
     * @param CopixDAOPropertyDefinition $pField Champ à vérifier
     * @return bool
     */
    private function _captureVersion ($pField) {
        return $pField->type == 'version';
    }
}