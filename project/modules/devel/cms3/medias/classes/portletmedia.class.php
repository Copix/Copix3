<?php
/**
 * Portlet Média 
 * Cette portlet n'est pas affiché tel quel mais étendu par les différentes portlet flash, vidéos..
 */
class PortletMedia extends Portlet {
	
	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'medias|portlettemplates/default.media.tpl';
	
	protected $p_mediaType = 'media';
	protected $p_portletFormTemplate = 'medias|portletmedia.form.php';
	
	public function getMediaType() {
		return $this->p_mediaType;
	}
	
	public function getPortletFormTemplate() {
		return $this->p_portletFormTemplate;
	}
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les medias sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "Portlet" . ucfirst(($this->p_mediaType));
		$this->addEnabledTypes (array ($this->p_mediaType));
	}

	/**
	 * rendu du contenu de l'media
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
	 * @return string
	 */
	private function _renderHTMLUpdate (){
		$toReturn = CopixZone::process ('portal|PortletMenu', array ('portlet'=>$this, 'module'=>'medias'));
		
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);		
		$tpl->assign ('newMediaVide', false);
		$tpl->assign ('arTemplates', CopixTpl::find ('medias', '.' . $this->p_mediaType . '.tpl'));
		
		$tpl->assign ('mediaType', $this->p_mediaType);
		
		//Ajout du code pour l'inclusion du média
		//$tpl->assign ('include_media_code', $tpl->fetch('medias|includecode.' . $this->p_mediaType . '.php'));
		
		if (!empty ($this->_arHeadingElements)){
			foreach ($this->_arHeadingElements as $position=>$portletElement){
				$media = _class ('medias|mediasservices')->getByPublicId ($portletElement->getHeadingElement()->public_id_hei);
				$tpl->assign ('position', $position);
				$tpl->assign ('media', $media);	
				$tpl->assign ('justAddMedia', false);
																								
				$toReturn .= $tpl->fetch ($this->p_portletFormTemplate);
			}
		}
		else {						
			$tpl->assign ('position', 0);
			$tpl->assign ('justAddMedia', false);						
			$toReturn .= $tpl->fetch ($this->p_portletFormTemplate);
		}
		//appel du template en mode ajout de bouton : justAddMedia=>true
		$tpl->assign ('position', $this->getLastHeadingElementPosition () == -1 ? 0 : $this->getLastHeadingElementPosition ());		
		$tpl->assign ('justAddMedia', true);		
		$toReturn .= $tpl->fetch ($this->p_portletFormTemplate);
		
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

		$listeTemplates = array();
		
		if (!empty($this->_arHeadingElements)){
			foreach ($this->_arHeadingElements as $portletElement){
				//on vérifie que l'utilisateur a les droits de lecture sur ce média
				if ($portletElement != null && HeadingElementCredentials::canRead($portletElement->getHeadingElement ()->public_id_hei)){
					$listeTemplates[] = $this->_renderElementHTMLDisplay ($portletElement);
				}
			}
		}
		
		$toReturn = '';
		$params = new CopixParameterHandler();
		$params->setParams ($this->_moreData);
		
		if ($params->getParam ('template', null) != null){
			$tpl = new CopixTpl ();
			$tpl->assign ('elementsTemplate', $listeTemplates);
			$tpl->assign ('elementsList', $this->_arHeadingElements);
			
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
	private function _renderElementHTMLDisplay (PortletElement $pPortletElement){
		$tpl = new CopixTpl ();
		$params = new CopixParameterHandler ();
		try{
			$media = _class ('medias|mediasservices')->getByPublicId ($pPortletElement->getHeadingElement ()->public_id_hei);
		}
		catch (CopixException $e){
			//On a supprimé le média : au lieu d'afficher une exception on n'affiche rien mais on log.
			_log($e->getMessage(), "errors", CopixLog::EXCEPTION);
			return '';
		}
		$options = $pPortletElement->getOptions();
		$params->setParams ($options);
		$tpl->assign ('portlet', $this);	
		$tpl->assign ('media', $media);
		$tpl->assign ('isContenu', $params->getParam ('file_media', true));
		$tpl->assign ('isDescription', $params->getParam ('description_hei', true));
		$tpl->assign ('width', $params->getParam ('x', '200'));
		$tpl->assign ('height', $params->getParam ('y', '200'));
		$tpl->assign ('options', $params);
		
		$tpl->assign ('include_media_code', $tpl->fetch('medias|includecode.' . $this->p_mediaType . '.php'));
		
		$options['template'] = self::DEFAULT_HTML_DISPLAY_TEMPLATE;
		
		return $tpl->fetch ($options['template']);
	}
}