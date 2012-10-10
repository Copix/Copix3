<?php
class ArticleProxy {
	public function __construct ($pArticle){
		$this->_article = $pArticle;
	}
	public function __get ($pPropertyName){
		if (in_array ($pPropertyName, array ('summary_article', 'description_hei', 'content_article'))){
			switch ($this->_article->editor_article){
				case CmsEditorServices::WYSIWYG_EDITOR :
					return _class ('cms_editor|cmswysiwygparser')->transform ($this->_article->$pPropertyName);
					break;
				default:
					return _class ('cms_editor|cmswikiparser')->transform ($this->_article->$pPropertyName);
			}
		}else{
			return $this->_article->$pPropertyName;
		}
	}
}

class PortletArticle extends Portlet {
	
	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'articles|portlettemplates/default.article.php';
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les articles sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletArticle";
		$this->addEnabledTypes (array ('articles'));
		$this->setOptions (array (
			'date_create' => false,
			'date_update' => false,
			'summary' => false,
			'content' => true
		));
	}

	/**
	 * rendu du contenu de l'article
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

		$toReturn = CopixZone::process ('portal|PortletMenu', array('portlet'=>$this, 'module'=>'articles', 'xmlPath'=>CopixTpl::getFilePath("articles|portlettemplates/portlettemplates.xml")));
		
		$tpl = new CopixTpl();
		$tpl->assign ('portlet', $this);	
		$tpl->assign ('newArticleVide', false);
		$position = null;
		if(!empty($this->_arHeadingElements)){
			foreach ($this->_arHeadingElements as $position=>$portletElement){
				$tpl->assign ('position', $position);
				$tpl->assign ('justAddArticle', false);
				try {
					$article = $portletElement->getHeadingElement()->type_hei == "heading" ? $portletElement->getHeadingElement () :
								 new ArticleProxy (_class ('articles|articleservices')->getByPublicId ($portletElement->getHeadingElement()->public_id_hei));								
					$tpl->assign ('article', $article);
					$tpl->assign ('articleNotFound', false);
					$toReturn .= $tpl->fetch ('articles|portletarticle.form.php');
				} catch (HeadingElementInformationNotFoundException $e){
					$tpl->assign ('articleNotFound', true);
					$tpl->assign ('article', $portletElement->getHeadingElement());
					$toReturn .= $tpl->fetch ('articles|portletarticle.form.php');
					_log ($e->getMessage (), 'errors', CopixLog::ERROR);
				}
			}
			
		}

		$tpl->assign ('article', null);
		$tpl->assign ('justAddArticle', false);
		$tpl->assign ('position', ($position === null ? 0 : $position +1));
		$tpl->assign ('articleNotFound', false);
		$toReturn .= $tpl->fetch ('articles|portletarticle.form.php');

		//appel du template en mode ajout de bouton : justAddArticle=>true
		$tpl->assign ('position', $position +1);
		$tpl->assign ('justAddArticle', true);
		$toReturn .= $tpl->fetch ('articles|portletarticle.form.php');
		
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

		if (!empty ($this->_arHeadingElements)){
			foreach ($this->_arHeadingElements as $portletElement){	
				if ($portletElement->getHeadingElement ()->type_hei == "heading"){
					$results = _ioClass('heading|headingelementinformationservices')->getChildrenByType ($portletElement->getHeadingElement ()->public_id_hei, 'article');
					$children = _ioClass('heading|headingelementinformationservices')->orderElements ($results, $portletElement->getOption ('order', 'display_order_hei'));
					foreach ($children as $child){
						if (HeadingElementCredentials::canRead($child->public_id_hei)){
							$newPortletElement = new PortletElement();
							$newPortletElement->setHeadingElement($child);
							$newPortletElement->setOptions($portletElement->getOptions ());
							$listeTemplates[] = $this->_renderElementHTMLDisplay ($newPortletElement, $pIsPageDisplayed);
							$listeElements[] = $newPortletElement;
						}
					}
				} else {
					if (HeadingElementCredentials::canRead($portletElement->getHeadingElement ()->public_id_hei)){
						$listeTemplates[] = $this->_renderElementHTMLDisplay ($portletElement, $pIsPageDisplayed);
						$listeElements[] = $portletElement;
					}
				}
			}
		}
		
		$toReturn = '';
		$params = new CopixParameterHandler ();
		$params->setParams ($this->_moreData);

		if ($params->getParam ('template', null) != null){
			$tpl->assign ('elementsTemplate', $listeTemplates);
			$tpl->assign ('elementsList', $listeElements);
			
			$toReturn = $tpl->fetch ($this->_moreData['template']);		
		}
		else{
			$toReturn = implode ('', $listeTemplates);				
		}
		return $toReturn;
	}
	
 	/* 
 	 * Renvoie le rendu display d'un element passé en parametre
	 *
	 * @param HeadingElement $pElement
	 */
	private function _renderElementHTMLDisplay (PortletElement $pPortletElement, $pIsPageDisplayed){
		$tpl = new CopixTpl ();
		$params = new CopixParameterHandler ();
		try{
			$article = _class ('articles|articleservices')->getByPublicId ($pPortletElement->getHeadingElement ()->public_id_hei);
		// On a supprimé l'article : au lieu d'afficher une exception on n'affiche rien mais on log.
		} catch (CopixException $e) {
			_log ($e->getMessage (), 'errors', CopixLog::EXCEPTION);
			return ($pIsPageDisplayed) ? null : $tpl->fetch ('articles|article.notfound.php');
		}
		// si on est en affichage de page et que l'article n'est pas publié, on ne l'affiche pas
		if ($article->status_hei != HeadingElementStatus::PUBLISHED && $pIsPageDisplayed){
			return '';
		}
		$options = $pPortletElement->getOptions();
		$params->setParams ($options);		

		$tpl->assign ('portlet', $this);	
		$tpl->assign ('article', new ArticleProxy ($article));
		$tpl->assign ('options', $pPortletElement->getOptions());
		
		//on laisse toutes ces options pour la compatibilité avec de vieilles utilisations de la portlet article
		$champs = array('caption', 'summary', 'content', 'description');	
		foreach ($champs as $champ){
			$tpl->assign ('is'.ucfirst($champ), $params->getParam ($champ, true));
			$tpl->assign ('is'.ucfirst($champ).'Truncate', $params->getParam ('is'.ucfirst($champ).'Truncate', true));
			$tpl->assign ($champ.'NbChar', $params->getParam ($champ.'NbChar', true));
			$tpl->assign ('isReadMore'.ucfirst($champ), $params->getParam ('isReadMore'.ucfirst($champ), true));
			$tpl->assign ('readMoreLink'.ucfirst($champ), $params->getParam ('readMoreLink'.ucfirst($champ), '#'));			
		}
		
		$tpl->assign ('isDateCreate', $params->getParam ('date_create', false));
		$tpl->assign ('isDateUpdate', $params->getParam ('date_update', false));
		$tpl->assign ('isReplaceCaption', $params->getParam ('isReplaceCaption', true) == 'true');
		$tpl->assign ('caption', ($params->getParam ('isReplaceCaption', true) == 'true') ? $params->getParam ('replacementCaption', true) : $article->caption_hei);
		
		if ($options['template'] == ""){
			$options['template'] = self::DEFAULT_HTML_DISPLAY_TEMPLATE;
		}
		
		return $tpl->fetch ($options['template']);
	}
}