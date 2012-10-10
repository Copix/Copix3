<?php
/**
 * @package copix
 * @subpackage core
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Stocke les informations sur la page courante
 *
 * @package copix
 * @subpackage core
 */
class CopixPage {
	/**
	 * Pile des pages
	 *
	 * @var array
	 */
	private static $_pages = array ();

	/**
	 * Identifiant unique, généré au premier appel, et valide pour la page uniquement (sera reset lors d'un appel ajax, par exemple)
	 *
	 * @var string
	 */
	private static $_pageId = null;

	/**
	 * Identifiant de la page
	 *
	 * @var string
	 */
	private $_id = null;

	/**
	 * Module qui contient la page
	 *
	 * @var string
	 */
	private $_module = null;

	/**
	 * Titre de la page
	 *
	 * @var string
	 */
	private $_title = null;

	/**
	 * Indique si la page fait partie de l'administration
	 *
	 * @var boolean
	 */
	private $_isAdmin = false;

	/**
	 * Informations supplémentaires
	 *
	 * @var array
	 */
	private $_extras = array ();

	/**
	 * Ajoute une page (devrait être appelé dans chaque action)
	 *
	 * @return CopixPage
	 */
	public static function add () {
		return self::$_pages[count (self::$_pages)] = new CopixPage ();
	}

	/**
	 * Retourne les informations sur la page
	 *
	 * @param int $pIndex Index de la page, null pour avoir la dernière page ajoutée
	 * @return CopixPage
	 */
	public static function get ($pIndex = null) {
		$index = ($pIndex === null) ? count (self::$_pages) - 1 : $pIndex;
		if (!isset (self::$_pages[$index])) {
			$toReturn = new CopixPage ();
			$toReturn->setId ('unknow');
			$toReturn->setModule (_request ('module'));
			$toReturn->setTitle ('Unknow page');
			return $toReturn;
		}
		return self::$_pages[$index];
	}

	/**
	 * Retourne le nombre de pages définies
	 *
	 * @return int
	 */
	public static function count () {
		return count (self::$_pages);
	}

	/**
	 * Retourne un identifiant unique, généré au premier appel, et valide pour la page uniquement (sera reset lors d'un appel ajax, par exemple)
	 *
	 * @return string
	 */
	public static function getPageId () {
		return (self::$_pageId === null) ? self::$_pageId = uniqid () : self::$_pageId;
	}

	/**
	 * Définit l'identifiant
	 *
	 * @param string $pId Identifiant
	 */
	public function setId ($pId) {
		$this->_id = $pId;
	}

	/**
	 * Retourne l'identifiant
	 *
	 * @return string
	 */
	public function getId () {
		return $this->_id;
	}

	/**
	 * Définit le module qui contient la page
	 *
	 * @param string $pModule Nom du module
	 */
	public function setModule ($pModule) {
		$this->_module = $pModule;
	}

	/**
	 * Retourne le nom du module qui contient la page
	 *
	 * @return string
	 */
	public function getModule () {
		return $this->_module;
	}

	/**
	 * Définit le titre
	 *
	 * @param string $pTitle Titre
	 */
	public function setTitle ($pTitle) {
		$this->_title = $pTitle;
	}

	/**
	 * Retourne le titre
	 *
	 * @return string
	 */
	public function getTitle () {
		return $this->_title;
	}

	/**
	 * Définit si la page est dans l'administration
	 *
	 * @param boolean $pIsAdmin Indique si la page est dans l'administration
	 * @param boolean $pBreadcrumbAdmin Indique si on veut lancer l'event breadcrumb sur Administration
	 */
	public function setIsAdmin ($pIsAdmin, $pBreadcrumbAdmin = true) {
		$this->_isAdmin = $pIsAdmin;
		if ($pIsAdmin) {
			CopixTPL::setTheme (CopixConfig::get ('default|adminTheme'));
			if ($pBreadcrumbAdmin) {
				_notify ('breadcrumb', array ('path' => array ('admin||' => 'Administration')));
			}
		}
	}

	/**
	 * Indique si la page est dans l'administration
	 *
	 * @return boolean
	 */
	public function isAdmin () {
		return $this->_isAdmin;
	}

	/**
	 * Définit les informations supplémentaires
	 *
	 * @param array $pExtras Informations supplémentaires
	 */
	public function setExtras ($pExtras) {
		$this->_extras = $pExtras;
	}

	/**
	 * Retourne les informations supplémentaires
	 *
	 * @return array
	 */
	public function getExtras () {
		return $this->_extras;
	}
}