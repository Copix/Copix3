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
class TemplateTagInputText {
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
        if (!isset($extra)) {
            $extra='';
        }
        $toReturn  = '<input type="text" '.$extra.' ';
        foreach ($pParams as $key=>$param) {
            if ($key!='next' && $key!='previous') {
                $toReturn .= $key.'="'.$param.'" ';
            }
        }	   
   		if (!isset($maxlength)) {
            $maxlength='1';
        } 
        if ((isset($next) && $next!=null && $maxlength!=null) || (isset($previous) && $previous!=null)) {
            CopixHTMLHeader::addJSCode('
			function autofocus(theField,len,e,previous) {
				var keyCode = e.keyCode; 
				var filter = [0,8,9,16,17,18,37,38,39,40,46];
				if(theField.value.length >= len && !containsElement(filter,keyCode)) {
					theField.form[(getIndex(theField)+1) % theField.form.length].focus();
				}
				if (keyCode==8 && theField.value.length==0 && previous!=null) {
					focusprevious(theField,previous);
				}
				return true;
			}

			function focusid(theField,len,e,id,previous) {
				var keyCode = e.keyCode; 
				var filter = [0,8,9,16,17,18,37,38,39,40,46];
				if(document.getElementById(id) && theField.value.length >= len && !containsElement(filter,keyCode)) {
					document.getElementById(id).focus();
				}
				if (keyCode==8 && theField.value.length==0 && previous!=null) {
					focusprevious(theField,previous);
				}				
				return true;
			}

			function focusprevious(theField,previous) {
				if (previous==true) {
				    if (getIndex(theField)!=1 && previous!=null) {
					    theField.form[(getIndex(theField)-1) % theField.form.length].focus();
				    }
                } else {
					if (document.getElementById(previous)) {
						document.getElementById(previous).focus();
					}
				}
            }

			function containsElement(arr, ele) {
				var found = false, index = 0;
				while(!found && index < arr.length)
					if(arr[index] == ele)
						found = true;
					else
						index++;
				return found;
			}

			function getIndex(input) {
				var index = -1, i = 0, found = false;
				while (i < input.form.length && index == -1)
					if (input.form[i] == input)
						index = i;
					else 
						i++;
				return index;
			}
			','autofocus');
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