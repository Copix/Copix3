<?php
/**
* @package      copix
* @subpackage   taglib
* @author       Gérald Croës
* @copyright    CopixTeam
* @link         http://www.copix.org
* @license      http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package     copix
 * @subpackage  taglib
 */
class TemplateTagImageProtect extends CopixTemplateTag {
    /**
     * Construction du message
     * @param    mixed   $pParams    tableau de paramètre ou clef
     * @param    mixed   $pContent   null (ImageProtect n'est pas censé recevoir de contenu)
     * @return   string  balise html contenant l'image
     */
    public function process ($pParams, $pContent=null){
    
        if (!isset ($pParams['id'])){
            throw new CopixTagException ("[ImageProtect] Missing id parameter");
        }
        
        return '<img src="'. _url('antispam|default|getimage') .'?id='. $pParams['id'] .'" />';
    }
}
?>