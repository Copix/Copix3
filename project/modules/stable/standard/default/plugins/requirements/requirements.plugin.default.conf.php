<?php
/**
 * @package standard
 * @subpackage refault
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe de configuration pour le plugin requirements
 *
 * @package standard
 * @subpackage default
 */
class PluginDefaultConfigRequirements {
	/**
	 * Indique si on doit tester le navigateur
	 * 
	 * @var boolean
	 */
	private $_testUserAgent = true;

	/**
	 * Version minimum de IE
	 * /!\ Ce ne sont pas les versions de IE qu'on test, mais les index de la méthode _testUserAgentLevel
	 *
	 * @var int
	 */
	private $_minIEVersion = 6;

	/**
	 * Adresses précises à tester, array () pour toutes les urls du site
	 *
	 * @var array
	 */
	private $_urls = array ();

	/**
	 * Indique si on bloque totalement l'accès au site (false) ou si on peut aller voir la page demandée (true)
	 *
	 * @var boolean
	 */
	private $_allowRedirect = true;

	/**
	 * Thème à utiliser pour la page d'erreur, null pour le thème courant
	 *
	 * @var string
	 */
	private $_errorTheme = null;

	/**
	 * Indique si on doit tester le navigateur
	 *
	 * @return boolean
	 */
	public function getTestUserAgent () {
		return $this->_testUserAgent;
	}

	/**
	 * Retourne le niveau minimum de la version de IE
	 *
	 * @return int
	 */
	public function getMinIEVersion () {
		return $this->_minIEVersion;
	}

	/**
	 * Retourne les adresses précises à tester, array () pour toutes les urls du site
	 *
	 * @return array
	 */
	public function getURLS () {
		return $this->_urls;
	}

	/**
	 *  Indique si on bloque totalement l'accès au site (false) ou si on peut aller voir la page demandée (true)
	 *
	 * @return boolean
	 */
	public function getAllowRedirect () {
		return $this->_allowRedirect;
	}

	/**
	 * Retourne le thème à utiliser pour la page d'erreur, null pour le thème courant
	 *
	 * @return string
	 */
	public function getErrorTheme () {
		return $this->_errorTheme;
	}
}