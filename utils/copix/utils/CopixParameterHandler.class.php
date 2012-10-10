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
 * Classe de base pour gérer des paramètres.
 *
 */
abstract class CopixParameterHandler {

	/**
	 * Tableau de l'ensemble des paramètres
	 *
	 * @var array
	 */
	private $_params = array();

	/**
	 * Tableau des paramètres non traités.
	 *
	 * @var array
	 */
	private $_extra = array();

	/**
	 * Erreurs.
	 *
	 * @var array
	 */
	private $_errors = array();

	/**
	 * Définit les paramètres.
	 *
	 * Remet à zéro les paramètres supplémentaires et les erreurs.
	 *
	 * @param array $pParams Nouveaux paramètres.
	 */
	public function setParams($pParams) {
		$this->_params = $pParams;
		$this->_extra = $pParams;
		$this->_errors = array();
	}

	/**
	 * Retourne l'ensemble des paramètres.
	 *
	 * @return array
	 */
	public function getParams() {
		return $this->_params;
	}

	/**
	 * Retourne les paramètres non traités.
	 *
	 * Tous les paramètres retournés sont marqués comme "traités".
	 *
	 * @return array
	 */
	public function getExtraParams() {
		$extra = $this->_extra;
		$this->_extra = array();
		return $extra;
	}

	/**
	 * Valide et convertit une valeur selon le type indiqué.
	 *
	 * Le type peut être le nom d'un type PHP comme ceux retourné par gettype($var).
	 *
	 * Cela peut aussi être le nom d'une classe, auquel cas on vérifie que la valeur est une instance de
	 * la classe indiquée.
	 *
	 * Dans le cas des entiers, booléens et flottants, une conversion est effectuée avec CopixFilter.
	 *
	 * @param mixed $pValue Valeur à vérifier
	 * @param string $pType Type de la valeur (ou null)
	 *   - int, integer, numeric : toujours valide, force une conversion en entier avec {@link CopixFilter::getInt()),
	 *   - bool, boolean : toujours valide, force une conversion en booléan avec {@link CopixFilter::getBoolean()},
	 *   - float, double, real : toujours valide, force une conversion en réel avec {$link copixFilter::getFloat()},
	 *   - string : force une conversion en chaîne de caractère,
	 *   - array : vérifie que $pValue est un tableau,
	 *   - object : vérifie que $pValue est une instance d'un objet, quelque soit sa classe,
	 *   - resource : vérifie que $pValue est une resource PHP,
	 *   - callable, callback, user function : vérifie que $pValue peut être utilisé comme callback,
	 *   - null, mixed : toujours valide, aucune conversion,
	 *   - autre : vérifie que $pValue est une instance de $pType. 
	 * @return array(isValid,valeur)
	 *   - isValid: vrai si la $pValue est valide pour le type indiqué,
	 *   - valeur: valeur convertie (quand c'est possible et/ou nécessaire).
	 */
	protected function _validateValue($pValue, $pType) {
		switch(strtolower($pType)) {
			case 'int': case 'integer': case 'numeric':
				return array(true, CopixFilter::getInt($pValue));

			case 'bool': case 'boolean':
				return array(true, CopixFilter::getBoolean($pValue));

			case 'float': case 'double': case 'real':
				return array(true, CopixFilter::getFloat($pValue));
					
			case 'string':
				return array(true, (string)$pValue);

			case 'array':
				return array(is_array($pValue), $pValue);

			case 'object':
				return array(is_object($pValue), $pValue);

			case 'resource':
				return array(is_resource($pValue), $pValue);

			case 'callable': case 'callback': case 'user function':
				return array(is_callable($pValue), $pValue);

			case null: case 'null': case 'mixed':
				return array(true, $pValue);
					
			default:
				return array(is_object($pValue) && $pValue instanceof $pType, $pValue);
		}
	}

	/**
	 * Extrait un paramètre de l'ensemble des paramètres.
	 *
	 * Marque le paramètre comme traité.
	 * Effectue la conversion et la validation de la valeur.
	 *
	 * Si le paramètre est défini mais que la valeur n'est pas valide, enregistre une erreur 'invalid'.
	 * 
	 * @uses _validateValue()
	 *
	 * @param string $pName Nom du paramètre.
	 * @param string $pType Type du paramètre.
	 * @return array(isSet, valid, value)
	 */
	private function _popParam($pName, $pType) {
		$valid = false;
		$value = null;
		if($isSet = isset($this->_params[$pName])) {
			$value = $this->_params[$pName];
			list($valid, $value) = $this->_validateValue($value, $pType);
			if(!$valid) {
				$this->_errors['invalid'][$pName] = $pType;
			}
		}
		unset($this->_extra[$pName]);
		return array($isSet, $valid, $value);
	}

	/**
	 * Récupère un paramètre optionnel.
	 *
	 * Si le type du paramètre n'est pas précisé mais qu'une valeur par défaut est fournie,
	 * le type de la valeur par défaut est pris en compte.
	 * 
	 * Par exemple :
	 * <code>
	 *   $p = $this->getParam('param', 5);
	 * </code>
	 * Est équivalent à :
	 * <code>
	 *   $p = $this->getParam('param', 5, 'integer');
	 * </code>
	 * 
	 * Si $pName est un tableau, getParam() agit de façon récursive. Elle retourne un tableau
	 * de valeurs, une pour chaque entrées de $pName ; la clef étant le nom du paramètre.
	 * Il est alors possible de fournir un tableau de valeurs par défaut, qui
	 * seront utilisées dans la même ordre que $pName. Si $pDefault n'est pas un tableau, il 
	 * est utilisé comme valeur par défaut de tous les paramètres. De la même façon, $pType
	 * peut être un tableau de type.
	 * 
	 * @uses _popParam()
	 *
	 * @param mixed $pName Nom du paramètre, ou tableau de noms de paramètres.
	 * @param mixed $pDefault Valeur par défaut, ou tableau de valeurs par défaut.
	 * @param mixed $pType Type de la valeur, ou tableau de types par défaut.
	 * @return mixed La valeur du paramètre ou un tableau des valeurs.
	 */
	public function getParam($pName, $pDefault = null, $pType = null) {
		if(is_array($pName)) {
			$toReturn = array();
			foreach($pName as $index=>$name) {
				$toReturn[$name] = $this->getParam(
					$name,
					is_array($pDefault) && isset($pDefault[$index]) ? $pDefault[$index] : $pDefault,
					is_array($pType) && isset($pType[$index]) ? $pType[$index] : $pType
				);
			}
			return $toReturn;
		}
		list($isSet, $valid, $value) = $this->_popParam($pName, (is_null($pType) && !is_null($pDefault)) ? gettype($pDefault) : $pType);
		return $valid && $isSet ? $value : $pDefault;
	}

	/**
	 * Récupère un paramètre obligatoire.
	 *
	 * Enregistre une erreur "missing" si le paramètre n'est pas défini.
	 *
	 * @uses _popParam()
	 *
	 * @param mixed pName Nom du paramètre, ou un tableau de noms de paramètres.
	 * @param mixed $pType Type de la valeur, ou un tableau des types de paramètres.
	 * @return mixed La valeur du paramètre ou null s'il n'est pas présent.
	 */
	public function requireParam($pName, $pType = null) {
		if(is_array($pName)) {
			$toReturn = array();
			foreach($pName as $index=>$name) {
				$toReturn[$name] = $this->requireParam(
					$name,
					is_array($pType) && isset($pType[$index]) ? $pType[$index] : $pType
				);
			}
			return $toReturn;
		}
		list($isSet, $valid, $value) = $this->_popParam($pName, $pType);
		if(!$isSet) {
			$this->_errors['missing'][$pName] = true;
		}
		return $valid ? $value : null;
	}

	/**
	 * Vérifie que les paramètres ont été traités correctement.
	 *
	 * Tous les paramètres supplémentaires non traités provoquent des erreurs 'unknown'.
	 *
	 * Appelle _reportErrors() si des erreurs ont été enregistrées.
	 *
	 * @uses _reportErrors()
	 *
	 * @return boolean Vrai si les paramètres sont valides.
	 */
	public function validateParams() {
		foreach($this->_extra as $key=>$value) {
			$this->_errors['unknown'][$key] = true;
		}
		if(count($this->_errors) > 0) {
			$this->_reportErrors($this->_errors);
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Définit le comportement en cas d'erreurs.
	 *
	 * Méthode à surcharger. Pourra aussi bien lancer une exception qu'afficher un warning...
	 *
	 * Les erreurs sont passées dans un tableau associatif à deux niveaux.
	 * Le premier niveau contient une entrée par type d'erreur: la clef est le type d'erreur,
	 * la valeur est un tableau contenant les paramètres en faute. Dans ce second tableaux,
	 * il y a une entrée par paramètre : la clef est le nom du paramètre, la valeur est information
	 * relative à l'erreur (ou vrai s'il y en a pas).
	 *
	 * Exemple :
	 * <code>
	 *   array(
	 *     'missing' => array(
	 *       'param1' => true,
	 *       'param3' => true
	 *     ),
	 *     'invalid' => array(
	 *       'param4' => 'integer'
	 *     )
	 *   );
	 * </code>
	 * 
	 * @param array $pErrors Tableaux des erreurs.
	 */
	abstract protected function _reportErrors($pErrors);

}

?>