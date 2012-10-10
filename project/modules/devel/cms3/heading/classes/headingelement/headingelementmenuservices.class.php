<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * Services de menu
 * @package     cms
 * @subpackage  heading
 */
class HeadingElementMenuServices {
	
	const MAIN = "MAIN";
	const MAIN_CAPTION = "Navigation principale";
	
	const LEFT_1 = "LEFT_1";
	const LEFT_1_CAPTION = "Menu de gauche 1";
	
	const LEFT_2 = "LEFT_2";
	const LEFT_2_CAPTION = "Menu de gauche 2";
	
	const LEFT_3 = "LEFT_3";
	const LEFT_3_CAPTION = "Menu de gauche 3";
	
	const RIGHT_1 = "RIGHT_1";
	const RIGHT_1_CAPTION = "Menu de droite 1";
	
	const RIGHT_2 = "RIGHT_2";
	const RIGHT_2_CAPTION = "Menu de droite 2";
	
	const RIGHT_3 = "RIGHT_3";
	const RIGHT_3_CAPTION = "Menu de droite 3";
	
	const IDENTIFICATION = "IDENTIFICATION";
	const IDENTIFICATION_CAPTION = "Identification";
	
	const FOOTER = "FOOTER";
	const FOOTER_CAPTION = "Pied de page";
	
	const FOOTER_2 = "FOOTER_2";
	const FOOTER_2_CAPTION = "Pied de page 2";
	
	const FOOTER_3 = "FOOTER_3";
	const FOOTER_3_CAPTION = "Pied de page 3";
	
	const HEADER = "HEADER";
	const HEADER_CAPTION = "Haut de page";
	
	const ACTUALITE = "ACTUALITE";
	const ACTUALITE_CAPTION = "Actualités";
	
	/**
	 * Renvoie le menu pour un element identifié 
	 *
	 * @param int $pPublicId
	 * @param String $pType
	 * @return record
	 */
	public function getMenu ($pPublicId, $pType = false){
		static $cachedMenu = array ();
		if (isset ($cachedMenu[$pPublicId])){
			return $pType === false ? $cachedMenu[$pPublicId] : (isset ($cachedMenu[$pPublicId][$pType]) ? $cachedMenu[$pPublicId][$pType] : null);
		}

		$arMenu = array();
		foreach (DAOcms_headingelementinformations_menus::instance ()->findBy (_daoSP ()->addCondition ('public_id_hei', '=', $pPublicId)) as $resultat){
			$arMenu[$resultat->type_hem] = $resultat;
		}

		//Sauvegarde en cache
		$cachedMenu[$pPublicId] = $arMenu;
		return isset ($cachedMenu[$pPublicId][$pType]) ? $cachedMenu[$pPublicId][$pType] : null;
	}
	
	/**
	 * Retourne le parent qui défini le menu de l'element identifié par son publicId
	 *
	 * @param int $pPublicId
	 * @param string/array $pType
	 * @return string/array
	 */
	public function getInheritedHeadingElementMenu ($pPublicId, $pType){
		if (is_array($pType)){
			$toReturn = array ();
			foreach ($pType as $type){
				$toReturn[$type] = $this->getInheritedHeadingElementMenu($pPublicId, $type);
			}
			return $toReturn;
		}
		
		$menu = $this->getMenu($pPublicId, $pType);
		try {
			$element = _ioClass('heading|headingelementinformationservices')->get ($pPublicId);

			if(!empty($menu)){
				return $element;
			}
			
			if ($element->parent_heading_public_id_hei === null){
				$elementVide = DAORecordcms_headingelementinformations::create ();
				$elementVide->caption_hei = "Thème"; 
				return $elementVide;
			}
			return $this->getInheritedHeadingElementMenu ($element->parent_heading_public_id_hei, $pType);
		} catch (CopixException $e){
			_log($e->getMessage(), "errors", CopixLog::EXCEPTION);
			return null;
		}
	}
	
	/**
	 * Retourne le menu herité.
	 *
	 * @param int $pPublicId
	 * @param string $pType
	 * @return record
	 */
	public function getHeadingElementMenu ($pPublicId, $pType){
		if (is_array($pType)){
			$toReturn = array ();
			foreach ($pType as $type){
				$toReturn[$type] = $this->getHeadingElementMenu($pPublicId, $type);
			}
			return $toReturn;
		}
		
		$menu = $this->getMenu($pPublicId, $pType);
		if(!empty($menu)){
			return $menu;
		}
		try{
			$element = _ioClass('heading|headingelementinformationservices')->get ($pPublicId);
		} catch (CopixException $e){
			_log($e->getMessage(), "errors", CopixLog::EXCEPTION);
			return null;
		}
		
		if ($element->parent_heading_public_id_hei === null){
			return null;
		}
		return $this->getHeadingElementMenu ($element->parent_heading_public_id_hei, $pType);
	}
	
	/**
	 * Retourne le menu du module en cours.
	 *
	 * @param int $pPublicId
	 * @param string $pType
	 * @return record
	 */
	public function getModuleMenu ($pType){
		$cacheId = 'menuservices|getModuleMenu|' . $pType . '|'._request('module');
		if (HeadingCache::exists ($cacheId)) {
			return HeadingCache::get ($cacheId);
		}

		$query = 'SELECT * FROM cms_headingelementinformations_menus WHERE type_hem = :type_hem
					AND ( modules_hem LIKE :module1 OR modules_hem LIKE :module2 OR modules_hem LIKE :module3 OR modules_hem LIKE :module4)';
		$results = _doQuery($query, array(':type_hem'=>$pType, ':module1'=>'%;'._request('module'), ':module2'=>_request('module').';%', ':module3'=>'%;'._request('module').';%', ':module4'=>_request('module')));
		$toReturn = empty($results) ? null : $results[0];
		HeadingCache::set ($cacheId, $toReturn, false);
		return $toReturn;
	}
	
	/**
	 * Récupération d'un libellé pour un type de contenu donné.
	 *
	 * @param String $pType
	 * @return String
	 */
	public function getCaption ($pType, $pTheme = false){	
		if(is_array($pType)){
			$toReturn = array();
			foreach ($pType as $type){
				$toReturn[$type] = $this->getCaption($type);
			}
			return $toReturn;
		}
		
		if (!$pTheme){
			$pTheme = CopixTpl::getTheme ();
		}
		$toReturn = array();
		$theme = CopixTpl::getThemeInformations ($pTheme);
		if ($theme->getRegistry ()) {
			if (isset ($theme->getRegistry ()->cmsmenus)) {
				foreach ($theme->getRegistry ()->cmsmenus->menu as $menu) {
					if ((string)$menu['name'] == $pType){
						return (string)$menu['caption'];
					}
				}
			}
		}
		
		$reflect = new Reflectionclass ($this);
		$consts = $reflect->getConstants ();
		
		$key = array_search ($pType, $consts);
		if (array_key_exists($key . '_CAPTION', $consts)){
			return $consts[$key . '_CAPTION'];
		}else{
			return $pType;
		}
	}
	
	/**
	 * Retourne la liste des menus definis par le theme ou par la config si rien n'est défini dans le theme
	 * @param String $pTheme le theme dont on cherche les menus, sinon le theme courant
	 */
	public function getListMenus ($pTheme = false){
		if (!$pTheme){
			$pTheme = CopixTpl::getTheme ();
		}
		$toReturn = array();
		$theme = CopixTpl::getThemeInformations ($pTheme);
		if ($theme->getRegistry ()) {
			if (isset ($theme->getRegistry ()->cmsmenus)) {
				foreach ($theme->getRegistry ()->cmsmenus->menu as $menu) {
					$template = (isset ($menu['template'])) ? (string)$menu['template'] : null;
					$toReturn[] = array ('name' => (string)$menu['name'], 'caption' => (string)$menu['caption'], 'template' => $template);
				}
			}
		}
		/* on n'accepte plus les themes sans menus définis
		if (empty ($toReturn)) {
			$listMenus = explode (',', CopixConfig::get ('heading|headingelementmenu'));
			foreach ($listMenus as $menu) {
				$toReturn[] = array ('name' => $menu, 'caption' => $this->getCaption ($menu), 'template' => null);
			}
		}*/
		return $toReturn;
	}
	
	/**
	 * 
	 * Retourne les menus utilisant l'element de publicId $pPublicId
	 * @param int $pPublicId
	 * @return array
	 */
	public function getDependencies ($pPublicId) {
		$results = DAOcms_headingelementinformations_menus::instance ()->findBy (_daoSP()->addCondition ('public_id_hem', '=', $pPublicId));
		$toReturn = array ();
		foreach ($results as $result) {
			$toReturn[] = $this->getMenu ($result->public_id_hei, $result->type_hem);
		}
		return $toReturn;
	}
}