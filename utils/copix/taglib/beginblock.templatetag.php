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
 * Début d'un block
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagBeginBlock extends CopixTemplateTag {
	/**
	 * Construction du code HTML
	 *
	 * @param string $pContent Contenu du tag
	 */
	public function process ($pContent = null) {
		$tpl = new CopixTPL ();
		$title = $this->getParam ('title');
		if ($title == null && $this->getParam ('titlei18n') != null) {
			$title = _i18n ($this->getParam ('titlei18n'));
		}
		$tpl->assign ('title', $title);
		$tpl->assign ('icon', $this->getParam ('icon'));
		$tpl->assign ('id', $this->getParam ('id'));
		$tpl->assign ('isFirst', $this->getParam ('isFirst', false));
		return $tpl->fetch ('default|taglib/beginblock.php');
	}
}