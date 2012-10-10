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
 * Génération de checkbox
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCheckBox extends CopixTemplateTag {
	/**
	 * Construction du code HTML
	 * On utilise également les modifications d'en tête HTML
	 */
	public function process ($pContent = null){
		$pParams = $this->getParams ();
		$toReturn = '';
		extract($pParams);

		//input check
		if (empty($name)) {
			throw new CopixTemplateTagException ("[plugin checkbox] parameter 'name' cannot be empty");
		}
		if (empty ($values)){
			$values = array ();
		}
		if ((!is_array ($values)) && ! ($values instanceof Iterator)){
			$values = (array) $values;
		}
		if(empty ($id)){
			$id = $name;
		}

		if (empty ($selected)){
			$selected = null;
		}

		if (!empty ($objectMap)){
			$tab = explode (';', $objectMap);
			if (count ($tab) != 2){
				throw new CopixTemplateTagException ("[plugin checkbox] parameter 'objectMap' must looks like idProp;captionProp");
			}
			$idProp      = $tab[0];
			$captionProp = $tab[1];
		}
		if (empty ($extra)){
			$extra = '';
		}

		if (empty($separator)) {
			$separator = '';
		}

		//check if there is a template or display in columns
		$isTemplate = (array_key_exists('template', $pParams) && $pParams['template']) || (array_key_exists('columns', $pParams) && $pParams['columns']);
		//each of the values.
		if (empty ($objectMap)){
			foreach ($values  as $key=>$caption) {
				$selectedString = ((array_key_exists('selected', $pParams)) && (in_array($key,(is_array($selected) ? $selected : array($selected))))) ? ' checked="checked" ' : '';
				$class = isset ($class) ? ' '.$class : '';
				$classString = ' class="copixcheck'.$name.$class.'"';
				$checkid = $id.'_'.$key;
				if ($isTemplate){
					$toReturn[] = '<input'.$classString.' id="'.$checkid.'" type="checkbox" name="'.$name.'[]" '.$extra.' value="'.$key.'"'.$selectedString.' /><label id="'.$checkid.'_label" for="'.$checkid.'" > '._copix_utf8_htmlentities($caption).'</label>'.$separator;
				} else {
					$toReturn .= '<input'.$classString.' id="'.$checkid.'" type="checkbox" name="'.$name.'[]" '.$extra.' value="'.$key.'"'.$selectedString.' /><label id="'.$checkid.'_label" for="'.$checkid.'" > ' . _copix_utf8_htmlentities($caption).'</label>'.$separator;
				}
			}
		}else{
			//if given an object mapping request.
			foreach ($values  as $key=>$object) {
				if (is_array($object)) {
					$object = (object)$object;
				}
				$class = isset ($class) ? ' '.$class : '';
				$classString = ' class="copixcheck'.$name.$class.'"';
				$selectedString = ((array_key_exists('selected', $pParams)) && (in_array ($object->$idProp, (is_array($selected) ? $selected : array($selected)), true)) ? ' checked="checked" ' : '');
				$checkid = $id.'_'.$object->$idProp;
				if ($isTemplate){
					$toReturn[] = '<input'.$classString.' id="'.$checkid.'" type="checkbox" name="'.$name.'[]" '.$extra.' value="'.$object->$idProp.'"'.$selectedString.' /><label id="'.$checkid.'_label" for="'.$checkid.'" > ' . _copix_utf8_htmlentities($object->$captionProp).'</label>'.$separator;
				} else {
					$toReturn .= '<input'.$classString.' id="'.$checkid.'" type="checkbox" name="'.$name.'[]" '.$extra.' value="'.$object->$idProp.'"'.$selectedString.' /><label id="'.$checkid.'_label" for="'.$checkid.'" > ' . _copix_utf8_htmlentities($object->$captionProp).'</label>'.$separator;
				}
			}
		}

		CopixHTMLHeader::addJSLink(_resource ('js/taglib/checkbox.js'));
		CopixHTMLHeader::addJSDOMReadyCode('copixcheckboxes ("copixcheck'.$name.'");');
		if ($isTemplate){
			$tpl = new CopixTpl();
			$tpl->assign('rows', $toReturn);
			$tpl->assign('params', $pParams);
			return $tpl->fetch(array_key_exists('template', $pParams) && $pParams['template'] ? $pParams['template'] : 'copix:templates/checkbox.tag.php');
		}
		return $toReturn;
	}
}