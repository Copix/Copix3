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
class TemplateTagAutoComplete {
    public function process ($pParams) {
    	extract ($pParams);
    	
    	if (!isset ($name)){
    		throw new CopixTemplateTagException ('[AutoComplete] Required parameter name');
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
    	    $onSelectTemp.= "eleme.selected.id = 'selector_autocomplete';";
    	    foreach ($maj as $key=>$field) {
    	    $onSelectTemp.= "
						$$('#selector_autocomplete .$key').each (function (el) {
							$('$field').value = el.innerHTML;
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

        $jsCode = "
window.addEvent('domready',function () {
	var elem = $('$id');
	$('autocompleteload_$name').setStyle('display', 'none');
	var completer$name = new Autocompleter.Ajax.Xhtml(elem, '"._url ($url)."', {
        'postData': {nb:10,
					 tomaj: '".$toMaj."'
";
         foreach ($pParams as $key=>$param) {
             $jsCode .= ",\n'$key':'$param'";
         }
		 $jsCode .= "
		},
		'onRequest': function(el) {			
			$('autocompleteload_$name').setStyle('display', '');
			$onRequest
		},
		'onComplete': function(el) {
			$('autocompleteload_$name').setStyle('display', 'none');
		},
		'onSelect': function (el,eleme,element) {
			$onSelect
		},
		'parseChoices': function(el) {
		    try{
				var value = el.getFirst().innerHTML;
				el.inputValue = value;
				this.addChoiceEvents(el).getFirst().setStyles({'width':'200px'}).setHTML(this.markQueryValue(value));
			}catch (e){};
		 },
		 minLength:$length,
		 maxChoices: 3
    });
});"; 

        CopixHTMLHeader::addJSCode ($jsCode);
        _eTag("mootools",array('plugin'=>"observer;autocompleter"));
        $toReturn  = '<input type="text" id="'.$name.'" name="'.$name.'" value="'.$value.'" '.$extra.' /><span id="autocompleteload_'.$name.'"><img src="'.CopixUrl::getResource('img/tools/load.gif').'" /></span>';
        return $toReturn;
    }
}
?>