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
 * Portlet de colonnes
 * @package     cms3
 * @subpackage  portal
 */
class PortletColumns extends Portlet {
	
	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'portal|portlettemplates/default.text.tpl';
	
	const LEFT_COLUMN = "leftColumn";
	
	const RIGHT_COLUMN = "rightColumn";
	
	private $_leftUpdateContent;
	
	private $_rightUpdateContent;
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les texts sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = __CLASS__;
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
		if ($pRendererContext == RendererContext::UPDATED && $this->getEtat () == self::DISPLAYED){
			return $this->_renderHTMLUpdate ();
		} else if (in_array ($pRendererContext, array (RendererContext::DISPLAYED, RendererContext::DISPLAYED_ADMIN)) 
			|| $this->getEtat () == self::DISPLAYED){
			return $this->_renderHTMLDisplay ();
		} else{
			return $this->_renderHTMLUpdate ();
		}
	}
	
	/**
	 * Retourne le contenu du template de la portlet en rendu update
	 *
	 * @return String
	 */
	private function _renderHTMLUpdate (){
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);	
		$tpl->assign (PortletColumns::LEFT_COLUMN, $this->_leftUpdateContent);	
		
		$tpl->assign (PortletColumns::RIGHT_COLUMN, $this->_rightUpdateContent);
		return $tpl->fetch ('portal|portletcolumns.form.php');
	}
	
	/**
	 * Retourne le contenu du template de la portlet en rendu display
	 *
	 * @return String
	 */
	private function _renderHTMLDisplay (){
		$tpl = new CopixTpl ();
		CopixHTMLHeader::addCSSLink(_resource('portal|styles/pageedit.css'));
		$tpl->assign ('portlet', $this);	
		$tpl->assign (PortletColumns::LEFT_COLUMN, $this->_leftUpdateContent);	
		
		$tpl->assign (PortletColumns::RIGHT_COLUMN, $this->_rightUpdateContent);
		return $tpl->fetch ('portal|portletcolumns.view.php');
	}
	
	public function setLeftUpdateContent($pLeftUpdateContent){
		$this->_leftUpdateContent = $pLeftUpdateContent;
	}
	
	public function setRightUpdateContent($pRightUpdateContent){
		$this->_rightUpdateContent = $pRightUpdateContent;
	}

}