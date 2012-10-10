<?php
/**
 * @package copix
 * @subpackage auth
 * @author DAmien Duboeuf
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Classe qui gère le parsing des module.xml pour enregistrer les handlers i18n
 *
 * @package copix
 * @subpackage auth
 */
class CopixI18nParserHandler {
	/**
	 * Parse les handlers de type i18n
	 *
	 * @param mixed $pXmlNode Node xml des i18nHandler
	 * @return array Un tableau de i18nHandler
	 */
	public static function parseI18nHandler ($pXmlNode) {
		$toReturn = array ();
		foreach ($pXmlNode as $module => $userHandlers) {
			foreach ($userHandlers as $userHandler) {
				$tempUserHandler = array ();
				$tempUserHandler['name'] = strtolower ((string)$userHandler['name']);
				$tempUserHandler['context'] = (string)$module;
				$tempUserHandler['order'] = (isset ($userHandler['order'])) ? (int)$userHandler['order'] : 99999;
				$tempUserHandler['caption'] = (string)$module . '|' . (string)$userHandler['name'];;  
				if (isset ($userHandler['caption'])){
					$tempUserHandler['caption'] = (string)$userHandler['caption'];  					
				}
				if (isset ($userHandler['captioni18n'])){
					CopixContext::push ($module);
					$tempUserHandler['caption'] = _i18n ((string)$userHandler['captioni18n']);
					CopixContext::pop ();
				}
				$toReturn[$tempUserHandler['name']] = $tempUserHandler;
			}
		}
		return $toReturn;
	}
}