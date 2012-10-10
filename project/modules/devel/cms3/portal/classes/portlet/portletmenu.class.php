<?php
/**
 * @package     cms3
 * @subpackage  portal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * Portlet des menus
 * @package     cms3
 * @subpackage  portal
 */
class PortletMenu extends Portlet {
		
	const DEFAULT_MENU_DISPLAY_TEMPLATE = 'heading|menu/headingmenulistnavigation.php';
	
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les texts sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletMenu";
		$this->_moreData['depth_hem'] = 1;
		$this->_moreData['level_hem'] = 0;
		$this->_moreData['public_id_hem'] = -1;
		$this->_moreData['type_hem'] = $this->getRandomId();
	}

	/**
	 * rendu du contenu du text
	 *
	 * @param string $pRendererContext le contexte de rendu (Modification, Moteur de recherche, affichage, ....)
	 * @param string $pRendererMode    le mode de rendu demandé (généralement le format de sortie attendu)
	 * @return string
	 */
	protected function _renderContent ($pRendererMode, $pRendererContext){
		if ($pRendererMode == RendererMode::HTML){
			return $this->_renderHTML ($pRendererContext);
		}
		throw new CopixException ('Mode de rendu non pris en charge');
	}

	/**
	 * Rendu pour le mode HTML
	 *
	 * @param string $pRendererContext le contexte de rendu
	 * @return string
	 */
	private function _renderHTML ($pRendererContext){
		if ($pRendererContext == RendererContext::DISPLAYED || $this->getEtat () == self::DISPLAYED){
			return $this->_renderHTMLDisplay ();
		}else{
			return $this->_renderHTMLUpdate ();
		}
	}
	
	/**
	 * Retourne le contenu du template de la portlet en rendu update
	 *
	 * @return String
	 */
	private function _renderHTMLUpdate (){
		$theme = null;
		if($this->public_id_hei){
			$theme = _ioClass('heading|headingelementinformationservices')->getTheme($this->public_id_hei, $foo);
		}
		$toReturn = CopixZone::process ('portal|PortletMenu', array('portlet'=>$this, 'module'=>'portal', 'xmlPath'=>CopixTpl::getFilePath("portal|portlettemplates/menutemplates.xml", $theme)));
		
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);		
		$toReturn .= $tpl->fetch ('portal|portletmenu.form.php');
		
		return $toReturn;
	}

	/**
	 * Retourne le contenu du template de la portlet en rendu display
	 *
	 * @return String
	 */
	private function _renderHTMLDisplay (){
		$tpl = new CopixTpl ();
		$menu = $this->_getMenu();
		$templateInfos = _ioClass ('portal|TemplateServices')->getInfos (CopixTpl::getFilePath("portal|portlettemplates/menutemplates.xml"), $menu->template_hem);
		$nocache = isset ($templateInfos->options->nocache) && (int)$templateInfos->options->nocache; 
		//_dump($menu);
		return CopixZone::process('heading|HeadingMenuList', array('type_hem'=>$this->getRandomId(), 'menu'=>$menu, 'noCache'=>$nocache)); 
	}
	
	/**
	 * Retourne un menu de type Record lisible par la zone HeadingMenuList
	 *
	 */
	private function _getMenu (){
		$params = new CopixParameterHandler ();
		$params->setParams($this->_moreData);
		$menu = DAORecordcms_headingelementinformations_menus::create ();
		$menu->depth_hem = $params->getParam('depth_hem', 1);
		$menu->level_hem = $params->getParam('level_hem', 0);
		$menu->public_id_hem = $params->getParam('public_id_hem', -1);
		$menu->portlet_hem = $params->getParam('portlet_hem', 0);
		$menu->type_hem = $this->getRandomId();
		$menu->template_hem = $params->getParam('template', self::DEFAULT_MENU_DISPLAY_TEMPLATE);
		return $menu;
	}

}