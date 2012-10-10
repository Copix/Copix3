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
    public function process ($pContent = null) {
    	$pParams = $this->getParams ();

    	if (!isset ($pParams['name'])){
    		throw new CopixTemplateTagException ('[AutoComplete] Required parameter name');
    	}
        if (!isset ($pParams['cle'])){
            //$cle = uniqid();
            static $pm = 0;
            $pParams['cle'] = $pm;
            $pm++;
    	}
    	if (!isset ($pParams['field'])){
    	   $pParams['field'] = $pParams['name'];
    	}
    	if (!isset ($pParams['id'])) {
    	    $pParams['id'] = $pParams['name'];
    	}

    	if (!isset ($pParams['value'])) {
    	    $pParams['value'] = "";
    	}

    	if (!isset($pParams['onSelect'])) {
    	    $pParams['onSelect'] = "";
    	}

        if (!isset($pParams['onRequest'])) {
    	    $pParams['onRequest'] = '';
    	}

    	if (!isset($pParams['extra'])) {
    	    $pParams['extra'] = '';
    	}

    	if (!isset($pParams['datasource'])) {
    	    $pParams['datasource'] = 'dao';
    	}

        $toMaj = '';
        $onSelectTemp = '';
    	if (isset ($pParams['$maj'])) {
    	    $onSelectTemp.= "eleme.selected.id = 'selector_autocomplete".$pParams['cle']."';";
    	    foreach ($pParams['$maj'] as $key=>$field) {
    	    $onSelectTemp.= "
						$$('#selector_autocomplete".$pParams['cle'].$key."').each (function (el) {
							$('$field').value = el.innerHTML;
						});
					";
    	    $toMaj .= $key.';';
    	    }
    	}
    	$pParams['onSelect'] = $onSelectTemp.$pParams['onSelect'];
    	if(!isset($pParams['url'])) {
            $pParams['url'] = 'generictools|ajax|getAutoComplete';
        }
        $pParams['url'] = _url($pParams['url']);

        $pParams['length'] = isset ($pParams['length']) ? $pParams['length'] : 1;
        $pParams['view'] = isset ($pParams['view']) ? $pParams['view'] : $pParams['field'];

        $tab = array();
        foreach ($pParams as $key=>$param) {
             $tab[$key]=$param;
        }
        $tab['nb'] = 10;
        $tab['tomaj'] = $toMaj;
		$js = new CopixJSWidget();
		$js->tag_autocomplete($pParams['id'],$pParams['name'],$pParams['length'],$tab,_url($pParams['url']),$js->function_(null, 'el', $pParams['onRequest']),$js->function_(null,'el,eleme,element',$pParams['onSelect']));
        CopixHTMLHeader::addJSDOMReadyCode($js);
        CopixHTMLHeader::addJSLink(_resource('js/taglib/tag_autocomplete.js'));
        _eTag("mootools",array('plugin'=>"observer;autocompleter;autocompleter.local;autocompleter.request"));
        $toReturn  = '<input type="text" id="'.$pParams['name'].'" name="'.$pParams['name'].'" value="'.$pParams['value'].'" '.$pParams['extra'].' />';
        //<span id="autocompleteload_'.$pParams['name'].'" style="display:none;"><img src="'.CopixUrl::getResource('img/tools/load.gif').'" /></span>';
        return $toReturn;
    }
}