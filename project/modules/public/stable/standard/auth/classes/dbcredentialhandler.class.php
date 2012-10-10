<?php
/**
 * @package standard
 * @subpackage auth
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gestion de droits ou les éléments sont stockés en base de données (utilisation des groupes dbhandler)
 * @package standard
 * @subpackage auth
 */
class DBCredentialHandler implements ICopixCredentialHandler {
	
	/**
	 * S'assure que l'utilisateur peut réaliser la chaine de droit donnée
	 *
	 * @param	string		$pString	La chaine de droit à tester
	 * @param 	CopixUser	$pUser		L'utilisateur dont on teste les droits
	 * @return	boolean
	 */
	public function assert ($pStringType, $pString, $pUser){
		switch ($pStringType){
			case 'basic':
				return $this->_basic ($pString, $pUser);
			case 'group':
				return $this->_group ($pString, $pUser);
			case 'module':
				return $this->_module ($pString, $pUser);
			default: 
				return null;
		}
	}
	
	/**
	 * Gestion du type basic
	 *
	 * @param string $pString	la chaine à tester
	 * @param CopixUser $pUser	l'utilisateur dont on teste les droits
	 */
	private function _basic ($pString, $pUser){
		switch ($pString){
			case 'admin':
				foreach ($pUser->getGroups () as $handler=>$arGroupForHandler){
					if ($handler == 'auth|dbgrouphandler'){
						$groupHandler = CopixGroupHandlerFactory::create ($handler);
						foreach ($arGroupForHandler as $id=>$groupCaption){
							$informations = $groupHandler->getInformations ($id);
							if ($informations->superadmin_dbgroup){
								return true;
							}
						}
					}
				}
				return false;

			case 'registered':
				return $pUser->isConnected ();
		}
	}
	
	/**
	 * Gestion du type "group"
	 * @param string $pString	la chaine de droit à tester
	 * @param CopixUser $pUser	l'utilisateur sur lequel on test les droits
	 */
	private function _group ($pString, $pUser){
		$arParts = explode ('@', $pString);
		//on regarde si c'est par caption ou id
		if (substr ($arParts[0], 0, 1) == '[' && substr ($arParts[0], -1) == ']'){
			$byCaption = true;
		}else{
			$byCaption = false;
		}
		
		//on regarde si la recherche est limitée à un seul handler
		if (count ($arParts) > 1){
			$byHandler = $arParts[1];
		}else{
			$byHandler = null;
		}
	
		foreach ($pUser->getGroups () as $handler=>$arGroupForHandler){
			foreach ($arGroupForHandler as $groupId=>$groupCaption){
				if (($byHandler === null) || ($byHandler === $handler)){
					if ($byCaption){
						if ($arParts[0] == '['.$groupCaption.']'){
							return true;
						}
					}else{
						if ($groupId == $arParts[0]){
							return true;
						}
					}
				}
			}
		}

		return false;
	}
	
	/**
	 * Gestion du type "module"
	 *
	 * @param string $pString	la chaine de droit à tester
	 * @param CopixUser $pUser	l'utilisateur sur lequel on test les droits
	 */
	private function _module ($pString, $pUser){
		//Si administrateur, ok, on a toujours les droits.
		if ($this->_basic ('admin', $pUser)){
			return true;
		}
		//Pas administrateur, on ne se prononce pas.
		return null;
	}
}
?>