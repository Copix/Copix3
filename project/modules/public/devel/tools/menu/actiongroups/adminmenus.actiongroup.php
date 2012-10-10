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
		// verification si l'utilisateur est connecte
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		
		// on "vide" l'id de rubrique qu'on voulait copier
		CopixSession::set ('menu|items|cut', null);			
	}
    
    /**
     * Fonction par défaut, liste des menus disponibles
     */
    public function processDefault () {
        $ppo = new CopixPPO ();
        $ppo->TITLE_PAGE = _i18n ('admin.title_gestion_menus');
        
        $ppo->arrMenus = _ioDao('menus')->findBy(_daoSP ()->orderBy ('name_menu'));
        
        // affichage des messsages d'erreur si besoin est
		$ppo->arErrors = _request ('errors') ? _ioDAO ('menus')->check (CopixSession::get ('menu|menus|edit')) : array ();
        
        // mise en session du mode ajout de menu
		CopixSession::set ('menu|menus|edit', _record ('menus'));
		CopixSession::set ('menu|menus|editmode', 'add');
        
        return _arPPO ($ppo, 'menus.list.tpl');
    }
    
    /**
     * Affichage formulaire modification
     */
    public function processEdit () {
    	// si on est en mode modification, sans erreur à afficher
		if (is_null (_request ('errors')) && $menu_id = CopixRequest::getInt ('id_menu')) {
			if ($toEdit = _ioDAO ('menus')->get ($menu_id)) {
				CopixSession::set ('menu|menus|edit', $toEdit);
				CopixSession::set ('menu|menus|editmode', 'edit');
			}
			
		// si on est en mode modification, avec erreur à afficher
		} elseif (!is_null (_request ('errors'))) {
			$toEdit = CopixSession::get ('menu|menus|edit');
		} 
  
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n ('admin.title_edit_menu');
		$ppo->toEdit = $toEdit;
 
		// si on demande à afficher les messages d'erreurs
		$ppo->arErrors = (_request ('errors')) ? _ioDAO ('menus')->check ($toEdit) : array ();
		
		//var_dump($toEdit);
		
		return _arPpo ($ppo, 'editmenu.form.tpl');
    }
    
    /**
     * Validation d'un ajout / modification
     */
    public function processValid () {
		// on vérifie que l'on est bien en train de modifier / ajouter un élément, sinon on retourne à la liste.
		if (($toEdit = CopixSession::get ('menu|menus|edit')) === null){
 			return _arRedirect (_url ('menu|adminmenus|'));
		}

		// validation des modification depuis le formulaire
		$toEdit->name_menu = _request ('name_menu');
		CopixSession::set ('menu|menus|edit', $toEdit);
		
		// sauvegarde (le check est fait dans les méthodes add et edit)
		$action_ok = false;
		if (!$toEdit->id_menu) {
			$action_ok = _class ('menusservices')->add ($toEdit);
		} else {
			$action_ok = _class ('menusservices')->edit ($toEdit);
		}
		
		// // si l'ajout n'a pas fonctionné (l'insertion génère déja une exception en cas d'erreur)
		if (!$action_ok) {
			$url = (CopixSession::get ('menu|menus|editmode') == 'add') ? _url ('menu|adminmenus|', array ('errors' => 1)) : _url ('menu|adminmenus|edit', array ('errors' => 1, 'id_menu' => _request ('id_menu')));
  			return _arRedirect ($url);
		}
		
		// on vide la session
		CopixSession::set ('menu|menus|edit', null);
		
		return _arRedirect(_url ('menu|adminmenus|'));
    }
    
    /**
     * Supprime un menu
     */
    public function processDelete () {
    	// verification des parametres
		CopixRequest::assert ('id_menu');
		
		$menu = _ioDao('menus')->findBy ( _daoSp ()->addCondition('id_menu', '=', _request ('id_menu')));
		
		// affichage du message de confirmation
		if (_request ('confirm') === null) {
			return CopixActionGroup::process (
				'generictools|Messages::getConfirm',
				array (
					'message' => _i18n ('admin.confirmdeletemenu', array ($menu[0]->name_menu)),
					'confirm' => _url ('menu|adminmenus|delete', array ('id_menu' => _request ('id_menu'), 'confirm' => 1)),
					'cancel' => _url ('menu|adminmenus|')
				)
			);
			
		// suppression du menu
		} else {
			_class ('menusservices')->delete (_request ('id_menu'));
		}
		
		return _arRedirect (_url ('menu|adminmenus|'));
    }
}
?>