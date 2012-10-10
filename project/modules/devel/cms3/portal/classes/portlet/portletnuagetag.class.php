<?php
class PortletNuageTag extends Portlet {
	
	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'portal|portlettemplates/default.nuagetag.php';
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les nuages sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletNuageTag";
	}

	/**
	 * rendu du contenu du nuage
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
				
		$toReturn = CopixZone::process ('portal|PortletMenu', array('portlet'=>$this, 'module'=>'portal', 'xmlPath'=>CopixTpl::getFilePath("portal|portlettemplates/nuageTagTemplates.xml")));
		
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);	
			
		if (!empty ($this->_arHeadingElements)){
			foreach ($this->_arHeadingElements as $position=>$portletElement){
				$nuage = _class ('portal|nuageservices')->getByPublicId ($portletElement->getHeadingElement()->public_id_hei);
				$tpl->assign ('nuage', $nuage);
				$toReturn .= $tpl->fetch ('portal|portletnuageTag.form.php');
			}
		}
		else {		
			$toReturn .= $tpl->fetch ('portal|portletnuageTag.form.php');
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
		$params = new CopixParameterHandler ();
		$params->setParams($this->_moreData);
		
		$arTags = $this->_parse($params->getParam('text'));			
		$tpl->assign ('arTags', $arTags);

		return $tpl->fetch ($params->getParam('template', self::DEFAULT_HTML_DISPLAY_TEMPLATE));
	}
	
	private function _parse ($pTexte){
		$lignes = explode ("\n", $pTexte);
		$arTags = array();
		
		
		foreach ($lignes as $ligne) {
			if ($ligne != ""){
				preg_match('/\[([^\]]*)\]\(([^ ]*)/',$ligne,$matches);

				if (isset ($matches[2])){		
					preg_match('%\(cms:(\d*)\)%', $matches[2], $publicIdMatches);
				}else{
					$publicIdMatches = null;
				}

				if (!empty($publicIdMatches)){
					$arTags[$matches[1]] = _url('heading||', array('public_id'=>$publicIdMatches[1]));
				}elseif (isset ($matches[1])){
					$arTags[$matches[1]] = isset ($matches[2]) ? $matches[2] : '';
				}
			}
		}
		
		return $arTags;
	}

}