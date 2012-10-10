<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Portlet d'affichage d'images  
 * 
 * @package cms
 * @subpackage images
 */
class PortletImage extends Portlet {
	
	const DEFAULT_HTML_DISPLAY_IMAGE_TEMPLATE = 'images|portlettemplates/columns.portlet.php';
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les images sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletImage";
		$this->addEnabledTypes (array ('images'));
	}
	
	public function getXmlPath(){
		return CopixTpl::getFilePath("images|portlettemplates/portlettemplates.xml");
	}
	
	protected function getDefaultBlocTemplate(){
		return self::DEFAULT_HTML_DISPLAY_IMAGE_TEMPLATE;
	}
	
	public function getTemplateElementEditor(){
		return 'images|portletimage.form.php';
	}

	/**
	 * rendu du contenu de l'image
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
			return $this->_renderHTMLDisplay ($pRendererContext);
		}else{
			return $this->_renderHTMLUpdate ();
		}
	}
	
	/**
	 * Retourne le contenu du template de la portlet en rendu update
	 *
	 * @return string
	 */
	protected function _renderHTMLUpdate (){
		
		$options = $this->getOptions();
		if (!$this->getOption('template', false)){
			$this->setOption('template', $this->getDefaultBlocTemplate());
		}

		$xmlPath = $this->getXmlPath();
		$toReturn = CopixZone::process ('portal|PortletMenu', array ('portlet'=>$this, 'module'=>'images', 'xmlPath' => $xmlPath));
		$toReturn .= "<div id='imageEditView".$this->getRandomId()."'>";
		$editionMode = "";
		if ($this->getOption('template', false)){
			try{
				$editionMode = _class('portal|templateservices')->getInfos ($xmlPath, $this->getOption('template'))->editionMode;
			} catch (CopixException $e){
				//template qui n'existe plus
				$this->setOption('template', $this->getDefaultBlocTemplate());
				$editionMode = "";
			}
		
		}
		$toReturn .= $this->getUpdateRender($editionMode);
		$toReturn .= "</div>";
		return $toReturn;
	}

	/**
	 * Retourne le contenu du template de la portlet en rendu display
	 *
	 * @return String
	 */
	protected function _renderHTMLDisplay ($pRendererContext){
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);
		$listeTemplates = array();
		$listeImages = array();
		
		if (!empty($this->_arHeadingElements)){
			ksort($this->_arHeadingElements);
			foreach ($this->_arHeadingElements as $portletElement){
				if ($portletElement->getHeadingElement ()->type_hei == "heading"){
					$results = _ioClass('heading|headingelementinformationservices')->getChildrenByType ($portletElement->getHeadingElement ()->public_id_hei, 'image');
					$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);
					foreach ($children as $child){
						$newPortletElement = new PortletElement();
						$newPortletElement->setHeadingElement($child);
						$newPortletElement->setOptions($portletElement->getOptions ());
						$listeImages[] = $newPortletElement;
					}
				} else {
					$listeImages[] = $portletElement;
				}
			}

			foreach ($listeImages as $portletElement){
				//on vérifie que l'utilisateur a les droits de lecture sur cette image
				if ($portletElement != null && HeadingElementCredentials::canRead($portletElement->getHeadingElement ()->public_id_hei)){
					$image = _ioClass ('images|imageservices')->getByPublicId ($portletElement->getHeadingElement ()->public_id_hei);
					// on appelle la zone d'affichage
					$listeTemplates[] = CopixZone::process('images|imageformview', array('options'=>$portletElement->getOptions(), 'image'=>$image));
				}
			}
		}
		
		$toReturn = '';
		$params = new CopixParameterHandler();
		$params->setParams ($this->_moreData);
		
		if (($template = $params->getParam ('template', null)) === null){
			$template = $this->getDefaultBlocTemplate();
		}

		$tpl->assign ('elementsTemplate', $listeTemplates);
		$tpl->assign ('elementsList', $listeImages);
		$tpl->assign ('context', $pRendererContext);
		$tpl->assign ('params', $params);

		return $tpl->fetch ($template);		
	}
	
	private function getProperties(){
			$properties = _ppo();
			$reflexion= new ReflectionObject ($this);
			foreach($reflexion->getProperties() as $property){
				$propertyName = $property->getName();
				$properties->$propertyName = $this->$propertyName;	 
			}
			return $properties;
	}
	
	public function setProperties(CopixPPO $properties){
		foreach ($properties as $propertyName => $property){
			try{
				$this->$propertyName = $property;
			}catch (Exception $e) {/* Do Nothing*/}
		}
	}
	
	public function cloneToDiaporama(){
		$portletDiaporama = _class('PortletDiaporama');
		$portletDiaporama->setProperties($this->getProperties());
		$portletDiaporama->type_portlet = "PortletDiaporama";
		$portletDiaporama->setOption('template', PortletDiaporama::DEFAULT_HTML_DISPLAY_DIAPORAMA_TEMPLATE);
		return $portletDiaporama; 
	}
	
	public function cloneToImage(){
		$portletDiaporama = new PortletImage();
		$portletDiaporama->setProperties($this->getProperties());
		$portletDiaporama->type_portlet = "PortletImage";
		$portletDiaporama->setOption('template', self::DEFAULT_HTML_DISPLAY_IMAGE_TEMPLATE);
		return $portletDiaporama; 
	}
	

	public function cloneToCoverFlow(){
		$portletDiaporama = new PortletCoverflow();
		$portletDiaporama->setProperties($this->getProperties());
		$portletDiaporama->type_portlet = "PortletCoverflow";
		$portletDiaporama->setOption('template', PortletCoverflow::DEFAULT_HTML_DISPLAY_COVERFLOW_TEMPLATE);
		return $portletDiaporama; 
	}

	/**
	 * Retourne la liste des liens sur les images de la portlet
	 * 
	 */
	public function getLinks (){
		$toReturn = array();
		foreach ($this->_arHeadingElements as $headingElement){
			if ($headingElement->getOption("link")){
				$element = _ioClass("heading|heading|headingelementinformationservices")->get($headingElement->getOption("link"));
				$portletElement = _class ('portal|PortletElement');
				$portletElement->setHeadingElement ($element);
				$toReturn[] = $portletElement;
			}
		}
		return $toReturn;
	}
	
	public function getElementsToSave (){
		return array_merge($this->getLinks(), $this->getElements());		
	}
	
	public function isCachable (){
		return true;
	} 
	
	public function getUpdateRender ($pEditionMode){
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);
		$tpl->assign ('editionMode', $pEditionMode);
		$tpl->assign ('currentTemplate', $this->getOption('template'));		
		$tpl->assign ('newImageVide', false);
		$position = null;
		$toReturn = "";
		if (!empty ($this->_arHeadingElements)){
			ksort($this->_arHeadingElements);
			
			foreach ($this->_arHeadingElements as $position=>$portletElement){
				if($portletElement){
					$tpl->assign ('position', $position);
					$tpl->assign ('justAddImage', false);
					try{
						$image = $portletElement->getHeadingElement()->type_hei == "heading" ? $portletElement->getHeadingElement () :
								 _ioClass ('images|imageservices')->getByPublicId ($portletElement->getHeadingElement()->public_id_hei);
						$tpl->assign ('image', $image);
						$tpl->assign ('imageNotFound', false);
						$toReturn .= $tpl->fetch ($this->getTemplateElementEditor());
					}catch(CopixException $e){
						$tpl->assign ('image', $portletElement->getHeadingElement ());
						$tpl->assign ('imageNotFound', true);
						$toReturn .= $tpl->fetch ($this->getTemplateElementEditor());
						_log($e->getMessage(), "errors", CopixLog::NOTICE);
					}
				}
			}
			
		} 

		$tpl->assign ('image', null);
		$tpl->assign ('position', ($position === null ? 0 : $position +1));
		$tpl->assign ('justAddImage', false);						
		$toReturn .= $tpl->fetch ($this->getTemplateElementEditor());

		//appel du template en mode ajout de bouton : justAddImage=>true
		$tpl->assign ('position', $position + 1);		
		$tpl->assign ('justAddImage', true);		
		$toReturn .= $tpl->fetch ($this->getTemplateElementEditor());
		return $toReturn;
	}
	
}