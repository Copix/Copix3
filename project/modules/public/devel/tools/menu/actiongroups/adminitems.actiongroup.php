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
		_currentUser ()->assertCredential ('module:write@menu');
		
		//on vérifie que l'élément "coupé" existe toujours
		if (CopixSession::get ('menu|items|cut') !== null){
			if (! _ioDAO ('menusitems')->get (CopixSession::get ('menu|items|cut'))){
				CopixSession::set ('menu|items|cut', null);				
			}
		}
	}
    
    /**
     * Fonction par défaut, liste des menus disponibles
     */
    public function processDefault () {
        CopixRequest::assert ('id_menu');

        if (! ($menu = _ioDAO ('menus')->get (_request ('id_menu')))){
        	return _arRedirect (_url ('adminmenus|'));
        }

        $ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('admin.title_edit_items', $menu->name_menu);
		$ppo->itemsList = CopixZone::process ('menu', array ('id_menu'=>_request ('id_menu'),
															 'paste'=>CopixSession::get ('menu|items|cut'), 
        													 'admin'=>true));
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
		if (((_request ('id_item', null, false)) == '') 
		     && (($toEdit = CopixSession::get ('menu|items|edit')) === null)) {
			$toEdit = _record ('menusitems');
			$toEdit->id_menu = _request ('id_menu');
			$toEdit->id_parent_item = _request ('id_parent');
			CopixSession::set ('menu|items|edit', $toEdit);
		}

		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = ($toEdit->id_item) ? _i18n ('admin.title_edit_item') : _i18n ('admin.title_add_item');
		$ppo->toEdit = $toEdit;
		$ppo->submit_caption = ($toEdit->id_item) ? _i18n ('copix:common.buttons.update') : _i18n ('copix:common.buttons.add');

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
		if ($toEdit->id_item === null) {
			if ($last = _ioDao ('menus')->getLastItemForParent ($toEdit->id_parent_item, $toEdit->id_menu)){
				$toEdit->order_item = $last->order_item + 1;
			}else{
				$toEdit->order_item = 0;
			}
		}
		CopixSession::set ('menu|items|edit', $toEdit);

		//On vérifie si les données sont correctes
		if (_ioDao ('menusitems')->check ($toEdit) !== true){
			// si l'ajout n'a pas fonctionné (l'insertion génère déja une exception en cas d'erreur)
			return _arRedirect (_url ('menu|adminitems|edit', array ('errors' => 1, 'id_menu' => _request ('id_menu'))));
		}

		//On sauvegarde
		if (!$toEdit->id_item) {
			_ioDao ('menusitems')->insert ($toEdit);
		} else {
			_ioDao ('menusitems')->update ($toEdit);
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
			// élément à supprimer trouvé
			if ($item = _ioDao ('menusitems')->get (_request('id_item'))) {			
				return CopixActionGroup::process (
					'generictools|Messages::getConfirm',
					array (
						'message' => _i18n('admin.confirmdeleteitem', array($item->name_item)),
						'confirm' => _url ('menu|adminitems|delete', array('confirm' => 1, 'id_item' => _request('id_item'), 'id_menu' => _request('id_menu'))),
						'cancel' => _url ('menu|adminitems|', array('id_menu' => _request('id_menu')))
					)
				);
			} else {
				// élément à supprimer non trouvé
				return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
			}
		} else {
			// mode suppression (confirmation ok)
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
		CopixRequest::assert ('id_menu', 'id_item');

		// collage de l'element
		_class ('itemsservices')->moveTo (CopixSession::get ('menu|items|cut'), _request ('id_item'), _request ('id_menu'));
		CopixSession::set ('menu|items|cut', null);

		return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
	}
	
	/**
	 * Monte un element dans le classement
	 */
	public function processUp () {
		 CopixRequest::assert ('id_menu', 'id_item');
		 _class ('itemsservices')->moveUp (_request ('id_item'));
		return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
	}
	
	/**
	 * Descend un element dans le classement
	 */
	public function processDown () {
		 CopixRequest::assert ('id_menu', 'id_item');
		 _class ('itemsservices')->moveDown (_request ('id_item'));
		return _arRedirect (_url ('menu|adminitems|', array ('id_menu' => _request ('id_menu'))));
	}
}
?>