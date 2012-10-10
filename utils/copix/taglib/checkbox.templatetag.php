<?php
/**
* @package copix
* @subpackage taglib
* @author Salleyron Julien
* @copyright CopixTeam
* @link	http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Génération de checkbox
 * 
 * @package	copix
 * @subpackage taglib
 */
class TemplateTagCheckBox extends CopixTemplateTag {
	/**
	 * Construction du code HTML
	 * On utilise également les modifications d'en tête HTML
	 */
	public function process ($pParams) {
		//input check
		if (empty ($pParams['name'])) {
			throw new CopixTemplateTagException ("[plugin checkbox] parameter 'name' cannot be empty");
		}

		$pParams['values'] = $this->getParam ('values', array ());
		if ((!is_array ($pParams['values'])) && ! ($pParams['values'] instanceof Iterator)) {
			$pParams['values'] = (array) $pParams['values'];
		}

		$pParams['id'] = $this->getParam ('id', $pParams['name']);
		$pParams['selected'] = $this->getParam ('selected', null);
		$pParams['encoding'] = $this->getParam ('encoding', null);
		$pParams['extra'] = $this->getParam ('extra', null);
		$pParams['separator'] = $this->getParam ('separator', '&nbsp;&nbsp;');

		if (!empty ($pParams['objectMap'])) {
			$tab = explode (';', $pParams['objectMap']);
			if (count ($tab) != 2) {
				throw new CopixTemplateTagException ("[plugin checkbox] parameter 'objectMap' must looks like idProp;captionProp");
			}
			$idProp = $tab[0];
			$captionProp = $tab[1];
		}
	   
		//each of the values.
		$checkboxes = array ();
		if (empty ($pParams['objectMap'])) {
			foreach ($pParams['values'] as $key => $caption) {
				$checkboxes[] = array (
					'selected' => ((array_key_exists ('selected', $pParams)) && (in_array ($key, (is_array ($pParams['selected']) ? $pParams['selected'] : array ($pParams['selected']))))) ? ' checked="checked" ' : '',
					'id' => $pParams['id'] . '_' . $key,
					'caption' => (!isset ($pParams['encodeCaption']) || $pParams['encodeCaption']) ? _copix_utf8_htmlentities ($caption, $pParams['encoding']) : $caption,
					'value' => $key
				);
			}
		//if given an object mapping request.
		} else {
			foreach ($pParams['values'] as $key => $object) {
				$checkboxes[] = array (
					'selected' => ((array_key_exists ('selected', $pParams)) && ($object->$idProp == $pParams['selected'])) ? ' checked="checked" ' : '',
					'id' => $pParams['id'] . '_' . $object->$idProp,
					'caption' => (!isset ($pParams['encodeCaption']) || $pParams['encodeCaption']) ? _copix_utf8_htmlentities ($object->$captionProp, $pParams['encoding']) : $object->$captionProp,
					'value' => $object->$idProp
				);
			}
		}

		$tpl = new CopixTPL ();
		$tpl->assign ('params', $pParams);
		$tpl->assign ('checkboxes', $checkboxes);
		return $tpl->fetch ('default|taglib/checkbox.php');
	}
}