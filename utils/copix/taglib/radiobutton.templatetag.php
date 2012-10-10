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
	   
  	  if (empty ($id)){
	      $id = $name;
	   }
	
	   if (empty ($separator)) {
	       $separator = '';
	   }
	   
	   if (empty ($class)) {
	       $class = '';
	   } else {
	       $class = ' class="'.$class.'"';
	   }
	   //each of the values.
	   if (empty ($objectMap)){
	      foreach ((array) $values  as $key=>$caption) {
	         $selectedString = ((array_key_exists('selected', $pParams)) && ($key == $selected)) ? ' checked="checked" ' : '';
	         $toReturn .= '<input type="radio" '.$class.' id="'.$id.'" name="'.$name.'" '.$extra.' value="'.$key.'"'.$selectedString.' />' .  _copix_utf8_htmlentities ($caption).$separator;
	      }
	   }else{
	      //if given an object mapping request.
	      foreach ((array) $values  as $object) {
	         $selectedString = ((array_key_exists('selected', $pParams)) && ($object->$idProp == $selected)) ? ' checked="checked" ' : '';
	         $toReturn .= '<input type="radio" id="'.$id.'" name="'.$name.'" '.$extra.' value="'.$object->$idProp.'"'.$selectedString.' />' .  _copix_utf8_htmlentities ($object->$captionProp).$separator;
	      }
	   }
       return $toReturn;
   } 
}
?>