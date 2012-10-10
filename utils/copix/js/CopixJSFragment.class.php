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
	 * On ne peut pas générer une vérification d'une cellule d'un tableau.
	 *
	 * @param mixed $pIndex Index de la cellule.
	 */
	public function offsetExists($pIndex) {
		throw new CopixException(null);
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