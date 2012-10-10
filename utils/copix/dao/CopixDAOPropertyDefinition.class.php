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
 * Définition d'une propriété d'un DAO
 *
 * @package		copix
 * @subpackage	dao
 */
class CopixDAOPropertyDefinition {
    /**
     * Nom de la propriété dans l'objet
     *
     * @var string
     */
    public $name = '';

    /**
     * Nom du champ dans la table
     *
     * @var string
     */
    public $fieldName = '';

    /**
     * Expression régulière qui doit valider le champ
     *
     * @var string
     */
    public $regExp = null;

    /**
     * Indique si le champ est obligatoire
     *
     * @var boolean
     */
    public $required = false;

    /**
     * Clef i18n pour le libellé du champ
     *
     * @var string
     */
    public $captionI18N = null;

    /**
     * Libellé du champ
     *
     * @var string
     */
    public $caption = null;

    /**
     * Indique si le champ fait partie de la clef primaire
     *
     * @var boolean
     */
    public $isPK = false;

    /**
     * Indique si le champ fait partie de la clef étrangère
     *
     * @var boolean
     */
    public $isFK = false;

    /**
     * Type du champ
     *
     * @var string
     */
    public $type;

    /**
     * Nom de la table dont fait partie ce champ
     *
     * @var string
     */
    public $table = null;

    /**
     * Indique si cette propriété fait partie de la table primaire
     *
     * @var boolean
     */
    public $ofPrimaryTable = true;

    /**
     * Motif de sélection
     *
     * @var string
     */
    public $selectMotif = '%s';

    /**
     * Méthode
     *
     * @var string
     */
    public $method = null;

    /**
     * Table qui contient la clef étrangère, si besoin
     *
     * @var string
     */
    public $fkTable = null;

    /**
     * Clef étrangère liée au champ
     *
     * @var string
     */
    public $fkFieldName = null;

    /**
     * Nom de la séquence (probablement pour oracle)
     *
     * @string
     */
    public $sequenceName = '';

    /**
     * Taille maximale du champ (null si non indiquée)
     *
     * @var int
     */
    public $maxlength = null;

    /**
     * Constructeur
     *
     * @param array $pParams Paramètres (name, fieldname, table, method, required, maxlength, regexp, etc)
     * @param CopixDAODefinition Définition du DAO
     * @throws Exception
     */
    public function __construct ($pParams, $pDef) {
        //Si def=null on viens de __set_state
        if ($pDef == null) {
            foreach ($pParams as $key => $field) {
                $this->$key = $field;
            }
            return null;
        }

        //converting into lowercase
        foreach ($pParams as $key => $name) {
            $newParams[strtolower ($key)] = (string) $name;
        }
        $pParams = $newParams;

        if (!isset ($pParams['name'])) {
            throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.missing.attr', array ('name', 'property')));
        }

        $this->name	= $pParams['name'];
        $this->fieldName = isset ($pParams['fieldname']) ? $pParams['fieldname'] : $this->name;
        $this->table = isset ($pParams['table']) ? $pParams['table'] : $pDef->getPrimaryTableName ();
        $this->method = isset($pParams['method']) ?$pParams['method'] : null;

        if (!$pDef->getTable ($this->table)) {
            throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.property.unknow.table', array ($this->name, $this->table)));
        }

        $this->required = isset ($pParams['required']) ? $this->_getBool($pParams['required']) : false;
        $this->maxlength = isset ($pParams['maxlength']) ? ($pParams['maxlength']) : null;

        if (isset ($pParams['regexp'])) {
            if (trim ($pParams['regexp']) != '') {
                $this->regExp = (string) $pParams['regexp'];
            }
        }

        $this->captionI18N = (isset ($pParams['captioni18n'])) ? $pParams['captioni18n'] : null;
        if ($this->captionI18N !== null) {
            if (strpos ($this->captionI18N, $pDef->getQualifier ()) !== 0) {
                $this->captionI18N = $pDef->getQualifier () . $this->captionI18N;
            }
        }
        $this->caption = isset ($pParams['caption']) ? $pParams['caption'] : null;
        if ($this->caption == null && $this->captionI18N == null) {
            $this->caption = $this->name;
        }

        $this->isPK = isset ($pParams['pk']) ? $this->_getBool ($pParams['pk']): false;
        if (!isset ($pParams['type'])) {
            throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.missing.attr', array ('type', 'field')));
        }
        $pParams['type'] = strtolower ($pParams['type']);
        $this->needsQuotes = $this->_typeNeedsQuotes ($pParams['type']);
        if (!in_array ($pParams['type'], array ('autoincrement', 'bigautoincrement', 'int','integer', 'varchar', 'string', 'varchartime', 'time', 'varchardate', 'date', 'datetime', 'numeric', 'double', 'float', 'version'))) {
            throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.wrong.attr', array ($this->name, $pParams['type'], $this->fieldName)));
        }

        $this->type = $pParams['type'];
        // on ignore les champs fktable et fkfieldName pour les propriétés qui n'appartiennent pas à la table principale
        if($this->table == $pDef->getPrimaryTableName ()) {
            $this->fkTable = isset ($pParams['fktable']) ? $pParams['fktable'] : null;
            $this->fkFieldName = isset ($pParams['fkfieldname']) ? $pParams['fkfieldname'] : '';
            if ($this->fkTable !== null) {
                if ($this->fkFieldName == '') {
                    throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.property.foreign.field.missing', array ($this->name, $this->fkFieldName)));
                }
            }
        }

        $this->isFK = ($this->fkTable !== null);
        if (($this->type == 'autoincrement' || $this->type == 'bigautoincrement') && isset ($pParams['sequence'])) {
            $this->sequenceName = $pParams['sequence'];
        }

        // on ignore les attributs *motif sur les champs PK et FK
        // (je ne sais plus pourquoi mais il y avait une bonne raison...)
        if (!$this->isPK && !$this->isFK) {
            $this->selectMotif = isset ($pParams['selectmotif']) ? $pParams['selectmotif'] :'%s';
        }

        // pas de motif update et insert pour les champs des tables externes
        if ($this->table != $pDef->getPrimaryTableName ()) {
            $this->required = false;
            $this->ofPrimaryTable = false;
        } else {
            $this->ofPrimaryTable = true;
        }
    }

    /**
     * Retourne un boolean suivant une chaine. true, 1 ou yes renvoient true, le reste, false
     *
     * @param string $pValue Valeur à tester
     * @return boolean
     */
    private function _getBool ($pValue) {
        return in_array (trim ($pValue), array ('true', '1', 'yes'));
    }

    /**
     * Indique si ce type de champ a besoin d'être entouré de quotes lors d'une requête SQL
     *
     * @param string $pTypeName Type à vérifier
     * @return bool
     */
    private function _typeNeedsQuotes ($pTypeName) {
        return in_array (trim ($pTypeName), array ('string', 'date', 'varchardate', 'varchartime', 'time'));
    }

    /**
     * Méthode appelée lors d'un var_export
     *
     * @param array $pArray Propriétés à passer au DAO
     * @return CopixDAOPropertyDefinition
     */
    public static function __set_state ($pArray) {
        $tempObject = new CopixDAOPropertyDefinition ($pArray, null);
        return $tempObject;
    }
}
