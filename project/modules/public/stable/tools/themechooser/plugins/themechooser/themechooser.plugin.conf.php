<?php
/**
 * @package		tools
 * @subpackage	themechooser
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Choix d'un thème par utilisateur
 * @package		tools
 * @subpackage	themechooser
 */
class PluginConfigThemeChooser {
	/**
	 * Liste des thèmes disponibles
	 *
	 * @var array
	 */
	private $arTheme = array ();

	/**
	 * Initialisation des thèmes disponibles au choix.
	 */
	public function __construct (){
		$this->arTheme = array ('generation','bigtoukan');
	}

	/**
	 * Récupère la liste des thèmes préconfigurés.
	 * @return array
	 */
	public function getThemeList () {
		return $this->arTheme;
	}
}
?>
