<?php
/**
* @package	cms
* @subpackage menu_2
* @author	Sylvain DACLIN
* @copyright 2001-2006 CopixTeam
* @link		http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage menu_2
* DAOMenu
*/
class DAOMenu {
	/**
    * getMenu
    * @param id_menu
    * @param depth if depth is set and > 0 then recursive
    * @return arMenu
    */
	function getMenu ($id_menu=1,$params=array()) {
		// Set Default Right Value
		if (!isset($params['right'])){
			//$params['right']=PROFILE_CCV_SHOW;
			$params['right']=0;
		}
		// Make profile path for right management
		if (! isset($params['profilePath'])) {
			$params['profilePath'] = $this->getProfilePath($id_menu);
		}
		if (!isset($params['id_head'])) {
			$params['id_head']=null;
		}
		// Profondeur
		if (!isset($params['depth'])){
			$params['depth']=null;
		}
		// IsOnline
		if (!isset($params['isOnline'])){
			$params['isOnline']=1;
		}
		// IdHead
		if (!isset($params['idHead'])){
			$params['idHead']=0;
		}

		$arChilds = $this->getChilds ($id_menu, array('isonline'=>$params['isOnline'], 'idHead'=>$params['idHead']));
		//var_dump($params);
		//var_dump($arChilds);
		$ar2Return = array();
		foreach ((array) $arChilds as $key=>$child) {
			$child->path  = $params['profilePath'] . '|' . $child->id_menu;
			$child->id_head = $params['id_head'];
			if (($currentValue = CopixUserProfile::valueOf ($child->path, 'menu_2')) < $params['right']){                        
				continue;
			}else{ // We've got enough right to get this menu, we store current right in menu.        
                $child->userRight = $currentValue;
			}
			//Get Menu link
			$child->htmlLink = $this->getHTMLLink($child);

			$ar2Return[] = $child;
			if (isset($params['depth']) && $params['depth']>1) {
				$ar2Return = array_merge($ar2Return, $this->getMenu($child->id_menu, array('id_head'=>($params['id_head'] + 1), 'right'=>$params['right'],'depth'=>($params['depth'] - 1),'profilePath'=>$child->path,'isOnline'=>$params['isOnline'])));
			}elseif ($params['depth']===null){
				$ar2Return = array_merge($ar2Return, $this->getMenu($child->id_menu, array('id_head'=>($params['id_head'] + 1), 'right'=>$params['right'],'depth'=>null,'profilePath'=>$child->path,'isOnline'=>$params['isOnline'])));
			}
		}

		return $ar2Return;
	}

	/**
    * getWithProfile
    * @param $id_menu
    * @return 
    */
	function getWithProfile ($id_menu) {
		if (! ($menu = $this->get ($id_menu))){
			$menu = new StdClass ();
		}
		$menu->path = $this->getProfilePath($id_menu);
		$menu->userRight = CopixUserProfile::valueOf ($menu->path, 'menu_2');
		return $menu;
	}

	/**
    * getProfilePath
    * @param id_menu
    * @return profilePath exemple : modules|menu_2|1|2
    */
	function getProfilePath ($id_menu) {
		$arPath = $this->getPath($id_menu);
		$pathStr = 'modules|menu_2';
		foreach ($arPath as $key=>$Menu) {
			$pathStr .= '|'.$Menu->id_menu;
		}
		return $pathStr;
	}

	/**
    * getHTMLLink
    * @param
    * @return
    */
	function getHTMLLink ($menu) {
		if ($menu->typelink_menu == 'cmsp') {
			// Add CopixUrl handler
			$menu->url_menu = CopixUrl::get ('cms||get', array('id'=>$menu->id_cmsp, 'selectedMenu'=>$menu->id_menu));
		}elseif (strpos($menu->url_menu,'http://')!==0 && strpos($menu->url_menu,'ftp://')!==0) {
			// Add menu id in path
			if (strpos($menu->url_menu,'?')===false) {
				$menu->url_menu .= '?selectedMenu='.$menu->id_menu;
			}else{
				$menu->url_menu .= '&selectedMenu='.$menu->id_menu;
			}
			// Add CopixUrl handler
			$menu->url_menu = CopixUrl::get ().$menu->url_menu;
		}

		// Set popup behavior
		if ($menu->popup_menu == 2){
			$strLink = 'href="'.addSlashes($menu->url_menu).'" onclick="window.open(this, \'popup\', \'toolbar=no,scrollbars=yes,height='.$menu->height_menu.',width='.$menu->width_menu.'\');return false;"';
		}else {
			$strLink = 'href="'.addSlashes($menu->url_menu).'"' ;
			if ($menu->popup_menu == 1){
				$strLink .= ' onclick="this.target=\'_blank\'"' ;
			}
		}
		return $strLink;
	}

	/**
    * getAssocMenu
    * @param Menu
    * @return 
    */
	function getAssocMenu (& $arMenu) {
		$ar2Return = array();
		foreach ($arMenu as $key=>$menu) {
			$ar2Return[$menu->father_menu][] = $menu;
		}
		return $ar2Return;
	}

	/**
    * getPath
    * @param $id_menu
    * @return arPath (ordonned array)
    */
	function getPath ($id_menu) {
		$exists = false;
//		Copix::RequireClass ('CopixMemoryCache');
//		$result = CopixMemoryCache::get ("menu_2", "menu::findAll", $exists);
		if (!$exists){
			$loadedMenu = $this->findAll();
			$var = serialize ($loadedMenu);
//			CopixMemoryCache::set ("menu_2", "menu::findAll", $var, 60*15);
		}else{
			$loadedMenu = unserialize ($result);
		}
		$currentMenu = null;
		foreach ($loadedMenu as $key=>$elem) {
			if ($elem->id_menu == $id_menu){
				$currentMenu=$elem;
				break;
			}
		}
		/* Version non optimisï¿½e DB
		$currentMenu = $this->get($id_menu);
		*/
		if ($currentMenu === null){
			return array ();
		}elseif ($currentMenu->father_menu > 0) {
			return array_merge($this->getPath($currentMenu->father_menu),array($currentMenu));
		}else{
			return array($currentMenu);
		}
	}

	/**
    * getByIdCmsp
    * @param id_cmsp
    * @return array of menu
    */
	function findByIdCmsp ($id_cmsp) {
		$exists = false;
		$arToReturn = array ();
//		$result = CopixMemoryCache::get ("menu_2", "menu::findAll", $exists);
		if (!$exists){
			$loadedMenu = $this->findAll();
//			CopixMemoryCache::set ("menu_2", "menu::findAll", serialize ($loadedMenu), 60*15);
		}else{
			$loadedMenu = unserialize ($result);
		}
		foreach ($loadedMenu as $key=>$elem) {
			if ($elem->id_cmsp == $id_cmsp){
				$arToReturn[]=$elem;
			}
		}
		return (array) $arToReturn;
	}

	/**
    * getChilds
    * @param $id_menu
    * @return array of childs
    */
	function getChilds ($id_menu, $params=array ()) {
		$exists = false;
//		$result=CopixMemoryCache::get ("menu_2", "menu::findAll",$exists);
		if (!$exists){
			$loadedMenu = $this->findAll();
//			CopixMemoryCache::set ("menu_2", "menu::findAll", serialize ($loadedMenu), 60*15);
		}else{
			$loadedMenu = unserialize ($result);
		}
		$arToReturn=array();
		//var_dump($loadedMenu);
		foreach ($loadedMenu as $key=>$currentMenu) {
			// skip offline menu if needed
			if (isset($params['isonline'])) {
				if ($params['isonline']==1 && $currentMenu->isonline_menu!=1) {
					//echo $currentMenu->id_menu.'is not online<br/>';
					continue;
				}
			}
            if (isset($params['idHead'])) {
                if ($currentMenu->father_menu == null) {
                    if (($params['idHead']!=0 && $currentMenu->id_head != $params['idHead'])
                        || ($params['idHead']==0 && $currentMenu->id_head != null && $currentMenu->id_head != 0)){
                //        	echo $currentMenu->id_menu.'is not idHead<br/>';                              
                        continue;
                    }
                }
            }
/*
			// filter by id head
			if (isset($params['idHead'])) {
                if ($currentMenu->father_menu == 1) {
                    if (($params['idHead']!=0 && $currentMenu->id_head != $params['idHead'])
                        || ($params['idHead']==0 && $currentMenu->id_head != null)){
                        continue;
                    }
                }
			}
*/		if ($currentMenu->father_menu==$id_menu){
				$currentMenu->nbchilds_menu = $this->countChilds($loadedMenu,$currentMenu->id_menu);
				$arToReturn[$currentMenu->order_menu] = $currentMenu;
				//echo $currentMenu->id_menu.'HOURRA<br/>';
			}else {
			  //echo $currentMenu->id_menu.'is not AAAAA<br/>';
		}
		
		} 
		ksort($arToReturn);
		return $arToReturn;
	}

	/**
    * indique le nombre d'enfants de $id_menu dans le tableau de menu $arMenu 
    * @param array $arMenu un tableau d'ï¿½lï¿½ments de menu
    * @param int $id_menu l'identifiant du menu dont on veut connaitre le nombre d'enfants  
    * @return int le nombre d'enfants de id_menu 
    */
	function countChilds ($arMenu, $id_menu) {
		$i=0;
		foreach ((array)$arMenu as $key=>$elem) {
			if ($elem->father_menu==$id_menu)
			$i++;
		}
		return $i;
	}

	/**
    * Indique la position que devrait prendre un fils dans le père donné
    * @param $father_id le menu dans lequel on souhaite mettre le nouveau menu
    * @return int la position du nouvel ï¿½lï¿½ment
    */
	function getNewPos ($father_id) {
		$father_id = (intval($father_id)>0) ? $father_id : 'null';
		$sql = 'SELECT max(order_menu)+1 as max FROM menu_2 WHERE father_menu'. ($father_id!=null ? '='.intval($father_id) : ' is null');
		$arResult = CopixDB::getConnection()->doQuery ($sql);
		if (isset($arResult[0]) && $arResult[0]->max > 0) {
			return $arResult[0]->max;
		}else{
			return 1;//encore aucun fils, premiÃ¨re position
		}
	}

	/**
    * Positionne le menu en haut
    * @param object $menu le menu ï¿½ monter
    * @return void 
    */
	function doUp ($menu) {
		if ($menu->order_menu > 1) {
			
			// MoveUp previous menu
			$sqlSwap1 = 'UPDATE menu_2 SET order_menu='.intval ($menu->order_menu).' WHERE father_menu'. ($menu->father_menu!=null ? '='.intval($menu->father_menu) : ' is null').' AND order_menu='.(intval($menu->order_menu) - 1);
			CopixDB::getConnection ()->doQuery($sqlSwap1);
			// MoveDown this menu
			$sqlSwap2 = 'UPDATE menu_2 SET order_menu=order_menu-1 WHERE id_menu='.intval ($menu->id_menu);
			CopixDB::getConnection ()->doQuery($sqlSwap2);
		}
	}

	/**
	* Positionne le menu dans une position infï¿½rieure ï¿½ sa position actuelle. 
    * @param object $menu le menu ï¿½ descendre 
    * @return boolean si la modification ï¿½ ï¿½tï¿½ effectuï¿½e
    */
	function doDown ($menu) {
		
		$RS = CopixDB::getConnection ()->doQuery('SELECT MAX(order_menu) as max FROM menu_2 WHERE father_menu'. ($menu->father_menu!=null ? '='.intval($menu->father_menu) : ' is null'));
		if (isset($RS[0])) {
			$record = $RS[0];
			$maxOrder = $record->max;
		}else{
			return false;
		}
		if ($menu->order_menu < $maxOrder) {
			// MoveDown next menu
			$sqlSwap1 = 'UPDATE menu_2 SET order_menu='.intval ($menu->order_menu).' WHERE father_menu'. ($menu->father_menu!=null ? '='.intval($menu->father_menu) : ' is null').' AND order_menu='.(intval($menu->order_menu) + 1);
			CopixDB::getConnection ()->doQuery($sqlSwap1);
			// MoveUp this menu
			$sqlSwap2 = 'UPDATE menu_2 SET order_menu=order_menu+1 WHERE id_menu='.intval ($menu->id_menu);
			CopixDB::getConnection ()->doQuery($sqlSwap2);
		}
	}

	/**
    * Insertion d'un ï¿½lï¿½ment de menu, crï¿½ation des capacitï¿½s associï¿½es
    * @param menu $toInsert l'ï¿½lï¿½ment ï¿½ insï¿½rer dans la base de donnï¿½es 
    * @return void 
    */
	function insertWithCapability (& $toInsert) {
		// Crï¿½ation du nouvel item dans copixcapabilitypath
		$profilePath = $this->getProfilePath($toInsert->father_menu);
		$this->insert($toInsert);
		$toInsert->path = $profilePath.'|'.$toInsert->id_menu;
		CopixProfileTools::createCapabilityPath ($toInsert->path, $toInsert->caption_menu);
	}

	/**
    * Supprime un ï¿½lï¿½ment de menu
    * @param int $id_menu l'ï¿½lï¿½ment de menu ï¿½ supprimer
    * @return boolean si la suppression ï¿½ bien ï¿½tï¿½ effectuï¿½e ou non
    */
	function delete ($id_menu) {
		if ($id_menu==1) {
			return false;
		}
		if ($currentMenu = $this->get($id_menu)) {
			// Delete each Child
			$arChilds = $this->getChilds($currentMenu->id_menu);
			foreach ((array) $arChilds as $key=>$menuEnfant) {
				$this->delete($menuEnfant->id_menu);
			}

			

			// Get profilePath
			$profilePath = $this->getProfilePath($currentMenu->id_menu);

			// Delete menu item
			$sqlDelete = 'DELETE FROM menu_2 WHERE id_menu=' . intval ($currentMenu->id_menu);
			CopixDB::getConnection ()->doQuery($sqlDelete);

			// Reorder
			$sqlOrdre = 'UPDATE menu_2 SET order_menu=order_menu - 1 WHERE father_menu'. ($currentMenu->father_menu!=null ? '='.intval($currentMenu->father_menu) : ' is null') . ' AND order_menu > '.intval ($currentMenu->order_menu);
			CopixDB::getConnection ()->doQuery($sqlOrdre);

			// Delete associated rights
			CopixProfileTools::deleteCapabilityPath ($profilePath);
			return true;
		}else{
			return false;
		}
	}

	/**
    * Resï¿½quence l'ordre des menus pour combler les vides ï¿½ventuels
    * @param int $father_menu le menu dont les fils sont ï¿½ rï¿½organiser.
    * @return void 
    */
	function reOrder ($father_menu) {
		
		$RS = CopixDB::getConnection ()->doQuery('SELECT id_menu,order_menu FROM menu_2 WHERE father_menu'. ($father_menu!=null ? '='.intval($father_menu) : ' is null').' ORDER BY order_menu');
		$arOrder = array();
		foreach ($RS as $record) {
			$arOrder[] = $record;
		}
		$currentOrder=1;
		foreach ($arOrder as $key=>$elem) {
			if ($elem->order_menu != intval($currentOrder)){
				$sqlOrder = 'UPDATE menu_2 SET order_menu = '.intval ($currentOrder).' WHERE id_menu='.intval ($elem->id_menu);
				CopixDB::getConnection ()->doQuery($sqlOrder);
			}
			$currentOrder++;
		}
	}

	/**
    * Rend un menu visible invisible et vis versa.
    * @param int $id_menu l'identifiant du menu dont l'affichage est ï¿½ changer  
    * @return void 
    */
	function toggleDisplay ($id_menu) {
		CopixDB::getConnection ()->doQuery('UPDATE menu_2 SET isonline_menu = 1-isonline_menu WHERE id_menu='.intval ($id_menu));
	}
    
    /**
    * get all menu defined on the given heading
    * @param int $id_head l'identifiant de la rubrique
    * @return array of menus
    */
    function findByHeadingMenu($id_head) {    
		$exists = false;
//		$result = CopixMemoryCache::get ("menu_2", "menu::findAll", $exists);
		if (!$exists){
			$loadedMenu = $this->findAll();
//			CopixMemoryCache::set ("menu_2", "menu::findAll", serialize ($loadedMenu), 60*15);
		}else{
			$loadedMenu = unserialize ($result);
		}
        
        $headingServices = CopixClassesFactory::getInstanceOf ('CopixHeadings|CopixHeadingsServices');
        $fathers = $headingServices->getFathers($id_head);
        $arToReturn = array();
		foreach ($loadedMenu as $key=>$elem) {
			if (((in_array($elem->id_head, $fathers)) || ($elem->id_head === null)) && ($elem->father_menu == null)){
                $elem->nbchilds_menu = count($this->getChilds($elem->id_menu));
                $arToReturn[$elem->var_name_menu] = $elem;
			}
		}
		return (array) $arToReturn;
    }
    
    /**
     * Recherche les menus disponibles dans une rubrique ?
     * TODO vï¿½rifier le role de la fonction  
     */
    function findHeadingMenuList($pIdHead){
        $arMenus = $this->getMenu(1, array('depth'=>1, 'isOnline'=>0, 'idHead'=>$pIdHead));      
        $arInheritedMenus = $this->findByHeadingMenu($pIdHead);            
        foreach ($arInheritedMenus as $key=>$cur){
            foreach ($arMenus as $overload){
                if ($overload->var_name_menu == $cur->var_name_menu){
                    unset($arInheritedMenus[$key]);
                }
            }
        }
        foreach ($arMenus as $overload){
            $arInheritedMenus[$overload->var_name_menu] = $overload;
        }
        return $arInheritedMenus;
    }
}
?>