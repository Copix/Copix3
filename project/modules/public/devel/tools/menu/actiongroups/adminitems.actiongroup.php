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
class ActionGroupAdminItems extends CopixActionGroup {

    /**
	 * Verifications avant l'execution de l'actiongroup
	 */
	public function beforeAction ($actionName){
		// verification si l'utilisateur est connecte
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		
		// si on fait n'importe quelle autre action que paste ou default,
		// on "vide" l'id de rubrique qu'on voulait copier
		$actions_allowed = array('paste', 'default');
		if (!in_array (strtolower ($actionName), $actions_allowed)) {
			CopixSession::set ('menu|items|cut', null);
		}

		// si on fait n'importe quelle autre action que edit ou valid
		// on "vide" l'element qu'on ajoutait / modifiait
		$actions_allowed = array('edit', 'valid');
		if (!in_array (strtolower ($actionName), $actions_allowed)) {
			CopixSession::set ('menu|items|edit', null);
		}
	}
    
    /**
     * Fonction par défaut, liste des menus disponibles
     */
    public function processDefault () {
        CopixRequest::assert('id_menu');
        
        $menu = _ioDAO ('menus')->get (_request ('id_menu'));
        
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('admin.title_edit_items', $menu->name_menu);
		
		$items = _class ('itemsservices');
		$items->setCutIdItem (CopixSession::get ('menu|items|cut'));
        $ppo->itemsList = $items->getItemsHTML (_request ('id_menu'), true);
                
        return _arPPO ($ppo, 'items.list.tpl');
    }
    
    /**
     * Ajouter / modifier un element de menu
     */
	public function processEdit () {
		CopixRequest::assert ('id_menu');
		
		// mode modification d'element
		if ($id_item = CopixRequest::getInt ('id_item')) {
			if ($toEdit = _ioDAO ('menusitems')->get ($id_item)) {				
				CopixSession::set ('menu|items|edit', $toEdit);
			}
		}
  
		// mode ajout d'element
		if (($toEdit = CopixSession::get ('menu|items|edit')) === null) {
			$toEdit = _record ('menusitems');
			$toEdit->id_menu = _request ('id_menu');
			$toEdit->id_parent_item = _request ('id_parent');
			
			CopixSession::set ('menu|items|edit', $toEdit);
		}
		
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = ($toEdit->id_item) ? _i18n ('admin.title_edit_item') : _i18n ('admin.title_add_item');
		$ppo->toEdit = $toEdit;
		$ppo->submit_caption = ($toEdit->id_item) ? _i18n ('admin.edit') : _i18n ('admin.add');
 
		// messages d'erreurs
  		$ppo->arErrors = _request ('errors') ? _ioDAO ('menusitems')->check ($toEdit) : array ();
  		
 		return _arPpo ($ppo, 'item.form.tpl');
	}
	
	/**
	 * Validation d'un ajout / modification
	 */
	public function processValid () {
		CopixRequest::assert ('id_menu', 'name_item', 'link_item');
		
		// on vérifie que l'on est bien en train de modifier / ajouter un élément, sinon on retourne à la liste.
		if (($toEdit = CopixSession::get ('menu|items|edit')) === null){
 			return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
		}

		// validation des modification depuis le formulaire
		$toEdit->name_item = _request ('name_item');
		$toEdit->link_item = _request ('link_item');
		CopixSession::set ('menu|items|edit', $toEdit);
		
		// sauvegarde (le check est fait dans les méthodes add et edit)
		$action_ok = false;
		if (!$toEdit->id_item) {
			$action_ok = _class ('itemsservices')->add ($toEdit);
		} else {
			$action_ok = _class ('itemsservices')->edit ($toEdit);
		}
		
		// si l'ajout n'a pas fonctionné (l'insertion génère déja une exception en cas d'erreur)
		if (!$action_ok) {
  			return _arRedirect (_url ('menu|adminitems|edit', array ('errors' => 1, 'id_menu' => _request ('id_menu'))));
		}
		
		// on vide la session
		CopixSession::set ('menu|items|edit', null);
		
		return _arRedirect(_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
	}
	
	/**
	 * Supprime un element de menu
	 */
	public function processDelete () {
		CopixRequest::assert ('id_menu', 'id_item');
		
		// mode confirmation
		if (_request ('confirm') === null) {
			$item = _ioDao('menusitems')->findBy ( _daoSp ()->addCondition('id_item', '=', _request('id_item')));
			
			// élément à supprimer trouvé
			if (is_object ($item) && count ($item) > 0) {			
				return CopixActionGroup::process (
					'generictools|Messages::getConfirm',
					array (
						'message' => _i18n('admin.confirmdeleteitem', array($item[0]->name_item)),
						'confirm' => _url ('menu|adminitems|delete', array('confirm' => 1, 'id_item' => _request('id_item'), 'id_menu' => _request('id_menu'))),
						'cancel' => _url ('menu|adminitems|', array('id_menu' => _request('id_menu')))
					)
				);
				
			// élément à supprimer non trouvé
			} else {
				return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
			}
			
		// mode suppression (confirmation ok)
		} else {
		
			// suppression de l'item, et ses enfants
			$delete = _class ('itemsservices')->delete (_request ('id_item'));
			
			// suppression qui n'a pas fonctionnée
			if (!$delete) {
				return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
			}
			
			// suppression ok
			return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
		}
	}
	
	/**
	 * Coupe un element de menu
	 */
	public function processCut () {
		CopixRequest::assert ('id_menu', 'id_item');
		
		CopixSession::set ('menu|items|cut', _request ('id_item'));
		
		return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
	}
	
	/**
	 * Colle un element de menu
	 */
	public function processPaste () {
		CopixRequest::assert ('id_menu', 'paste_id_parent');
		
		// collage de l'element
		$item = _class ('itemsservices');
		$item->setCutIdItem (CopixSession::get ('menu|items|cut'));
		$paste_id_parent = (_request ('paste_id_parent') == 'null') ? null : _request ('paste_id_parent');
		$item->paste (_request ('id_menu'), $paste_id_parent);
		
		CopixSession::set ('menu|items|cut', null);
		
		return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
	}
	
	/**
	 * Monte un element dans le classement
	 */
	public function processUp () {
		 CopixRequest::assert ('id_menu', 'id_item');
		 
		 _class ('itemsservices')->up (_request ('id_item'));
		 
		return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
	}
	
	/**
	 * Descend un element dans le classement
	 */
	public function processDown () {
		 CopixRequest::assert ('id_menu', 'id_item');
		 
		 _class ('itemsservices')->down (_request ('id_item'));
		 
		return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
	}
}
?>