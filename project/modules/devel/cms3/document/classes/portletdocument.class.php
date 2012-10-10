<?php

class PortletDocument extends Portlet {
	
	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'document|portlettemplates/default.document.tpl';
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les documents sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletDocument";
		$this->addEnabledTypes (array ('document'));
	}

	/**
	 * rendu du contenu du document
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
			return $this->_renderHTMLDisplay ($pRendererContext == RendererContext::DISPLAYED);
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
		$toReturn = CopixZone::process ('portal|PortletMenu', array('portlet'=>$this, 'module'=>'document'));
		
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);	
		$tpl->assign ('newDocumentVide', false);		
		$position = null;
		if (!empty ($this->_arHeadingElements)){
			foreach ($this->_arHeadingElements as $position=>$portletElement){
				$tpl->assign ('position', $position);
				$tpl->assign ('justAddDocument', false);
				try {
					$document = $portletElement->getHeadingElement()->type_hei == "heading" ? $portletElement->getHeadingElement () :
								 _ioClass ('document|documentservices')->getByPublicId ($portletElement->getHeadingElement()->public_id_hei);
					$tpl->assign ('document', $document);
					$tpl->assign ('documentNotFound', false);
					$toReturn .= $tpl->fetch ('document|portletdocument.form.php');
				} catch (Exception $e) {
					$tpl->assign ('document', $portletElement->getHeadingElement ());
					$tpl->assign ('documentNotFound', true);
					$toReturn .= $tpl->fetch ('document|portletdocument.form.php');
					_log ($e->getMessage (), 'errors', CopixLog::ERROR);
				}
			}
		}
	
		$tpl->assign ('documentNotFound', false);
		$tpl->assign ('document', null);
		$tpl->assign ('justAddDocument', false);
		$tpl->assign ('position', ($position === null ? 0 : $position +1));
				
		$toReturn .= $tpl->fetch ('document|portletdocument.form.php');

		//appel du template en mode ajout de bouton : justAdddocument=>true
		$tpl->assign ('position', $position +1);
		$tpl->assign ('justAddDocument', true);
		$toReturn .= $tpl->fetch ('document|portletdocument.form.php');
		
		return $toReturn;
	}
	
	/**
	 * Retourne le contenu du template de la portlet en rendu display
	 *
	 * @return String
	 */
	private function _renderHTMLDisplay ($pIsPageDisplayed){
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);	
		$listeTemplates = array ();
		$listeElements  = array ();
        $filesizes      = array ();

		$toReturn = '';
		if (!empty($this->_arHeadingElements)){
			foreach ($this->_arHeadingElements as $portletElement){
				if ($portletElement->getHeadingElement ()->type_hei == "heading"){
					$results = _ioClass('heading|headingelementinformationservices')->getChildrenByType ($portletElement->getHeadingElement ()->public_id_hei, 'document');
					$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);
					foreach ($children as $child){
						$newPortletElement = new PortletElement();
						$newPortletElement->setHeadingElement($child);
						$newPortletElement->setOptions($portletElement->getOptions ());
						$listeElements[] = $newPortletElement;
					}
				} else {
					$listeElements[] = $portletElement;
				}
			}
			
			foreach ($listeElements as $portletElement){				
				try{
					$document = _class ('document|documentservices')->getByPublicId ($portletElement->getHeadingElement ()->public_id_hei);
					// si on est en affichage de page et que le document n'est pas publié, on ne l'affiche pas
					if (!($document->status_hei != HeadingElementStatus::PUBLISHED && $pIsPageDisplayed)){
						//on vérifie que l'utilisateur a les droits de vision sur ce document
						if ($portletElement != null && HeadingElementCredentials::canShow($portletElement->getHeadingElement ()->public_id_hei)){					
							$listeTemplates[] = $this->_renderElementHTMLDisplay ($portletElement, $document);
						}
					}
				}
				catch (CopixException $e){
					if (!$pIsPageDisplayed) {
						$tpl = new CopixTPL ();
						$listeTemplates[] = $tpl->fetch ('document|document.notfound.php');
					}
					//On a supprimé le document : au lieu d'afficher une exception on n'affiche rien mais on log.
					_log($e->getMessage (), 'errors', CopixLog::ERROR);
				}
			}
		}
		// C'est crade, il faudrait utiliser un filtre mais pour l'instant c'est plus rapide.
		foreach ($listeElements as $key => $element){
			$filesizes[$key] = $this->_byteConvert($element->size_document);
		}
		
		$toReturn = '';
		$params = new CopixParameterHandler ();
		$params->setParams ($this->_moreData);
		
		if($params->getParam ('template', null) != null){
			$tpl->assign ('elementsTemplate', $listeTemplates);
			$tpl->assign ('elementsList', $listeElements);
			$tpl->assign ('filesizes', $filesizes);
			
			$toReturn = $tpl->fetch ($this->_moreData['template']);		
		}
		else{
			$toReturn = implode ('', $listeTemplates);				
		}			
		return $toReturn;
	}
	
	
	/**
	 * Renvoie le rendu display d'un element passé en parametre
	 *
	 * @param HeadingElement $pElement
	 */
	private function _renderElementHTMLDisplay (PortletElement $pPortletElement, $pDocument){		
		$tpl = new CopixTpl ();
		$params = new CopixParameterHandler ();
		
		$options = $pPortletElement->getOptions();
		$params->setParams ($options);
		$tpl->assign ('portlet', $this);	
		$tpl->assign ('document', $pDocument);
		$tpl->assign ('type', str_replace( '.', '', CopixFile::extractFileExt($pDocument->file_document)));
		$tpl->assign ('isTitre', $params->getParam ('caption_hei', true));
		$tpl->assign ('isContenu', $params->getParam ('file_document', true));
		$tpl->assign ('isDescription', $params->getParam ('description_hei', true));
		$tpl->assign ('content_disposition', $params->getParam ('content_disposition'));
		$tpl->assign ('filesize', $this->_byteConvert ($pDocument->size_document));
		$tpl->assign ('options', $options);
		if (!array_key_exists('template', $options) || $options['template'] == ""){
			$options['template'] = self::DEFAULT_HTML_DISPLAY_TEMPLATE;
		}
		
		return $tpl->fetch ($options['template']);
	}
	
	private function _byteConvert(&$bytes){
        $b = (int)$bytes;
        $s = array('B', 'kB', 'MB', 'GB', 'TB');
        if($b <= 0){
            return "0 ".$s[0];
        }
        $con = 1024;
        $e = (int)(log($b,$con));
        return number_format($b/pow($con,$e),2,',','.').' '.$s[$e]; 
	}
	
	public function isCachable (){
		return true;
	}
}