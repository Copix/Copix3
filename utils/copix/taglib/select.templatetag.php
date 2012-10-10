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
 * Balise capable d'afficher une liste déroulante
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagSelect extends CopixTemplateTag {
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
    public function process ($pParams, $pContent=null) {
	   extract($pParams);

	   //input check
	   if (empty($name)) {
	   	  throw new CopixTemplateTagException ("[plugin select] parameter 'name' cannot be empty");
	   }

	   if (!empty ($objectMap)){
	      $tab = explode (';', $objectMap);
	      if (count ($tab) != 2){
	         throw new CopixTemplateTagException ("[plugin select] parameter 'objectMap' must looks like idProp;captionProp");
	      }
	      $idProp      = $tab[0];
	      $captionProp = $tab[1];
	   }

	   if (empty ($emptyValues)){
	      $emptyValues = array (''=>'-----');
	   }elseif (!is_array ($emptyValues)){
	   	  $emptyValues = array (''=>$emptyValues);
	   }

	   if (empty ($extra)){
	      $extra = '';
	   }
	   
	   if (empty ($id)){
	      $id = $name;
	   }
	   
	   if (empty ($encoding)) {
	      $encoding = null;
	   }	   
	   
	   if (empty ($values)){
	   	   $values = array ();
	   }
   	   if ((!is_array ($values)) && ! ($values instanceof Iterator)){
	   	   $values = (array) $values;
	   } elseif ($values instanceof Iterator) {
	       $values = iterator_to_array ($values);	
	   }

	   //proceed
	   $toReturn  = '<select name="'.$name.'" id="'.$id.'" '.$extra.'>';
	   if ((!isset ($emptyShow)) || $emptyShow == true){
	      //the "empty" element. If no key is the selected value, then its the one.
	      $selectedString = (isset ($selected) && in_array ($selected, array_keys ($values))) ? '' : ' selected="selected" ';
	      list ($keyEmpty, $valueEmpty) = each ($emptyValues);
	      $toReturn .= '<option value="'.$keyEmpty.'"'.$selectedString.'>'.$valueEmpty.'</option>';
	   }
	   
	   //each of the values.
	   if (empty ($objectMap)){
	      foreach ($values  as $key=>$caption) {
	         $selectedString = ((!empty($selected)) && ($key == $selected)) ? ' selected="selected" ' : '';
	         $toReturn .= '<option value="'.$key.'"'.$selectedString.'>' . _copix_utf8_htmlentities ($caption, $encoding) . '</option>';
	      }
	   }else{
	      //if given an object mapping request.
	      foreach ($values  as $object) {
	         $selectedString = ((!empty($selected)) && ($object->$idProp == $selected)) ? ' selected="selected" ' : '';
	         $toReturn .= '<option value="'.$object->$idProp.'"'.$selectedString.'>' . _copix_utf8_htmlentities ($object->$captionProp, $encoding) . '</option>';
	      }
	   }
	   $toReturn .= '</select>';
	   return $toReturn;
    }
}
?>