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
class TemplateTagEndBlock extends CopixTemplateTag {
	/**
	 * Construction du code HTML
	 *
	 * @param string $pContent Contenu du tag
	 */
	public function process ($pContent = null) {
		$tpl = new CopixTPL ();
		return $tpl->fetch ('default|taglib/endblock.php');
	}
}