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
class PortletDiaporama extends PortletImage {
	
	const DEFAULT_HTML_DISPLAY_DIAPORAMA_TEMPLATE = 'images|portlettemplates/slideshow.portlet.php';
	
	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les images sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletDiaporama";
		$this->addEnabledTypes (array ('images'));
	}

	
	public function getXmlPath(){
		return CopixTpl::getFilePath("images|portlettemplates/portlettemplatesdiaporama.xml");
	}
	
	protected function getDefaultBlocTemplate(){
		return self::DEFAULT_HTML_DISPLAY_DIAPORAMA_TEMPLATE;
	}
	
	public function getTemplateElementEditor(){
		return 'images|portletdiaporama.form.php';
	}
	
	
	
}