<?php
/**
 * @package devtools
 * @subpackage sqldesigner
 * @author Steevan Barboyon
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gère les demandes de traductions par sqldesigner
 * 
 * @package devtools
 * @subpackage sqldesigner
 */
class ActionGroupDesigner extends CopixActionGroup {
	/**
	 * Retourne un XML au format sqldesigner, en prenant les infos dans un fichier .properties au format Copix
	 * 
	 * @return CopixActionReturn
	 */
	public function processGetLocale () {
		$ppo = new CopixPPO ();
		$locale = _request ('locale', 'fr');
		$ppo->MAIN = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
		$ppo->MAIN .= '<locale>' . "\n";
		$keys = CopixI18N::getBundle ('sqldesigner', $locale)->getKeys (null);
		foreach ($keys as $key => $value) {
			$ppo->MAIN .= '<string name="' . substr ($key, strpos ($key, '.') + 1) . '">' . $value . '</string>' . "\n";
		}
		$ppo->MAIN .= '</locale>';
		
		return _arContent ($ppo->MAIN, array ('content-type'=>CopixMIMETypes::getFromExtension ('.xml')));
	}
	
	public function processSave () {
		$content = str_replace ('[__AMP__]', '&amp;', _request ('content'));
		$fileName = _request ('file') . '.sqldesigner.xml';
		file_put_contents (CopixModule::getPath ('sqldesigner') . COPIX_RESOURCES_DIR . $fileName, $content);
	}
}