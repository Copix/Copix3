<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Gérald Croës
* @copyright	2000-2006 CopixTeam
* @link			http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Génération d'une boite de saisie pour les dates
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagRadioButton extends CopixTemplateTag {
	/**
	 * Construction du code HTML
	 * On utilise également les modifications d'en tête HTML
	 *
	 * Paramètres requis :
	 * 	name : nom de l'input radio
	 *
	 * Paramètres optionels :
	 * 	id : identifiant de l'input si non précisé identique au name
	 * 	selected : clé de l'élément à sélectionner
	 * 	values : tableau contenant les valeurs à afficher
	 * 	extra : autres paramètres en extra
	 */
   public function process ($pParams, $pContent=null){
		//input check
		if (empty ($pParams['name'])) {
			throw new CopixTemplateTagException ("[plugin radiobutton] parameter 'name' cannot be empty");
		}
		if (empty ($pParams['values'])){
			$values = array ();
		}

		if ((!is_array ($pParams['values'])) && ! ($pParams['values'] instanceof Iterator)) {
			$pParams['values'] = (array)$pParams['values'];
		}

		$pParams['id'] = $this->getParam ('id', $pParams['name']);
		$pParams['selected'] = $this->getParam ('selected', null);
		$pParams['extra'] = $this->getParam ('extra', null);
		$pParams['separator'] = $this->getParam ('separator', '&nbsp;&nbsp;');
	
		if (!empty ($pParams['objectMap'])) {
			$tab = explode (';', $pParams['objectMap']);
			if (count ($tab) != 2) {
				throw new CopixTemplateTagException ("[plugin radiobutton] parameter 'objectMap' must looks like idProp;captionProp");
			}
			$idProp = $tab[0];
			$captionProp = $tab[1];
		}
	  
		//each of the values.
		$radios = array ();
		if (empty ($pParams['objectMap'])) {
			foreach ($pParams['values'] as $key => $caption) {
				$radios[] = array (
					'selected' => ((array_key_exists ('selected', $pParams)) && ($key == $pParams['selected'])) ? ' checked="checked" ' : '',
					'id' => $pParams['id'] . '_' . $key,
					'caption' => _copix_utf8_htmlentities ($caption),
					'value' => $key
				);
			}
		//if given an object mapping request.
		} else {
			foreach ($pParams['values'] as $object) {
				$radios[] = array (
					'selected' => ((array_key_exists('selected', $pParams)) && ($object->$idProp == $pParams['selected'])) ? ' checked="checked" ' : '',
					'id' => $pParams['id'] . '_' . $object->$idProp,
					'caption' => _copix_utf8_htmlentities ($object->$captionProp),
					'value' => $object->$idProp
				);
			}
		}

		$tpl = new CopixTPL ();
		$tpl->assign ('params', $pParams);
		$tpl->assign ('radios', $radios);
		return $tpl->fetch ('default|taglib/radiobutton.php');
	}
}