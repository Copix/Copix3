<?php
/**
 * @package    copix
 * @subpackage utils
 * @author     Guillaume Perréal
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de base pour la construction de Javascript.
 *
 */
class CopixJSBase implements ICopixJSONEncodable {

	/**
	 * Code Javascript.
	 *
	 * @var mixed
	 */
	protected $_code;

	/**
	 * Constructeur.
	 *
	 * @param mixed $pCode Code initial.
	 */
	public function __construct($pCode) {
		$this->_code = $pCode;
	}

	/**
	 * Représentation texte : retourne le code Javascript.
	 *
	 * @return string Code Javascript.
	 */
	public function __toString() {
		return _toString($this->_code);
	}

	/**
	 * Conversion en JSON : retourne la représentation texte.
	 *
	 * @return string Code Javascript.
	 */
	public function toJSON() {
		return $this->__toString();
	}

	/**
	 * Construit la représentation d'un appel de fonction.
	 *
	 * @param string $pName Nom de la fonction/méthode.
	 * @param array $pArgs Liste des arguments.
	 * @return string Appel de fonction.
	 */
	protected function _buildCall($pName, $pArgs) {
		return $pName.'('.implode(',', array_map(array('CopixJSONEncoder', 'encode'), $pArgs)).')';
	}
	
}

/**
 * Fragment de code Javascript.
 * 
 * Un fragment de code peut être une instruction (STATEMENT) ou une expression (EXPRESSION).
 * 
 * Lorsqu'un fragment de type instruction est utilisé comme une expression (à droite d'une assignation,
 * dans une liste d'argument, comme objet), son type change automatiquement.
 *
 */
class CopixJSFragment extends CopixJSBase implements ArrayAccess {

	/**
	 * Type de fragment : instruction qui pourrait être utilisée comme une expression.
	 *
	 */
	const STATEMENT = 0;
	
	/**
	 * Type de fragement : expression.
	 *
	 */
	const EXPRESSION = 1;
		
	/**
	 * Type de fragement.
	 *
	 * @var integer
	 */
	protected $_kind;

	/**
	 * Widget
	 *
	 * @var CopixJSWidget
	 */
	protected $_widget;
	
	/**
	 * Construit un fragment de code.
	 *
	 * @param string $pCode Code.
	 * @param integer $pKind Type.
	 * @param CopixJSWidget $pWidget Widget.
	 */
	public function __construct($pCode, $pKind, $pWidget) {
		parent::__construct($pCode);
		$this->_widget = $pWidget;
		$this->_kind = $pKind;
	}

	/**
	 * Retourne la représentation JSON du fragement.
	 * 
	 * Change le type du fragment, qui devient une expression. 
	 *
	 * @return string Code Javascript.
	 */
	public function toJSON() {
		$this->_kind = CopixJSFragment::EXPRESSION;
		return parent::toJSON();
	}

	/**
	 * Détermine si le fragment est une instruction.
	 *
	 * @return boolean Vrai si le est de type instruction.
	 */
	public function isStatement_() {
		return $this->_kind == CopixJSFragment::STATEMENT;
	}

	/**
	 * Génère un appel de méthode.
	 *
	 * @param string $pMethod Nom de la méthode.
	 * @param array $pArgs Arguments.
	 * @return CopixJSFragment Un fragment de type instruction.
	 */
	public function __call($pMethod, $pArgs) {
		return $this->_widget->addStatement_($this->toJSON().'.'.$this->_buildCall($pMethod, $pArgs));
	}

	/**
	 * Génère une récupération de propriété.
	 *
	 * @param string $pName Nom de la propriété.
	 * @return CopixJSFragment Un fragment de type expression.
	 */
	public function __get($pName) {
		return $this->_widget->addExpression_($this->toJSON().'.'.$pName);
	}

	/**
	 * Génère une assignation de propriété.
	 * 
	 * Crée un fragment de type instruction.
	 *
	 * @param string $pName Nom de la propriété.
	 * @param mixed $pValue Valeur.
	 */
	public function __set($pName, $pValue) {
		$this->_widget->addStatement_($this->toJSON().'.'.$pName.' = '.CopixJSONEncoder::encode($pValue));
	}
	
	/**
	 * Génère une suppression de propriété.
	 * 
	 * Crée un fragement de type instruction.
	 *
	 * @param string $pName Nom de la propriété.
	 */
	public function __unset($pName) {
		$this->_widget->addStatement_('delete '.$this->toJSON().'.'.$pName);
	}
	
	/**
	 * Génère la récupération d'une cellule d'un tableau.
	 *
	 * @param mixed $pIndex Offset.
	 * @return CopixJSFragment Un fragment de type expression.
	 */
	public function offsetGet($pIndex) {
		return $this->_widget->addExpression_($this->toJSON().'['.CopixJSONEncoder::encode($pIndex).']');
	}

	/**
	 * Génère une assignation à une cellule d'un tableau.
	 * 
	 * Crée un fragement de type instruction. 
	 *
	 * @param mixed $pIndex Index de la cellule.
	 * @param mixed $pValue Valeur.
	 */
	public function offsetSet($pIndex, $pValue) {
		$this->_widget->addStatement_($this->toJSON().'['.CopixJSONEncoder::encode($pIndex).'] = '.CopixJSONEncoder::encode($pValue));
	}

	/**
	 * Génère la supression d'une cellule d'un tableau.
	 * 
	 * Crée un fragement de type instruction. 
	 * 
	 * @param mixed $pIndex Index de la cellule.
	 */
	public function offsetUnset($pIndex) {
		$this->_widget->addStatement_('delete '.$this->toJSON().'['.CopixJSONEncoder::encode($pIndex).']');
	}
	
	/**
	 * Génère une vérification d'
	 *
	 * @param mixed $pIndex Index de la cellule.
	 * @return CopixJSFragment Un fragment de type expression.
	 */
	public function offsetExists($pIndex) {
		return $this->_widget->addExpression_($this->toJSON().'['.CopixJSONEncoder::encode($pIndex).']');
	}
	
	/**
	 * Génère une expression de construction d'un objet.
	 * 
	 * @param mixed ... Arguments du constructeur.
	 * @return CopixJSFragment Un fragment de type expression.
	 */
	public function new_() {
		$args = func_get_args();
		return $this->_widget->addExpression_('new '.$this->_buildCall($this->toJSON(), $args));
	}
	
}

/**
 * Objet permettant la construction de code Javascript.
 * 
 * Cet objet correspond à un bloc d'instructions Javascript.
 *
 */
class CopixJSWidget extends CopixJSBase {
	
	/**
	 * Liste des variables "locales" du bloc.
	 *
	 * @var mixed
	 */
	protected $_vars = array();
	
	/**
	 * Construit un nouveau bloc de code.
	 *
	 * @param mixed $pVars Nom des variables locales existantes.
	 */
	public function __construct($pVars = null) {
		parent::__construct(array());
		if($pVars) {
			foreach($pVars as $varName) {
				$this->__get($varName);
			}
		}
	}

	/**
	 * Ajoute une instruction.
	 * 
	 * L'instruction est ajoutée au code du bloc.
	 *
	 * @param string $pCode Code de l'instruction.
	 * @return CopixJSFragment Un fragment de type instruction.
	 */
	public function addStatement_($pCode) {
		return $this->_code[] = new CopixJSFragment($pCode, CopixJSFragment::STATEMENT, $this); 
	}
 
	/**
	 * Ajoute une expression.
	 *
	 * @param string $pCode Code de l'expression.?
	 * @return CopixJSFragment Un fragment de type expression.
	 */
	public function addExpression_($pCode) {
		return new CopixJSFragment($pCode, CopixJSFragment::EXPRESSION, $this);
	}

	/**
	 * Ajoute un bloc "brut".
	 * 
	 * @see addStatement_()
	 *
	 * @param string $pCode Code de l'instruction.
	 * @return CopixJSFragment Un fragment de type instruction.
	 */
	public function raw_($pCode) {
		return $this->addStatement_($pCode);
	}
	
	/**
	 * Filtre les instructions.
	 *
	 * @param mixed $pItem Fragment de code.
	 * @return boolean Vrai si $pItem est CopixJSFragment de type instruction.
	 */
	private function filterStatements_($pItem) {
		return ($pItem instanceof CopixJSFragment) && $pItem->isStatement_();
	}

	/**
	 * Génère le code Javascript du bloc.
	 * 
	 * Seul les instructions sont prises en compte puisque les expressions ont été utilisées
	 * dans d'autres fragments.
	 *
	 * @return string Code Javascript.
	 */
	public function __toString() {
		$stmts = array_map('_toString', array_filter($this->_code, array($this, 'filterStatements_')));
		return count($stmts) ? implode(";\n", $stmts).';' : '';
	}

	/**
	 * Génère une expression correspondant à l'accès à une variable locale.
	 * 
	 * La variable est ajoutée à la liste des variables locales ($this->_vars).
	 *
	 * @param string $pName Nom de la variable.
	 * @return CopixJSFragment Un fragment de type expression.
	 */
	public function __get($pName) {
		if(isset($this->_vars[$pName])) {
			return $this->_vars[$pName];
		}
		return $this->_vars[$pName] = $this->addExpression_($pName);
	}

	/**
	 * Génère l'assignation à une variable locale.
	 * 
	 * Si la variable n'a pas encore été déclarée, génère une instruction var.
	 *
	 * @param string $pName Nom de la variable.
	 * @param mixed $pValue Valeur à assigner.
	 */
	public function __set($pName, $pValue) {
		$stmt = $pName.' = '.CopixJSONEncoder::encode($pValue);
		if(!isset($this->_vars[$pName])) {
			$stmt = 'var '.$stmt;
		}
		$this->addStatement_($stmt);
		$this->__get($pName);
	}
	
	/**
	 * Détermine si une variable a été déclarée localement.
	 * 
	 * Attention: ne génère pas de code Javascript.
	 *
	 * @param string $pName Nom de la variable.
	 * @return boolean Vrai si la variable a été déclarée.
	 */
	public function __isset($pName) {
		return isset($this->_vars[$pName]);
	}

	/**
	 * Génère un appel de fonction.
	 *
	 * @param string $pName Nom de la fonction.
	 * @param array $pArgs Arguments.
	 * @return CopixJSFragment Un fragment de type instruction.
	 */
	public function __call($pName, $pArgs) {
		return $this->addStatement_($this->_buildCall($pName, $pArgs));
	}
		
	/**
	 * Génère une déclaration de fonction.
	 *
	 * @param string $pName Nom de la fonction, ou null pour créer une fonction anonyme.
	 * @param mixed $pArgs Liste des arguments (tableau ou chaîne). 
	 * @param string $pBody Corps de la fonction.
	 * @return CopixJSFragment Un fragment de type instruction si la fonction est nommée 
	 *                         ou de type expression pour une fonction anonyme.
	 */
	public function function_($pName, $pArgs, $pBody) {
		$code = 
			'function'.(empty($pName) ? '' : ' '.$pName).
			'('.(is_array($pArgs) ? join(',', $pArgs) : (!empty($pArgs) ? $pArgs : '')).')'
			.'{'._toString($pBody).'}';
		if(empty($pName)) {
			return $this->addExpression_($code);
		} else {
			$this->addStatement_($code);
		}
	}
	
	/**
	 * Génère une déclaration de variable locale si la variable n'a pas encore été déclarée.
	 *
	 * @param string $pName Nom de la variable.
	 * @return CopixJSFragment Un fragment de type expression.
	 */
	public function var_($pName) {
		if(!isset($this->_vars[$pName])) {
			$this->addStatement_('var '.$pName);
		}
		return $this->__get($pName);
	}
	
	/**
	 * Génère une instruction "return".
	 *
	 * @param mixed $pValue Valeur à retourner.
	 */
	public function return_($pValue) {
		$this->addStatement_('return '.CopixJSONEncoder::encode($pValue));
	}
	
	/**
	 * Génère un appel à la fonction '$'.
	 *
	 * @param mixed ... Argument de la fonction '$'.
	 * @return CopixJSFragment Un fragment de type expression.
	 */
	public function _() {
		$args = func_get_args();
		return $this->__call('$', $args);
	}
	
	/**
	 * Génère un appel à la fonction '$$'.
	 *
	 * @param mixed ... Argument de la fonction '$$'.
	 * @return CopixJSFragment Un fragment de type expression.
	 */
	public function __() {
		$args = func_get_args();
		return $this->__call('$$', $args);
	}

	/**
	 * Génère un appel à la fonction '$A'.
	 *
	 * @param mixed ... Argument de la fonction '$A'.
	 * @return CopixJSFragment Un fragment de type expression.
	 */	
	public function _A() {
		$args = func_get_args();
		return $this->__call('$A', $args);
	}
	
}

?>