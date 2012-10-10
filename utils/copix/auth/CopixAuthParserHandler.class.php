<?php
/**
 * @package copix
 * @subpackage auth
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Classe qui gère le parsing des module.xml pour enregistrer les handlers 
 *
 * @package copix
 * @subpackage auth
 */
class CopixAuthParserHandler {
	/**
	 * Parse les handlers de type User
	 *
	 * @param mixed $pXmlNode Node xml des userHandler
	 * @return array Un tableau de userHandler
	 */
	public static function parseUserHandler ($pXmlNode) {
		$toReturn = array ();
		foreach ($pXmlNode as $module => $userHandlers) {
			foreach ($userHandlers as $userHandler) {
				$tempUserHandler = array ();
				$tempUserHandler['name'] = (string)$module . '|' . $userHandler['name'];
				$tempUserHandler['required'] = (isset ($userHandler['required'])) ? ($userHandler['required'] != 'false') : null;
				$tempUserHandler['rank'] = (isset ($userHandler['rank'])) ? (string)$userHandler['rank'] : null;
				$tempUserHandler['module'] = (string) $module;
				$tempUserHandler['caption'] = $tempUserHandler['name'];  
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

	/**
	 * Parse les handlers de type Credential
	 *
	 * @param mixed $pXmlNode Node xml des credentialHandler
	 * @return array Un tableau de credentialHandler
	 */
	public static function parseCredentialHandler ($pXmlNode) {
		$toReturn = array ();
		foreach ($pXmlNode as $module => $credentialHandlers) {
			foreach ($credentialHandlers as $credentialHandler) {
				$tempCredentialHandler = array ();
				$tempCredentialHandler['name'] = (string)$module . '|' . $credentialHandler['name'];
				$tempCredentialHandler['stopOnSuccess'] = (isset ($credentialHandler['stopOnSuccess'])) ? ($credentialHandler['stopOnSuccess'] != 'false') : null;
				$tempCredentialHandler['stopOnFailure'] = (isset ($credentialHandler['stopOnFailure'])) ? ($credentialHandler['stopOnFailure'] != 'false') : null;
				$tempCredentialHandler['module'] = (string) $module;
				if (isset ($credentialHandler->handle)) {
					$tempHandle = array ();
					foreach ($credentialHandler->handle as $handle) {
						$tempHandle[] = (string)$handle['name'];
					}
					$tempCredentialHandler['handle'] = $tempHandle;
				}
				if (isset ($credentialHandler->handleExcept)) {
					$tempHandleExcept = array ();
					foreach ($credentialHandler->handleExcept as $handleExcept) {
						$tempHandleExcept[] = (string)$handleExcept['name'];
					}
					$tempCredentialHandler['handleExcept'] = $tempHandleExcept;
				}
				
				$tempCredentialHandler['caption'] = $tempCredentialHandler['name'];  
				if (isset ($credentialHandler['caption'])){
					$tempCredentialHandler['caption'] = (string)$credentialHandler['caption'];  					
				}
				if (isset ($userHandler['captioni18n'])){
					CopixContext::push ($module);
					$tempCredentialHandler['caption'] = _i18n ((string)$credentialHandler['captioni18n']);
					CopixContext::pop ();
				}
				$toReturn[$tempCredentialHandler['name']] = $tempCredentialHandler;
			}
		}
		return $toReturn;
	}

	/**
	 * Parse les handlers de type Group
	 *
	 * @param mixed $pXmlNode Node xml des groupHandler
	 * @return array Un tableau de groupHandler
	 */
	public static function parseGroupHandler ($pXmlNode) {
		$toReturn = array ();
		foreach ($pXmlNode as $module => $groupHandlers) {
			foreach ($groupHandlers as $groupHandler) {
				$tempGroupHandler = array ();
				$tempGroupHandler['name'] = (string)$module . '|' . $groupHandler['name'];
				$tempGroupHandler['required'] = (isset ($groupHandler['required'])) ? ($groupHandler['required'] != 'false') : null;
				$tempGroupHandler['module'] = (string) $module;				

				$tempGroupHandler['caption'] = $tempGroupHandler['name'];  
				if (isset ($credentialHandler['caption'])){
					$tempGroupHandler['caption'] = (string)$groupHandler['caption'];  					
				}
				if (isset ($userHandler['captioni18n'])){
					CopixContext::push ($module);
					$tempGroupHandler['caption'] = _i18n ((string)$groupHandler['captioni18n']);
					CopixContext::pop ();
				}				
				$toReturn[$tempGroupHandler['name']] = $tempGroupHandler;
			}
		}
		return $toReturn;
	}
}