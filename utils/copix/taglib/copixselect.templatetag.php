<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Benguigui Landry
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
class TemplateTagCopixSelect extends CopixTemplateTag {
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
	public function process ($pContent=null) {
		$pParams = $this->getParams ();
		extract($pParams);
		//input check
		$this->assertParams ('name');		

		if (!empty ($objectMap)){
			$tab = explode (';', $objectMap);
			if (count ($tab) != 2){
				throw new CopixTemplateTagException ("[plugin copixselect] parameter 'objectMap' must looks like idProp;captionProp");
			}
			$idProp      = $tab[0];
			$captionProp = $tab[1];
			$shortCaptionProp = $captionProp;
			if (isset ($tab[2])){
				$shortCaptionProp = $tab[2];
			}
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
			   
		if ((!is_array ($values)) && ! ($values instanceof Iterator)){
			$values = (array) $values;
		}
				
		if (empty ($zIndex)){
			$zIndex = '1000';
		}
		
		if (empty($heightDiv)) {
			$heightDiv = 'auto';
		} else {
			$heightDiv = intval($heightDiv).'px';
		}
			   
		if (empty($widthDiv)) {
			$widthDiv = 'auto';
		} else {
			$widthDiv = intval($widthDiv).'px';
		}
		
		if (empty($width)) {
			$width = 'auto';
		} else {
			$width = intval($width).'px';
		}
			   
		if (!isset($img)) {
			$img = 'img/tools/multiple.gif';
		}
			   
		if (!isset ($selected)){
			$selected = '';
		}
		
		if (!isset ($selectedIsIdentical)) {
			$selectedIsIdentical = false;
		}
		
		if (!isset ($class)){
			$class = 'copixselect';
		}
		$js = new CopixJSWidget();
		$toReturn = "
		<span id='clicker_$id'>
			<input id='caption$id' name='caption$name' type='text' readonly='readonly' class='$class' style='width:$width;cursor:default;text-align:left;padding-left:3px;height:13px;' value ='' /><img src=".CopixUrl::getResource('img/tools/multiple.gif')." align='absbottom' vspace='1' alt='' />
			<input id='$id' name='$name' type='hidden' class='$class' value ='$selected' />
		</span>
		<div class='$class' style='position:absolute;z-index:$zIndex;display:none;width:$widthDiv;height:$heightDiv' id='div$id'>
			<table class='$class' style='width:$widthDiv;'>";
		//each of the values.s
		$arValues = array();
		$toReturnValue = '';
		
		$event = "onmouseover = \"$(this).setStyle('cursor','pointer');$(this).setStyle ('background-color','#AAAAAA');\" onmouseout = \"$(this).setStyle('background-color','white');\" onclick =\" $('$id').value = $(this).getProperty('rel');$('caption$id').value = $(this).getText ();$('caption$id').fireEvent ('mouseleavezone');\" ";
		
		if (empty ($objectMap)){
			$compteur = 0;
			foreach ($values  as $key=>$caption) {
				if (($selectedIsIdentical && $selected === $key) || (!$selectedIsIdentical && $selected == $key)) {
					$js->_("caption".$id)->value = $caption;
				}
				$toReturn .= "<tr>
								<td class='$id' rel='$key' ".$event." >$caption</td>
							</tr>";
			}
		}else{
			//if given an object mapping request.
			$compteur = 0;
			foreach ($values  as $object) {
				if (($selectedIsIdentical && $selected === $object->$idProp) || (!$selectedIsIdentical && $selected == $object->$idProp)) {
					$js->_("caption".$id)->value = $object->$shortCaptionProp;
				}
				$arValues[$object->$idProp]=$object->$captionProp;
				$toReturn .= "<tr>
								<td class='$id' rel='".$object->$idProp."' ".$event." >".$arValues[$object->$idProp]."</td>
							</tr>";
			}
		}
		$toReturn .= "</table>
		</div>";
		_tag('mootools', array('plugin'=>array ('zone','overlayfix')));
		CopixHTMLHeader::addCSSLink(_resource('styles/taglib/copixselect.css'), array('id'=>'taglib_copix_select_css'));
		CopixHTMLHeader::addJSLink(_resource('js/taglib/copixselect.js'));
		$js->selectoverload ($id);
		CopixHTMLHeader::addJSDOMReadyCode($js);	    
		return $toReturn;
	}
}