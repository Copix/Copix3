<?php
/**
* @package copix
* @subpackage taglib
* @author Steevan BARBOYON
* @copyright CopixTeam
* @link http://www.copix.org
* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Sélection du thème
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagThemeChooser extends CopixTemplateTag {
	/**
	 * Construction du code HTML
	 *
	 * @param array $pParams Paramètres
	 * @return string
	 */
	public function process ($pContent = null) {
		$this->assertParams ('input');

		$tpl = new CopixTPL ();
		$themes = CopixTheme::getList ();
		$informations = array ();
		foreach ($themes as $id => $caption) {
			$informations[] = CopixTheme::getInformations ($id);
		}
		$tpl->assign ('themes', $informations);
		if ($this->getParam ('selected') != null) {
			$tpl->assign ('selected', CopixTheme::getInformations ($this->getParam ('selected')));
		} else {
			$tpl->assign ('selected', false);
		}
		$tpl->assign ('input', $this->getParam ('input'));
		$tpl->assign ('showName', $this->getParam ('showName', true));
		$tpl->assign ('clicker', $this->getParam ('clicker', uniqid ('selectTheme')));
		return $tpl->fetch ($this->getParam ('template', 'default|taglib/themechooser.php'));
	}
}