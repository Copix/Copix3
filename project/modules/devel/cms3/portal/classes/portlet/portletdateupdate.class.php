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
class PortletDateUpdate extends Portlet {
	
	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'portal|portlettemplates/default.dateupdate.tpl';
		
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les texts sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletDateUpdate";
		if (!array_key_exists('template', $this->_moreData)){
			$this->_moreData['template'] = self::DEFAULT_HTML_DISPLAY_TEMPLATE;
		}
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
	private function _renderHTMLUpdate (){
		$toReturn = CopixZone::process ('portal|PortletMenu', array('portlet'=>$this, 'module'=>'portal', 'xmlPath'=>CopixTpl::getFilePath("portal|portlettemplates/dateupdatetemplates.xml")));
		$params = new CopixParameterHandler ();
		$params->setParams($this->_moreData);
		
		$element = null;
		if ($this->id_page){
			$element = _ioClass('headingelementinformationservices')->getById ($this->id_page, 'page');
		
		} else if (($public_id = _request('public_id')) != null){
			$element = _ioClass('headingelementinformationservices')->get ($public_id);
		}
		$dateUpdateTpl = new CopixTpl ();
		$dateUpdateTpl->assign ('date', $element ? CopixDateTime::yyyymmddhhiissToFormat($element->date_update_hei, 'd/m/Y') : '');
		$dateUpdateTpl->assign ('heure', $element ? CopixDateTime::yyyymmddhhiissToFormat($element->date_update_hei, 'H:i:s') : '');
		
		$tpl = new CopixTpl();
		$tpl->assign ('portlet', $this);
		$tpl->assign ('tpl', $dateUpdateTpl->fetch ($params->getParam('template', self::DEFAULT_HTML_DISPLAY_TEMPLATE)));
		
		return $toReturn . $tpl->fetch('portal|portletdateupdate.form.php') ;
	}

	/**
	 * Retourne le contenu du template de la portlet en rendu display
	 *
	 * @return String
	 */
	private function _renderHTMLDisplay (){
		$tpl = new CopixTpl ();
		$params = new CopixParameterHandler ();
		$params->setParams($this->_moreData);

		$element = null;
		if ($this->id_page){
			$element = _ioClass('headingelementinformationservices')->getById ($this->id_page, 'page');
		
		} else if (($public_id = _request('public_id')) != null){
			$element = _ioClass('headingelementinformationservices')->get ($public_id);
		}
		
		$tpl->assign ('date', $element ? CopixDateTime::yyyymmddhhiissToFormat($element->date_update_hei, 'd/m/Y') : '');
		$tpl->assign ('heure', $element ? CopixDateTime::yyyymmddhhiissToFormat($element->date_update_hei, 'H:i:s') : '');

		return $tpl->fetch ($params->getParam('template', self::DEFAULT_HTML_DISPLAY_TEMPLATE));
	}

}