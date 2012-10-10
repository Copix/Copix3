<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Steevan Barboyon
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Implémente les selecteurs de fichiers/composant de theme (theme:)
 * 
 * @package		copix
 * @subpackage	utils
 */
class CopixThemeFileSelector extends CopixAbstractSelector {
	/**
	 * Thème attaché
	 * 
	 * @var string
	 */
	private $_theme = 'default';
	
	/**
	 * Sélecteur demandé
	 *
	 * @var string
	 */
	private $_selector = null;

	/**
	 * Constructeur
	 * 
	 * @param string $pSelector Sélecteur de theme (theme:myTheme|fileName)
	 */
	public function __construct ($pSelector) {
		$this->type = 'theme';
		
		$tab = explode (':', $pSelector);
		if (count ($tab) <> 2) {
			throw new CopixException (_i18n ('copix:copix.error.fileselector.invalidSelector', $pSelector));
		}
		$selector = explode ('|', $tab[1]);
		$this->_theme = (count ($selector) == 1) ? CopixTPL::getThemeName () : $selector[0];
		$this->fileName = (count ($selector) == 1) ? $selector[0] : $selector[1];
		$this->_selector = 'theme:' . $this->_theme . '|' . $this->fileName;
	}

	/**
	 * Retourne le chemin d'accès complet à la ressource
	 * 
	 * @param string $pDirectory Chemin vers le fichier
	 * @return string
	 */
	protected function _getPath ($pDirectory) {
		return CopixTpl::getThemePath ($this->_theme) . $pDirectory;
	}

	/**
	 * Récupère le sélecteur Copix complet de l'élément
	 * 
	 * @return string
	 */
	protected function _getSelector () {
		return $this->_selector;
	}

	/**
	 * Récupère la partie qualifier de l'élément
	 * 
	 * @return string
	 */
	protected function _getQualifier () {
		return 'theme:';
	}
}