<?php
/**
 * @package    copix
 * @subpackage taglib
 * @author     Salleyron Julien
 * @copyright  CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
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
	public function process ($pContent=null) {
		$pParams = $this->getParams ();
		extract ($pParams);

		//input check
		$this->assertParams ('name');

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
		if ((!is_array ($values)) && ! ($values instanceof Iterator)){
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
				$selectedString = ((isset($selected) && (in_array($key,(is_array($selected) ? $selected : array($selected)), $this->getParam ('strict', true))))) ? ' checked="checked" ' : '';
				$currentId = uniqid ('ch');
				$compteur++;
				$color = ($compteur % 2 == 0) ? '#cccccc' : '#ffffff';
				$toReturnValue .= '<div class="multipleselect_checker_'.$id.'" rel="'.$id.'" style="width:auto;background-color:'.$color.';color:black"><input type="checkbox" class="multipleselect_check_'.$id.'" rel="'.$id.'" id="'.$currentId.'" value="'.$key.'"'.$selectedString.' /><label id="label_'.$currentId.'" for="'.$currentId.'" style="color:black">' . _copix_utf8_htmlentities ($caption). '</label><br /></div>';
			}
		}else{
			//if given an object mapping request.
			$compteur=0;
			foreach ($values  as $object) {
				$arValues[$object->$idProp]=$object->$captionProp;
				$currentId = uniqid ('check');
				$compteur++;
				$color = ($compteur % 2 == 0) ? '#cccccc' : '#ffffff';
				$selectedString = ((array_key_exists('selected', $pParams)) && (in_array($object->$idProp,(is_array($selected) ? $selected : array($selected))))) ? ' checked="checked" ' : '';
				$toReturnValue .= '<div class="multipleselect_checker_'.$id.'" rel="'.$id.'" style="width:auto;background-color:'.$color.';color:black"><input type="checkbox" class="multipleselect_check_'.$id.'" rel="'.$id.'" id="'.$currentId.'" value="'.$object->$idProp.'"'.$selectedString.' /><label id="label_'.$currentId.'" for="'.$currentId.'" style="color:black">' . _copix_utf8_htmlentities ($object->$captionProp) . '</label><br /></div>';
			}
		}

		_tag('mootools', array('plugin'=>array ('zone','overlayfix')));

		CopixHTMLHeader::addJSLink(_resource('js/taglib/multipleselect.js'));
		CopixHTMLHeader::addJsDomReadyCode('multipleselect ();','multipleselect');
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
		} elseif (isset($selected) && is_string($selected)) {
			$value .= isset ($arValues[$selected]) ? $arValues[$selected] : '';
		}
		//Div caché pour avoir des paramètres disponible dans le DOM
		$toReturn = '<div style="display:none;" class="multipleselect_id" rel="'.$id.'"></div><div style="display:none;" id="name_'.$id.'" rel="'.$name.'"></div><div style="display:none;" id="height_'.$id.'" rel="'.$height.'"></div>';
		//Commence par un &nbsp; car bug d'alignement dans certains nav
		$noreset = false;
		if (isset($pParams['noreset']) && $pParams['noreset']) {
			$noreset = true;
		}

		$input = _tag ('inputtext', array ('name' => 'input_' . $id, 'value' => $value, 'extra' => $extra, 'readonly' => 'readonly', 'noreset' => 'noreset', 'style' => 'width: ' . $width));
		$toReturn .= '<span id="div_'.$id.'" style="vertical-align:center;" >' . $input;
                if ($this->getParam('displayimg', true)) {
                        $toReturn .= '<img src="'.CopixUrl::getResource($img).'" align="absbottom" vspace="1" alt="" />';
                }
                $toReturn .= '</span>';

		//Div contenant la liste
		$toReturn .= '<div id="'.$idDiv.'" style="margin:auto;visibility:hidden;top:1px;left:1px;position:absolute;z-index:9999;background-color:white;border:1px solid #bbbbbb">'.$toReturnValue.'</div>';
		//Div contenant les champs hidden permettant de passer les valeurs sélectionner
		$toReturn .= '<div id="hidden_'.$id.'" style="visibility:hidden;position:absolute">'.$hidden.'</div>';
		return $toReturn;
	}
}