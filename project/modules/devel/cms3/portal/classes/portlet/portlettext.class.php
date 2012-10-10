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
class PortletText extends Portlet {
	
	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'portal|portlettemplates/default.text.tpl';
		
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les texts sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletText";
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
		$toReturn = CopixZone::process ('portal|PortletMenu', array('portlet'=>$this, 'module'=>'portal', 'xmlPath'=>CopixTpl::getFilePath("portal|portlettemplates/texttemplates.xml")));
		$tpl = new CopixTpl ();
		$this->getElements (); // Pour vérifier que tous les éléments liés existent bien
		$tpl->assign ('portlet', $this);
		$toReturn .= $tpl->fetch ('portal|portlettext.form.php');
		
		return $toReturn;
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
		$tpl->assign ('portlet', $this);

		switch ($params->getParam('editor', CmsEditorServices::WIKI_EDITOR)){
			case CmsEditorServices::WYSIWYG_EDITOR :
				$text = _ioClass ('cms_editor|cmswysiwygparser')->transform ($params->getParam('html'));
				break;
			default:
				$text = _ioClass ('cms_editor|cmswikiparser')->transform ($params->getParam('text'));
		}
		$tpl->assign ('text', $text);

		try{
			$toReturn = $tpl->fetch ($params->getParam('template', self::DEFAULT_HTML_DISPLAY_TEMPLATE));
		} catch (CopixException $e){
			//le template n'existe plus 
			$toReturn = $tpl->fetch (self::DEFAULT_HTML_DISPLAY_TEMPLATE);
			_log ('Le template '.$params->getParam('template').' n\'existe plus, template par défaut utilisé', 'debug');
		}
		return $toReturn;
	}	
	
	/**
	 * Récupère les éléments contenu dans le texte
	 */
	public function getElements (){
		$this->_arHeadingElements = array();
		$this->_arHeadingElementsError = array();
		$params = new CopixParameterHandler ();
		$params->setParams($this->_moreData);

		switch ($params->getParam('editor', CmsEditorServices::WIKI_EDITOR)){
			case CmsEditorServices::WYSIWYG_EDITOR :
				$text = $params->getParam('html');
				break;
			default:
				$text = $params->getParam('text');
		}

		$elementsInText = array();
		
		preg_match_all('%\(cms:(\d*)(#?[^)]*)\)%', $text, $matches, PREG_SET_ORDER);
		foreach ($matches as $itemToReplace) {
			$elementsInText[] = $itemToReplace[1];
		}
		
		preg_match_all('%\(image:(\d*)\)%', $text, $matches, PREG_SET_ORDER);
		foreach ($matches as $itemToReplace){
			$elementsInText[] = $itemToReplace[1];
		}
		
		preg_match_all ('%<img(.*)public_id="(\d*)"(.*)/>%U', $text, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$elementsInText[] = $match[2];
		}
		
		foreach ($elementsInText as $id){
			try{
				$element = _ioClass ('heading|HeadingElementInformationServices')->get ($id);
				$portletElement = _class ('portal|PortletElement');
				$portletElement->setHeadingElement ($element);
				$this->_arHeadingElements[$id] = $portletElement;
			} catch (CopixException $e){
				$this->_arHeadingElementsError[$id] = $e->getMessage ();
				_log ($e->getMessage (), 'errors', CopixLog::ERROR);
			}
		}
		
		return $this->_arHeadingElements;		
	}
	
	public function isCachable (){
		return true;
	}	
}