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
* @package		copix
* @subpackage	taglib
*/
class TemplateTagCsv  extends CopixTemplateTag  {
    
    public function process ($pContent = null) {
	    extract ($this->getParams ());
	
	    //are there any values given ?
	    $this->assertParams ('values', array (), 'array');

	    //checinkg if value is an array of object or an array of array.
	    if (count ($values) <= 0){
	        $output = '';
	    }else{
	        $first = $values[0];
	        if (is_object ($first)){
	            $objectMode = true;
	        }elseif (is_array ($first)){
	            $objectMode = false;
	        }else{
	            throw new CopixTemplateTagException("[plugin CSV] parameter 'values' must be an array of object or an array of array");
	        }
	    }
	
	    //the separator
	    if (!(isset ($separator) && is_string ($separator))) {
	        $separator=',';
	    }
	
	    //no values ? empty output.
	    if (count ($values) <= 0){
	        $output = '';
	    }else{
	        $firstRow = $values[0];
	        if (is_object ($firstRow)){
	            $objectMode = true;
	        }elseif (is_array ($firstRow)){
	            $objectMode = false;
	        }else{
	            throw new CopixTemplateTagException("[plugin CSV] parameter 'values' must be an array of object or an array of associative array");
	        }
	    }
	
	    //calculating headers.
	    if (!empty ($displayHeaders) && $displayHeaders){
	        if ($objectMode){
	            $headers = get_object_vars ($firstRow);
	        }else{
	            $headers = array_key ($firstRow);
	        }
	        $output .= implode ($separator, $headers)."\n";
	    }
	
	    //exporting values into csv
	    foreach ($values as $rowNumber=>$rowValues) {
	        $rowValues = $objectMode ? array_values (get_object_vars ($rowValues)) : array_values ($rowValues);
	        $output .= implode ($separator, $rowValues)."\n";
	    }
	
	    //now sorting elements.
	    //TODO.
        return $output;
    }
}