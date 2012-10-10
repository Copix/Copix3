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
class TemplateTagi18nSelecter extends CopixTemplateTag {
    /**
     * Construction du message
     * @param    mixed   $pParams    tableau de paramètre ou clef
     * @param    mixed   $pContent   null (ImageProtect n'est pas censé recevoir de contenu)
     * @return   string  balise html contenant le bouton de selection i18n
     */
    public function process ($pParams, $pContent=null){
    
        if (!isset ($pParams['name'])){
            throw new CopixTagException ("[ImageProtect] Missing name parameter");
        }
        if (!isset ($pParams['id'])){
            throw new CopixTagException ("[ImageProtect] Missing id parameter");
        }
        $caption = isset($pParams['caption'])?$pParams['caption']:'';
        
        
        $listeModules = CopixModule::getList(false);
        sort ($listeModules);
        
        $winSelecter = '<div id="divpopup_'.$pParams['id'].'" style="display:none; border:1px; border-style:solid; border-color:#000; background-color:#FFF ;" >'."\n\n".
        
                       '    <div id="divpopup1_'.$pParams['id'].'" style="float:left;" >'."\n".
                       '    '._tag('select', array('id' => 'selectmodule1_'.$pParams['id'], 'name' => 'selectmodule_'.$pParams['id'], 'values' => $listeModules, 'extra' => 'style="width:200px;" size="12"', 'emptyShow' => false))."\n".
                       '    </div>'."\n\n".
                       
                       '    <div id="divpopup2_'.$pParams['id'].'" style="display:none;float:left;" >'."\n".
                       '        '._tag('select', array('id' => 'selectmodule2_'.$pParams['id'], 'name' => 'selectmodule_'.$pParams['id'], 'values' => $listeModules, 'extra' => 'style="width:200px;" size="10"', 'emptyShow' => false))."\n".
                       '        <div id="divtrad_'.$pParams['id'].'" > '."\n".
                       '             <textarea cols="30" rows="1" readonly="readonly" style="width:200px; overflow:hidden;" >traduction de  \'i18n</textarea>'."\n".
                       '        </div>'."\n".
                       '    </div>'."\n\n".
                       
                       '</div>'."\n";
        
        $javascript = '<script type="text/javascript">'."\n".
                      '$("'.$pParams['id'].'").addEvent("mouseover", function (){'."\n".
                      '    $("divpopup_'.$pParams['id'].'").setStyles({'."\n".
                      '        "position":"absolute",'."\n".
                      '        "top" : 200+"px",'."\n".
                      '        "left" : 200+"px",'."\n".
                      '        "zIndex":"1000",'."\n".
                      '        "display":"block"'."\n".
                      '    });'."\n".
                      '});'."\n\n".
                      
                      '$("selectmodule1_'.$pParams['id'].'").addEvent("change", function () {'."\n".
                      '    $("divpopup2_'.$pParams['id'].'").setStyle("display","block");'."\n".
                      '});'."\n".
                      '</script>'."\n";
        
        return '<label>'.$caption.'<input type="text" name="'.$pParams['name'].'" /></label>'."\n".
               '<img src="'._resource ("img/icons/languages.gif").'" id="'.$pParams['id'].'" alt="'._i18n('copix:common.buttons.select').'" />'."\n".
               $winSelecter.
               $javascript;
    }
}
?>