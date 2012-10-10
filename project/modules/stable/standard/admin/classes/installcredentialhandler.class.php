<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gestionnaire de droit qui accepte 'tout' si jamais nous sommes en cours d'installation
 * @package standard
 * @subpackage admin 
 */
class InstallCredentialHandler implements ICopixCredentialHandler {
	/**
	 * S'assure que l'utilisateur peut réaliser la chaine de droit donnée
	 *
	 * @param	string		$pString	La chaine de droit à tester
	 * @param 	CopixUser	$pUser		L'utilisateur dont on teste les droits
	 * @return	boolean
	 */
	public function assert ($pStringType, $pString, $pUser){
		if (!CopixModule::isEnabled ('auth')){
			return true;
		}
		return null;
	}
}