<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link			http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagI18N extends CopixTemplateTag {
	/**
	 * Construction du message
	 * @param	mixed	$pParams	tableau de paramètre ou clef
	 * @param 	mixed	$pContent	null (i18n n'est pas censé recevoir de contenu)
	 * @return 	string	message traduit
	 */
	public function process ($pContent=null){
		$pParams = $this->getParams ();
		if (! is_array ($pParams)){
			$pParams = array ('key'=>$pParams);
			$this->setParams ($pParams);
		}

		$this->requireParam ('key');
		$pParams = $this->getParams ();

		if (! is_array ($pParams)){
			$pParams = array ('key'=>$pParams);
		}

		if (isset($pParams['lang'])) {
			$lang = $pParams['lang'];
			unset ($pParams['lang']);
		}else{
			$lang = null;
		}

		$key = $pParams['key'];
		unset ($pParams['key']);

		if (isset ($pParams['noEscape'])){
			$noEscape = $pParams['noEscape'];
			unset ($pParams['noEscape']);
		}

		if (count ($pParams) == 0){
			$pParams = null;
		}

		$message = CopixI18N::get ($key, $pParams, $lang);
		if (!isset ($noEscape)){
			return _copix_utf8_htmlentities ($message);
		}
		return $message;
	}
}