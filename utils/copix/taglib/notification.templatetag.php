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
 * Affiche un bloc de notification
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagNotification extends CopixTemplateTag {
	/**
	 * Retourne le contenu HTML du tag
	 *
	 * @param array $pParams Paramètres passés au tag
	 * @return string
	 */
	public function process ($pParams = null) {
		$this->assertParams ('message');

		$tpl = new CopixTPL ();
		$title = $this->getParam ('title');
		if ($title == null && $this->getParam ('titlei18n') != null) {
			$title = _i18n ($this->getParam ('titlei18n'));
		} else if ($title == null) {
			$title = 'Notification';
		}
		$tpl->assign ('title', $title);
		$tpl->assign ('message', $this->getParam ('message'));
		return $tpl->fetch ('default|taglib/notification.php');
	}
}