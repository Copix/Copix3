<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Gérald Croës
* @copyright	CopixTeam
* @link			http://www.copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Equivalent à la fonction smarty "_cycle"  
* @package		copix
* @subpackage	taglib
*/
class TemplateTagCycle extends CopixTemplateTag  {
	public function process ($pContent=null){
	    static $cycle_vars;
	    $pParams = $this->getParams ();
	    
	    $name = (empty($pParams['name'])) ? 'default' : $pParams['name'];
	    $print = (isset($pParams['print'])) ? (bool)$pParams['print'] : true;
	    $advance = (isset($pParams['advance'])) ? (bool)$pParams['advance'] : true;
	    $reset = (isset($pParams['reset'])) ? (bool)$pParams['reset'] : false;
	            
	    if (!in_array('values', array_keys($pParams))) {
	        if(!isset($cycle_vars[$name]['values'])) {
	            $smarty->trigger_error("cycle: missing 'values' parameter");
	            return;
	        }
	    } else {
	        if(isset($cycle_vars[$name]['values'])
	            && $cycle_vars[$name]['values'] != $pParams['values'] ) {
	            $cycle_vars[$name]['index'] = 0;
	        }
	        $cycle_vars[$name]['values'] = $pParams['values'];
	    }
	
	    $cycle_vars[$name]['delimiter'] = (isset($pParams['delimiter'])) ? $pParams['delimiter'] : ',';
	    
	    if(is_array($cycle_vars[$name]['values'])) {
	        $cycle_array = $cycle_vars[$name]['values'];
	    } else {
	        $cycle_array = explode($cycle_vars[$name]['delimiter'],$cycle_vars[$name]['values']);
	    }
	    
	    if(!isset($cycle_vars[$name]['index']) || $reset ) {
	        $cycle_vars[$name]['index'] = 0;
	    }
	    
	    if (isset($pParams['assign'])) {
	        $print = false;
	        $smarty->assign($pParams['assign'], $cycle_array[$cycle_vars[$name]['index']]);
	    }
	        
	    if($print) {
	        $retval = $cycle_array[$cycle_vars[$name]['index']];
	    } else {
	        $retval = null;
	    }
	
	    if($advance) {
	        if ( $cycle_vars[$name]['index'] >= count($cycle_array) -1 ) {
	            $cycle_vars[$name]['index'] = 0;
	        } else {
	            $cycle_vars[$name]['index']++;
	        }
	    }
	    
	    return $retval;
	}
}