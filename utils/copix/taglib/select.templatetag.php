<?php
/**
 * @package copix
 * @subpackage taglib
 * @author Gérald Croës
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Balise capable d'afficher une liste déroulante
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagSelect extends CopixTemplateTag {
	/**
	 * Input:    name     = (required name of the select box
	 *           id       = (optional) id of SELECT element.
	 *           values   = (optional) values to display the values captions will be
	 *                        html_escaped, not the ids
	 *           selected = (optional) id of the selected element
	 *           assign   = (optional) name of the template variable we'll assign
	 *                      the output to instead of displaying it directly
	 *           emptyValues = id / value for the empty selection
	 *           emptyShow = [true] / false - wether to show or not the "emptyString"
	 *           objectMap = (optional) if given idProperty;captionProperty
	 *           extra = (optional) if given, will be added directly in the select tag
	 *           strict (optional) if given, will strict compare selected with id 
	 */
	public function process ($pContent = null) {
		$pParams = $this->getParams ();
		extract ($pParams);

		//input check
		$this->assertParams ('name');
		if (!empty ($objectMap)) {
			$tab = explode (';', $objectMap);
			if (count ($tab) != 2) {
				throw new CopixTemplateTagException ("[plugin select] parameter 'objectMap' must looks like idProp;captionProp");
			}
			$idProp = $tab[0];
			$captionProp = $tab[1];
		} else if (!empty ($objectMapMethod)) {
			$tab = explode (';', $objectMapMethod);
			if (count ($tab) != 2) {
				throw new CopixTemplateTagException ("[plugin select] parameter 'objectMapMEthod' must looks like method;caption");
			}
			$idMethod = $tab[0];
			$captionProp = $tab[1];
		}

		if (empty ($emptyValues)) {
			$emptyValues = array ('' => '-----');
		} else if (!is_array ($emptyValues)) {
			$emptyValues = array ('' => $emptyValues);
		}

		if (empty ($extra)) {
			$extra = '';
		}

		if (empty ($id)) {
			$id = $name;
		}
		
		if (empty ($strict)) {
			$strict = false;
		}

		if (empty ($values)) {
			$values = array ();
		}
		if ((!is_array ($values)) && ! ($values instanceof Iterator)) {
			$values = (array)$values;
		} else if ($values instanceof Iterator) {
			$values = iterator_to_array ($values);
		}

		//proceed
		$toReturn = '<select name="' . $name . '" id="' . $id . '" ' . $extra . '>';
		if ((!isset ($emptyShow)) || $emptyShow == true) {
			//the "empty" element. If no key is the selected value, then its the one.
			$selectedString = (isset ($selected) && in_array ($selected, array_keys ($values),$strict)) ? '' : ' selected="selected" ';
			list ($keyEmpty, $valueEmpty) = each ($emptyValues);
			$toReturn .= '<option value="' . $keyEmpty . '"' . $selectedString . '>' . $valueEmpty . '</option>';
		}

		//each of the values.
		if (empty ($objectMap) && empty ($objectMapMethod)) {
			$selected = (array_key_exists ('selected', $pParams)) ? $pParams['selected'] : null;
			foreach ($values as $key => $caption) {
				if (is_array ($caption)) {
					$toReturn .= '<optgroup label="' . str_replace ('"', "''", $key) . '">';
					foreach ($caption as $realKey => $realCaption) {
						$toReturn .= $this->_getOptionHTML ($realKey, $realCaption, $strict, $selected);
					}
					$toReturn .= '</optgroup>';
				} else {
					$toReturn .= $this->_getOptionHTML ($key, $caption, $strict, $selected);
				}
			}
		} else if (!empty ($objectMap)) {
			foreach ($values as $object) {
				if($strict){
					$selectedString = ((array_key_exists('selected', $pParams)) && ($object->$idProp === $selected)) ? ' selected="selected" ' : '';
				}else{
					$selectedString = ((array_key_exists('selected', $pParams)) && ($object->$idProp == $selected)) ? ' selected="selected" ' : '';
				}
				$toReturn .= '<option value="'.$object->$idProp.'"'.$selectedString.'>' . _copix_utf8_htmlentities ($object->$captionProp) . '</option>';
			}
		} else {
			foreach ($values as $object) {
				if ($strict) {
					$selectedString = ((array_key_exists ('selected', $pParams)) && ($object->$idMethod () === $selected)) ? ' selected="selected" ' : '';
				} else {
					$selectedString = ((array_key_exists ('selected', $pParams)) && ($object->$idMethod () == $selected)) ? ' selected="selected" ' : '';
				}
				$toReturn .= '<option value="' . $object->$idMethod () . '"' . $selectedString . '>' . _copix_utf8_htmlentities ($object->$captionProp ()) . '</option>';
			}
		}
		$toReturn .= '</select>';
		return $toReturn;
	}

	/**
	 * Retourne l'HTML d'une option
	 *
	 * @param string $pKey Clef
	 * @param string $pCaption Libellé
	 * @param boolean $pStrict Indique si le test pour le selected doit être strict
	 * @return string
	 */
	private function _getOptionHTML ($pKey, $pCaption, $pStrict, $pSelected) {
		if ($pStrict) {
			$selectedString = ($pKey === $pSelected) ? ' selected="selected" ' : '';
		} else {
			$selectedString = ($pKey == $pSelected) ? ' selected="selected" ' : '';
		}
		return '<option value="' . $pKey . '"' . $selectedString . '>' . _copix_utf8_htmlentities ($pCaption) . '</option>';
	}
}