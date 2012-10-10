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
 * Portlet de texte
 * @package     cms3
 * @subpackage  portal
 */
class PortletAnchor extends Portlet {
	
	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'portal|portlettemplates/default.text.tpl';
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les texts sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletAnchor";
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
			return $this->_renderHTMLDisplayAdmin ();
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
				
		$toReturn = CopixZone::process ('portal|PortletMenu', array('portlet'=>$this, 'module'=>'portal', 'xmlPath'=>CopixTpl::getFilePath("portal|portlettemplates/texttemplates.xml")));
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);	
		$toReturn .= $tpl->fetch ('portal|portletanchor.form.php');
		
		return $toReturn;
	}
	
	/**
	 * Retourne le contenu du template de la portlet en rendu display admin
	 *
	 * @return String
	 */
	private function _renderHTMLDisplayAdmin (){
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);	
		$toReturn = "<img width='23px' style='vertical-align:middle' src='"._resource('portal|img/anchor.png')."' />";
		if (array_key_exists('name', $this->_moreData) && $this->_moreData['name']){
			$toReturn .= $this->_moreData['name'] . "<a name='". $this->_moreData['name'] ."'></a>";
		} else {
			$toReturn .= "Pas de nom d'ancre"; 
		}
		return $toReturn;
	}

	/**
	 * Retourne le contenu du template de la portlet en rendu display
	 *
	 * @return String
	 */
	private function _renderHTMLDisplay (){
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);	
		$toReturn = "";
		if (array_key_exists('name', $this->_moreData) && $this->_moreData['name']){
			$toReturn = "<a name='". $this->_moreData['name'] ."'></a>";
		}

		return $toReturn;
	}

}