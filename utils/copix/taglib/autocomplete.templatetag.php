<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagAutoComplete  extends CopixTemplateTag {
    public function process ($pParams) {
    	extract ($pParams);
    	
    	if (!isset ($name)){
    		throw new CopixTemplateTagException ('[AutoComplete] Required parameter name');
    	}
        if (!isset ($cle)){
            //$cle = uniqid();
            static $pm = 0;
            $cle = $pm;
            $pm++;
    	}
    	if (!isset ($field)){
    	   $field = $name;
    	}
    	if (!isset ($id)) {
    	    $id = $name;
    	}
    	
    	if (!isset ($value)) {
    	    $value = "";
    	}
    	
    	if (!isset($onSelect)) {
    	    $onSelect = "";
    	}
    	
        if (!isset($onRequest)) {
    	    $onRequest = '';
    	}
    	
    	if (!isset($extra)) {
    	    $extra = '';
    	}
    	
    	if (!isset($pParams['datasource'])) {
    	    $pParams['datasource'] = 'dao';
    	}
    	
        $toMaj = '';
        $onSelectTemp = '';
    	if (isset ($maj)) {
    	    $onSelectTemp.= "eleme.selected.id = 'selector_autocomplete$cle';";
    	    foreach ($maj as $key=>$field) {
    	    $onSelectTemp.= "
						$$('#selector_autocomplete$cle .$key').each (function (el) {
                            $('$field').value = el.getText();
						});
					";
    	    $toMaj .= $key.';'; 
    	    }
    	}
    	$onSelect = $onSelectTemp.$onSelect;
    	
    	$url = 'generictools|ajax|getAutoComplete';
    	if (isset($pParams['url'])) {
    	    $url = $pParams['url'];
        }
    	
        $length = isset ($length) ? $length : 1;
        $pParams['view'] = isset ($pParams['view']) ? $pParams['view'] : $field; 

        $tab = array();
        foreach ($pParams as $key=>$param) {
             $tab[$key]=$param;
        }
        $tab['nb'] = 10;
        $tab['tomaj'] = $toMaj;
		$js = new CopixJSWidget();
		$js->tag_autocomplete($id,$name,$length,$tab,_url($url),$js->function_(null, 'el', $onRequest),$js->function_(null,'el,eleme,element',$onSelect));
        CopixHTMLHeader::addJSDOMReadyCode($js);
        CopixHTMLHeader::addJSLink(_resource('js/taglib/tag_autocomplete.js'));
        _eTag("mootools",array('plugin'=>"observer;autocompleter"));
        $toReturn  = '<input type="text" id="'.$name.'" name="'.$name.'" value="'.$value.'" '.$extra.' /><span id="autocompleteload_'.$name.'" style="display:none;"><img src="'.CopixUrl::getResource('img/tools/load.gif').'" /></span>';
        return $toReturn;
    }
}
?>