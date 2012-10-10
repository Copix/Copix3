<?php
/**
 * @package	copix
 * @subpackage	dao
 * @author	Croës Gérald, Jouanneau Laurent
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour la génération de DAO
 *
 * @package		copix
 * @subpackage	dao
 */
class CopixDAOGenerator {
    /**
     * La définition à utiliser pour la génération
     *
     * @var CopixDAODefinition
     */
    protected $_definition = null;

    /**
     * Constructeur
     *
     * @param CopixDAODefinition $pDefinition Définition du DAO
     */
    public function __construct ($pDefinition) {
        $this->_definition = $pDefinition;
    }

    /**
     * Préparation d'une liste de champ dans un tableau $array[':nomPropriete'] = $pPrefixField->nomPropriete;
     *
     * @param array $pFieldList Tableau des champs. Exemple : array ('monChamp' => CopixDAOPropertyDefinition)
     * @param string $pPrefixField Prefixe à ajouter à chaque nom de champ
     * @return array
     */
    private function _prepareValuesForNewDB ($pFieldList, $pPrefixField = '') {
        $database = $this->_definition->getDatabase ();

        $values = $fields = $formatted = array ();
        foreach ((array) $pFieldList as $fieldName => $field) {
            if (!$field->ofPrimaryTable) {
                continue;
            }
            if ($field->type == 'version') {
                $values[':' . $fieldName] = '(intval($' . $pPrefixField . $fieldName . ') + 1)';
                $formatted[':' . $fieldName] = ':' . $fieldName;
            } else if (isset ($field->method) && $field->method !== null) {
                $values[':' . $fieldName] = '$' . $pPrefixField . $fieldName;
                $formatted[':' . $fieldName] = $field->method . " (:" . $fieldName . ")";
            } else if ($database == 'oci' && in_array ($field->type, array ('date', 'datetime', 'time'))) {
                switch ($field->type) {
                    case 'datetime':
                        $values[':' . $fieldName] = '$' . $pPrefixField . $fieldName;
                        $formatted[':' . $fieldName] = "to_date (:" . $fieldName . ", \\'YYYYMMDDHH24MISS\\')";
                        break;
                    case 'date':
                        $values[':' . $fieldName] = '$' . $pPrefixField . $fieldName;
                        $formatted[':' . $fieldName] = "to_date (:" . $fieldName . ", \\'YYYYMMDD\\')";
                        break;
                    case 'time':
                        $values[':' . $fieldName] = '$' . $pPrefixField . $fieldName;
                        $formatted[':' . $fieldName] = "to_date (:" . $fieldName . ", \\'HH24MISS\\')";
                        break;
                }
            } else if (($database == 'mysql' || $database == 'sqlite') && in_array ($field->type, array ('date', 'datetime', 'time'))) {
                // MySQL et SQLite gèrent les entrées sous le même format
                switch ($field->type) {
                    case 'datetime':
                        $values[':' . $fieldName] = 'CopixDateTime::yyyymmddhhiissToFormat ($' . $pPrefixField . $fieldName.", 'Y-m-d H:i:s')";
                        $formatted[':' . $fieldName] = ":" . $fieldName;
                        break;
                    case 'date':
                        $values[':' . $fieldName] = 'CopixDateTime::yyyymmddToFormat ($' . $pPrefixField . $fieldName.", 'Y-m-d')";
                        $formatted[':' . $fieldName] = ":" . $fieldName;
                        break;
                    case 'time':
                        $values[':' . $fieldName] = 'CopixDateTime::hhiissToFormat ($' . $pPrefixField . $fieldName.", 'H:i:s')";
                        $formatted[':' . $fieldName] = ":" . $fieldName;
                        break;
                }
            } elseif ($database == 'pgsql' && $field->type == 'datetime') {
                $values[':' . $fieldName] = 'CopixDateTime::getIsoDateTime ($' . $pPrefixField . $fieldName.")";
                $formatted[':' . $fieldName] = ":" . $fieldName;
            } elseif (($database == 'mysql') && in_array ($field->type, array ('blob'))) {
                //Champs lobs / blob & stuff pour MySql
                $values[':'.$fieldName] = 'base64_encode ($' . $pPrefixfield . $fieldName.")";
                $formatted[':'.$fieldName] = ":".$fieldName;
            } else {
                $values[':' . $fieldName] = '$' . $pPrefixField . $fieldName;
                $formatted[':' . $fieldName] = ':' . $fieldName;
            }
            $fields[$fieldName] = $this->_quoteIdentifier ($field->fieldName, $database);
        }
        return array (
                $fields,
                $values,
                $formatted
        );
    }

    /**
     * Formatte les champs avec un début ($pPrefix), une fin ($pPostfix) et un sparateur ($pSeparator)
     *
     * @param string $pFieldProperty Propriété de l'objet que l'on utilise pour l'écriture
     * @param string $pPrefix A ajouter avant
     * @param string $pPostfix A ajouter après
     * @param string $pSeparator Séparateur
     * @param array $pFields Champs à écrire, si null alors on utilise tous les champs
     * @param boolean $pShowPHPDoc Indique si on veut afficher un commentaire PHPDoc sur le avant avant $pPrefix
     * @return string
     */
    private function _writeFieldsInfoWith ($pFieldProperty, $pPrefix = '', $pPostfix = '', $pSeparator = '', $pFields = null, $pShowPHPDoc = false) {
        $php = new CopixPHPGenerator ();
        if ($pFields === null) {
            //Si aucun champ n'est donn, on utilise les champs de la dfinition
            $pFields = $this->_definition->getProperties ();
        }

        $result = array ();
        foreach ($pFields as $id => $field) {
            $definition = '';
            if ($pShowPHPDoc) {
                $comment = array ('Valeur du champ ' . $field->$pFieldProperty, '', '@var ' . $this->_getFieldTypeForPHPDoc ($field->type));
                $definition = $php->getPHPDoc ($comment, 1);
            }
            $definition .= $pPrefix . $field->$pFieldProperty . $pPostfix;
            $result[] = $definition;
        }

        return implode ($pSeparator, $result);
    }

    /**
     * Compilation de l'objet record
     *
     * @return string
     */
    public function getPHPCode4DAORecord () {
        $result = '';
        $php = new CopixPHPGenerator ();

        //--Vars
        $classVars = array ();
        $classMethods = array ();

        $result .= $php->getPHPDoc ('Définition d\'un enregistrement pour le DAO ' . $this->_definition->getDAOId ());
        $result .= $php->getLine ('class Compiled' . $this->_definition->getDAORecordName () . ' implements ICopixDAORecord {');

        //DAORecord fields (not in user's DAO)
        //building the tab for the required properties.
        $usingFields = array ();
        $classVarsList = array_keys ($classVars);
        foreach ($this->_definition->getProperties () as $id => $field) {
            if (!in_array ($field->name, $classVarsList)) {
                $usingFields[$id] = $field;
            }
        }

        //declaration of properties.
        $result .= $this->_writeFieldsInfoWith ('name', "\t" . 'public $', " = null;\n", '', $usingFields, true);

        //création des constantes
        foreach ($usingFields as $id => $field) {
            $definition  = $php->getPHPDoc (array ('Association de la propriété ' . $field->name.' avec sa représentation en base', ''), 1);
            $result .= $definition."\t const FIELD_" .  strtoupper ($field->name) . " = '".$field->name."';\n";
        }

        //InitFromDBObject
        $database = $this->_definition->getDatabase ();

        $result .= $php->getEndLine ();
        $comments = array (
                'Initialise le record avec les valeurs de $pRecord',
                null,
                '@param ' . $this->_definition->getDAORecordName () . ' $pRecord Enregistrement dont on veut récupérer les valeurs',
                '@return ' . $this->_definition->getDAORecordName ()
        );
        $result .= $php->getPHPDoc ($comments, 1);
        $result .= $php->getLine ('public function initFromDBObject ($pRecord) {', 1);
        $result .= $php->getLine ('$record = _ppo ($pRecord);', 2);
        foreach ($this->_definition->getProperties () as $field) {
            if (($database == 'mysql') && in_array ($field->type, array ('blob'))) {
                $result .= $php->getLine ('$this->' . $field->name . ' = base64_decode ($record->' . $field->name . ");", 2);
            } else {
                $result .= $php->getLine ('$this->' . $field->name . ' = $record->' . $field->name . ";", 2);
            }
        }
        $result .= $php->getLine ('return $this;', 2);
        $result .= $php->getLine ('}', 1);
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4GetDAOId ();
        $result .= $this->_generatePHP4DAORecordCreate ();
        $result .= $php->getLine ('}');
        return $php->getPHPTags ($result);
    }
    
    private function _generatePHP4DAORecordCreate (){
        $php = new CopixPHPGenerator ();
        $comments = array ('Création', null, '@return '.$this->_definition->getDAORecordName ());
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('public static function create () {', 1);
        $result .= $php->getLine ('   return new '.$this->_definition->getDAORecordName (). '();', 1);
        $result .= $php->getLine ('}', 1);

        return $result;
    } 

    /**
     * Compresse le nom de variable passé en paramètres à 30 caractères, et s'assure grâce à une table interne de l'unicité des noms
     *
     * @param string $pFieldName Nom du champ à réduire
     * @return string
     */
    private function _getVariableName ($pFieldName) {
        static $fields = array ();
        if (isset ($fields[$pFieldName])) {
            return $fields[$pFieldName];
        }

        $result = null;
        $try = 0;
        //tant que nous ne sommes pas arrivé à générer une variable qui n'existe pas déja
        while ($result !== null && isset ($fields[$result])) {
            $result = $this->_compressVariableName ($pFieldName, $try++);
        }
        return $fields[$pFieldName] = $result;
    }

    /**
     * Retourne $pName . $pTryNum, avec longueur max $pNumChars
     *
     * @param string $pName
     * @param int $pTryNum
     * @param int $pNumChars Nombre maximum de caractètres
     * @return string
     */
    private function _packVariableName ($pName, $pTryNum = 0, $pNumChars = 30) {
        $final = $pName . (($pTryNum === 0) ? '' : $pTryNum);
        if (strlen ($final) <= $pNumChars) {
            return $final;
        }
        return substr ($final, 0, -1 * strlen ($final) - $pNumChars);
    }
    /**
     * Génération du code PHP pour le DAO
     *
     * @return string
     */
    public function getPHPCode4DAO () {
        $result = '';
        $php = new CopixPHPGenerator ();

        if (! $this->_definition->getPrimaryTable ()) {
            return $php->getPHPTags ('abstract class Compiled' . $this->_definition->getDaoName () . ' implements ICopixDAO {}');
        }

        $result .= $php->getPHPDoc ('Version compilée du DAO ' . $this->_definition->getDaoId ());
        $result .= $php->getLine ('class Compiled' . $this->_definition->getDaoName () . ' implements ICopixDAO {');

        $comments = array ('Nom de la table à laquelle est rattaché ce DAO', null, '@var string');
        $result .= $php->getPHPDoc ($comments, 1);
        $result .= $php->getLine ('protected $_table = \'' . $this->_definition->getPrimaryTableRealName () . '\';', 1);
        $result .= $php->getEndLine ();
        $comments = array ('Nom du profil de connexion', null, '@var string');
        $result .= $php->getPHPDoc ($comments, 1);
        if (($connectionName = $this->_definition->getConnectionName ()) === null) {
            $result .= $php->getLine ('protected $_connectionName = null;', 1);
        } else {
            $result .= $php->getLine ('protected $_connectionName = \'' . $connectionName . '\';', 1);
        }
        $result .= $php->getEndLine ();
        $comments = array ('Requête SQL pour le select avec tous les champs', null, '@var string');
        $result .= $php->getPHPDoc ($comments, 1);
        $result .= $php->getLine ('protected $_selectQuery = null;', 1);
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4DAOConstructor ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4Check ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4Get ();
        $result .= $php->getEndLine ();

        $result .= $this->_generatePHP4Instance ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4Create ();
        $result .= $php->getEndLine ();

        $result .= $this->_generatePHP4FindAll (false);
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4FindBy (false);
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4FindAll (true);
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4FindBy (true);
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4Insert ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4Update ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4UpdateBy ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4Delete ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4DeleteBy ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4CountBy ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4GetPrimaryKey ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4DefinedMethods ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4DAODescribeField ();
        $result .= $php->getEndLine ();
        $result .= $this->_generatePHP4GetDAOId ();
        $result .= $php->getEndLine ();

        $comments = array (
                'Supprime les champs qui ont pour valeur ___COPIX___DELETE___ME___FROM____DAO___QUERIES___ et retourne le tableau épuré',
                null,
                '@param array $pFields Champs à vérifier, forme array (\'nom\' => \'valeur\')',
                '@return array'
        );
        $result .= $php->getPHPDoc ($comments, 1);
        $result .= $php->getLine ('private function _dirtyClearNullValuesForSelectQueries ($pFields) {', 1);
        $result .= $php->getLine ('$toReturn = array ();', 2);
        $result .= $php->getLine ('foreach ($pFields as $key => $value) {', 2);
        $result .= $php->getLine ('if ($value !== \'___COPIX___DELETE___ME___FROM____DAO___QUERIES___\') {', 3);
        $result .= $php->getLine ('$toReturn[$key] = $value;', 4);
        $result .= $php->getLine ('}', 3);
        $result .= $php->getLine ('}', 2);
        $result .= $php->getLine ('return $toReturn;', 2);
        $result .= $php->getLine ('}', 1);

        // fin de la classe
        $result .= $php->getLine ('}');
        return $php->getPHPTags ($result);
    }

    /**
     * Génération de la clause from pour les requêtes de sélection (jointures entre les tables)
     *
     * @return array array[0] = FROM (avec le mot clef FROM inclus) et array[1] = WHERE (avec le mot clef WHERE inclus, vide si aucune condition)
     */
    public function getFromClause () {
        $database = $this->_definition->getDatabase ();
        $ptable = $this->_definition->getPrimaryTable ();

        if ($ptable['name'] != $ptable['tablename']) {
            $sqlFrom = $this->_quoteIdentifier ($ptable['tablename'], $database). ' ' .$ptable['name'];
        } else {
            $sqlFrom = $this->_quoteIdentifier ($ptable['tablename'], $database);
        }

        $sqlWhere = '';
        foreach ($this->_definition->getJoins () as $tablename => $arJoin) {
            $fromPassed = false;
            foreach ($arJoin as $join) {
                if ($tablename != $ptable['name']) {
                    $table = $this->_definition->getTable ($tablename);
                    if ($table['name'] != $table['tablename']) {
                        $sqltable = $this->_quoteIdentifier ($table['tablename'], $database) . ' ' . $this->_quoteIdentifier ($table['name'], $database);
                    } else {
                        $sqltable = $this->_quoteIdentifier ($table['tablename'], $database);
                    }

                    //car particulier des bases oracle
                    if ($database == 'oci') {
                        if ($join['join'] == 'left') {
                            $fieldjoin = $ptable['name'] . '.' . $join['pfield'] . '=' . $table['name'] . '.' . $join['ffield'] . '(+)';
                        } else if ($join['join'] == 'right') {
                            $fieldjoin = $ptable['name'] . '.' . $join['pfield'] . '(+)=' . $table['name'] . '.' . $join['ffield'];
                        } else {
                            $fieldjoin = $ptable['name'] . '.' . $join['pfield'] . '=' . $table['name'] . '.' . $join['ffield'];
                        }
                        if (!$fromPassed) {
                            $sqlFrom .= ', ' . $sqltable;
                            $fromPassed = true;
                        }
                        $sqlWhere .= ' AND ' . $fieldjoin;

                    } else if ($database == 'pgsql') {
                        $fieldjoin = $ptable['name'] . '.' . $join['pfield'] . '=' . $table['name'] . '.' . $join['ffield'];
                        if ($join['join'] == 'left') {
                            $sqlFrom .= ' LEFT JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
                        } else if ($join['join'] == 'right') {
                            $sqlFrom .= ' RIGHT JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
                        } else {
                            if (!$fromPassed) {
                                $sqlFrom .= ' JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
                                $fromPassed = true;
                            } else {
                                $sqlWhere .= ' AND ' . $fieldjoin;
                            }
                        }
                    } else {
                        $fieldjoin = $this->_quoteIdentifier ($ptable['name'], $database) . '.' . $this->_quoteIdentifier ($join['pfield'], $database) . '=' . $this->_quoteIdentifier ($table['name'], $database) . '.' . $this->_quoteIdentifier ($join['ffield'], $database);
                        if ($join['join'] == 'left') {
                            $sqlFrom .= ' LEFT JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
                        } else if ($join['join'] == 'right') {
                            $sqlFrom .= ' RIGHT JOIN ' . $sqltable . ' ON (' . $fieldjoin . ')';
                        } else {
                            if (!$fromPassed) {
                                $sqlFrom .= ' JOIN ' . $sqltable;
                                $fromPassed = true;
                            }
                            $sqlWhere .= ' AND ' . $fieldjoin;
                        }
                    }
                }
            }
        }
        $sqlWhere = ($sqlWhere != '') ? ' WHERE ' . substr ($sqlWhere, 4) : '';
        return array (
                ' FROM ' . $sqlFrom,
                $sqlWhere
        );
    }

    /**
     * Créé la partie SELECT pour toutes les requpetes de type SELECT
     *
     * @return string
     */
    public function getSelectClause () {
        $result = array ();

        $database = $this->_definition->getDatabase ();

        foreach ($this->_definition->getProperties () as $id => $prop) {
            $table = $this->_quoteIdentifier ($prop->table, $database) . '.';
            
            $tableAndFieldName = $table.$this->_quoteIdentifier ($prop->fieldName, $database);

            if ($prop->selectMotif == '%s') {
                if ($prop->fieldName != $prop->name) {
                    //in oracle we must escape name
                    if ($database == 'oci') {
                        if ($prop->type == 'datetime') {
                            $result[] = "to_char(" . $tableAndFieldName . ", \\'YYYYMMDDHH24MISS\\')" . ' "' . $prop->name . '"';
                        } else if ($prop->type == 'date') {
                            $result[] = "to_char(" . $tableAndFieldName . ", \\'YYYYMMDD\\')" . ' "' . $prop->name . '"';
                        } else if ($prop->type == 'time') {
                            $result[] = "to_char(" . $tableAndFieldName . ", \\'HH24MISS\\')" . ' "' . $prop->name . '"';
                        } else {
                            $result[] = $tableAndFieldName . ' "' . $prop->name . '"';
                        }
                    } else if ($database == 'mssql') {
                        if ($prop->type == 'varchardate') {
                            $result[] = 'convert(varchar, ' . $tableAndFieldName . ', 121) as ' . $prop->name;
                        } else if ($prop->type == 'numeric' || $prop->type == 'bigautoincrement' || $prop->type == 'autoincrement') {
                            $result[] = 'convert(varchar, ' . $tableAndFieldName . ') as ' . $prop->name;
                        } else {
                            $result[] = $tableAndFieldName . ' ' . $prop->name;
                        }
                    } else if (($database == 'mysql') && in_array ($prop->type, array ('date', 'datetime', 'time'))) {
                        if ($prop->type == 'date') {
                            $result[] = "DATE_FORMAT(" . $tableAndFieldName . ", \\'%Y%m%d\\') " . $this->_quoteIdentifier ($prop->name, $database);
                        }else if ($prop->type == 'time') {
                            $result[] = "DATE_FORMAT(" . $tableAndFieldName . ", \\'%H%i%s\\') " . $this->_quoteIdentifier ($prop->name, $database);
                        }else if ($prop->type == 'datetime') {
                            $result[] = "DATE_FORMAT(" . $tableAndFieldName . ", \\'%Y%m%d%H%i%s\\') " . $this->_quoteIdentifier ($prop->name, $database);
                        }
                    } else if (($database == 'sqlite') && in_array ($prop->type, array ('date', 'datetime', 'time'))) {
                        if ($prop->type == 'date') {
                            $result[] = "strftime(\\'%Y%m%d\\', " . $tableAndFieldName.") " . $prop->name;
                        } else if ($prop->type == 'time') {
                            $result[] = "strftime(\\'%H%M%S\\', " . $tableAndFieldName.") " . $prop->name;
                        } else if ($prop->type == 'datetime') {
                            $result[] = "strftime(\\'%Y%m%d%H%M%S\\', " . $tableAndFieldName.") " . $prop->name;
                        }
                    } else if ($database == 'pgsql') {
                        $result[] = $tableAndFieldName . ' AS ' . $prop->name;
                    } else {
                        $result[] = $tableAndFieldName . ' ' . $this->_quoteIdentifier ($prop->name, $database);
                    }
                } else {
                    if ($database == 'mssql' && ($prop->type == 'numeric' || $prop->type == 'bigautoincrement' || $prop->type == 'autoincrement')) {
                        $result[] = 'convert(varchar, ' . $tableAndFieldName . ') as ' . $prop->fieldName;
                    } else if ($database == 'sqlite') {
                        $result[] = $tableAndFieldName . ' ' . $prop->name;
                    } else if ($database == 'oci' && in_array ($prop->type, array ('date', 'datetime', 'time'))) {
                        if ($prop->type == 'datetime') {
                            $result[] = "to_char(" . $tableAndFieldName . ", \\'YYYYMMDDHH24MISS\\')" . ' "' . $prop->fieldName . '"';
                        } else if ($prop->type == 'date') {
                            $result[] = "to_char(" . $tableAndFieldName . ", \\'YYYYMMDD\\')" . ' "' . $prop->fieldName . '"';
                        } else if ($prop->type == 'time') {
                            $result[] = "to_char(" . $tableAndFieldName . ", \\'HH24MISS\\')" . ' "' . $prop->fieldName . '"';
                        }
                    } else if (($database == 'mysql') && in_array ($prop->type, array ('date', 'datetime', 'time'))) {
                        if ($prop->type == 'date') {
                            $result[] = "DATE_FORMAT(" . $tableAndFieldName . ", \\'%Y%m%d\\') " . $this->_quoteIdentifier ($prop->fieldName, $database);
                        } else if ($prop->type == 'time') {
                            $result[] = "DATE_FORMAT(" . $tableAndFieldName . ", \\'%H%i%s\\') " . $this->_quoteIdentifier ($prop->fieldName, $database);
                        } else if ($prop->type == 'datetime') {
                            $result[] = "DATE_FORMAT(" . $tableAndFieldName . ", \\'%Y%m%d%H%i%s\\') " . $this->_quoteIdentifier ($prop->fieldName, $database);
                        }
                    } else if (($database == 'sqlite') && in_array ($prop->type, array ('date', 'datetime', 'time'))) {
                        if ($prop->type == 'date') {
                            $result[] = "strftime(\\'%Y%m%d\\', " . $tableAndFieldName . ") " . $prop->fieldName;
                        } else if ($prop->type == 'time') {
                            $result[] = "strftime(\\'%H%M%S\\', " . $tableAndFieldName . ") " . $prop->fieldName;
                        }else if ($prop->type == 'datetime') {
                            $result[] = "strftime(\\'%Y%m%d%H%M%S\\', " . $tableAndFieldName . ") " . $prop->fieldName;
                        }
                    } else {
                        $result[] = $tableAndFieldName;
                    }
                }
            } else {
                $result[] = sprintf ($prop->selectMotif, $tableAndFieldName) . ' ' . $prop->name;
            }

        }
        return 'SELECT ' . (implode (', ', $result));
    }

    /**
     * Génération des conditions supplémentaires dans l'optique où l'on va utiliser PDO
     *
     * @param array $pFields Champs des conditions
     * @param string $pPrefix Prefixe à appliquer aux noms des champs
     * @param boolean $pForSelect Si on veut générer des conditions pour un SELECT
     * @return array
     */
    private function _buildConditionsForNewDB (&$pFields, $pPrefix = '', $pForSelect = false) {
        $database = $this->_definition->getDatabase ();

        $array = array ();
        $sqlCondition = array ();

        foreach ($pFields as $field) {
            $fieldValue = '$' . $pPrefix . $field->name;
            $fieldVar = trim (':' . $field->table . '_'.$field->name);

            $fieldValueAssign = $fieldValue;
            $fieldVarAssign = $fieldVar;

            if (in_array ($field->type, array ('date', 'datetime', 'time'))) {
                if ($database === 'mysql' || $database === 'sqlite') {
                    switch ($field->type) {
                        case 'datetime':
                            $fieldValueAssign = 'CopixDateTime::yyyymmddhhiissToFormat (' . $fieldValue .", 'Y-m-d H:i:s')";
                            break;
                        case 'date':
                            $fieldValueAssign =  'CopixDateTime::yyyymmddToFormat (' . $fieldValue .", 'Y-m-d')";
                            break;
                        case 'time':
                            $fieldValueAssign = 'CopixDateTime::hhiissToFormat (' . $fieldValue .", 'H:i:s')";
                            break;
                    }
                }elseif ($database === 'oci') {
                    switch ($field->type) {
                        case 'datetime':
                            $fieldVarAssign = "to_date (" . $fieldVar . ", \\'YYYYMMDDHH24MISS\\')";
                            break;
                        case 'date':
                            $fieldVarAssign = "to_date (" . $fieldVar . ", \\'YYYYMMDD\\')";
                            break;
                        case 'time':
                            $fieldVarAssign = "to_date (" . $fieldVar . ", \\'HH24MISS\\')";
                            break;
                    }
                }
                //Ici il faut convertir la valeur
                $array[$fieldVar] = '(' . $fieldValue . ' === null ? "___COPIX___DELETE___ME___FROM____DAO___QUERIES___" : ' . $fieldValueAssign . ')';
                //ici il faut mettre le to_char pour OCI avant :tset_date_pk
                $sqlCondition[] = ($pForSelect ? $this->_quoteIdentifier ($field->table, $database) . '.' : '') . $this->_quoteIdentifier ($field->fieldName, $database) . " ' . ($fieldValue === null ? 'IS' : '=') . ' ' . ($fieldValue === null ? 'NULL' : '$fieldVarAssign') . ' ";
            }else {
                $array[$fieldVar] = '(' . $fieldValue . ' === null ? "___COPIX___DELETE___ME___FROM____DAO___QUERIES___" : ' . $fieldValue . ')';
                $sqlCondition[] = ($pForSelect ? $this->_quoteIdentifier ($field->table, $database) . '.' : '') . $this->_quoteIdentifier ($field->fieldName, $database) . " ' . ($fieldValue === null ? 'IS' : '=') . ' ' . ($fieldValue === null ? 'NULL' : '$fieldVar') . ' ";
            }
        }
        return array ($array, implode (' AND ', $sqlCondition));
    }

    /**
     * Retourne le champ autoincrementé
     *
     * @param array $pUsing Liste des champs où on va chercher l'autoincrement. Si null, va chercher la définition des champs par défaut.
     * @return CopixDAOPropertyDefinition
     */
    private function _getAutoIncrementField ($pUsing = null) {
        $result = array ();
        if ($pUsing === null) {
            //if no fields are provided, using _userDefinition's as default.
            $pUsing = $this->_definition->getProperties ();
        }

        $database = $this->_definition->getDatabase ();

        foreach ($pUsing as $id => $field) {
            if ($field->type == 'autoincrement' || $field->type == 'bigautoincrement') {
                if ($database == "pgsql" && !strlen ($field->sequenceName)) {
                    $field->sequenceName = $this->_definition->getPrimaryTableRealName () . "_" . $field->name . "_seq";
                }
                return $field;
            }
        }
        return null;
    }

    /**
     * Création d'une chaine de caractère à partir d'un tableau afin de l'insérer dans l'appel à une requête
     * Exemple :
     * Tableau source : $array[':champ'] = '$field->champ', $array[':champ2'] = '$field->champ2'
     * Génération obtenue : array (':champ' => $field->champ, ':champ2' => $field->champ2)
     *
     * @param array $arraySource Tableau des éléments que l'on souhaite transformer
     * @return string
     */
    private function _makeArrayParamsForQuery ($pArraySource) {
        $finalString = '$this->_dirtyClearNullValuesForSelectQueries (array (';
        $first = true;
        foreach ($pArraySource as $paramName => $paramStringValue) {
            if (!$first) {
                $finalString .= ', ';
            }
            $finalString .= "'" . $paramName . "'=>" . $paramStringValue;
            $first = false;
        }
        return $finalString . '))';
    }

    /**
     * Génération du code PHP pour la fonction check
     *
     * @return string
     */
    private function _generatePHP4Check () {
        $php = new CopixPHPGenerator ();

        // méthode pour récupérer les messages d'erreur
        $comments = array (
                'Retourne le texte du message d\'erreur',
                null,
                '@param string $pName Nom du champ',
                '@param mixed $pValue Valeur du champ',
                '@param int $pError Type de l\'erreur, utiliser les constantes CopixDAORecord::ERROR_X',
                '@param array $pExtras Informations supplémentaires sur l\'erreur'
        );
        $result = $php->getPHPDoc ($comments, 1);
        $result .= $php->getLine ('protected function _getError ($pName, $pCaption, $pValue, $pError, $pExtras = array ()) {', 1);
        $result .= $php->getLine ('$i18n = (isset ($pExtras[\'i18n\'])) ? $pExtras[\'i18n\'] : \'copix:dao.errors.\';', 2);
        $result .= $php->getLine ('switch ($pError) {', 2);
        $result .= $php->getLine ('case CopixDAORecord::ERROR_REQUIRED :', 3);
        $result .= $php->getLine ('$toReturn = _i18n ($i18n . \'required\', $pCaption);', 4);
        $result .= $php->getLine ('break;', 4);
        $result .= $php->getLine ('case CopixDAORecord::ERROR_FORMAT :', 3);
        $result .= $php->getLine ('$toReturn = _i18n ($i18n . \'format\', $pCaption);', 4);
        $result .= $php->getLine ('break;', 4);
        $result .= $php->getLine ('case CopixDAORecord::ERROR_SIZE_LIMIT :', 3);
        $result .= $php->getLine ('$toReturn = _i18n ($i18n . \'sizeLimit\', array ($pCaption, $pExtras[\'maxlength\']));', 4);
        $result .= $php->getLine ('break;', 4);
        $result .= $php->getLine ('case CopixDAORecord::ERROR_NUMERIC :', 3);
        $result .= $php->getLine ('$toReturn = _i18n ($i18n . \'numeric\', $pCaption);', 4);
        $result .= $php->getLine ('break;', 4);
        $result .= $php->getLine ('case CopixDAORecord::ERROR_DATE :', 3);
        $result .= $php->getLine ('$toReturn = _i18n ($i18n . \'date\', $pCaption);', 4);
        $result .= $php->getLine ('break;', 4);
        $result .= $php->getLine ('case CopixDAORecord::ERROR_TIME :', 3);
        $result .= $php->getLine ('$toReturn = _i18n ($i18n . \'time\', $pCaption);', 4);
        $result .= $php->getLine ('break;', 4);
        $result .= $php->getLine ('case CopixDAORecord::ERROR_DATE_FORMAT :', 3);
        $result .= $php->getLine ('$toReturn = _i18n ($i18n . \'yyyymmddhhiiss\', array ($pCaption, $pValue));', 4);
        $result .= $php->getLine ('break;', 4);
        $result .= $php->getLine ('default :', 3);
        $result .= $php->getLine ('$toReturn = \'UNKNOW ERROR\';', 4);
        $result .= $php->getLine ('break;', 4);
        $result .= $php->getLine ('}', 2);
        $result .= $php->getLine ('return $toReturn;', 2);
        $result .= $php->getLine ('}', 1);
        $result .= $php->getLine ();

        // méthode pour vérifier les données
        $comments = array (
                'Vérifie que les données contenues dans $pRecord sont compatibles avec la base de données',
                null,
                '@param CompiledDAORecord' . $this->_definition->getDAOId () . ' $pRecord Enregistrement dont on veut vérifier les données',
                '@return mixed True si l\'enregistrement est valide, array si il ne l\'est pas'
        );
        $result .= $php->getPHPDoc ($comments, 1);
        $result .= $php->getLine ('public function check ($pRecord) {', 1);
        $result .= $php->getLine ('$errorObject = new CopixErrorObject ();', 2);
        foreach ($this->_definition->getProperties () as $id => $field) {
            $caption = ($field->captionI18N !== null) ? '_i18n (\'' . $field->captionI18N . '\')' : '\'' . str_replace ("'", "\'", $field->caption) . '\'';
            $result .= $php->getEndLine ();
            $result .= $php->getLine ('// Tests pour le champ ' . $id, 2);


            //if required, add the test.
            if ($field->required && ($field->type != 'autoincrement' && $field->type != 'bigautoincrement')) {
                $result .= $php->getLine ('if ($pRecord->' . $field->name . ' === null) {', 2);
                $result .= $php->getLine ('$errorObject->addError (\'' . $field->name . '\', ' . $this->_generatePHPCall4GetError ($field->name, $caption, 'REQUIRED') . ');', 3);
                $result .= $php->getLine ('}', 2);
            }

            //if a regexp is given, check it....
            if ($field->regExp !== null) {
                $result .= $php->getLine ('if (strlen ($pRecord->' . $field->name . ') > 0) {', 2);
                $result .= $php->getLine ('if (preg_match (\'' . $field->regExp . '\', $pRecord->' . $field->name . ') === 0) {', 3);
                $result .= $php->getLine ('$errorObject->addError (\'' . $field->name . '\', ' . $this->_generatePHPCall4GetError ($field->name, $caption, 'FORMAT') . ');', 4);
                $result .= $php->getLine ('}', 3);
                $result .= $php->getLine ('}', 2);
            }

            //if a maxlength is given
            if ($field->maxlength !== null && (!in_array ($field->type, array ('date', 'varchardate', 'time', 'varchartime', 'datetime')))) {
                $result .= $php->getLine ('if (strlen ($pRecord->' . $field->name . ') > ' . intval ($field->maxlength) . ') {', 2);
                $result .= $php->getLine ('$errorObject->addError (\'' . $field->name . '\', ' . $this->_generatePHPCall4GetError ($field->name, $caption, 'SIZE_LIMIT', array ('maxlength' => intval ($field->maxlength))) . ');', 3);
                $result .= $php->getLine ('}', 2);
            }

            //if int or numeric, will check if it is really a numeric.
            if (in_array ($field->type, array ('numeric', 'int', 'integer'))) {
                $result .= $php->getLine ('if (strlen ($pRecord->' . $field->name . ') > 0 && !is_numeric ($pRecord->' . $field->name . ')) {', 2);
                $result .= $php->getLine ('$errorObject->addError (\'' . $field->name . '\', ' . $this->_generatePHPCall4GetError ($field->name, $caption, 'NUMERIC') . ');', 3);
                $result .= $php->getLine ('}', 2);
            }

            //if date, will check if the format is correct
            if (in_array ($field->type, array ('date', 'varchardate'))) {
                $result .= $php->getLine ('if (CopixDateTime::yyyymmddToDate ($pRecord->' . $field->name . ') === false) {', 2);
                $result .= $php->getLine ('$errorObject->addError (\'' . $field->name . '\', ' . $this->_generatePHPCall4GetError ($field->name, $caption, 'DATE') . ');', 3);
                $result .= $php->getLine ('}', 2);
            }

            //if time, will check if the format is correct
            if (in_array($field->type, array ('time', 'varchartime'))) {
                $result .= $php->getLine ('if (CopixDateTime::hhiissToTime ($pRecord->' . $field->name . ') === false) {', 2);
                $result .= $php->getLine ('$errorObject->addError (\'' . $field->name . '\', ' . $this->_generatePHPCall4GetError ($field->name, $caption, 'TIME') . ');', 3);
                $result .= $php->getLine ('}', 2);
            }

            if (in_array ($field->type, array ('datetime'))) {
                $result .= $php->getLine ('if (CopixDateTime::ISODateTimeToDateTime ($pRecord->' . $field->name.') === false && CopixDateTime::yyyymmddhhiisstodatetime ($pRecord->' . $field->name.') === false) {', 2);
                $result .= $php->getLine ('$errorObject->addError(\'' . $field->name . '\', ' . $this->_generatePHPCall4GetError ($field->name, $caption, 'DATE_FORMAT') . ');', 3);
                $result .= $php->getLine ('}', 2);
            }
        }

        $result .= $php->getEndLine ();
        $result .= $php->getLine ('return ($errorObject->isError ()) ? $errorObject->asArray () : true;', 2);
        $result .= $php->getLine ('}', 1);

        return $result;
    }

    /**
     * Retourne le code PHP pour l'appel à la méthode _getError
     *
     * @param string $pName Nom du champ
     * @param string $pCaption Libellé du champ, doit déja contenir les ' ' et les échappements
     * @param string $pError Type de l'erreur, n'indiquer que la partie après CopixDAORecord::ERROR_ (exemple : REQUIRED)
     * @param array $pExtras Informations supplémentaires
     * @return string
     */
    private function _generatePHPCall4GetError ($pName, $pCaption, $pError, $pExtras = array ()) {
        $toReturn = '$this->_getError (\'' . str_replace ("'", "\'", $pName) . '\', ' . $pCaption . ', $pRecord->' . $pName . ', CopixDAORecord::ERROR_' . $pError;
        if (count ($pExtras) > 0) {
            $toReturn .= ', ' . var_export ($pExtras, true);
        }
        $toReturn .= ')';
        return $toReturn;
    }

    /**
     * Génération du code PHP pour la fonction get
     *
     * @return string
     */
    private function _generatePHP4Get () {
        list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause ();
        $sqlSelectClause = $this->getSelectClause ();
        $pkFields = $this->_definition->getPropertiesBy ('PkFields');
        $php = new CopixPHPGenerator ();

        $comments = array ('Retourne un enregistrement en fonction de la clef primaire', null);
        foreach ($pkFields as $id => $infos) {
            $comments[] = '@param ' . $this->_getFieldTypeForPHPDoc ($infos->type) . ' $' . $id . ' Champ de la clef primaire';
        }
        $comments[] = '@return mixed ' . $this->_definition->getDAORecordName () . ' si un enregistrement a été trouvé, false sinon';
        $result = $php->getPHPDoc ($comments, 1);

        //Selection, get.
        $result .= $php->getLine ('public function get (' . $this->_writeFieldsInfoWith ('name', '$', '', ',', $pkFields) . ') {', 1);

        //condition on the PK
        list ($arSqlCondition, $sqlCondition) = $this->_buildConditionsForNewDB ($pkFields, '', true);
        $glueCondition = ($sqlWhereClause != '') ? ' AND ' : ' WHERE ';

        if ($sqlCondition != '') {
            $sqlCondition = (($sqlCondition == '') ? '' : $glueCondition) . $sqlCondition;
        }
        $result .= $php->getLine ('$query = $this->_selectQuery . \'' . $sqlCondition . '\';', 2);
        $result .= $php->getLine ('$results = new CopixDAORecordIterator (_doQuery ($query, ' . $this->_makeArrayParamsForQuery ($arSqlCondition) . ', $this->_connectionName), $this->getDAOId (), $this->_connectionName);', 2);
        $result .= $php->getLine ('return (isset ($results[0])) ? $results[0] : false;', 2);
        $result .= $php->getLine ('}', 1);

        return $result;
    }

    /**
     * Génération du code PHP pour la fonction findAll
     *
     * @param boolean $pUseIterator Si on veut utiliser un itérateur ou non
     * @return string
     */
    private function _generatePHP4FindAll ($pUseIterator) {
        if ($pUseIterator) {
            $methodBaseName = 'iFindAll';
            $queryMethod = 'iDoQuery';
            $comment = 'Retourne tous les enregistrements du DAO, utilise doQuery';
        } else {
            $methodBaseName = 'findAll';
            $queryMethod = 'doQuery';
            $comment = 'Retourne tous les enregistrements du DAO, utilise iDoQuery';
        }
        $php = new CopixPHPGenerator ();
        $result = $php->getPHPDoc (array ($comment, '', '@return CopixDAORecordIterator'), 1);

        //Selection, findAll.
        $result .= $php->getLine ('public function '.$methodBaseName. ' () {', 1);
        $result .= $php->getLine ('return new CopixDAORecordIterator (CopixDB::getConnection ($this->_connectionName)->' . $queryMethod . ' ($this->_selectQuery), $this->getDAOId (), $this->_connectionName);', 2);
        $result .= $php->getLine ('}', 1);
        return $result;
    }

    /**
     * Génération du code PHP pour la fonction insert
     *
     * @return string
     */
    private function _generatePHP4Insert () {
        $php = new CopixPHPGenerator ();
        $comments = array (
                'Ajoute un enregistrement dans la table principale et retourne le nombre d\'enregistrements insérés',
                null,
                '@param mixed $pObject Données à insérer, peut être de la forme array (\'champ\' => \'Valeur\') ou être un objet dont les propriétés publiques sont à insérer',
                '@param boolean $pUseId Indique si on veut préciser l\'identifiant dans la requête INSERT',
                '@return int'
        );
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('public function insert ($pObject, $pUseId = false) {', 1);
        $result .= $php->getLine ('if (is_array ($pObject)) {', 2);
        $result .= $php->getLine ('$tmpRecord = _record (\'' . $this->_definition->getDAOId () . '\');', 3);
        $result .= $php->getLine ('$tmpRecord->initFromDBObject ($pObject);', 3);
        $result .= $php->getLine ('$pObject = $tmpRecord;', 3);
        $result .= $php->getLine ('}', 2, 2);

        $result .= $php->getLine ('if (($checkResult = $this->check ($pObject)) !== true) {', 2);
        $result .= $php->getLine ('throw new CopixDAOCheckException ($checkResult, $pObject);', 3);
        $result .= $php->getLine ('}', 2, 2);

        $result .= $php->getLine ('$ct = CopixDB::getConnection ($this->_connectionName);', 2, 2);

        $database = $this->_definition->getDatabase ();

        $pkai = $this->_getAutoIncrementField ();
        if ($useSequence = (in_array ($database, array ('oci')) && ($pkai !== null) && ($pkai->sequenceName != ''))) {
            $result .= $php->getLine ('if (!$pUseId) {', 2);
            $result .= $php->getLine ('$pObject->' . $pkai->name . ' = $ct->lastId (\'' . $pkai->sequenceName . '\');', 3);
            $result .= $php->getLine ('}', 2, 2);
        }

        $fieldsNoAuto = $this->_definition->getPropertiesBy ('PrimaryFieldsExcludeAutoIncrement');
        $fields = $this->_definition->getPropertiesBy ('All');

        if ($pkai !== null && !$useSequence) {
            $result .= $php->getLine ('if (($pObject->' . $pkai->name .' !== null) && $pUseId) {', 2);
            $line = '$query = \'INSERT INTO ' . $this->_quoteIdentifier ($this->_definition->getPrimaryTableRealName (), $database) . ' (';
            list ($fields, $values, $formatted) = $this->_prepareValuesForNewDB ($fields, 'pObject->');
            $line .= implode (',', $fields);
            $line .= ') VALUES (';
            $line .= implode (', ', array_values ($formatted));
            $line .= ")';";
            $result .= $php->getLine ($line, 3);
            $result .= $php->getLine ('$toReturn = $ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery ($values) . ');', 3);
            $result .= $php->getLine ('} else {', 2);
            $line = '$query = \'INSERT INTO ' . $this->_quoteIdentifier ($this->_definition->getPrimaryTableRealName (), $database) . ' (';
            list ($fieldsNoAuto, $values, $formatted) = $this->_prepareValuesForNewDB ($fieldsNoAuto, 'pObject->');
            $line .= implode (',', $fieldsNoAuto);
            $line .= ') VALUES (';
            $line .= implode (', ', array_values ($formatted));
            $line .= ")';";
            $result .= $php->getLine ($line, 3);
            $result .= $php->getLine ('$toReturn = $ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery ($values) . ');', 3);
            $result .= $php->getLine ('}', 2);
        } else {
            $line = '$query = \'INSERT INTO ' . $this->_quoteIdentifier ($this->_definition->getPrimaryTableRealName (), $database) . ' (';
            list ($fields, $values, $formatted) = $this->_prepareValuesForNewDB ($fields, 'pObject->');
            $line .= implode (',', $fields);
            $line .= ') VALUES (';
            $line .= implode (', ', array_values ($formatted));
            $line .= ")';";
            $result .= $php->getLine ($line, 2);
            $result .= $php->getLine ('$toReturn = $ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery ($values) . ');', 2);
        }

        //return lastid after inserting for mysql
        if ($pkai !== null) {
            switch ($database) {
                case 'pgsql':
                    if ($pkai->sequenceName) {
                        $result .= $php->getLine ('if (!$pUseId) {', 2);
                        $result .= $php->getLine ('$pObject->' . $pkai->name . ' = $ct->lastId (\''.$pkai->sequenceName.'\');', 3);
                        $result .= $php->getLine ('}', 2);
                        break;
                    }

                case 'mysql':
                case 'mssql':
                case 'sqlite':
                    $result .= $php->getLine ('if (!$pUseId) {', 2);
                    $result .= $php->getLine ('$pObject->' . $pkai->name . ' = $ct->lastId ();', 3);
                    $result .= $php->getLine ('}', 2);
                    break;
            }
        }

        $result .= $php->getLine ('return $toReturn;', 2);
        $result .= $php->getLine ('}', 1);
        return $result;
    }

    /**
     * Génération du code PHP pour la fonction update
     *
     * @return string
     */
    private function _generatePHP4Update () {
        $database = $this->_definition->getDatabase ();
    	$pkFields = $this->_definition->getPropertiesBy ('PkFields');
        $versionFields = $this->_definition->getPropertiesBy ('Version');
        $conditionFields = array_merge ($pkFields, $versionFields);
        $php = new CopixPHPGenerator ();
        $comments = array (
                'Met à jour un enregistrement dans la table principale et retourne le nombre d\'enregistrements modifiés',
                null,
                '@param mixed $pObject Données à modifier, peut être de la forme array (\'champ\' => \'Valeur\') ou être un objet dont les propriétés publiques sont à modifier',
                '@return int'
        );
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('public function update ($pObject) {', 1);
        $result .= $php->getLine ('if (is_array ($pObject)) {', 2);
        $result .= $php->getLine ('$tmpRecord = _record (\'' . $this->_definition->getDAOId () . '\');', 3);
        $result .= $php->getLine ('$tmpRecord->initFromDBObject ($pObject);', 3);
        $result .= $php->getLine ('$pObject = $tmpRecord;', 3);
        $result .= $php->getLine ('}', 2);
        $result .= $php->getLine ('if (($checkResult = $this->check ($pObject)) !== true) {', 2);
        $result .= $php->getLine ('throw new CopixDAOCheckException ($checkResult, $pObject);', 3);
        $result .= $php->getLine ('}', 2, 2);

        $result .= $php->getLine ('$ct = CopixDB::getConnection ($this->_connectionName);', 2);
        $line = '$query = \'UPDATE ' . $this->_quoteIdentifier ($this->_definition->getPrimaryTableRealName (), $database) . ' SET ';
        list ($fields, $values, $formatted) = $this->_prepareValuesForNewDb ($this->_definition->getPropertiesBy ('PrimaryFieldsExcludePk'), 'pObject->');
        $sqlSet = '';

        $arSqlFields = array_values ($formatted);
        foreach (array_values ($fields) as $key => $fieldName) {
            $sqlSet .= ', ' . $fieldName . ' = ' . $arSqlFields[$key];
        }
        $line .= substr ($sqlSet, 1);

        //condition on the PK
        list ($arSqlCondition, $sqlCondition) = $this->_buildConditionsForNewDB ($pkFields, 'pObject->');
        if ($sqlCondition != '') {
            $line .= ' WHERE ' . $sqlCondition;
        }

        $line .= "';";
        $result .= $php->getLine ($line, 2);
        $result .= $php->getLine ('$affectedRows = $ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery (array_merge ($values, $arSqlCondition)) . ');', 2);
        if (count ($versionFields) > 0) {
            $result .= $php->getLine ('if ($affectedRows === 0) {', 2);
            $result .= $php->getLine ('throw new CopixDAOVersionException ($pObject);', 3);
            $result .= $php->getLine ('}', 2);
        }
        foreach ($versionFields as $versionField) {
            $result .= $php->getLine ('$pObject->' . $versionField->name . ' = $pObject->' . $versionField->name . ' + 1;', 2);
        }
        $result .= $php->getLine ('return $affectedRows;', 2);
        $result .= $php->getLine ('}', 1);
        return $result;
    }

    /**
     * Génération du code PHP pour la fonction delete
     *
     * @return string
     */
    private function _generatePHP4Delete () {
        $database = $this->_definition->getDatabase ();    	
        $pkFields = $this->_definition->getPropertiesBy ('PkFields');
        $php = new CopixPHPGenerator ();
        $comments = array ('Supprime un enregistrement dans la table principale et retourne le nombre d\'enregistrements supprimés', null);
        foreach ($pkFields as $id => $infos) {
            $comments[] = '@param ' . $this->_getFieldTypeForPHPDoc ($infos->type) . ' $' . $id . ' Champ de la clef primaire';
        }
        $comments[] = '@return int';
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('public function delete (' . $this->_writeFieldsInfoWith ('name', '$', '', ',', $pkFields) . ') {', 1);
        $result .= $php->getLine ('$ct = CopixDB::getConnection ($this->_connectionName);', 2);
        $line = '$query = \'DELETE FROM ' . $this->_quoteIdentifier ($this->_definition->getPrimaryTableRealName (), $database) . ' WHERE ';
        list ($arSqlCondition, $sqlCondition) = $this->_buildConditionsForNewDB ($pkFields);
        $line .= $sqlCondition;
        $line .= "';";
        $result .= $php->getLine ($line, 2);
        $result .= $php->getLine ('return $ct->doQuery ($query, ' . $this->_makeArrayParamsForQuery ($arSqlCondition) . ');', 2);
        $result .= $php->getLine ('}', 1);
        return $result;
    }

    /**
     * Génération du code PHP pour la fonction deleteBy
     *
     * @return string
     */
    private function _generatePHP4DeleteBy () {
        $database = $this->_definition->getDatabase ();
    	list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause ();
        $php = new CopixPHPGenerator ();
        $comments = array (
                'Supprime des enregistrements dans la table principale suivant les conditions demandées, et retourne le nombre d\'enregistrements supprimés',
                null,
                '@param CopixDAOSearchParams $pSearchParams Paramètres de recherche des enregistrements à supprimer',
                '@return int'
        );
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('public function deleteBy (CopixDAOSearchParams $pSearchParams) {', 1);
        $result .= $php->getLine ('$ct = CopixDB::getConnection ($this->_connectionName);', 2);
        $result .= $php->getLine ('$query = \'DELETE FROM ' . $this->_quoteIdentifier ($this->_definition->getPrimaryTableRealName (), $database). '\';', 2);
        $result .= $php->getLine ('$params = array ();', 2);
        //les conditions du By de la mthode deleteBy.
        $result .= $php->getLine ('if (!$pSearchParams->isEmpty ()) {', 2);
        $result .= $php->getLine ('$query .= \' WHERE \';', 3);

        //gnration des paramtres de la mthode explain
        $fieldsType = array ();
        $fieldsTranslation = array ();

        foreach ($this->_definition->getProperties () as $name => $field) {
            //ajout pour appliquer une method
            $method = '';
            if ($field->method !== null) {
                $method = '\',\'' . $field->method;
            }
            $fieldsTranslation[] = '\'' . $field->name . '\' => array (\'' . $field->fieldName . '\', \'' . $field->type . '\',\'' . $field->table . '\',\'' . str_replace ("'", "\\'", $field->selectMotif) . $method. '\', null, \''.$this->_quoteIdentifier ($field->fieldName, $database).'\', \''.$this->_quoteIdentifier ($field->table, $database).'\')';
        }
        $fieldsTranslation = 'array (' . implode (', ', $fieldsTranslation) . ')';

        //fin de la requete
        $result .= $php->getLine ('list ($querySql, $params) = $pSearchParams->explainSQL (', 3);
        $result .= $php->getLine ($fieldsTranslation . ',', 4);
        $result .= $php->getLine ('$ct', 4);
        $result .= $php->getLine (');', 3);
        $result .= $php->getLine ('$query .= $querySql;', 3);
        $result .= $php->getLine ('}', 2);
        $result .= $php->getLine ('return $ct->doQuery ($query, $params);', 2);
        $result .= $php->getLine ('}', 1);
        return $result;
    }

    /**
     * Génération du code PHP pour la fonction countBy
     *
     * @return string
     */
    private function _generatePHP4CountBy () {
        $database = $this->_definition->getDatabase ();
    	list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause ();
        $php = new CopixPHPGenerator ();
        $comments = array (
                'Compte le nombre d\'enregistrements qui respectent les conditions demandées',
                null,
                '@param CopixDAOSearchParams $pSearchParams Paramètres de recherche des enregistrements',
                '@return int'
        );
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('public function countBy (CopixDAOSearchParams $pSearchParams) {', 1);

        $result .= $php->getLine ('$ct = CopixDB::getConnection ($this->_connectionName);', 2);
        $result .= $php->getLine ('$query = \'SELECT COUNT(*) AS "COUNT" ' . $sqlFromClause . $sqlWhereClause . '\';', 2);
        $result .= $php->getLine ('$params = array ();', 2);
        //les conditions du By de la mthode deleteBy.
        $result .= $php->getLine ('if (!$pSearchParams->isEmpty ()) {', 2);
        $result .= $php->getLine ('$query .= \'' . ($sqlWhereClause != '' ? ' AND ' : ' WHERE ') . '\';', 3);

        //gnration des paramtres de la mthode explain
        $fieldsType = array ();
        $fieldsTranslation = array ();

        foreach ($this->_definition->getProperties () as $name => $field) {
            //ajout pour appliquer une method
            $method = '';
            if ($field->method !== null) {
                $method = '\',\'' . $field->method;
            }
            $fieldsTranslation[] = '\'' . $field->name . '\'=>array(\'' . $field->fieldName . '\', \'' . $field->type . '\',\'' . $field->table . '\',\'' . str_replace ("'", "\\'", $field->selectMotif) . $method. '\', null, \''.$this->_quoteIdentifier ($field->fieldName, $database).'\', \''.$this->_quoteIdentifier ($field->table, $database).'\')';
        }
        $fieldsTranslation = 'array (' . implode (', ', $fieldsTranslation) . ')';

        //fin de la requete
        $result .= $php->getLine ('list ($querySql, $params) = $pSearchParams->explainSQL (', 3);
        $result .= $php->getLine ($fieldsTranslation . ',', 4);
        $result .= $php->getLine ('$ct', 4);
        $result .= $php->getLine (');', 3);
        $result .= $php->getLine ('$query .= $querySql;', 3);
        $result .= $php->getLine ('}', 2);
        $result .= $php->getLine ('$result = $ct->doQuery ($query, $params);', 2);
        $result .= $php->getLine ('return $result[0]->COUNT;', 2);
        $result .= $php->getLine ('}', 1);
        return $result;
    }

    /**
     * Génération du code PHP pour la fonction findBy
     *
     * @return string
     */
    private function _generatePHP4FindBy ($pUseIterator) {
        $database = $this->_definition->getDatabase ();
    	if ($pUseIterator) {
            $methodBaseName = 'iFindBy';
            $queryMethod = 'iDoQuery';
            $comment = 'Retourne des enregistrements suivant les conditions demandées, utilise iDoQuery';
        }else {
            $methodBaseName = 'findBy';
            $queryMethod = 'doQuery';
            $comment = 'Retourne des enregistrements suivant les conditions demandées, utilise doQuery';
        }
        $php = new CopixPHPGenerator ();
        $comments = array (
                $comment,
                null,
                '@param CopixDAOSearchParams $pSearchParams Paramètres de recherche',
                '@param array $pJoins Jointures à effectuer avec d\'autres tables, forme array (\'table\' => array (\'champ1\', \'=\', \'champ2\'))',
                '@return CopixDAORecordIterator'
        );
        $result = $php->getPHPDoc ($comments, 1);

        list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause ();

        $result .= $php->getLine ('public function '.$methodBaseName. ' (CopixDAOSearchParams $pSearchParams, $pJoins = array ()) {', 1);
        $result .= $php->getLine ('$ct = CopixDB::getConnection ($this->_connectionName);', 2);
        $result .= $php->getLine ('$query = $this->_selectQuery;', 2);
        $result .= $php->getLine ('$params = array ();', 2, 2);

        //generation de jointure
        $result .= $php->getLine ('if (count ($pJoins)) {', 2);
        $result .= $php->getLine ('$query = preg_replace (\'/SELECT (.*?) FROM /\', \'SELECT * FROM \', $query);', 3);
        $result .= $php->getLine ('foreach ($pJoins as $table => $join) {', 3);
        $result .= $php->getLine ('$query .= \' LEFT JOIN \' . $table . \' ON \' . $join[0] . $join[1] . $join[2] . \' \';', 4);
        $result .= $php->getLine ('}', 3);
        $result .= $php->getLine ('}', 2);

        //les conditions du By de la mthode findBy.
        $result .= $php->getLine ('if (!$pSearchParams->isEmpty ()) {', 2);
        $result .= $php->getLine ('$query .= \'' . ($sqlWhereClause != '' ? ' AND ' : ' WHERE ') . '\';', 3);

        //gnration des paramètres de la méthode explain
        $fieldsType = array ();
        $fieldsTranslation = array ();

        foreach ($this->_definition->getProperties () as $name => $field) {
            //ajout pour appliquer une method
            $method = '';
            if ($field->method !== null) {
                $method = '\',\'' . $field->method;
            }
            $fieldsTranslation[] = '\'' . $field->name . '\' => array (\'' . $field->fieldName . '\', \'' . $field->type . '\',\'' . $field->table . '\',\'' . str_replace ("'", "\\'", $field->selectMotif) . $method. '\', null, \''.$this->_quoteIdentifier ($field->fieldName, $database).'\', \''.$this->_quoteIdentifier ($field->table, $database).'\')';
        }
        $fieldsTranslation = 'array (' . implode (', ', $fieldsTranslation) . ')';

        //fin de la requete
        $result .= $php->getLine ('list ($querySql, $params) = $pSearchParams->explainSQL (', 3);
        $result .= $php->getLine ($fieldsTranslation . ',', 4);
        $result .= $php->getLine ('$ct', 4);
        $result .= $php->getLine (');', 3);
        $result .= $php->getLine ('$query .= $querySql;', 3);
        $result .= $php->getLine ('}', 2);

        $result .= $php->getLine ('if (count ($pJoins)) {', 2);
        $result .= $php->getLine ('return $ct->' . $queryMethod.' ($query, $params, $pSearchParams->getOffset (), $pSearchParams->getCount ());', 3);
        $result .= $php->getLine ('}', 2, 2);

        $result .= $php->getLine ('return new CopixDAORecordIterator ($ct->doQuery ($query, $params, $pSearchParams->getOffset (), $pSearchParams->getCount ()), $this->getDAOId (), $this->_connectionName);', 2);
        $result .= $php->getLine ('}', 1);

        return $result;
    }
    
    /**
     * Génération du code PHP pour la fonction updateBy
     *
     * @return string
     */
    private function _generatePHP4UpdateBy () {
        $database = $this->_definition->getDatabase ();

		$methodBaseName = 'updateBy';
		$queryMethod = 'doQuery';
		$comment = 'Retourne des enregistrements suivant les conditions demandées, utilise doQuery';

        $php = new CopixPHPGenerator ();
        $comments = array (
                $comment,
                null,
                '@param CopixDAOSearchParams $pSearchParams Paramètres de recherche',
                '@param array $pJoins Jointures à effectuer avec d\'autres tables, forme array (\'table\' => array (\'champ1\', \'=\', \'champ2\'))',
                '@return CopixDAORecordIterator'
        );
        $result = $php->getPHPDoc ($comments, 1);

        list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause ();

        $result .= $php->getLine ('public function '.$methodBaseName. ' ($pToUpdate, CopixDAOSearchParams $pSearchParams) {', 1);
        $result .= $php->getLine ('$ct = CopixDB::getConnection ($this->_connectionName);', 2);
        $result .= $php->getLine ('$query = $this->_selectQuery;', 2);
        $result .= $php->getLine ('$params = array ();', 2, 2);

        //les conditions du By de la méthode findBy.
        $result .= $php->getLine ('if (!$pSearchParams->isEmpty ()) {', 2);
        $result .= $php->getLine ('$query .= \'' . ($sqlWhereClause != '' ? ' AND ' : ' WHERE ') . '\';', 3);

        //gnration des paramètres de la méthode explain
        $fieldsType = array ();
        $fieldsTranslation = array ();

        foreach ($this->_definition->getProperties () as $name => $field) {
            //ajout pour appliquer une method
            $method = '';
            if ($field->method !== null) {
                $method = '\',\'' . $field->method;
            }
            $fieldsTranslation[] = '\'' . $field->name . '\' => array (\'' . $field->fieldName . '\', \'' . $field->type . '\',\'' . $field->table . '\',\'' . str_replace ("'", "\\'", $field->selectMotif) . $method. '\', null, \''.$this->_quoteIdentifier ($field->fieldName, $database).'\', \''.$this->_quoteIdentifier ($field->table, $database).'\')';
        }
        $fieldsTranslation = 'array (' . implode (', ', $fieldsTranslation) . ')';

        //fin de la requete
        $result .= $php->getLine ('list ($querySql, $params) = $pSearchParams->explainSQL (', 3);
        //On en profite pour affecter la variable knownFields qui resservira par la suite dans l'update
        $result .= $php->getLine ('$knownFields = '.$fieldsTranslation . ',', 4);
        $result .= $php->getLine ('$ct', 4);
        $result .= $php->getLine (');', 3);
        $result .= $php->getLine ('$query .= $querySql;', 3);
        $result .= $php->getLine ('}', 2);

        //Vérification des données à mettre à jour en utilisant la méthode check de chaque record
        $result .= $php->getLine ('$results = new CopixDAORecordIterator ($ct->doQuery ($query, $params, $pSearchParams->getOffset (), $pSearchParams->getCount ()), $this->getDAOId (), $this->_connectionName);', 2);
        $result .= $php->getLine ('foreach ($results as $record){', 2);
        $result .= $php->getLine ('$errors = array ();', 3); 
        foreach ($this->_definition->getProperties () as $field) {
        	$result .= $php->getLine ('if (array_key_exists (\''.$field->name.'\', $pToUpdate)){', 3);
        	$result .= $php->getLine ('   $record->'.$field->name.'= $pToUpdate[\''.$field->name.'\'];', 3);
        	$result .= $php->getLine ('}', 3);
        }
        $result .= $php->getLine ('if (($result = '.$this->_definition->getDAOName ().'::instance ()->check ($record)) !== true){', 3);
        $result .= $php->getLine ('   $errors[] = $result;', 3);
        $result .= $php->getLine ('}', 3);
        
        $result .= $php->getLine ('if (count ($errors)){', 3);
        $result .= $php->getLine ('   return $errors;', 3);
        $result .= $php->getLine ('}', 3);

        //On va maintenant mettre à jour les données 
        $result .= $php->getLine ('$query = \'UPDATE ' . $this->_quoteIdentifier ($this->_definition->getPrimaryTableRealName (), $database) . ' SET \';', 3);
        $result .= $php->getLine ('$sqlSet = "";', 3);
        $result .= $php->getLine ('foreach ($pToUpdate as $field=>$value){', 3);
        $result .= $php->getLine ('   if (array_key_exists ($field, $knownFields)){', 3);
        $result .= $php->getLine ('      $sqlSet .= \', \'.$knownFields[$field][5] . \'= :update_\'.$field; ', 3);
        $result .= $php->getLine ('      $updateValues[\':update_\'.$field] = $value;', 3); 
        $result .= $php->getLine ('   }', 3);
        $result .= $php->getLine ('}', 3);
        
        $result .= $php->getLine ('$sqlSet = substr ($sqlSet, 1);', 2);
        $result .= $php->getLine ('$query .= $sqlSet;', 2);
        
        //maintenant on s'occupe du where.... du update
        $result .= $php->getLine ('if (!$pSearchParams->isEmpty ()) {', 2);
        $result .= $php->getLine ('$query .= \' WHERE \';', 2);

        //fin de la requete
        $result .= $php->getLine ('list ($querySql, $params) = $pSearchParams->explainSQL (', 3);
        //On en profite pour affecter la variable knownFields qui resservira par la suite dans l'update
        $result .= $php->getLine ('$knownFields,', 4);
        $result .= $php->getLine ('$ct', 4);
        $result .= $php->getLine (');', 3);
        $result .= $php->getLine ('$query .= $querySql;', 3);
        $result .= $php->getLine ('}', 2);
        
        $result .= $php->getLine ('return $ct->doQuery ($query, array_merge ($updateValues, $params));');

        //Il faut maintenant prendre en charge le "where"
        $result .= $php->getLine ('}', 2);
        $result .= $php->getLine ('}', 1);

        return $result;
    }

    /**
     * Génération du code des méthodes personnalisées
     *
     * @return string
     */
    private function _generatePHP4DefinedMethods () {
        $database = $this->_definition->getDatabase ();
    	list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause ();

        $result = '';
        // autres méthodes personnaliss
        $allField = array ();
        foreach ($this->_definition->getPropertiesBy ('All') as $field) {
            $allField[$field->name] = array (
                    $field->fieldName,
                    $field->type,
                    $field->table,
                    str_replace ("'", "\\'", $field->selectMotif)
            );
        }
        $primaryFields = array ();
        // pour delete
        foreach ($this->_definition->getPropertiesBy ('PrimaryTable') as $field) {
            $primaryFields[$field->name] = array (
                    $field->fieldName,
                    $field->type,
                    '',
                    str_replace("'", "\\'",	$field->selectMotif)
            );
        }
        $ct = null;

        foreach ($this->_definition->getMethods () as $name => $method) {
            $result .= ' function ' . $method->name . ' (';
            $mparam = implode (', $', $method->getParameters ());
            if ($mparam != '') {
                $result .= '$' . $mparam;
            }
            $result .= ") {\n";
            $result .= '    $ct = CopixDB::getConnection ($this->_connectionName);' . "\n";
            $limit = '';

            switch ($method->type) {
                case 'delete' :
                    $result .= '    $query = \'DELETE FROM ' . $this->_quoteIdentifier ($this->_definition->getPrimaryTableRealName (), $database) . ' \'';
                    $glueCondition = ' WHERE ';
                    break;
                case 'update' :
                    $result .= '    $query = \'UPDATE ' . $this->_quoteIdentifier ($this->_definition->getPrimaryTableRealName (), $database) . ' SET ';
                    $updatefields = $this->_definition->getPropertiesBy ('PrimaryFieldsExcludePk');
                    $sqlSet = '';
                    foreach ($method->_values as $propname => $value) {
                        $sqlSet .= ', ' . $updatefields[$propname]->fieldName . '= ' . $value;
                    }
                    $result .= substr ($sqlSet, 1) . ' \'';

                    $glueCondition = ' WHERE ';
                    break;
                case 'selectfirst' :
                case 'select' :
                default :
                    $result .= '    $query = $this->_selectQuery';
                    $glueCondition = ($sqlWhereClause != '' ? ' AND ' : ' WHERE ');
                    if ($method->getLimit () !== null) {
                        $arrLimit = $method->getLimit();
                        $limit = ', array (), ' . $arrLimit['offset'] . ', ' . $arrLimit['count'];
                    }
                    break;
            }

            if ($method->getSearchParams () !== null) {
                if ($method->type == 'delete' || $method->type == 'update') {
                    $sqlCondition = trim ($method->getSearchParams ()->explainPHPSQL ($primaryFields, $ct));
                } else {
                    $sqlCondition = trim ($method->getSearchParams ()->explainPHPSQL ($allField, $ct));
                }

                if (trim($sqlCondition) != '') {
                    $result .= '.\'' . $glueCondition . $sqlCondition . "';\n";
                } else {
                    $result .= ";\n";
                }
            } else {
                $result .= ";\n";
            }

            switch ($method->type) {
                case 'delete' :
                case 'update' :
                    $result .= '    return $ct->doQuery ($query);' . "\n";
                    break;
                case 'selectfirst' :
                    $result .= '    $results = new CopixDAORecordIterator ($ct->doQuery ($query), $this->getDAOId (), $this->_connectionName);';
                    $result .= '    if (isset ($results[0])){return $results[0];}else{return false;}';
                    break;
                case 'select' :
                default :
                    $result .= '    return new CopixDAORecordIterator ($ct->doQuery ($query' . $limit . '), $this->getDAOId (), $this->_connectionName);' . "\n";
            }
            $result .= " }\n";
        }
        return $result;
    }

    /**
     * Génération du code PHP pour le constructeur du DAO
     *
     * @return string
     */
    private function _generatePHP4DAOConstructor () {
        list ($sqlFromClause, $sqlWhereClause) = $this->getFromClause ();
        $sqlSelectClause = $this->getSelectClause ();
        $php = new CopixPHPGenerator ();
        $comments = array ('Constructeur', null, '@param string $pConnectionName Nom du profil de connexion');
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('public function __construct ($pConnectionName = null) {', 1);
        //ne remplace que si on spécifie une connexion à utiliser
        $result .= $php->getLine ('if ($pConnectionName != null) {', 2);
        $result .= $php->getLine ('$this->_connectionName = $pConnectionName;', 3);
        $result .= $php->getLine ('}', 2);
        $result .= $php->getLine ('$this->_selectQuery =\'' . $sqlSelectClause . $sqlFromClause . $sqlWhereClause . '\';', 2);
        $result .= $php->getLine ('}', 1);

        return $result;
    }

    /**
     * Génération de la méthode singleton
     *
     * @return string
     */
    private function _generatePHP4Instance () {
        $php = new CopixPHPGenerator ();
        $comments = array ('Singleton', null, '@return '.$this->_definition->getDAOName ());
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('private static $_instance = array ();', 1);
        $result .= $php->getLine ('public static function instance ($pConnectionName = null) {', 1);
        $result .= $php->getLine ('   $pConnectionName = $pConnectionName === null ? "" : $pConnectionName;', 1);
        $result .= $php->getLine ('   if (! array_key_exists ($pConnectionName, self::$_instance)){', 1);
        $result .= $php->getLine ('      self::$_instance[$pConnectionName] = self::create ($pConnectionName);', 1);
        $result .= $php->getLine ('   }', 1);
        $result .= $php->getLine ('   return self::$_instance[$pConnectionName];', 1);
        $result .= $php->getLine ('}', 1);

        return $result;
    }

    /**
     * Génération de la méthode de création de la dao
     */
    private function _generatePHP4Create () {
        $php = new CopixPHPGenerator ();
        $comments = array ('Création', null, '@return '.$this->_definition->getDAOName ());
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('public static function create ($pConnectionName = null) {', 1);
        $result .= $php->getLine ('   return new '.$this->_definition->getDAOName (). '($pConnectionName);', 1);
        $result .= $php->getLine ('}', 1);

        return $result;
    }

    /**
     * Génération du code PHP pour la fonction de description des champs
     *
     * @return array
     */
    private function _generatePHP4DAODescribeField () {
        $fields = $this->_definition->getPropertiesBy ('All');
        $php = new CopixPHPGenerator ();
        $comments = array (
                'Retourne la description des champs du DAO',
                null,
                '@return array'
        );
        $result = $php->getPHPDoc ($comments, 1);

        $result .= $php->getLine ('public function getFieldsDescription () {', 1);
        $result .= $php->getLine ($php->getVariableDeclaration ('$fields', $fields), 2);
        $result .= $php->getLine ('return $fields;', 2);
        $result .= $php->getLine ('}', 1);

        return $result;
    }

    /**
     * Génération du code PHP pour la récupération de l'ID du DAO
     *
     * @return string
     */
    private function _generatePHP4GetDAOId () {
        $daoId = $this->_definition->getDAOId ();
        $php = new CopixPHPGenerator ();

        $comments = array (
                'Retourne l\'identifiant du DAO qui utilise ce record',
                null,
                '@return string'
        );
        $result = $php->getPHPDoc ($comments, 1);
        $result .= $php->getLine ('public function getDAOId () {', 1);
        $result .= $php->getVariableReturn ($daoId, 2);
        $result .= $php->getLine ('}', 1);

        return $result;
    }

    /**
     * Retourne le code PHP pour la méthode getPrimaryKey
     *
     * @return string
     */
    private function _generatePHP4GetPrimaryKey () {
        $php = new CopixPHPGenerator ();

        $comments = array (
                'Retourne la liste des clefs primaires',
                null,
                '@return CopixDAOPropertyDefinition[]'
        );
        $result = $php->getPHPDoc ($comments, 1);
        $result .= $php->getLine ('public function getPrimaryKey () {', 1);
        $pk = array ();
        foreach ($pkFields = $this->_definition->getPropertiesBy ('PkFields') as $name => $field) {
            $pk[] = '\'' . $name . '\'';
        }
        $result .= $php->getLine ('return array (' . implode (', ', $pk) . ');', 2);
        $result .= $php->getLine ('}', 1);
        return $result;
    }

    /**
     * Retourne le type d'une variable PHP compatible PHPDoc depuis le type d'un champ d'un DAO
     *
     * @param string $pType Type du champ d'un DAO
     * @return string
     */
    private function _getFieldTypeForPHPDoc ($pType) {
        return (in_array ($pType, array ('numeric', 'int', 'integer', 'autoincrement', 'bigautoincrement'))) ? 'int' : 'string';
    }

    /**
     * Retourne le code de la DAO "utilisateur"
     *
     * @string
     */
    public function getPHPCode4UserDao () {
        if ($this->_definition->getUserDAOFilePath () !== null) {
            return CopixFile::read ($this->_definition->getUserDAOFilePath ());
        }
        $php = new CopixPHPGenerator ();
        $result  = 'class '.$this->_definition->getDAOName (). ' extends Compiled'.$this->_definition->getDAOName (). '{';
        $result .= '}';
        return $php->getPHPTags ($result);
    }

    /**
     * Retourne le code du record "utilisateur"
     *
     * @return string
     */
    public function getPHPCode4UserDaoRecord () {
        if ($this->_definition->getUserDAORecordFilePath () !== null) {
            return CopixFile::read ($this->_definition->getUserDAORecordFilePath ());
        }
        $php = new CopixPHPGenerator ();
        $result = 'class '.$this->_definition->getDAORecordName (). ' extends Compiled'.$this->_definition->getDAORecordName (). '{';
        $result .= '}';
        return $php->getPHPTags ($result);
    }
    
    /**
     * Quote les identifiants en fonction de la base de données.
     *
     * @param string $pId le nom de l'identifier à utiliser dans la requête.
     * @param string $pDatabase le nom de la base de données.
     */
    private function _quoteIdentifier ($pId, $pDatabase){
    	if ($pDatabase === 'mysql'){
    		return '`'.$pId.'`';    		
    	}
    	return $pId;
    }
}