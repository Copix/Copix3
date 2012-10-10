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
 * Affiche un lien pour revenir en arrière
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagBack extends CopixTemplateTag {
	/**
	 * Retourne l'HTML
	 *
	 * @param string $pContent Contenu de base
	 */
	public function process ($pContent = null) {
		$tpl = new CopixTpl ();
		$tpl->assign ('url', _url ($this->getParam ('url', _url ())));
		$tpl->assign ('align', $this->getParam ('align', 'right'));
		return $tpl->fetch ('default|taglib/back.php');
	}
}