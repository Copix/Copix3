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
   public function process ($pParams, $pContent=null){
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
	   
       if (empty ($encoding)){
	      $encoding = null;
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
	   
	   //each of the values.
	   if (empty ($objectMap)){
	      foreach ($values  as $key=>$caption) {
	         $selectedString = ((array_key_exists('selected', $pParams)) && (in_array($key,(is_array($selected) ? $selected : array($selected))))) ? ' checked="checked" ' : '';
	         $classString = (!empty($class)) ? ' class="'.$class.'"' : '';
	         $checkid = $id.'_'.$key;
	         $toReturn .= '<input'.$classString.' id="'.$checkid.'" type="checkbox" name="'.$name.'[]" '.$extra.' value="'.$key.'"'.$selectedString.' /><label id="'.$checkid.'_label" for="'.$checkid.'" >'._copix_utf8_htmlentities($caption, $encoding).'</label>'.$separator;
	      }
	   }else{
	      //if given an object mapping request.
	      foreach ($values  as $key=>$object) {
	         $selectedString = ((array_key_exists('selected', $pParams)) && ($object->$idProp == $selected)) ? ' checked="checked" ' : '';
	         $checkid = $id.'_'.$object->$idProp;
	         $toReturn .= '<input'.$classString.' id="'.$checkid.'" type="checkbox" name="'.$name.'[]" '.$extra.' value="'.$object->$idProp.'"'.$selectedString.' /><label id="'.$checkid.'_label" for="'.$checkid.'" >' . _copix_utf8_htmlentities($object->$captionProp, $encoding).'</label>'.$separator;
	      }
	   }
       return $toReturn;
   } 
}
?>