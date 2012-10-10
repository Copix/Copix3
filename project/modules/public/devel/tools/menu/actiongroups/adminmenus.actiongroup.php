<?php
/**
 * @package		menu
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		tools 
 * @subpackage	menu 
 */
class ActionGroupAdminMenus extends CopixActionGroup {
    /**
	 * Verifications avant l'execution de l'actiongroup
	 */
	public function beforeAction ($actionName){
		_currentUser ()->assertCredential ('module:write@menu');
	}
    
    /**
     * Fonction par défaut, liste des menus disponibles
     */
    public function processDefault () {
        $ppo = new CopixPPO ();
        $ppo->TITLE_PAGE = _i18n ('admin.title_gestion_menus');
        $ppo->arrMenus = _ioDao('menus')->findBy (_daoSP ()->orderBy ('name_menu'));
        
        return _arPPO ($ppo, 'menus.list.tpl');
    }
    
    /**
     * Affichage formulaire modification
     */
    public function processEdit () {
    	// si on est en mode modification, sans erreur à afficher
		if ($menu_id = CopixRequest::getInt ('id_menu')) {
			if ($toEdit = _ioDAO ('menus')->get ($menu_id)) {
				CopixSession::set ('menu|menus|edit', $toEdit);
			}
		} else {
			$toEdit = CopixSession::get ('menu|menus|edit');
		} 

		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n ('admin.title_edit_menu');
		$ppo->editedMenu = $toEdit;
 
		// si on demande à afficher les messages d'erreurs
		$ppo->arErrors = (_request ('errors')) ? _ioDAO ('menus')->check ($toEdit) : array ();
        $ppo->arrMenus = _ioDao('menus')->findBy (_daoSP ()->orderBy ('name_menu'));

        return _arPpo ($ppo, 'menus.list.tpl');
    }
    
    /**
     * Validation d'un ajout / modification
     */
    public function processValid () {
    	CopixRequest::assert ('name_menu');

		// on vérifie que l'on est bien en train de modifier / ajouter un élément, sinon on retourne à la liste.
		if (($toEdit = CopixSession::get ('menu|menus|edit')) === null){
 			$toEdit = _record ('menus');
		}

		// validation des modification depuis le formulaire
		$toEdit->name_menu = _request ('name_menu');
		CopixSession::set ('menu|menus|edit', $toEdit);
		
		// sauvegarde (le check est fait dans les méthodes add et edit)
		$action_ok = false;
		if (_ioDAO ('menus')->check ($toEdit) !== true){
  			return _arRedirect (_url ('adminmenus|edit', array ('errors' => 1)));
		}

		//Ajout ou modification ?
		if (!$toEdit->id_menu) {
			_ioDAO ('menus')->insert ($toEdit);
		} else {
			_ioDAO ('menus')->update ($toEdit);
		}

		// on vide la session
		CopixSession::set ('menu|menus|edit', null);
		return _arRedirect(_url ('adminmenus|'));
    }
    
    /**
     * Supprime un menu
     */
    public function processDelete () {
    	// verification des parametres
		CopixRequest::assert ('id_menu');
		$menu = _ioDao('menus')->get (_request ('id_menu'));

		// affichage du message de confirmation
		if (_request ('confirm') === null) {
			return CopixActionGroup::process (
				'generictools|Messages::getConfirm',
				array (
					'message' => _i18n ('admin.confirmdeletemenu', array ($menu->name_menu)),
					'confirm' => _url ('adminmenus|delete', array ('id_menu' => _request ('id_menu'), 'confirm' => 1)),
					'cancel' => _url ('adminmenus|')
				)
			);
		} else {
			// suppression du menu
			_class ('menusservices')->delete (_request ('id_menu'));
		}
		return _arRedirect (_url ('adminmenus|'));
    }
    
    /**
     * Annulation d'une modification en cours
     */
    public function processCancel (){
		CopixSession::set ('menu|menus|edit', null);
		return _arRedirect(_url ('adminmenus|'));
    } 
}
?>