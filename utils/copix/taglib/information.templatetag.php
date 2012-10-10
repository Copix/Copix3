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
 * Affiche un bloc d'information
 * Paramètres :
 * 		- message : chaine ou tableau de chaines, contenant le ou les messages
 * 		- title : titre du bloc
 * 		- titlei18n : titre i18n du bloc
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagInformation extends CopixTemplateTag {
	/**
	 * Retourne le contenu HTML du tag
	 *
	 * @param array $pParams Paramètres passés au tag
	 * @return string
	 */
	public function process ($pParams = null) {
		$message = $this->getParam ('message');
		if ((is_array ($message) && count ($message) == 0) || trim ($message) == '') {
			return null;
		}
		
		$tpl = new CopixTPL ();
		
		if (!is_array ($message)) {
			$message = array ($message);
		}
		$tpl->assign ('message', $message);

		if ($this->getParam ('titlei18n') != null) {
			$tpl->assign ('title', _i18n ($this->getParam ('titlei18n')));
		} else {
			$tpl->assign ('title', $this->getParam ('title'), _i18n ('copix:taglib.information.title'));
		}

		$links = $this->getParam ('links', array ());
		$realLinks = array ();
		foreach ($links as $url => $caption) {
			$realLinks[_url ($url)] = $caption;
		}
		$tpl->assign ('links', $realLinks);

		if ($this->getParam ('redirect_url') != null) {
			$time = $this->getParam ('redirect_time');
			if ($time == null) {
				$time = CopixConfig::get ('default|messageRedirectTime');
			}
			CopixHTMLHeader::addOthers ('<meta http-equiv="refresh" content="' . $time . '; url=' . _url ($this->getParam ('redirect_url')) . '" />');
		}
		
		return $tpl->fetch ('default|taglib/information.php');
	}
}