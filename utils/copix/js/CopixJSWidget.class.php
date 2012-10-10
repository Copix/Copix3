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