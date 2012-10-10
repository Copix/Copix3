<?php
/**
 * @package standard
 * @subpackage auth
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gestion de droits pour les modules
 * @package standard
 * @subpackage auth
 */
class dbModuleCredentialHandler implements ICopixCredentialHandler {
	
	/**
	 * S'assure que l'utilisateur peut réaliser la chaine de droit donnée
	 *
	 * @param	string		$pString	La chaine de droit à tester
	 * @param 	CopixUser	$pUser		L'utilisateur dont on teste les droits
	 * @return	boolean
	 */
	public function assert ($pStringType, $pString, $pUser){
		switch ($pStringType){
			case 'module':
				return $this->_module ($pString, $pUser);
			default: 
				return null;
		}
	}
	
	/**
	 * Gestion du type module
	 *
	 * @param string $pString	la chaine à tester
	 * @param CopixUser $pUser	l'utilisateur dont on teste les droits
	 */
	private function _module ($pString, $pUser){
	    _classInclude('auth|dbmodulegrouphandler');
		foreach ($pUser->getGroups () as $handler=>$arGroupForHandler) {
	    	foreach ($arGroupForHandler as $id=>$groupCaption){
	            $handlerCredential = new dbModuleGroupHandler ($handler,$id);
	            if ($handlerCredential->isOk ($pString)) {
	                return true;
	            }
	        }
	    }
	    return false;
	}
	
}
?>