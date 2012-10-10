<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Salleyron Julien
* @copyright	2000-2006 CopixTeam
* @link			http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Balise capable d'afficher une liste déroulante à séléction multiple
 * 
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagMultipleSelect extends CopixTemplateTag {
    /**
    * Input:    name     = (required  name of the select box
    *           id       = (optional) id of SELECT element.
    *           values   = (optional) values to display the values captions will be
    *                        html_escaped, not the ids
    *           selected = (optional) id of the selected element
    *           assign   = (optional) name of the template variable we'll assign
    *                      the output to instead of displaying it directly
    *           emptyValues = id / value for the empty selection
    *           emptyShow   = [true] / false - wether to show or not the "emptyString"
    *           objectMap   = (optional) if given idProperty;captionProperty
    *           extra = (optional) if given, will be added directly in the select tag 
    */
    public function process ($pParams, $pContent=null) {
	   extract($pParams);

	   //input check
	   if (empty($name)) {
	   	  throw new CopixTemplateTagException ("[plugin multipleselect] parameter 'name' cannot be empty");
	   }

	   if (!empty ($objectMap)){
	      $tab = explode (';', $objectMap);
	      if (count ($tab) != 2){
	         throw new CopixTemplateTagException ("[plugin multipleselect] parameter 'objectMap' must looks like idProp;captionProp");
	      }
	      $idProp      = $tab[0];
	      $captionProp = $tab[1];
	   }

	   if (empty ($extra)){
	      $extra = '';
	   }
	   
	   if (empty ($id)){
	      $id = $name;
	   }
	   
	   if (empty ($values)){
	   	   $values = array ();
	   }
	   if ((!is_array ($values)) || ! ($values instanceof Iterator)){
	   	$values = (array) $values;
	   }
	   if (empty ($height)) {
	       $height = 'auto';
	   } else {
	       $height = intval($height).'px';
	   }
	   
	   if (empty($width)) {
	       $width = 'auto';
	   } else {
	       $width = intval($width).'px';
	   }
	   
	   if (!isset($img)) {
	       $img = 'img/tools/multiple.gif';
	   }
	   
	   //each of the values.
	   $idDiv = 'divdata_'.$id;
       $arValues = array();
       $toReturnValue = '';
	   if (empty ($objectMap)){
	       $arValues = $values;
           $compteur=0;
	       foreach ($values  as $key=>$caption) {
	           $selectedString = ((isset($selected) && (in_array($key,(is_array($selected) ? $selected : array($selected)), true)))) ? ' checked="checked" ' : '';
               $currentId = uniqid ('ch');
	           $compteur++;
	           $color = ($compteur % 2 == 0) ? '#cccccc' : '#ffffff';
	           $toReturnValue .= '<div class="multipleselect_checker_'.$id.'" rel="'.$id.'" style="width:auto;background-color:'.$color.';color:black"><input type="checkbox" class="multipleselect_check_'.$id.'" rel="'.$id.'" id="'.$currentId.'" value="'.$key.'"'.$selectedString.' /><label id="label_'.$currentId.'" for="'.$currentId.'" style="color:black">' . _copix_utf8_htmlentities ($caption). '</label><br /></div>';
	       }
	   }else{
	       //if given an object mapping request.
           $compteur=0;
	       foreach ((array) $values  as $object) {
	           $arValues[$object->$idProp]=$object->$captionProp;
               $currentId = uniqid ('check');
   	           $compteur++;
	           $color = ($compteur % 2 == 0) ? '#cccccc' : '#ffffff';
	           $selectedString = ((array_key_exists('selected', $pParams)) && (in_array($object->$idProp,(is_array($selected) ? $selected : array($selected))))) ? ' checked="checked" ' : '';
	           $toReturnValue .= '<div class="multipleselect_checker_'.$id.'" rel="'.$id.'" style="width:auto;background-color:'.$color.';color:black"><input type="checkbox" class="multipleselect_check_'.$id.'" rel="'.$id.'" id="'.$currentId.'" value="'.$object->$idProp.'"'.$selectedString.' /><label id="label_'.$currentId.'" for="'.$currentId.'" style="color:black">' . _copix_utf8_htmlentities ($object->$captionProp) . '</label><br /></div>';
	       }
	   }
	   
	   _tag('mootools', array('plugin'=>array ('zone','overlayfix')));
	   
	   $jsCode = "
		window.addEvent('domready', function () {
			$$('.multipleselect_id').each(function (elementDiv) {
				var id = elementDiv.getProperty('rel');
    			var input = $('input_'+id);
    			var divinput = $('div_'+id);
    			var div   = $('divdata_'+id);
    			div.injectInside(document.body);
				if (!window.ie) {
					divinput.setStyle('border-top','1px solid transparent');
				}
    			divinput.addEvent('click', function () {
    				if (div.getStyle('visibility') != 'visible') {
        				div.setStyles({
        					'visibility':'visible',
        					'position':'absolute',
        					'top':input.getTop ()+input.getSize().size.y,
        					'left':input.getLeft (),
        					'height':$('height_'+id).getProperty('rel'),
        					'overflow':'auto'
        				});
    					if (div.getSize().size.x < divinput.getSize().size.x) {
    						div.setStyle('width',divinput.getSize().size.x);
    					}
    					div.fixdivShow();
        				input.testZone ( divinput.getTop()-5, divinput.getLeft()-5, divinput.getSize().size.y+div.getSize().size.y+10,div.getSize().size.x+10 );
    				} else {
    					div.setStyles({
        			    	'visibility':'hidden'
        				});
    					div.fixdivHide();
    				}
    			});
    			
    			input.addEvent('reset' , function () {
    				$$('.multipleselect_check_'+id).each ( function (el) {
    					el.checked = false;
    				});
    				$('hidden_'+id).setHTML ('');
    			});
    
    			input.addEvent('mouseleavezone', function () {
    				div.fixdivHide();
    				div.setStyles({
        			    'visibility':'hidden'
        			});
    			});
    			$$('.multipleselect_checker_'+id).each (function (el) {
    				el.addEvent ('click', function () {
    					var value = '';
    					$('hidden_'+id).setHTML(''); 
    					$$('.multipleselect_check_'+id).each ( function (elem) {
    						if (elem.checked) {
    							if (value!='') {
    								value += ',';
    							}
    							value += $('label_'+elem.getProperty('id')).innerHTML;
    							$('hidden_'+id).setHTML ($('hidden_'+id).innerHTML+'<input type=\"hidden\" name=\"'+$('name_'+id).getProperty('rel')+'[]\" value=\"'+elem.value+'\" />');
    						}
    					});
    					input.value = value;
    				});
    			});
			});
		});";
	   CopixHTMLHeader::addJsCode($jsCode,'multipleselect');
	   //proceed
	   $value = '';
	   $hidden = '';
	   if (isset($selected) && is_array($selected)) {
    	    foreach ($selected as $select) {
    	        if ($value!=null) {
    	            $value .= ',';
    	        }
    	        $value .= isset ($arValues[$select]) ? $arValues[$select] : '';
    	        $hidden .= '<input type="hidden" name="'.$name.'[]" value="'.$select.'" />';
    	    }
	    } elseif (isset($selected)) {
	        $value .= isset ($arValues[$select]) ? $arValues[$selected] : '';
	    }
	    //Div caché pour avoir des paramètres disponible dans le DOM
        $toReturn = '<div style="display:none;" class="multipleselect_id" rel="'.$id.'"></div><div style="display:none;" id="name_'.$id.'" rel="'.$name.'"></div><div style="display:none;" id="height_'.$id.'" rel="'.$height.'"></div>';
        //Commence par un &nbsp; car bug d'alignement dans certains nav       
   	    $toReturn .= '<span id="div_'.$id.'" style="width:'.$width.';vertical-align:center;" ><input type="text" id="input_'.$id.'" name="input_'.$name.'" value="'.$value.'" '.$extra.' style="width:'.$width.'" readonly="readonly" /><img src="'.CopixUrl::getResource($img).'" align="absbottom" vspace="1" alt="" /></span>';
	    //Div contenant la liste
   	    $toReturn .= '<div id="'.$idDiv.'" style="margin:auto;visibility:hidden;position:absolute;z-index:9999;background-color:white;border:1px solid #bbbbbb">'.$toReturnValue.'</div>';
   	    //Div contenant les champs hidden permettant de passer les valeurs sélectionner
   	    $toReturn .= '<div id="hidden_'.$id.'" style="visibility:hidden;position:absolute">'.$hidden.'</div>';
	    return $toReturn;
    }
}
?>