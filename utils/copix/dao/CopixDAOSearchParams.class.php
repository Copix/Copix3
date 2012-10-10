<?php
/**
 * @package	copix
 * @subpackage	dao
 * @author	Croës Gérald
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Structure stockant les paramètres d'une condition pour un DAO
 *
 * @package		copix
 * @subpackage	dao
 */
class CopixDAOSearchParamsCondition {
    /**
     * Condition parente
     *
     * @var CopixDAOSearchParams
     */
    public $parent = null;

    /**
     * Les conditions
     *
     * @var array
     */
    public $conditions = array ();

    /**
     * Les sous-groupes
     *
     * @var array
     */
    public $group = array ();

    /**
     * Le type de groupe (AND / OR)
     *
     * @var string
     */
    public $kind;

    /**
     * Construction du groupe
     *
     * @param CopixDAOSearchParamsCondition $pParent Le groupe parent
     * @param string $pKind OR ou AND, si le groupe est régie par un OR ou AND
     */
    public function __construct ($pParent, $pKind) {
        if (strtolower (get_class ($pParent)) == strtolower ('copixdaosearchparamscondition')) {
            $this->parent = $pParent;
        }
        $this->kind = $pKind;
    }

    /**
     * Indique si le groupe de condition est vide
     *
     * @return bool
     */
    public function isEmpty () {
        $toReturn = true;
        foreach ($this->conditions as $condition) {
            if (array_key_exists ('sql', $condition) && (strlen ($condition['sql']) > 0)) {
                return false;
            } else if (array_key_exists ('value', $condition)) {
                return false;
            }
        }
        foreach ($this->group as $group) {
            if (!$group->isEmpty ()) {
                return false;
            }
        }
        return $toReturn;
    }
}

/**
 * Gestion des critères de recherche. Permet d'effectuer des recherches dans un DAO précis, en indiquant les critères. Voir la méthode findBy des objets DAO générés
 *
 * @package		copix
 * @subpackage	dao
 */
class CopixDAOSearchParams {
    /**
     * Informations sur la condition
     *
     * @var CopixDAOSearchParamsCondition
     */
    public $condition;

    /**
     * Groupes de condition demandés
     *
     * @var array
     */
    public $groupby = array ();

    /**
     * Ordre de tri
     */
    public $order = array ();

    /**
     * Liste des champs à récupérer
     *
     * @var array
     */
    public $fields = array ();

    /**
     * Condition en cours de parcours
     *
     * @var CopixDAOSearchParamsCondition
     */
    private $_currentCondition = null;

    /**
     * Offset à partir duquel on souhaites récupérer les enregistrements.
     * Ce paramètre n'est pas pris en compte dans explainSQL mais sert juste de transport d'info
     * Null si rien demandé.
     *
     * @var int
     */
    private $_offset = null;

    /**
     * Nombre d'enregistrements que l'on souhaite ramener.
     * Ce paramètre n'est pas pris en compte dans explainSQL mais sert juste de transport d'info
     * Null si rien demandé.
     *
     * @var int
     */
    private $_count = null;

    /**
     * Variable interne qui nous sert à nous assurer d'avoir des noms uniques pour les variables bindées.
     * Ceci est pratique lorsque les DAOSearchConditions disposent de plusieurs valeurs possibles sur un même champs
     *
     * @var int
     */
    private static $_countParam = 0;

    /**
     * Tableau des variables utilisées
     *
     * @var array
     */
    private $_assigned_variables = array ();

    /**
     * Constructeur
     *
     * @param string $pType Type de condition, AND ou OR
     */
    public function __construct ($pType = 'AND') {
        $this->condition = new CopixDAOSearchParamsCondition ($this, $pType);
        $this->_currentCondition = $this->condition;
    }

    /**
     * Compresse une variable à X caractères à partir de son nom d'origine et s'assure de l'unicité de son utilisation
     *
     * @param string $pVariableName Nom de la variable d'origine
     * @param int $pTryNum Numéro d'essai de génération
     * @return string
     */
    private function _compressVariable ($pVariableName) {
        if (isset ($this->_assigned_variables[$pVariableName])) {
            return $this->_assigned_variables[$pVariableName];
        }
        
        //Compression du nom de la variable, ajout du numéro d'essai
        $result = CopixFormatter::getReduced ($pVariableName, 25);

        //On vérifie que le nom trouvé ne corresponds pas déjà à une variable existante.
        if (in_array ($result, $this->_assigned_variables)) {
        	while (in_array ($newResult = $result.rand (1, 99999), $this->_assigned_variables)){
        		;//essais alléatoires de génération de variable
        	}
        	$result = $newResult;
        }

        return $this->_assigned_variables[$pVariableName] = $result;        
    }


    /**
     * Définition des tris sur les champs
     * <code>
     * $mySearchParams->orderBy (array ('monChamp' => 'ASC', 'monChamp2' => 'DESC'));
     * $mySearchParams->orderBy (array ('monChamp' => 'ASC'), array ('monChamp2' => 'DESC'));
     * $mySearchParams->orderBy ('monChamp', array ('monChamp2' => 'DESC'));
     * </code>
     *
     * @param mixed Tableau qui contient la liste des champs par lesquels on souhaite trier
     * @return CopixDAOSearchParams
     */
    public function orderBy () {
        $args = func_get_args ();
        foreach ($args as $arg) {
            if (is_array ($arg)) {
                $this->order[$arg[0]] = $arg[1];
            } else {
                $this->order[$arg] = 'ASC';
            }
        }
        return $this;
    }

    /**
     * Définition pour le groupage par champs
     *
     * @param string $pFieldName1,$pFieldName2,$pFieldName3...
     * @return CopixDAOSearchParams
     */
    public function groupBy () {
        $args = func_get_args ();
        foreach ($args as $arg) {
            $this->groupby[] = $arg;
        }
        return $this;
    }

    /**
     * Indique si la condition est vide
     *
     * @return boolean
     */
    public function isEmpty () {
        return
                $this->condition->isEmpty ()
                && (count ($this->groupby) == 0)
                && (count ($this->order) == 0);
    }

    /**
     * Définit l'offset et le nombre d'enregistrements que l'on souhaites récupérer
     *
     * @param int $pOffset Offset à partir duquel on souhaite récupérer les enregistrements
     * @param int $pCount Nombre d'enregistrements que l'on souhaite récupérer
     * @return CopixDAOSearchParams
     */
    public function setLimit ($pOffset, $pCount) {
        return $this->setOffset ($pOffset)->setCount ($pCount);
    }

    /**
     * Définit l'offset à partir duquel on souhaite récupérer les enregistrements
     *
     * @param int $pOffset Offset à partir duquel on souhaite récupérer les enregistrements
     * @return CopixDAOSearchParams
     */
    public function setOffset ($pOffset) {
        $this->_offset = $pOffset;
        return $this;
    }

    /**
     * Définit le nombre d'enregistremnet que l'on souhaites récupérer
     *
     * @param int $pCount Nombre d'enregistrements que l'on souhaite récupérer
     * @return CopixDAOSearchParams
     */
    public function setCount ($pCount) {
        $this->_count = $pCount;
        return $this;
    }

    /**
     * Retourne le nombre d'enregistrements que l'on souhaite récupérer au maximum
     * @return int
     */
    public function getCount () {
        return $this->_count;
    }

    /**
     * Retourne le numéro d'enregistrement à partir duquel on souhaite afficher les résultats
     *
     * @return int
     */
    public function getOffset () {
        return $this->_offset;
    }

    /**
     * Démarre un groupe de condition
     *
     * @param string $pKind Type de groupe que l'on souhaite démarrer, AND ou OR
     * @return CopixDAOSearchparams
     */
    public function startGroup ($pKind = 'AND') {
        $this->_currentCondition->group[] = new CopixDAOSearchParamsCondition ($this->_currentCondition, $pKind);
        $this->_currentCondition = $this->_currentCondition->group[count ($this->_currentCondition->group) - 1];
        return $this;
    }

    /**
     * Termine un groupe de condition
     *
     * @return CopixDAOSearchparams
     */
    public function endGroup () {
        if ($this->_currentCondition->parent !== null) {
            $this->_currentCondition = $this->_currentCondition->parent;
        }
        return $this;
    }

    /**
     * Ajoute une condition
     *
     * @param string $pFieldId Nom du champ du dao sur lequel on ajoute la condition
     * @param string $pCondition Condition à appliquer (=, !=, <>, <, >, LIKE ...)
     * @param mixed $pValue Valeur de recherche (inutile de quotter les chaines)
     * @param string $pKind Type de condition, AND ou OR
     * @return CopixDAOSearchparams
     */
    public function addCondition ($pFieldId, $pCondition, $pValue, $pKind = 'AND') {
        //On supporte la condition "!=" pour "<>"
        if ($pCondition == '!=') {
            $pCondition = '<>';
        }
        $this->_currentCondition->conditions[] = array ('field_id' => $pFieldId, 'value' => $pValue, 'condition' => $pCondition, 'kind' => $pKind);
        return $this;
    }

    /**
     * Permet de rajouter directement du SQL dans la recherche
     *
     * @param string $pSQL SQL à intégrer dans la requête
     * @param array	$pParams Tableau de paramètres relatifs à la chaine
     * @param string $pKind Type de condition, AND ou OR
     * @return CopixDAOSearchParams
     */
    public function addSql ($pSql, $pParams = array (), $pKind = 'AND') {
        $this->_currentCondition->conditions[] = array ('sql' => $pSql, 'params' => $pParams, 'kind' => $pKind);
        return $this;
    }

    /**
     * Transforme le jeu de conditions en une chaine SQL
     *
     * @param array $pFields Tableau des champs à traiter 'nomDePropriete' => array (0 => 'nomeDuChampEnBase', 1 => 'typeDuChamp', 2 => 'nomDeLaTable (alias)')
     * @param CopixDBConnection $pConnection Connection utilisée pour la requête dont on demande la génération
     * @return array 0 => Partie WHERE + GROUP BY + ORDER BY de la requête SQL, 1 => Paramètres
     */
    public function explainSQL ($pFields, $pConnection = null) {
        // génération de la clause where
        list ($sql, $params) = $this->_explainSQLCondition ($this->condition, false, $pFields, $pConnection);
        $desc = false;
        $order = array ();

        $groupSQL = '';
        $firstGroup = true;
        foreach ($this->groupby as $name) {
            if (!$firstGroup) {
                $groupSQL .= ', ';
            }
            $firstGroup = false;
            $groupSQL .= $pFields[$name][6] . '.' . $pFields[$name][5];
        }

        if (strlen ($groupSQL) > 0) {
            if (trim ($sql) == '') {
                $sql = ' 1=1 ';
            }
            $sql .= ' GROUP BY ' . $groupSQL;
        }

        $firstOrder = true;
        $orderSQL = '';
        foreach ($this->order as $name => $direction) {
            if (!$firstOrder) {
                $orderSQL .= ', ';
            }
            $firstOrder = false;
            $orderSQL .= $pFields[$name][6] . '.' . $pFields[$name][5] . ' ' . $direction;
        }

        if (strlen ($orderSQL) > 0) {
            if (trim ($sql) == '') {
                $sql = ' 1=1 ';
            }
            $sql .= ' ORDER BY ' . $orderSQL;
        }
        return array ($sql, $params);
    }

    /**
     * Retourne la valeur de la variable en fonction de son type, de sa valeur et du type de driver
     *
     * @param string $pType Type du champ (varchar, date, datetime, time, etc)
     * @param mixed $pValue Valeur actuelle
     * @param string $pDriverName Nom du driver (mysql, sqlite, etc)
     * @return mixed
     */
    private function _variableValue ($pType, $pValue, $pDriverName) {
        if ($pDriverName == 'mysql' || $pDriverName == 'sqlite') {
            //Mysql et Sqlite gèrent les mêmes formats d'entrée pour les dates / datetime / time
            switch ($pType) {
                case 'date':
                    return CopixDateTime::yyyymmddToFormat ($pValue, 'Y-m-d H:i:s');
                case 'datetime':
                    return CopixDateTime::yyyymmddhhiissToFormat ($pValue, 'Y-m-d H:i:s');
                case 'time':
                    return CopixDateTime::hhiissToFormat ($pValue, 'Y-m-d H:i:s');
            }
        }
        return $pValue;
    }

    /**
     * Retourne les conditions en SQL
     *
     * @param CopixDAOSearchParamsCondition $pConditions Conditions à mettre dans le SQL
     * @param boolean $pExplainKind Si l'on souhaite expliquer le AND / OR
     * @param array	$pFields Tableau des champs sur lesquels travailler. array ('nomPropriete' => array (0 => 'nomChamp', 1 => 'TypeChamp', 2 => 'table'))
     * @param CopixDBConnection $pConnection Connection où l'on souhaite executer la requête
     * @return array 0 => Conditions, 1 => Valeurs
     */
    private function _explainSQLCondition ($pConditions, $pExplainKind, $pFields, $pConnection = null) {
        $r = ' ';
        $fieldsForQueryParams = array ();

        //direct conditions for the group
        $first = true;
        foreach ($pConditions->conditions as $conditionDescription) {
            //Si c'est une forme SQL et que la chaine est vide, on passe à la condition suivante.
            if (array_key_exists ('sql', $conditionDescription) && (strlen ($conditionDescription['sql']) === 0)) {
                continue;
            }

            //Si ce n'est pas le premier passage, il faut ajouter le mot clef relatif à la condition
            if (!$first) {
                $r .= ' ' . $conditionDescription['kind'] . ' ';
            }

            //Nous ne sommes plus dans le premier passage.
            $first = false;

            if (isset ($conditionDescription['sql'])) {
                //C'est une condition SQL rajoutée à la main.

                //on remplace les noms de paramètres pour s'assurer de l'unicité.
                //On parcours dans le sens inverse de l'ordre alpha pour éviter de remplacer
                //des portions de nom de variable (ex :a avant :ab)
                krsort ($conditionDescription['params']);
                foreach ($conditionDescription['params'] as $paramName => $paramValue) {
                    $conditionDescription['sql'] = str_replace ($paramName, ($newParamName = ($paramName . self::$_countParam)), $conditionDescription['sql']);
                    self::$_countParam++;
                    //ajout du paramètre dans le tableau des paramètres
                    $fieldsForQueryParams[$newParamName] = $paramValue;
                }

                //Ajout de la chaine SQL traitée.
                $r .= ' ' . $conditionDescription['sql'] . ' ';
            } else {
                //C'est une condition gérée par addCondition.
                $prefix = $pFields[$conditionDescription['field_id']][6] . '.' . $pFields[$conditionDescription['field_id']][5];
                $prefixNoCondition = $prefix;
                $prefix .= ' ' . $conditionDescription['condition'] . ' ';

                if (!is_array ($conditionDescription['value'])) {
                    $variableName = ':' . $this->_compressVariable ($pFields[$conditionDescription['field_id']][0] . '_' . $pFields[$conditionDescription['field_id']][2] . '_' . self::$_countParam);

                    if (($conditionDescription['value'] === null) && ($conditionDescription['condition'] == '=')) {
                        $r .= $prefixNoCondition . ' IS NULL';
                    } else if (($conditionDescription['value'] === null) && ($conditionDescription['condition'] == '<>')) {
                        $r .= $prefixNoCondition . ' IS NOT NULL';
                    } else {
                        $fieldsForQueryParams[$variableName] = $this->_variableValue ($pFields[$conditionDescription['field_id']][1], $conditionDescription['value'], $pConnection ? $pConnection->getProfile ()->getDriverName () : null);
                        if (($pConnection !== null && $pConnection->getProfile ()->getDriverName () == 'oci') && in_array ($pFields[$conditionDescription['field_id']][1], array ('datetime', 'date', 'time'))) {
                            if ($pFields[$conditionDescription['field_id']][1] == 'datetime') {
                                $r .= 'to_char (' . $prefixNoCondition . ', \'YYYYMMDDHH24MISS\') ' . $conditionDescription['condition'] . ' ' . $variableName;
                            } else if ($pFields[$conditionDescription['field_id']][1] == 'date') {
                                $r .= 'to_char (' . $prefixNoCondition . ', \'YYYYMMDD\') ' . $conditionDescription['condition'] . ' ' . $variableName;
                            } else if ($pFields[$conditionDescription['field_id']][1] == 'time') {
                                $r .= 'to_char (' . $prefixNoCondition . ', \'HH24MISS\') ' . $conditionDescription['condition'] . ' ' . $variableName;
                            }
                        } else {
                            $methodDebut = '';
                            $methodFin = '';
                            if (isset ($pFields[$conditionDescription['field_id']][4])) {
                                $methodDebut = $pFields[$conditionDescription['field_id']][4] . '(';
                                $methodFin = ')';
                            }
                            $r .= $prefix . $methodDebut . $variableName . $methodFin;
                        }
                        self::$_countParam++;
                    }
                } else {
                    if (count ($conditionDescription['value'])) {
                        $r .= ' ( ';
                        $firstCV = true;
                        foreach ($conditionDescription['value'] as $conditionValue) {
                            $variableName = ':' . $this->_compressVariable ($pFields[$conditionDescription['field_id']][0] . '_' . $pFields[$conditionDescription['field_id']][2] . '_' . self::$_countParam);
                            if (!$firstCV) {
                                $r .= ' or ';
                            }
                            if (($conditionValue === null) && ($conditionDescription['condition'] == '=')) {
                                $r .= $prefixNoCondition . ' IS NULL';
                            } else if (($conditionValue === null) && ($conditionDescription['condition'] == '<>')) {
                                $r .= $prefixNoCondition . ' IS NOT NULL';
                            } else {
                                if (($pConnection !== null && $pConnection->getProfile ()->getDriverName () == 'oci') && in_array ($pFields[$conditionDescription['field_id']][1], array ('datetime', 'date', 'time'))) {
                                    if ($pFields[$conditionDescription['field_id']][1] == 'datetime') {
                                        $r .= 'to_char (' . $prefixNoCondition . ', \'YYYYMMDDHH24MISS\') ' . $conditionDescription['condition'] . ' ' . $variableName;
                                    } else if ($pFields[$conditionDescription['field_id']][1] == 'date') {
                                        $r .= 'to_char (' . $prefixNoCondition . ', \'YYYYMMDD\') ' . $conditionDescription['condition'] . ' ' . $variableName;
                                    } else if ($pFields[$conditionDescription['field_id']][1] == 'time') {
                                        $r .= 'to_char (' . $prefixNoCondition . ', \'HH24MISS\') ' . $conditionDescription['condition'] . ' ' . $variableName;
                                    }
                                } else {
                                    $methodDebut = '';
                                    $methodFin = '';
                                    if (isset ($pFields[$conditionDescription['field_id']][4])) {
                                        $methodDebut = $pFields[$conditionDescription['field_id']][4] . '(';
                                        $methodFin = ')';
                                    }
                                    $r .= $prefix . $methodDebut . $variableName . $methodFin;
                                }
                                $fieldsForQueryParams[$variableName] = $this->_variableValue ($pFields[$conditionDescription['field_id']][1], $conditionValue, $pConnection ? $pConnection->getProfile ()->getDriverName () : null);
                            }
                            $firstCV = false;
                            self::$_countParam++;
                        }
                        $r .= ' ) ';
                    }else {
                        $r .= ' (1 = 2) ';
                    }
                }
            }
        }

        //sub conditions
        foreach ($pConditions->group as $conditionDetail) {
            list ($sql, $fields) = $this->_explainSQLCondition ($conditionDetail, !$first, $pFields, $pConnection);
            $r .= $sql;
            $fieldsForQueryParams = array_merge ($fieldsForQueryParams, $fields);
            if (!$conditionDetail->isEmpty ()) {
                $first = false;
            }
        }

        //adds parenthesis around the sql if needed (non empty)
        if (strlen (trim ($r)) > 0) {
            $r = ($pExplainKind ? ' ' . $pConditions->kind . ' ' : '') . '(' . $r . ')';
        }

        return array ($r, $fieldsForQueryParams);
    }

    /**
     * Retourne le SQL de la partie WHERE
     *
     * @param array $pFields Informations sur les champs : array ('name' => array (0 => 'fieldname' , 1 => 'type', 2 => 'table'))
     * @param undefined $pCt Paramètre inutilisé dans le code
     * @return string
     * @todo vérifier s'il n'y a pas mieux pour faire ça
     */
    public function explainPHPSQL ($pFields, $pCt) {
        $sql = $this->_explainPHPSQLCondition ($this->condition, $pFields, $pCt, true);

        $order = array ();
        foreach ($this->order as $name => $way) {
            if (isset ($pFields[$name])) {
                $order[] = $pFields[$name][6] . '.' . $pFields[$name][5] . ' ' . $way;
            }
        }
        if (count ($order) > 0) {
            if (trim ($sql) =='') {
                $sql .= ' 1=1 ';
            }
            $sql .= ' ORDER BY ' . implode (', ', $order);
        }
        return $sql;
    }

    /**
     * Retourne le SQL pour un groupe de conditions
     *
     * @param array $pFields Informations sur les champs : array ('name' => array (0 => 'fieldname', 1 => 'type', 2 => 'table', 3 => 'motif'))
     * @param array $pCondition Tableau représentants les conditions : array ('fieldname' => array ('fieldId' => Identifiant, 'value' => Valeur, 'condition' => Condition (AND ou OR)))
     * @return string
     * @todo vérifier s'il n'y a pas mieux pour ça
     */
    private function _explainPHPSQLCondition ($pCondition, &$pFields, $ct, $principal = false) {
        $r = ' ';

        //direct conditions for the group
        $first = true;
        foreach ($pCondition->conditions as $condDesc) {
            if (!$first) {
                $r .= ' ' . $pCondition->kind . ' ';
            }
            $first = false;

            $property = $pFields[$condDesc['field_id']];

            if (isset ($property[6]) && $property[6] != '') {
                $prefix = $property[6] . '.' . $property[5];
            } else {
                $prefix = $property[5];
            }

            if (isset ($property[3]) && $property[3] != '' && $property[3] != '%s') {
                $prefix = sprintf ($property[3], $prefix);
            }

            $prefixNoCondition = $prefix;
            // ' ' pour les like..
            $prefix .= ' ' . $condDesc['condition'] . ' ';

            if (!is_array ($condDesc['value'])) {
                //handles equality of "NULL" values.
                if ($condDesc['condition'] == '=') {
                    $r .= $prefixNoCondition . '\' . (' . $condDesc['value'] . ' === null ? \' IS \' : \' = \') . \'' . $this->_preparePHPValue ($condDesc['value'], $property[1]);
                } else if ($condDesc['condition'] == '<>') {
                    $r .= $prefixNoCondition . '\' . (' . $condDesc['value'] . ' === null ? \' IS NOT \' : \' <> \') . \'' . $this->_preparePHPValue ($condDesc['value'], $property[1]);
                } else {
                    $r .= $prefix . $this->_preparePHPValue ($condDesc['value'], $property[1]);
                }
            } else {
                $r .= ' ( ';
                $firstCV = true;
                foreach ($condDesc['value'] as $conditionValue) {
                    if (!$firstCV) {
                        $r .= ' or ';
                    }
                    //handles equality of "NULL" values in the PHP generation.
                    if ($condDesc['condition'] == '=') {
                        $r .= $prefixNoCondition .'\' . (' . $conditionValue . ' === null ? \' IS \' : \' = \') . \'' . $this->_preparePHPValue ($conditionValue,$property[1]);
                    } else if ($condDesc['condition'] == '<>') {
                        $r .= $prefixNoCondition . '\' . (' . $conditionValue . ' === null ? \' IS NOT \' : \' <> \') . \'' . $this->_preparePHPValue ($conditionValue,$property[1]);
                    } else {
                        $r .= $prefix . $this->_preparePHPValue ($conditionValue, $property[1]);
                    }
                    $firstCV = false;
                }
                $r .= ' ) ';
            }
        }
        //sub conditions
        foreach ($pCondition->group as $conditionDetail) {
            if (!$first) {
                $r .= ' ' . $pCondition->kind . ' ';
            }
            $r .= $this->_explainPHPSQLCondition ($conditionDetail, $pFields, $ct);
            $first = false;
        }

        //adds parenthesis around the sql if needed (non empty)
        if (strlen (trim ($r)) > 0 && !$principal) {
            $r = '(' . $r . ')';
        }
        return $r;
    }

    /**
     * Retourne une chaine prête à être écrite dans un fichier PHP, de la forme ' . code PHP . ' (quotes comprises)
     *
     * @param mixed $pValue Valeur du champ.
     * @param string $pFieldType Type du champ
     * @return string
     * @todo Supprimer et remplacer par le système de bind
     */
    public function _preparePHPValue ($pValue, $pFieldType) {
        switch (strtolower ($pFieldType)) {
            case 'int':
            case 'integer':
            case 'autoincrement':
                $pValue = '\' . (' . $pValue . ' === null ? \'NULL\' : intval (' . $pValue . ')) . \'';
                break;

            case 'double':
            case 'float':
                $pValue = '\' . (' . $pValue . ' === null ? \'NULL\' : doubleval (' . $pValue . ')) . \'';
                break;

            //usefull for bigint and stuff
            case 'numeric':
            case 'bigautoincrement':
                if (!is_numeric ($pValue)) {
                    $pValue = '\' . (' . $pValue . ' === null ? \'NULL\' : (is_numeric (' . $pValue . ') ? ' . $pValue . ' : intval (' . $pValue . '))) . \'';
                }
                break;

            default:
                $pValue = '\' . $ct->quote (' . $pValue . ') . \'';
        }
        return $pValue;
    }
}