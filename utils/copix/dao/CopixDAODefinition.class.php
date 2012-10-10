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
* Représente la définition de DAO qui permettra à un générateur de créer le DAO final
* @package copix
* @subpackage dao
*/
class CopixDAODefinition {
    /**
    * Liste des propriétés
    * Clefs => Les noms des champs
    * Valeurs => des objets de type CopixPropertyForDAO
    */
    private $_properties = array ();

    /**
    * Tableau de toute les tables
    * 
    * keys = Nom de la table
    * values = array()
    *              'name'=> nom de la table, 'tablename'=>'le nom de la table en base', 'JOIN'=>'type de jointure',
    *              'primary'=>'bool', 'fields'=>array(liste des noms de champs)
    */
    private $_tables = array();

    /**
    * Le nom de la table principale
    * @var string
    */
    private $_primaryTableName = null;

    /**
    * liste des jointures, entre toutes les tables
    * 
    * keys = foreign table name
    * values = array('join'=>'type jointure', 'pfield'=>'real field name', 'ffield'=>'real field name');
    * @var array
    */
    private $_joins = array ();

    /**
    * La connection à utiliser pour la génération
    * @var string
    */
    private $_connectionName = null;

    /**
     * Liste des méthodes générées
     * @var array
     */
    private $_methods = array();

 	/**
 	 * Le chemin vers le fichier de définition du DAO
 	 * @var string 
 	 */
 	private $_xmlFilePath = null;

 	/**
 	 * Le chemin vers le fichier PHP écrit par l'utilisateur
 	 * @var string
 	 */
 	private $_phpClassFilePath = null; 

 	/**
 	 * L'identifiant du DAO
 	 * @var string
 	 */
 	private $_DAOId = null;

    /**
    * Le nom du DAO à utiliser pour la génération. Ce DAO devra être défini dans le fichier 
    * $this->_phpClassFilePath pour être valide. Il sera surchargé par le générateur
    * @var string
    */
    private $_userDefinedDAOName = null;

    /**
    * Le nom du DAORecord à utiliser pour la génération. Ce DAO devra être défini dans le fichier 
    * $this->_phpClassFilePath pour être valide. Il sera surchargé par le générateur
    * @var string
    */
    private $_userDefinedDAORecordName = null;

    /**
     * Définition de l'identifiant de DAO que l'on souhaite générer
     * @param string $pDAOId l'identifiant de DAO que l'on souhaites générer
     */
    public function setDAOId ($pDAOId){
    	$this->_DAOId = $pDAOId;
    }

    /**
     * Retourne le sélecteur de DAO (utile pour les générations complètes de clefs i18n)
     * @return string
     */
    public function getQualifier (){
    	return CopixSelectorFactory::create ($this->_DAOId)->getQualifier ();
    }

    /**
     * récupération de l'identifiant de DAO que l'on est en train de générer
     * @return string
     */
    public function getDAOId (){
    	return $this->_DAOId;
    }

    /**
    * Ajoute un champ à la liste
    */
    public function addProperty ($field){
        $this->_properties[$field->name] = $field;
        $this->_tables[$field->table]['fields'][] = $field->name;

        if($field->fkTable !== null){
            if(! isset ($this->_joinTypes[$field->fkTable])){
               throw new Exception (_i18n ('copix:dao.error.definitionfile.properties.foreign.table.missing', $field->name));
            }
            $this->_joins[$field->fkTable][] = array ('join'=>$this->_joinTypes[$field->fkTable], 'pfield'=>$field->fieldName, 'ffield'=> $field->fkFieldName);
        }
        uasort($this->_joins, array('CopixDAODefinition', '_sortJoins'));
    }
    
    /**
     * Récupération des jointures définies dans l'objet 
     * @return array 
     */
    public function getJoins (){
    	return $this->_joins;
    }

    /**
     * Récupération de la liste des propriétés
     * @return array
     */
    public function getProperties () {
        return $this->_properties;
    }
    
    /**
     * Retourne la liste des méthodes inclues dans la définition
     * @return array
     */
    public function getMethods (){
       return $this->_methods;    	
    }
    
    /**
     * Définition du nom de la connexion à utiliser
     * @param string $pConnectionName le nom de la connexion à utiliser
     */
    public function setConnectionName ($pConnectionName){
    	$this->_connectionName = $pConnectionName;
    }

    /**
     * Récupération du nom de la connexion à utiliser
     * @return string
     */
    public function getConnectionName (){
    	return $this->_connectionName;
    }

    /**
     * Ajoute une table à la définition de DAO
     * 
     * @param array $tableinfos un tableau contenant les informations de la table à ajouter.
     * @return void 
     */
    public function addTable ($tableinfos){
    	//converting tableinfo into strings
    	foreach ($tableinfos as $key=>$name){
    		$newTableInfo[(string) $key] = (string) $name; 
    	}
    	$tableinfos = $newTableInfo;
    	
        if (!isset ($tableinfos['name']) || trim ($tableinfos['name']) == ''){
           throw new Exception (_i18n ('copix:dao.error.definitionfile.table.name'));
        }

        if (!isset ($tableinfos['tablename']) || $tableinfos['tablename'] == ''){
           $tableinfos['tablename'] = $tableinfos['name'];
        }

        $tableinfos['fields'] = array ();
        $this->_tables[$tableinfos['name']] = $tableinfos;

        if (isset ($tableinfos['primary']) && $this->_getBool ($tableinfos['primary'])){
            if($this->_primaryTableName !== null){
                throw new Exception (_i18n ('copix:dao.error.definitionfile.table.primary.duplicate',$this->_primaryTableName));
            }
            $this->_primaryTableName = $tableinfos['name'];
        }else{
            $join = isset($tableinfos['join']) ? strtolower(trim($tableinfos['join'])) : '';
            if (!in_array ($join, array('left','right','inner',''))){
                throw new Exception (_i18n ('copix:dao.error.definitionfile.table.join.invalid',$tableinfos['NAME']));
            }

            if ($join == 'inner'){
                $join = '';
            }
            $this->_joinTypes[$tableinfos['name']] = $join;
        }
    }

    /**
     * Fonction de comparaison pour le tri des jointures
     */
    static private function _sortJoins($join1, $join2){
        $j1 = isset ($join1['join']) ? $join1['join'] : '';
        $j2 = isset ($join2['join']) ? $join2['join'] : '';
        if ($j1 == '' && $j2 !=''){
           return 1;
        }else if($j1 != '' && $j2 ==''){
          return -1;
        }else{
          return 0;
        }
    }

    /**
     * Récupération des tables
     */
    public function getTables(){
        return $this->_tables;
    }
    
    /**
     * Récupère les informations sur la table primaire
     * @return string ou null si non défini
     */
    public function getPrimaryTable (){
    	return $this->getTable ($this->getPrimaryTableName ());
    }
    
    /**
     * Récupère le nom de la table primaire (dans le fichier de définition)
     * @return string ou null si non défini
     */
    public function getPrimaryTableName (){
    	return $this->_primaryTableName;
    }    
    
    /**
     * retourne les informations sur une table donnée 
     * @param string $pTableName le nom de la table dont on veut récupérer les informations
     * @return array informations sur la table ou null si la table n'est pas trouvée 
     */
    public function getTable ($pTableName){
       return isset ($this->_tables[$pTableName]) ? $this->_tables[$pTableName] : null; 
    }

    /**
     * Donne le nom (celui en base) de la table primaire associée au DAO
     * @return string 
     */
    public function getPrimaryTableRealName (){
    	if ($primary = $this->getPrimaryTable ()){
        	return $primary['tablename'];
    	}
    	return null; 
    }

    /**
     * Ajout d'une méthode
     */
    public function addMethod (&$method) {
        if(isset ($this->_methods[$method->name])){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.method.duplicate', $method->name));
        }
        $this->_methods[$method->name] = $method;
    }

    /**
    * just a quick way to retriveve boolean values from a string.
    *  will accept yes, true, 1 as "true" values
    *  the rest will be considered as false values.
    * @return boolean true / false
    */
    private function _getBool ($value) {
        return in_array (trim ($value), array ('true', '1', 'yes'));
    }
    
    /**
     * Assigne le chemin vers le fichier PHP à utiliser et à surcharger pour générer le DAO
     * @param string $pFilePath le chemin absolu vers le fichier PHP à utiliser
     * @param string $pRecordName le nom de la classe PHP qui est définie par l'utilisateur pour reprsenter l'enregistrement  
     * @param string $pDAOName le nom de la classe PHP qui est définie par l'utilisateur pour représenter le DAO
     */
    public function setPHPClassFilePath ($pFilePath, $pRecordName = null, $pDAOName = null){
    	if (Copix::RequireOnce ($this->_phpClassFilePath = $pFilePath)){
    		//On s'occupe de récupérer le nom du Record à utiliser si besoin
    		if ($pRecordName !== null){
    			if (! class_exists ($pRecordName)){
     	    		throw new Exception ('Demande d utilisation de  '.$pRecordName.' pour le record mais la classe n est pas définie dans le fichier '.$pFilePath);
    			}else{
    				$this->_userDefinedDAORecordName = $pRecordName;
    			}    			
    		}else{
    			//on utilise le nom par défaut
    			if (class_exists ($daoRecordName = CopixDAOFactory::getDAORecordName ($this->_DAOId, false))){
    				$this->_userDefinedDAORecordName = $daoRecordName;
    			}
    		}

    		//On s'occupe de récupérer le nom du DAO à utiliser si besoin
    		if ($pDAOName !== null){
    			if (! class_exists ($pRecordName)){
     	    		throw new Exception ('Demande d utilisation de  '.$pDAOName.' pour le DAO mais la classe n est pas définie dans le fichier '.$pFilePath);
    			}else{
    				$this->_userDefinedDAOName = $pDAOName;
    			}    			
    		}else{
    			//on utilise le nom par défaut
    			if (class_exists ($daoName = CopixDAOFactory::getDAOName ($this->_DAOId, false))){
    				$this->_userDefinedDAOName = $daoName;
    			}
    		}
    	}else{
    		throw new Exception ('impossible de charger le fichier de définition demandé pour le DAO '.$pFilePath);
    	}
    }
    
    /**
     * Retourne le chemin du fichier PHP assigné à la définition de DAO
     * @return string
     */
    public function getPHPClassFilePath (){
    	return $this->_phpClassFilePath;
    }

    /**
     * Indique le nom de la classe DAORecord définie si l'on a assigné un fichier PHP
     * à la définition du DAO
     * @return string
     */
    function getUserDefinedDAORecordName (){
    	return $this->_userDefinedDAORecordName;
    }    
    
    /**
     * Indique le nom de la classe DAO définie si l'on a assigné un fichier PHP
     * à la définition du DAO
     * @return string
     */
    function getUserDefinedDAOName (){
    	return $this->_userDefinedDAOName;
    }
    
	/**
    * Récupération des champs en fonction de la méthode de capture $captureMethod
    */
	public function getPropertiesBy ($captureMethod){
		$captureMethod = '_capture'.$captureMethod;
		$result = array ();
        $fields = $this->getProperties ();

		foreach ($this->getProperties () as $field){
			if ( $this->$captureMethod($field)){
				//Avant $field, on avait $this->_userDefinition->_properties[$field->name];
				$result[$field->name] = $fields[$field->name];
			}
		}
		return $result;
	}

	/**
	 * Indique si le champ appartient à la clef primaire
	 */
	function _capturePkFields($field){
		return ($field->table == $this->getPrimaryTableName ()) && $field->isPK;
	}

	/**
	 * Récupération des champs de la table principale à l'exception des champs 
	 * auto incrémentés
	 */
	function _capturePrimaryFieldsExcludeAutoIncrement($field){
		return ($field->table == $this->getPrimaryTableName ()) &&
		($field->type != 'autoincrement') && ($field->type != 'bigautoincrement');
	}

	/**
	 * Indique si le champ appartient à la table principale, et qu'il ne fait pas parti de la clef primaire 
	 */
	function _capturePrimaryFieldsExcludePk($field){
		return ($field->table == $this->getPrimaryTableName ()) && !$field->isPK;
	}

	/**
	 * Indique si le champ appartient à la table principale
	 */
	function _capturePrimaryTable($field){
		return ($field->table == $this->getPrimaryTableName ());
	}

	/**
	 * récupération de tous les champs
	 */
	function _captureAll($field){
		return true;
	}
	/**
	 * Indique si le champ est de type version
	 */
	function _captureVersion ($field){
		return $field->type == 'version';
	}

    /**
     * Récupération des méthodes déclarées dans l'objet crée par l'utilisateur 
     */
    public function getUserDAOClassMethods (){
		if ($this->getUserDefinedDAOName () !== null){
		    return (array) get_class_methods ($this->getUserDefinedDAOName ());
		}
		return array ();
    }
}

/**
* Définition d'une propriété
* @package copix
* @subpackage dao
*/
class CopixPropertyForDAO {
    /**
    * the name of the property of the object
    */
    var $name = '';

    /**
    * the name of the field in table
    */
    var $fieldName = '';

    /**
    * give the regular expression that needs to be matched against.
    * @var string
    */
    var $regExp = null;

    /**
    * says if the field is required.
    * @var boolean
    */
    var $required = false;

    /**
    * The i18n key for the caption of the element.
    * @var string
    */
    var $captionI18N = null;
    /**
    * the caption of the element.
    * @var string
    */
    var $caption = null;

    /**
    * Says if it's a primary key.
    * @var boolean
    */
    var $isPK = false;

    /**
    * Says if it's a forign key
    * @var boolean
    */
    var $isFK = false;

    var $type;

    var $table=null;
    var $selectMotif='%s';
    
    var $method = null;

    var $fkTable=null;
    var $fkFieldName=null;
    var $sequenceName='';

    /**
    * the maxlength of the key if given
    * @var int
    */
    var $maxlength = null;

    /**
    * constructor.
    */
    function CopixPropertyForDAO ($params, $def){
        
        //Si def=null on viens de __set_state
    	if ($def==null) {
	        foreach ($params as $key=>$field) {
	            $this->$key = $field;
	        }
	        return null;
    	}
    	
        //converting into lowercase
    	foreach ($params as $key=>$name){
    		$newParams[strtolower ($key)] = (string) $name;
    	}
    	$params = $newParams;
    	
        if (!isset ($params['name'])){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.missing.attr', array('name', 'property')));
        }
        
        $this->name       = $params['name'];
        $this->fieldName  = isset ($params['fieldname']) ? $params['fieldname'] : $this->name;
        $this->table      = isset ($params['table']) ? $params['table'] : $def->getPrimaryTableName ();
        $this->method	  = isset($params['method']) ?$params['method']:null;
                
        if(!$def->getTable ($this->table)){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.property.unknow.table', array($this->name,$this->table)));
        }

        $this->required   = isset ($params['required']) ? $this->_getBool($params['required']) : false;
        $this->maxlength  = isset ($params['maxlength']) ? ($params['maxlength']) : null;

        if (isset ($params['regexp'])){
            if(trim ($params['regexp']) != ''){
                $this->regExp     = (string) $params['regexp'];
            }
        }

        $this->captionI18N = isset($params['captioni18n']) ? $params['captioni18n'] : null;
        if ($this->captionI18N !== null){
        	if (strpos ($this->captionI18N, $def->getQualifier ()) !== 0){
        		$this->captionI18N = $def->getQualifier ().$this->captionI18N;
        	}
        }
        $this->caption     = isset($params['caption']) ? $params['caption'] : null;
        if ($this->caption == null && $this->captionI18N == null){
            $this->caption = $this->name;
        }

        $this->isPK       = isset($params['pk']) ? $this->_getBool($params['pk']): false;
        if (!isset ($params['type'])){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.missing.attr', array('type', 'field')));
        }
        $params['type'] = strtolower ($params['type']);
        $this->needsQuotes = $this->_typeNeedsQuotes ($params['type']);
        if (!in_array ($params['type'], array ('autoincrement', 'bigautoincrement', 'int','integer', 'varchar', 'string', 'varchartime', 'time', 'varchardate', 'date',  'datetime', 'numeric', 'double', 'float', 'version'))){
           throw new Exception (_i18n ('copix:dao.error.definitionfile.wrong.attr', array($this->name,$params['type'], $this->fieldName)));
        }

        $this->type = $params['type'];
        if($this->table == $def->getPrimaryTableName ()){ // on ignore les champs fktable et fkfieldName pour les propriétés qui n'appartiennent pas à la table principale
            $this->fkTable = isset ($params['fktable']) ? $params['fktable'] : null;
            $this->fkFieldName = isset ($params['fkfieldname']) ? $params['fkfieldname'] : '';
            if($this->fkTable !== null){
                if($this->fkFieldName == ''){
                   throw new Exception (_i18n ('copix:dao.error.definitionfile.property.foreign.field.missing', array($this->name,$this->fkFieldName)));
                } 
            }
        }

        $this->isFK =  $this->fkTable !== null;
        if(($this->type == 'autoincrement' || $this->type == 'bigautoincrement') && isset ($params['sequence'])){
           $this->sequenceName = $params['sequence'];
        }

        // on ignore les attributs *motif sur les champs PK et FK
        // (je ne sais plus pourquoi mais il y avait une bonne raison...)
        if(!$this->isPK && !$this->isFK){
            $this->selectMotif = isset($params['selectmotif']) ? $params['selectmotif'] :'%s';
        }

        // pas de motif update et insert pour les champs des tables externes
        if($this->table != $def->getPrimaryTableName ()){
            $this->required = false;
            $this->ofPrimaryTable = false;
        }else{
            $this->ofPrimaryTable=true;
        }
    }

    /**
    * just a quick way to retriveve boolean values from a string.
    *  will accept yes, true, 1 as "true" values
    *  the rest will be considered as false values.
    * @return boolean true / false
    */
    private function _getBool ($value) {
        return in_array (trim ($value), array ('true', '1', 'yes'));
    }

    /**
    * says if the data type needs to be quoted while being SQL processed
    */
    private function _typeNeedsQuotes ($typeName) {
        return in_array (trim ($typeName), array ('string', 'date', 'varchardate', 'varchartime', 'time'));
    }
    
    static function __set_state($pArray) {
        $tempObject = new CopixPropertyForDAO($pArray,null);
        return $tempObject;
    }
    
}

/**
* objet comportant les données d'une propriété d'un DAO
* @package copix
* @subpackage dao
*/
class CopixMethodForDAO {
    /**
     * Nom de la méthode
     */
    public $name;
    /**
     * Type de la méthode
     */
    public $type;

    private $_searchParams = null;

    private $_parameters   = array();

    private $_limit = null;

    //DDT attribut rendu public
    public $_values = array();

    private $_def;

    function getParameters (){
    	return $this->_parameters;
    }

    function getLimit (){
    	return $this->_limit;
    }

    function getSearchParams (){
    	return $this->_searchParams;
    }

    /**
     * Description d'une méthode de DAO
     */
    function CopixMethodForDAO ($method, $def){
    	$this->_def = $def;
        $attributes = array ();
        foreach ($method->attributes () as $key=>$value){
           $attributes[strtolower ($key)] = (string) $value;        
        }

        if (!isset ($attributes['name'])){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.missing.attr', array('name', 'method')));
        }

        $this->name  = $attributes['name'];
        $this->type  = isset ($attributes['type']) ? strtolower($attributes['type']) : 'select';

        if (isset ($method->parameters) && isset ($method->parameters->parameter)){
            foreach ($method->parameters->parameter as $param){
                $this->addParameter($param->attributes ());
            }
        }
        if (isset ($method->conditions)){
        	$methodConditionsAttributes = array ();
        	foreach ($method->conditions->attributes () as $key=>$name){
               $methodConditionsAttributes[strtolower ($key)] = (string) $name;        		
        	} 
            if(isset ($methodConditionsAttributes['logic'])){
                $kind = $methodConditionsAttributes['logic'];
            }else{
                $kind = 'AND';
            }
            $this->_searchParams = CopixDAOFactory::createSearchParams($kind);
            $this->_parseConditions ($method, true);
        }else{
            $this->_searchParams = CopixDAOFactory::createSearchParams('AND');
        }

        if($this->type == 'update'){
            if(isset($method->values) && isset($method->values->value)){
                foreach ($method->values->value as $val){
                    $this->addValue($val->attributes ());
                }
            }else{
                throw new Exception (_i18n ('copix:dao.error.definitionfile.method.values.undefine',array($this->name)));
            }
        }
        if (isset ($method->order) && isset($method->order->orderitem)){
            foreach($method->order->orderitem as $item){
                $this->addOrder ($item->attributes());
            }
        }

        if (isset($method->limit)){
        	if (count ($method->limit) > 1){
               throw new Exception (_i18n ('copix:dao.error.definitionfile.tag.duplicate', array('limit', $this->name)));        		
        	}

            if ($this->type == 'select' || $this->type == 'selectfirst'){
                $attr   = $method->limit->attributes();
                $offset = (isset ($attr['offset']) ? $attr['offset']:null);
                $count  = (isset ($attr['count']) ? $attr['count']:null);

                if ($offset === null){
                    throw new Exception (_i18n ('copix:dao.error.definitionfile.missing.attr',array('offset','limit')));
                }
                if ($count === null){
                    throw new Exception (_i18n ('copix:dao.error.definitionfile.missing.attr',array('count','limit')));
                }

                if (substr ($offset,0,1) == '$'){
                    if (in_array (substr ($offset,1),$this->_parameters)){
                        $offset=' intval('.$offset.')';
                    }else{
                        throw new Exception (_i18n ('copix:dao.error.definitionfile.method.limit.parameter.unknow', array($this->name, $offset)));
                    }
                }else{
                    if (is_numeric ($offset)){
                        $offset = intval ($offset);
                    }else{
                        throw new Exception (_i18n ('copix:dao.error.definitionfile.method.limit.badvalue', array($this->name, $offset)));
                    }
                }

                if (substr ($count,0,1) == '$'){
                    if(in_array (substr ($count,1),$this->_parameters)){
                        $count=' intval('.$count.')';
                    }else{
                        throw new Exception (_i18n ('copix:dao.error.definitionfile.method.limit.parameter.unknow', array($this->name, $count)));
                    }
                }else{
                    if(is_numeric($count)){
                        $count=intval($count);
                    }else{
                        throw new Exception (_i18n ('copix:dao.error.definitionfile.method.limit.badvalue', array($this->name, $count)));
                    }
                }
                $this->_limit= compact('offset', 'count');

            }else{
                throw new Exception (_i18n ('copix:dao.error.definitionfile.method.limit.forbidden'));
            }
        }
    }

    /**
     * Analyse des confitions
     */
    function _parseConditions ($node, $first=false){
        if (isset ($node->conditions)){
            if (!$first){
            	$nodeConditionsAttributes = $node->conditions->attributes (); 
                if (isset ($nodeConditionsAttributes['logic'])){
                    $kind = $nodeConditionsAttributes['logic'];
                }else{
                    $kind = 'AND';
                }
                $this->_searchParams->startGroup ($kind);
            }

            foreach ($node->conditions as $cond){
                if (isset ($node->conditions->condition)){
                    $this->addCondition ($node->conditions->condition);
                }
            }

            $this->_parseConditions ($node->conditions);

            if (!$first) {
                $this->_searchParams->endGroup();
            }
        }
    }

    /**
     * Ajout d'une condition
     */
    function addCondition ($node){
        foreach($node as $param){
            $this->_addCondition ($param->attributes());
        }
    }

    /**
     * Ajout d'une condition
     */
    function _addCondition ($attributes){
    	$newAttributes = array ();
    	foreach ($attributes as $key=>$value){
    		$newAttributes[strtolower ($key)] = (string) $value;
    	}
    	$attributes = $newAttributes;
        $field_id = (isset($attributes['property']) ? $attributes['property']:'');
        $operator = (isset($attributes['operator']) ? $attributes['operator']:'');
        $value    = (isset($attributes['value']) ? $attributes['value']:'');

        // for compatibility with dev version. valueofparam attribute = deprecated
        if(isset($attributes['valueofparam'])){
            $value='$'.$attributes['valueofparam'];
        }
        
        $properties = $this->_def->getProperties ();

        if (!isset ($properties[$field_id])){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.method.property.unknown', array ($this->name, $field_id)));
        }

        if($this->type=='update'){
            if($properties[$field_id]->table != $this->_def->getPrimaryTableName ()){
                throw new Exception (_i18n ('copix:dao.error.definitionfile.method.property.forbidden', array($this->name, $field_id)));
            }
        }

        if (substr($value,0,1) == '$'){
            if (in_array (substr ($value,1),$this->_parameters)){
                $this->_searchParams->addCondition ($field_id, $operator, $value);
            }else{
                throw new Exception (_i18n ('copix:dao.error.definitionfile.method.parameter.unknow', array($this->name, $value)));
            }
        }else{
            if(substr($value,0,2) == '\$'){
                $value=substr($value,1);
            }
            $this->_searchParams->addCondition ($field_id, $operator, '\''.str_replace("'","\'",$value).'\'');
        }
    }

    /**
     * Ajout d'un paramètre
     */
    function addParameter($attributes){
        if (!isset ($attributes['name'])){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.method.parameter.unknowname', array($this->name)));
        }
        $this->_parameters[]=$attributes['name'];
    }

    function addOrder($attr){
        $prop = (isset ($attr['property'])? trim ($attr['property']) : '');
        $way  = (isset ($attr['way']) ? trim ($attr['way']) : 'ASC');
        $properties = $this->_def->getProperties ();
        
        if ($prop != ''){
            if(isset($properties[$prop])){
                $this->_searchParams->orderBy(array ($prop, $way));
            }else{
                throw new Exception (_i18n ('copix:dao.error.definitionfile.method.orderitem.bad', array($prop, $this->name)));
            }
        }else{
            throw new Exception (_i18n ('copix:dao.error.definitionfile.method.orderitem.bad', array($prop, $this->name)));
        }
    }

    /**
     * Ajout d'une valeur
     */
    function addValue($attr){
        $prop   = (isset ($attr['property'])?trim($attr['property']):'');
        $value  = (isset ($attr['value'])?trim($attr['value']):'');
        $properties = $this->_def->getProperties ();

        if ($prop == ''){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.method.values.property.unknow', array($this->name, $prop)));
        }
        if(!isset($properties[$prop])){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.method.values.property.unknow', array($this->name, $prop)));
        }
        if($properties[$prop]->table != $this->_def->getPrimaryTableName ()){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.method.values.property.bad', array($this->name,$prop )));
        }
        if($properties[$prop]->isPK){
            throw new Exception (_i18n ('copix:dao.error.definitionfile.method.values.property.pkforbidden', array($this->name,$prop )));
        }

        if (substr($value,0,1) == '$'){
            if (in_array (substr ($value,1),$this->_parameters)){
                $this->_values [$prop]= $this->_searchParams->_preparePHPValue($value, $properties[$prop]->type);
            }else{
                throw new Exception (_i18n ('copix:dao.error.definitionfile.method.values.unknowparameter', array($this->name, $value)));
            }
        }else{
            $this->_values[$prop] = $this->_searchParams->_preparePHPValue('\''.str_replace("'","\'",$value).'\'', $properties[$prop]->type);
        }
    }
}
?>