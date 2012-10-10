<?php
/**
 * @package		tools
 * @subpackage	menu
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Services sur les éléments de menu
 * @package tools
 * @subpackage menu
 */
class ItemsServices {
    /**
     * Retourne les éléments qui composent un menu
     * @param	int	$pIdMenu	L'identifiant du menu que l'on souhaite récupérer.
     * @param	int	$pIdparent	L'identifiant du parent à partir duquel on récupère les fils du menu. Si on passe un parent, on ne récupère pas le parent en lui même.
     * @param	int	$pLevels	Le nombre de niveau que l'on souhaite récupérer à partir de cet élément (null = infini)
     *
     * @return array of menu items
     */
    public function getMenu ($pIdMenu, $pIdParent = null, $pLevels = null){
		$criteres = _daoSp ()->addCondition ('id_menu', '=', $pIdMenu)
							 ->addCondition ('id_parent_item', '=', $pIdParent)
							 ->orderBy ('order_item');
    	$arMenus = _ioDao ('menusitems')->findBy ($criteres)->fetchAll ();
    	foreach ($arMenus as & $menuItem){
    		if ($pLevels === null || $pLevels > 0){
    			$menuItem->childs = $this->getMenu ($pIdMenu, $menuItem->id_item, $pLevels === null ? null : $pLevels -1);    			
    		}else{
    			$menuItem->childs = array ();
    		}
    	}
    	return $arMenus;
    }
    
    /**
     * Récupère les identifiants des menus dans l'arbre passé en paramètre.
     */
    private function _getAllIdForMenu ($pMenuToDelete){
    	$toReturn = array ();
    	foreach ($pMenuToDelete as $menuItem){
			$toReturn += $this->_findChildsIdInRecord ($menuItem);
    	}
    	return $toReturn;
    }

    /**
     * Récupère les identifiants des noeuds enfant
     *
     * @param CopixDAORecord $pRecord	l'enregistrement à supprimer
     * @return array of id
     */
    private function _findChildsIdInRecord ($pRecord){
    	$toReturn = array ();
   		foreach ($pRecord->childs as $child){
   			foreach ($this->_findChildsIdInRecord ($child) as $id){
   				$toReturn[] = $id;
   			}
   		}
   		$toReturn[] = $pRecord->id_item;
   		return $toReturn;
    }
	
	/**
	 * Supprime un élément (et ses enfants)
	 * 
	 * @param record $item
	 */
	public function delete ($pIdItem) {
		//récupération de l'enregistrement à partir duquel on souhaite supprimer
		if (! ($menu = _ioDAO ('menusitems')->get ($pIdItem))){
			return false;
		}

		//récupération de l'arborescence complète des éléments à supprimer.
		$menu->childs = $this->getMenu ($menu->id_menu, $menu->id_item);
		$menuToDelete = array ($menu);
		foreach ($this->_getAllIdForMenu ($menuToDelete) as $menuId){
			_ioDAO ('menusitems')->delete ($menuId);
		}
	}
	
	/**
	 * Monte un element dans le classement
	 * @param	int	$id_item	le numéro de l'item à monter
	 * @return boolean	si l'élément a été déplacé
	 */
	public function moveUp ($id_item) {
		if (! ($item = _ioDao ('menusitems')->get ($id_item))){
			// si l'element n'a pas été trouvé
			return false;
		}
		//On récupère l'élément qui le précède.
		if (! ($previousItem = _ioDao ('menus')->getPreviousItem ($item))){
			//pas d'éléments précédent, fini
			return false;
		}
		
		//on stocke dans des variables temporaires les ordres pour les éléments
		$orderForPrevious = $item->order_item;
		$orderForItem = $previousItem->order_item;
		
		//Si les ordres sont identiques, on met l'ordre de l'élément déscendu à 1 de moins que l'élément précédent
		if ($orderForItem == $orderForPrevious){
			$orderForItem--;
		}
		
		//Maj en base de données
		$item->order_item = $orderForItem;
		$previousItem->order_item = $orderForPrevious;
		_ioDao ('menusitems')->update ($item);
		_ioDao ('menusitems')->update ($previousItem);

		return true;
	}
	
	/**
	 * Descend un element dans le classement
	 * @param	int	$id_item	le numéro de l'item à descendre
	 * @return boolean	si l'élément a été déplacé
	 */
	public function moveDown ($id_item) {
		if (! ($item = _ioDao ('menusitems')->get ($id_item))){
			// si l'element n'a pas été trouvé
			return false;
		}
		//On récupère l'élément qui le précède.
		if (! ($nextItem = _ioDao ('menus')->getNextItem ($item))){
			//pas d'éléments précédent, fini
			return false;
		}
		
		//on stocke dans des variables temporaires les ordres pour les éléments
		$orderForNext = $item->order_item;
		$orderForItem = $nextItem->order_item;
		
		//Si les ordres sont identiques, on met l'ordre de l'élément monté à 1 de plus que l'élément suivant
		if ($orderForItem == $orderForNext){
			$orderForItem++;
		}
		
		//Maj en base de données
		$item->order_item = $orderForItem;
		$nextItem->order_item = $orderForNext;
		_ioDao ('menusitems')->update ($item);
		_ioDao ('menusitems')->update ($nextItem);
		return true;
	}
	
	/**
	 * Déplace un élément de menu vers un autre parent
	 * 
	 * A noter que l'élément sera collé en tant que dernier élément dans la position collée
	 * 
	 * @param	int	$pIdToPaste		l'élément de menu à coller
	 * @param	int $pIdNewParent	le nouveau parent pour l'élemnt de menu $pIdToPaste
	 * @param	int	$pIdMenuParent	Le nouveau menu parent pour l'élément de menu
	 */
	public function moveTo ($pIdToPaste, $pIdNewParent, $pIdMenuParent){
		//Pas possible de coller l'élément sur lui même
		if ($pIdToPaste === $pIdNewParent){
			return false;
		}
		
		//Il est possible d'avoir idNewParent à null ou $pIdMenuParent à null, mais pas les deux
		if ($pIdNewParent === null && $pIdMenuParent === null){
			return false;
		}

		//on vérifie que tous les éléments demandés existent.
		if (! ($menuToPaste = _ioDAO ('menusitems')->get ($pIdToPaste))){
			return false;
		}
		
		//on vérifie que le nouveau menu demandé existe
		if ($pIdMenuParent !== null && (! $menu = _ioDAO ('menusitems')->get ($pIdMenuParent))){
			return false;
		}
		
		//on vérifie que le nouveau parent existe, et si c'est le cas on attribue à nouveau menu la valeur de son parent
		$menuParent = false;
		if ($pIdNewParent !== null && (! $menuParent = _ioDAO ('menusitems')->get ($pIdNewParent))){
			//spécification d'un parent qui n'existe pas
			return false;
		}elseif ($menuParent !== false){
			$pIdMenuParent = $menuParent->id_menu;
		}
		
		//on vérifie que l'élément parent n'appartient pas à un des fils de l'élément à coller
		$menu = $this->getMenu ($menuToPaste->id_menu, $pIdToPaste);
		$ids = $this->_getAllIdForMenu ($menu);
		if (in_array ($pIdNewParent, $ids)){
			return false;
		}

		//tout semble ok, on met à jour.
		$menuToPaste->id_parent_item = $pIdNewParent; 
		return _ioDAO ('menusitems')->update ($menuToPaste);
	} 
}
?>