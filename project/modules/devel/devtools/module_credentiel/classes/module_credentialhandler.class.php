<?php
/**
 * @package module_credentiel
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gestion de droits pour les modules
 * @package module_credentiel
 */
class module_CredentialHandler implements ICopixCredentialHandler {
	
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
	    foreach ($pUser->getGroups () as $handler=>$arGroupForHandler) {
	        foreach ($arGroupForHandler as $id=>$groupCaption){
	            if (_ioClass('module_credentiel|module_groupHandler')->isOk ($handler, $id, $pString)) {
	                return true;
	            }
	        }
	    }
	    return false;
	}
	
}
?>