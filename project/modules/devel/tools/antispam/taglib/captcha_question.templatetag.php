<?php
/**
 * @package     tools
 * @subpackage  antispam
 * @author      Duboeuf Damien
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Génère un captcha
 * Le parametre namespace permet une multitude de captcha dans la meme page
 * 
 * @package     copix
 * @subpackage  taglib
 * 
 */
class TemplateTagCaptcha_Question extends CopixTemplateTag {
    /**
     * Construction du message
     * @param    mixed   $pContent   null (ImageProtect n'est pas censé recevoir de contenu)
     * @return   string  balise html contenant l'image
     */
	public function process ($pContent=null){
		
		
		$namespace = $this->getParam ('namespace', NULL);
		$namespace = ($namespace) ? '_' . $namespace : NULL;
		
		$params = $this->getParams();
		if (isset ($param['namespace'])) {
			unset ($param['namespace']);
		}
		
		return _class('antispam|captcha')->create()->getHTMLQuestion ($namespace, $params);
	}
}