<?php
/**
 * @package    copix
 * @subpackage ajax
 * @author     Guillaume Perréal
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Session AJAX de page.
 * @package    copix
 * @subpackage ajax 
 */
class CopixAJAXSession implements Countable, IteratorAggregate {

	/**
	 * Durée de vie d'une session en secondes.
	 *
	 */
	const TIMEOUT = 300;

	/**
	 * Valeurs en session.
	 *
	 * @var array
	 */
	private $_values = array ();

	/**
	 * Date de dernière utilisation.
	 *
	 * @var integer
	 */
	private $_lastModified;

	/**
	 * Identifiant de la page à laquelle cette session est associé.
	 *
	 * @var string
	 */
	private $_sessionId;


	/**
	 * Indique que la session vient juste d'être créée.
	 *
	 * @var boolean
	 */
	private $_newSession;

	/**
	 * Représentation textuelle.
	 *
	 * @return string
	 */
	public function __toString() {
		ob_start ();
		var_dump ($this);
		$dump = ob_get_contents ();
		ob_end_clean ();

		preg_match('/^object\(CopixAJAXSession\)\[(\d+)\]/', $dump, $parts);
		return 'CopixAJAXSession('.$this->_sessionId.')['.(isset ($parts[1]) ? $parts[1] : '').']';
	}

	/**
	 * Crée une nouvelle session pour cette page.
	 *
	 * @param string $psessionId Identifiant de la page.
	 */
	public function __construct ($pSessionId) {
		$this->_sessionId = $pSessionId;
		$this->_lastModified = time ();
		$this->_newSession = true;
	}

	/**
	 * Au "réveil" (désérialisation), met $this->_newSession à faux.  
	 */
	public function __wakeup () {
		$this->_newSession = false;
	}

	/**
	 * Détermine si la session vient juste d'être créée.
	 *
	 * @return boolean Vrai si la session vient d'être créée.
	 */
	public function isNewSession() {
		return $this->_newSession;
	}

	/**
	 * Retourne l'identifiant de la page à laquelle cette session est associé
	 *
	 * @return string
	 */
	public function getSessionId () {
		return $this->_sessionId;
	}

	/**
	 * Détermine si cette session n'est plus utilisée.
	 *
	 * @return boolean Vrai si on peut la détruire sans risque.
	 */
	public function isStale () {
		return time () - $this->_lastModified > self::TIMEOUT;
	}

	/**
	 * Met à jour la date de dernière utilisation.
	 */
	public function touch () {
		$this->_lastModified = time ();
	}

	/**
	 * Récupère une valeur pour une clef donnée.
	 *
	 * @param string $pKey Clef de la valeur.
	 * @return mixed Valeur.
	 */
	public function &__get ($pKey) {
		return $this->_values[$pKey];
	}

	/**
	 * Définit la valeur pour une clef donnée.
	 *
	 * @param string $pKey Clef de la valeur.
	 * @param mixed $pValue Valeur à fixer.
	 * @return mixed Référence à la valeur.
	 */	
	public function __set ($pKey, $pValue) {
		$this->_values[$pKey] = $pValue;
		return $this->_values[$pKey];
	}

	/**
	 * Teste si une valeur est définie pour une clef donnée.
	 *
	 * @param string $pKey Clef de la valeur.
	 * @return boolean Vrai si la valeur est définie. 
	 */
	public function __isset ($pKey) {
		return isset ($this->_values[$pKey]);
	}

	/**
	 * Supprime une valeur
	 *
	 * @param string $pKey Clef de la valeur.
	 */
	public function __unset ($pKey) {
		unset ($this->_values[$pKey]);
	}

	/**
	 * Retourne le nombre de valeurs.
	 *
	 * @return integer
	 */
	public function count () {
		return count ($this->_values);
	}

	/**
	 * Crée un itérateur sur les valeurs.
	 *
	 * @return Iterator
	 */
	public function getIterator () {
		return new ArrayIterator ($this->_values);
	}
	/**
	 * Retourne la valeur pour une clef donnée.
	 *
	 * @see __get ()
	 *
	 * @param integer|string $pKey
	 * @param mixed $pDefault Valeur par défaut.
	 * @return mixed Donnée en session.
	 */
	public function get ($pKey, $pDefault = null) {
		if($this->__isset($pKey)) {
			return $this->__get($pKey);
		} else {
			return $pDefault; 
		}
	}

	/**
	 * Définit la valeur pour une clef donnée.
	 *
	 * @see __set ()
	 *
	 * @param integer|string $pKey
	 * @param mixed $pValue Valeur à définir.
	 */
	public function set ($pKey, $pValue) {
		return $this->__set ($pKey, $pValue);
	}

	/**
	 * Combinaison de get () et delete () : supprime l'entrée et retourne sa valeur.
	 *
	 * @see get (), __unset ()
	 *
	 * @param string $pKey Clef de la valeur.
	 * @param mixed $pValue Valeur à définir.
	 * @return mixed Donnée en session.
	 */
	public function pop ($pKey, $pDefault = null) {
		$value = $this->get($pKey, $pDefault);
		$this->__unset($pKey);
		return $value;
	}

	/**
	 * Supprime une valeur pour une clef donnée.
	 *
	 * @see offsetUnset ()
	 *
	 * @param string $pKey
	 */
	public function delete ($pKey) {
		return $this->__unset($pKey);
	}
}