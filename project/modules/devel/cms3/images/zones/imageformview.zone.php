<?php
/**
 * @package     cms
 * @subpackage  image
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Affichage pour les images  
 * 
 * @package cms
 * @subpackage images
 */
class ZoneImageFormView extends CopixZone {
	
	public function _createContent (&$toReturn){
		$options = $this->getParam('options');
		$image = $this->getParam('image');
				
		$tpl = new CopixTpl ();
		$tpl->assign('image', $image);
		
		$params = new CopixParameterHandler();
		$params->setParams($options);
					
		$width = null;
		$height = null;
		if ($params->getParam ('thumb_enabled', false)){
			$width = $params->getParam ('thumb_width');
			$height = $params->getParam ('thumb_height');
		}
		
		switch ($params->getParam('alt_image')){
			case 'caption' : 
				$alt = $image->caption_hei;
				break;
			case 'description' : 
				$alt = $image->description_hei;
				break;
			default:
				$alt = null;	
		}
		
		switch ($params->getParam('title_image')){
			case 'caption' : 
				$title = $image->caption_hei;
				break;
			case 'description' : 
				$title = $image->description_hei;
				break;
			default:
				$title = null;	
		}
		
		//pour le cache des navigateurs
		$v = 0;
		$service = new ImageServices();
		$file = $service->getPathByPublicId($image->public_id_hei);
		if(file_exists($file)){
			$v = filemtime($file);
		}
		
		//assignations
		$tpl->assign('params', $params);
		$tpl->assign ('v', $v);		
		$tpl->assign('width', $width);
		$tpl->assign('height', $height);
		$tpl->assign('alt', $alt);
		$tpl->assign('title', $title);
		$toReturn = $tpl->fetch ($this->getParam('template', 'imageformview.php'));
	
		return true;
	}
}
?>