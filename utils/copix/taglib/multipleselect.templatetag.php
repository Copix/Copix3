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

	   if (empty ($emptyValues)){
	      $emptyValues = array (''=>'-----');
	   }elseif (!is_array ($emptyValues)){
	   	  $emptyValues = array (''=>$emptyValues);
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
	    $idDiv = uniqid ($id);
       $arValues = array();
       $toReturnValue = '';
	   if (empty ($objectMap)){
	      $arValues = $values;
          $compteur=0;
	      foreach ((array) $values  as $key=>$caption) {
	         $selectedString = ((isset($selected) && (in_array($key,(is_array($selected) ? $selected : array($selected)), true)))) ? ' checked="checked" ' : '';
             $currentId = uniqid ();
	         $compteur++;
	         $color = ($compteur % 2 == 0) ? '#cccccc' : '#ffffff';
	         $toReturnValue .= '<div class="divcheck_'.$id.'" style="width:100%;background-color:'.$color.';color:black"><input type="checkbox" class="check_'.$id.'" id="'.$currentId.'" value="'.$key.'"'.$selectedString.' /><label id="label_'.$currentId.'" for="'.$currentId.'" style="color:black">' . _copix_utf8_htmlentities ($caption) . '</label><br /></div>';
	      }
	   }else{
	      //if given an object mapping request.
          $compteur=0;
	      foreach ((array) $values  as $object) {
	         $arValues[$object->$idProp]=$object->$captionProp;
             $currentId = uniqid ();
   	         $compteur++;
	         $color = ($compteur % 2 == 0) ? '#cccccc' : '#ffffff';
	         $selectedString = ((array_key_exists('selected', $pParams)) && (in_array($object->$idProp,(is_array($selected) ? $selected : array($selected))))) ? ' checked="checked" ' : '';
	         $toReturnValue .= '<div class="divcheck_'.$id.'" style="width:100%;background-color:'.$color.';color:black"><input type="checkbox" class="check_'.$id.'" id="'.$currentId.'" value="'.$object->$idProp.'"'.$selectedString.' /><label id="label_'.$currentId.'" for="'.$currentId.'" style="color:black">' . _copix_utf8_htmlentities ($object->$captionProp) . '</label><br /></div>';
	      }
	   }
	   
	   _tag('mootools', array('plugin'=>array ('zone')));
	   
	   $jsCode = "
		

		window.addEvent('domready', function () {
			var flag_".$name." = 0;
			var input = $('input_$id');
			var divinput = $('div_$id');
			var div   = $('$idDiv');
			var fix = new OverlayFix(div);
			div.injectInside(document.body);
			divinput.addEvent('click', function () {
				if (div.getStyle('visibility') != 'visible') {
    				div.setStyles({
    					'visibility':'visible',
    					'position':'absolute',
    					'top':input.getTop ()+input.getSize().size.y,
    					'left':input.getLeft (),
    					'width':divinput.getSize().size.x,
    					'height':'".$height."',
    					'overflow':'auto'
    				});
					fix.show();
    				input.testZone ( divinput.getTop()-5, divinput.getLeft()-5, div.getSize().size.y+divinput.getSize().size.y+10,divinput.getSize().size.x+10 );
				} else {
					div.setStyles({
    			    	'visibility':'hidden'
    				});
					fix.hide();
				}
			});
			
			input.addEvent('reset' , function () {
				$$('.check_$id').each ( function (el) {
					el.checked = false;
				});
				$('hidden_$id').setHTML ('');
			});

			input.addEvent('mouseleavezone', function () {
				fix.hide();
				div.setStyles({
    			    'visibility':'hidden'
    			});
			});

			$$('.divcheck_$id').each (function (el) {
				el.addEvent ('click', function () {
					var value = '';
					$('hidden_$id').setHTML(''); 
					$$('.check_$id').each ( function (el) {
						if (el.checked) {
							if (value!='') {
								value += ',';
							}
							value += $('label_'+el.getProperty('id')).innerHTML;
							$('hidden_$id').setHTML ($('hidden_$id').innerHTML+'<input type=\"hidden\" name=\"".$name."[]\" value=\"'+el.value+'\" />');
						}
					});
					input.value = value;
				});
			});


});
		";
	   
	   CopixHTMLHeader::addJsCode($jsCode);
	   CopixHTMLHeader::addJsCode ("
	var OverlayFix = new Class({

	initialize: function(el) {
		this.element = $(el);
		if (window.ie){
			this.element.addEvent('trash', this.destroy.bind(this));
			this.fix = new Element('iframe', {
				properties: {
					frameborder: '0',
					scrolling: 'no',
					src: 'javascript:false;'
				},
				styles: {
					position: 'absolute',
					border: 'none',
					display: 'none',
					filter: 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)'
				}
			}).injectAfter(this.element);
		}
	},

	show: function() {
		if (this.fix) this.fix.setStyles(\$extend(
			this.element.getCoordinates(), {
				display: '',
				position:'absolute',
				zIndex: (this.element.getStyle('zIndex') || 1) - 1
			}));
		return this;
	},

	hide: function() {
		if (this.fix) this.fix.setStyle('display', 'none');
		return this;
	},

	destroy: function() {
		this.fix.remove();
	}
});",'fixiediv');
	   
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
       
   	   $toReturn = '<span id="div_'.$id.'" style="width:'.$width.';vertical-align:top;" ><input type="text" id="input_'.$id.'" name="input_'.$name.'" value="'.$value.'" '.$extra.' style="width:'.$width.'" readonly="readonly" /><img style="margin-bottom:-4px;margin-left:-1px;" src="'.CopixUrl::getResource($img).'" /></span>';
	   $toReturn .= '<div id="'.$idDiv.'" style="visibility:hidden;position:absolute;z-index:9999;background-color:white;border:1px solid #bbbbbb">'.$toReturnValue;
   	   $toReturn .= '</div><div id="hidden_'.$id.'" style="visibility:hidden;position:absolute">'.$hidden.'</div>';
	   return $toReturn;
    }
}
?>