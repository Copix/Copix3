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
   	   $toReturn = '';
	   extract($pParams);

	   //input check
	   if (empty($name)) {
	     throw new CopixTemplateTagException ("[plugin radiobutton] parameter 'name' cannot be empty");
	   }
	   if (empty ($values)){
	   	   $values = array ();
	   }
	   
	   if ((!is_array ($values)) || ! ($values instanceof Iterator)){
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
	       $separator = '';
	   }
	  
	   //each of the values.
	   if (empty ($objectMap)){
	      foreach ($values  as $key=>$caption) {
	        $selectedString = ((array_key_exists('selected', $pParams)) && ($key == $selected)) ? ' checked="checked" ' : '';
	        $idRadio = $id.'_'.$key;
	        $toReturn .= '<input type="radio" id="'.$idRadio.'" name="'.$name.'" '.$extra.' value="'.$key.'"'.$selectedString.' /><label for="'.$idRadio.'">'.  _copix_utf8_htmlentities ($caption).'</label>'.$separator;
	      }
	   }else{
	      //if given an object mapping request.
	      foreach ((array) $values  as $object) {
	         $selectedString = ((array_key_exists('selected', $pParams)) && ($object->$idProp == $selected)) ? ' checked="checked" ' : '';
	         $idRadio = $id.'_'.$object->$idProp;
	         $toReturn .= '<input type="radio" id="'.$idRadio.'" name="'.$name.'" '.$extra.' value="'.$object->$idProp.'"'.$selectedString.' /><label for="'.$idRadio.'">'.  _copix_utf8_htmlentities ($object->$captionProp).'</label>'.$separator;
	      }
	   }
       return $toReturn;
   } 
}
?>