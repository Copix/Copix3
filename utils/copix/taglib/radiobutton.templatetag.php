<?php
/**
 * @package    copix
 * @subpackage taglib
 * @author     Gérald Croës
 * @copyright  CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
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
	public function process ($pContent=null){
		$toReturn = '';
		$pParams = $this->getParams ();
		extract ($pParams);

		//input check
		$this->assertParams ('name');
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
				throw new CopixTemplateTagException ("[plugin radiobutton] parameter 'objectMap' must looks like idProp;captionProp");
			}
			$idProp      = $tab[0];
			$captionProp = $tab[1];
		}
		if (empty ($extra)){
			$extra = '';
		}

		if (empty ($separator)) {
			$separator = '&nbsp;&nbsp;';
		}
		 
		//each of the values.
		if (empty ($objectMap)){
			$index = 0;
			foreach ($values  as $key => $caption) {
				$selectedString = ((array_key_exists('selected', $pParams)) && ($key == $selected)) ? ' checked="checked" ' : '';
				$idRadio = $id .'_'. preg_replace('"\W"', '_', $key); // pour éviter un identifiant invalide
				$toReturn .= '<input type="radio" id="'.$idRadio.'" name="'.$name.'" '.$extra.' value="'.$key.'"'.$selectedString.' /><label for="'.$idRadio.'"> ' . _copix_utf8_htmlentities ($caption).'</label>';
				if ($index < count ($values) - 1) {
					$toReturn .= $separator;
				}
				$index++;
			}
		// if given an object mapping request.
		} else {
			$index = 0;
			foreach ($values  as $object) {
				$selectedString = ((array_key_exists('selected', $pParams)) && ($object->$idProp == $selected)) ? ' checked="checked" ' : '';
				$idRadio = $id.'_'.preg_replace('"\W"', '_', $object->$idProp); // pour éviter un identifiant invalide
				$toReturn .= '<input type="radio" id="'.$idRadio.'" name="'.$name.'" '.$extra.' value="'.$object->$idProp.'"'.$selectedString.' /><label for="'.$idRadio.'"> ' . _copix_utf8_htmlentities ($object->$captionProp).'</label>';
				if ($index < count ($values) - 1) {
					$toReturn .= $separator;
				}
				$index++;
			}
		}
		return $toReturn;
	}
}