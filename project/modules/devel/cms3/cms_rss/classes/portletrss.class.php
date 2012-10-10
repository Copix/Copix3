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
 * Portlet RSS
 * @package     cms3
 * @subpackage  portal
 */
class PortletRss extends PortletRenderHTML {
	
	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'cms_rss|portlettemplates/default.rss.php';
		
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les texts sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletRss";
	}
	
	/**
	 * Rendu pour le mode HTML
	 *
	 * @param string $pRendererContext le contexte de rendu
	 * @return string
	 */
	private function _renderHTML ($pRendererContext){
        if (in_array ($pRendererContext, array (RendererContext::DISPLAYED, RendererContext::DISPLAYED_ADMIN))
			|| $this->getEtat () == self::DISPLAYED){
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
	protected function _renderHTMLUpdate (){			
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);	
		$toReturn = $tpl->fetch ('cms_rss|portletrss.form.php');		
		return $toReturn;
	}

	/**
	 * Retourne le contenu du template de la portlet en rendu display
	 *
	 * @return String
	 */
	protected function _renderHTMLDisplay ($pIsAdmin){
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);	
		$params = new CopixParameterHandler ();
		$params->setParams($this->_moreData);
		$tpl->assign ('id_helt', $this->getPortletElementAt(0) ? $this->getPortletElementAt(0)->getHeadingElement()->id_helt : null);
		$tpl->assign('isAdmin', $pIsAdmin);

		return $tpl->fetch ($params->getParam('template', self::DEFAULT_HTML_DISPLAY_TEMPLATE));
	}

}