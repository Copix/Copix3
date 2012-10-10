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
 * Objet comportant les données d'une propriété d'un DAO
 * 
 * @package		copix
 * @subpackage	dao
 */
class CopixDAOMethodDefinition {
	/**
	 * Nom de la méthode
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * Type de la méthode
	 * 
	 * @var string
	 */
	public $type;

	/**
	 * Paramètres de recherche
	 * 
	 * @var CopixDAOSearchParams ou null
	 */
	private $_searchParams = null;
	
	/**
	 * Paramètres
	 * 
	 * @var array
	 */
	private $_parameters = array ();

	/**
	 * Limite du nombre de retours du DAO
	 * 
	 * @var array ou null
	 */
	private $_limit = null;

	/**
	 * Valeurs
	 * 
	 * @var array
	 */
	public $_values = array();
	
	/**
	 * Définition du DAO
	 * 
	 * @var CopixDAODefinition
	 */
	private $_def;
	
	/**
	 * Retourne les paramètres
	 * 
	 * @return array
	 */
	public function getParameters () {
		return $this->_parameters;
	}
	
	/**
	 * Retourne la limite
	 * 
	 * @return array ou null
	 */
	public function getLimit () {
		return $this->_limit;
	}
	
	/**
	 * Retourne les critères de recherche
	 * 
	 * @return CopixDAOSearchParams ou null
	 */
	public function getSearchParams (){
		return $this->_searchParams;
	}

	/**
	 * Constructeur
	 * 
	 * @param SimpleXMLElement $pMethod Informations sur la méthode
	 * @param CopixDAODefinition $pDef Définition du DAO
	 * @throws Exception
	 */
	public function __construct ($pMethod, $pDef) {
		$this->_def = $pDef;
		$attributes = array ();
		foreach ($pMethod->attributes () as $key => $value){
			$attributes[strtolower ($key)] = (string) $value;		
		}

		if (!isset ($attributes['name'])) {
			throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.missing.attr', array ('name', 'method')));
		}

		$this->name = $attributes['name'];
		$this->type = isset ($attributes['type']) ? strtolower ($attributes['type']) : 'select';

		if (isset ($pMethod->parameters) && isset ($pMethod->parameters->parameter)) {
			foreach ($pMethod->parameters->parameter as $param) {
				$this->addParameter($param->attributes ());
			}
		}
		if (isset ($pMethod->conditions)) {
			$methodConditionsAttributes = array ();
			foreach ($pMethod->conditions->attributes () as $key => $name) {
				$methodConditionsAttributes[strtolower ($key)] = (string) $name;				
			} 
			if (isset ($methodConditionsAttributes['logic'])) {
				$kind = $methodConditionsAttributes['logic'];
			} else {
				$kind = 'AND';
			}
			$this->_searchParams = CopixDAOFactory::createSearchParams ($kind);
			$this->_parseConditions ($pMethod, true);
		} else {
			$this->_searchParams = CopixDAOFactory::createSearchParams ('AND');
		}

		if ($this->type == 'update') {
			if (isset ($pMethod->values) && isset ($pMethod->values->value)) {
				foreach ($pMethod->values->value as $val) {
					$this->addValue ($val->attributes ());
				}
			} else {
				throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.values.undefine', array ($this->name)));
			}
		}
		if (isset ($pMethod->order) && isset ($pMethod->order->orderitem)) {
			foreach ($pMethod->order->orderitem as $item) {
				$this->addOrder ($item->attributes());
			}
		}

		if (isset ($pMethod->limit)) {
			if (count ($pMethod->limit) > 1) {
				throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.tag.duplicate', array ('limit', $this->name)));				
			}

			if ($this->type == 'select' || $this->type == 'selectfirst') {
				$attr = $pMethod->limit->attributes ();
				$offset = (isset ($attr['offset']) ? $attr['offset'] : null);
				$count = (isset ($attr['count']) ? $attr['count'] : null);

				if ($offset === null) {
					throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.missing.attr', array ('offset','limit')));
				}
				if ($count === null) {
					throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.missing.attr', array ('count','limit')));
				}

				if (substr ($offset, 0, 1) == '$') {
					if (in_array (substr ($offset, 1), $this->_parameters)) {
						$offset = ' intval(' . $offset . ')';
					} else {
						throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.limit.parameter.unknow', array ($this->name, $offset)));
					}
				} else {
					if (is_numeric ($offset)) {
						$offset = intval ($offset);
					} else {
						throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.limit.badvalue', array ($this->name, $offset)));
					}
				}

				if (substr ($count, 0, 1) == '$') {
					if (in_array (substr ($count, 1), $this->_parameters)) {
						$count = ' intval(' . $count . ')';
					} else {
						throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.limit.parameter.unknow', array ($this->name, $count)));
					}
				} else {
					if (is_numeric ($count)) {
						$count = intval ($count);
					} else {
						throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.limit.badvalue', array ($this->name, $count)));
					}
				}
				$this->_limit = compact ('offset', 'count');

			} else {
				throw new CopixDAODefinitionException ($pDef->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.limit.forbidden'));
			}
		}
	}

	/**
	 * Analyse des conditions
	 * 
	 * @param SimpleXMLElement $pNode Informations sur les conditions
	 * @param boolean $pFirst Indique si c'est la 1ère condition du groupe
	 */
	private function _parseConditions ($pNode, $pFirst = false) {
		if (isset ($pNode->conditions)) {
			if (!$pFirst) {
				$nodeConditionsAttributes = $pNode->conditions->attributes (); 
				if (isset ($nodeConditionsAttributes['logic'])){
					$kind = $nodeConditionsAttributes['logic'];
				} else {
					$kind = 'AND';
				}
				$this->_searchParams->startGroup ($kind);
			}

			foreach ($pNode->conditions as $cond) {
				if (isset ($pNode->conditions->condition)) {
					$this->addCondition ($pNode->conditions->condition);
				}
			}

			$this->_parseConditions ($pNode->conditions);

			if (!$pFirst) {
				$this->_searchParams->endGroup ();
			}
		}
	}

	/**
	 * Ajout d'une ou plusieurs condition(s)
	 * 
	 * @param SimpleXMLElement $pNode Informations sur la ou les condition(s)
	 */
	public function addCondition ($pNode) {
		foreach ($pNode as $param) {
			$this->_addCondition ($param->attributes ());
		}
	}

	/**
	 * Ajout d'une condition
	 * 
	 * @param array $pAttributes Attributs d'une condition
	 * @throws Exception
	 */
	private function _addCondition ($pAttributes) {
		$newAttributes = array ();
		foreach ($pAttributes as $key => $value){
			$newAttributes[strtolower ($key)] = (string) $value;
		}
		$pAttributes = $newAttributes;
		$field_id = (isset ($pAttributes['property'])) ? $pAttributes['property'] : '';
		$operator = (isset ($pAttributes['operator'])) ? $pAttributes['operator'] : '';
		$value = (isset ($pAttributes['value'])) ? $pAttributes['value'] : '';

		// for compatibility with dev version. valueofparam attribute = deprecated
		if (isset ($pAttributes['valueofparam'])) {
			$value = '$' . $pAttributes['valueofparam'];
		}

		$properties = $this->_def->getProperties ();

		if (!isset ($properties[$field_id])) {
			throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.property.unknown', array ($this->name, $field_id)));
		}

		if ($this->type == 'update') {
			if ($properties[$field_id]->table != $this->_def->getPrimaryTableName ()) {
				throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.property.forbidden', array ($this->name, $field_id)));
			}
		}

		if (substr ($value, 0, 1) == '$') {
			if (in_array (substr ($value, 1) ,$this->_parameters)) {
				$this->_searchParams->addCondition ($field_id, $operator, $value);
			} else {
				throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.parameter.unknow', array ($this->name, $value)));
			}
		} else {
			if (substr ($value, 0, 2) == '\$') {
				$value = substr ($value, 1);
			}
			$this->_searchParams->addCondition ($field_id, $operator, '\'' . str_replace ("'", "\'", $value) . '\'');
		}
	}

	/**
	 * Ajout d'un paramètre
	 * 
	 * @param array $pAttributes Attributs d'un paramètre
	 * @throws Exception
	 */
	public function addParameter ($attributes) {
		if (!isset ($attributes['name'])) {
			throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.parameter.unknowname', array ($this->name)));
		}
		$this->_parameters[] = $attributes['name'];
	}
	
	/**
	 * Ajoute un ordre de tri
	 *
	 * @param array $pAttributes Attributs d'une node pour l'ordre de tri
	 * @throws Exception
	 */
	public function addOrder ($pAttributes) {
		$prop = (isset ($pAttributes['property']) ? trim ($pAttributes['property']) : '');
		$way = (isset ($pAttributes['way']) ? trim ($pAttributes['way']) : 'ASC');
		$properties = $this->_def->getProperties ();
		
		if ($prop != '') {
			if (isset ($properties[$prop])) {
				$this->_searchParams->orderBy (array ($prop, $way));
			} else {
				throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.orderitem.bad', array ($prop, $this->name)));
			}
		} else {
			throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.orderitem.bad', array ($prop, $this->name)));
		}
	}

	/**
	 * Ajout d'une valeur
	 * 
	 * @param array $pAttributes Attributs d'une node pour l'ordre de tri
	 * @throws Exception
	 */
	public function addValue ($pAttributes) {
		$prop = (isset ($pAttributes['property']) ? trim($pAttributes['property']) : '');
		$value = (isset ($pAttributes['value']) ? trim($pAttributes['value']) : '');
		$properties = $this->_def->getProperties ();

		if ($prop == '') {
			throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.values.property.unknow', array ($this->name, $prop)));
		}
		if (!isset ($properties[$prop])) {
			throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.values.property.unknow', array ($this->name, $prop)));
		}
		if ($properties[$prop]->table != $this->_def->getPrimaryTableName ()) {
			throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.values.property.bad', array ($this->name, $prop )));
		}
		if ($properties[$prop]->isPK) {
			throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.values.property.pkforbidden', array ($this->name, $prop )));
		}

		if (substr ($value, 0, 1) == '$') {
			if (in_array (substr ($value, 1), $this->_parameters)) {
				$this->_values [$prop] = $this->_searchParams->_preparePHPValue ($value, $properties[$prop]->type);
			} else {
				throw new CopixDAODefinitionException ($this->_def->getDAOId (), _i18n ('copix:dao.error.definitionfile.method.values.unknowparameter', array ($this->name, $value)));
			}
		} else {
			$this->_values[$prop] = $this->_searchParams->_preparePHPValue ('\'' . str_replace ("'", "\'", $value) . '\'', $properties[$prop]->type);
		}
	}
}