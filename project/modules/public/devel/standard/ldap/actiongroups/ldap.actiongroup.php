<?php

/**
 * @package standard
 * @subpackage ldap
 *
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions de configuration des profils LDAP.
 * @package standard
 * @subpackage ldap
 */

class ActionGroupLdap extends CopixActionGroup {

	/**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction ($actionName){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Fonction par défaut : Affichage du formulaire d'édition des profils de connexion LDAP 
	 */
	public function processDefault (){
		$ppo = new CopixPPO();
		$ppo->configurationFileIsWritable = true;
		return _arPPO ($ppo, 'ldap.form.php');
	} 
	
	/**
	 * Validation du formulaire
	 */
	public function processValidForm() {
		return _arRedirect (_url('ldap|ldap|'));
	}
}
?>