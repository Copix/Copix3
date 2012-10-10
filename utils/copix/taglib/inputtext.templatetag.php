<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagInputText extends CopixTemplateTag {
	/**
    * Construction de l'input
    * @param	mixed	$pParams	tableau de paramètre ou clef
    * @param 	mixed	$pContent	
    * @return 	string	l'input fabriqué
    * 	Paramètres recommandés :
    * 		id : identifiant utile pour les labels, le javascript..
    * 		name : nom de l'input utile pour récupérer sa valeur avec php
    * 
    * 	Autres paramètres (liste non exhaustive)
    * 		value : valeur à afficher
    * 		maxlength : nombre de caractères maximals
    * 		size : taille de l'input affiché
    * 		next : zone suivante qui prendra le focus lorsque maxlenght sera atteind
    * 		previous : zone précédente qui prendra le focus lorsque tous les caratères seront effacés
    * 		(ces deux derniers paramètres sont gérés à l'aide de javascript)
    */
    public function process($pParams) {
        extract ($pParams);

        if (!isset ($pParams['id']) && !isset ($pParams['name'])){
	   		throw new CopixTagException ("[CopixTagInput] Missing id or name parameter");
        }
        
        if (!isset ($pParams['id'])){
        	$pParams['id'] = $pParams['name'];  
        }elseif (!isset ($pParams['name'])){
        	$pParams['name'] = $pParams['id'];
        }
        $readonly = '';
        if (isset ($pParams['readonly'])){
        	if($pParams['readonly']){
        		$readonly = 'readonly="readonly"';
        	}
        	unset ($pParams['readonly']);
        }   
        
        if (!isset($extra)) {
            $extra='';
        } else {
            unset($pParams['extra']);
        }
        $toReturn  = '<input type="text" '.$readonly.' '.$extra.' ';
        foreach ($pParams as $key=>$param) {
            if ($key!='next' && $key!='previous') {
                $toReturn .= $key.'="'.$param.'" ';
            }
        }	   
   		if (!isset($maxlength)) {
            $maxlength='1';
        } 
        if ((isset($next) && $next!=null && $maxlength!=null) || (isset($previous) && $previous!=null)) {
            CopixHTMLHeader::addJSLink(_resource('js/taglib/tag_inputtext.js'));
            if (!isset($previous)) {
                $previous='null';
            }
            if (!isset($next)) {
                $next='';
            }
            
            if ($next=='true') {
                $toReturn .= 'onKeyDown="javascript:autofocus(this,'.$maxlength.',event,\''.$previous.'\');" ';
            } else {
                $toReturn .= 'onKeyDown="javascript:focusid(this,'.$maxlength.',event,\''.$next.'\',\''.$previous.'\');" ';
            }
        }
        
        $toReturn.=' />';
        return $toReturn;
    }
}

?>