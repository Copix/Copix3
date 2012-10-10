<?php
/**
 * @package		standard 
 * @subpackage	generictools
* @author	Salleyron Julien
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
* @experimental
*/

/**
 * Prise en charges d'écrans de dialogues communs
* @package		standard 
 * @subpackage	generictools
 */
class ActionGroupAjax extends CopixActionGroup {
    
    public function processGetAutoComplete () {
        $datasource = CopixDatasourceFactory::get(CopixRequest::get('datasource'),CopixRequest::asArray());
	    $datasource->addCondition(CopixRequest::get('field'),'like',CopixRequest::get('value').'%');
	    $results = $datasource->find();
	    $fieldId = CopixRequest::get('field');
	    $html = '';
	    $arMaj   = explode(';',CopixRequest::get('tomaj','')); 
	    foreach ($results as $key=>$result) {
	        if ($key==CopixRequest::get('nb')) {
	         //   break;
	        }
	        $html .= '<li>';
	        foreach (explode(';',CopixRequest::get('view')) as $field) {
	            if ($fieldId == $field) {
	                $html.= '<span>'.$result->$field.'</span> ';
	            } else {
	                if (is_array($arMaj) && in_array($field,$arMaj)) {
	                    $html.= '<span class="'.$field.'">'.$result->$field.'</span> ';
	                } else {
	                    $html.= $result->$field.' ';
	                }
	            }
	        }
	        $html .= '</li>';
	    }
	    //$ppo->MAIN = '<li><span>taba</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=taba">[Dict]</a></li><li><span>tabaca</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=tabaca">[Dict]</a></li><li><span>tabacalera</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=tabacalera">[Dict]</a></li><li><span>tabacaleras</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=tabacaleras">[Dict]</a></li><li><span>tabacalero</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=tabacalero">[Dict]</a></li><li><span>tabacaleros</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=tabacaleros">[Dict]</a></li><li><span>tabaco</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=tabaco">[Dict]</a></li><li><span>tabacones</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=tabacones">[Dict]</a></li><li><span>tabacos</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=tabacos">[Dict]</a></li><li><span>tabacosa</span><a class="example-info" target="_blank" title="Dictonary Link" href="http://www.spanishdict.com/AS.cfm?e=tabacosa">[Dict]</a></li>';
	    $ppo->MAIN = $html;
	    //$ppo->HEAD  = CopixHTMLHeader::getJSCode();
	    
	    // $ppo->HEAD = CopixHTMLHeader::get();
        return _arDirectPPO ($ppo, 'blank.tpl');
    }
	
	public function processTest () {
	    $ppo = new CopixPPO ();
	    $ppo->MAIN = var_export(CopixRequest::asArray());
	    return _arDirectPPO ($ppo, 'blank.tpl');
	}
	
	public function processGetLog () {
	    $ppo = new CopixPPO ();
	    $ppo->MAIN = CopixZone::process('admin|showlog',array('profil'=>CopixRequest::get('profil')));
	    return _arDirectPPO ($ppo, 'blank.tpl');
	}
	
	public function processGetWikiPreview(){
		$wikicontent = _request('torender');		
		$ppo = new CopixPPO ();
		$ppo->MAIN = _ioClass('wikirender|wiki')->render($wikicontent);
		return _arDirectPPO ($ppo, 'blank.tpl');
	}
	
	public function processGetZone() {
	    $ppo = new CopixPPO ();
	    $zone = CopixSession::get(CopixRequest::get ('zone'));
	    if ($zone !== null) {
    	    $ppo->MAIN = CopixZone::process($zone, CopixSession::get(CopixRequest::get('sessionvar')));
    	    CopixSession::set(CopixRequest::get('sessionvar'),null);
    	    CopixSession::set(CopixRequest::get('zone'),null);
	    } else {
	        $ppo->MAIN = "";
	        CopixHTMLHeader::addJsCode("window.addEvent('domready', function () { document.location.href = ''; });");
	    }
    	    return _arDirectPPO ($ppo, 'blank.tpl');
	}
	
	public function processGetMultipleSelectContent () {
	    $classString = CopixSession::get(CopixRequest::get('class'));
	    $arClass = explode('::',$classString);
	    $class = _ioClass($arClass[0]);
	    $values = $class->$arClass[1]();
	    
	    $objectMap = CopixRequest::get ('objectMap');
		if (!empty ($objectMap)){
	      $tab = explode (';', $objectMap);
	      if (count ($tab) != 2){
	         throw new CopixTemplateTagException ("[plugin select] parameter 'objectMap' must looks like idProp;captionProp");
	      }
	      $idProp      = $tab[0];
	      $captionProp = $tab[1];
	    }
	    $id = CopixRequest::get('idselect');
	    $name = CopixRequest::get('nameselect');
	    $toReturn = '';
	    $compteur=0;
	    if (empty ($objectMap)){
	        foreach ((array) $values  as $key=>$caption) {
	            $currentId = uniqid ();
	            $compteur++;
	            $color = ($compteur % 2 == 0) ? '#cccccc' : '#ffffff';
	            $toReturn .= '<div style="width:100%;background-color:'.$color.'"><input type="checkbox" class="check_'.$id.'" id="'.$currentId.'" value="'.$key.'" /><label id="label_'.$currentId.'" for="'.$currentId.'">' . _copix_utf8_htmlentities ($caption) . '</label></div>';
	        }
	   }else{
	       //if given an object mapping request.
	       foreach ((array) $values  as $object) {
	           $color = ($compteur % 2 == 0) ? '#cccccc' : '#ffffff';
	           $toReturn .= '<div style="width:100%;background-color:'.$color.'"><input type="checkbox" id="'.$currentId.'" class="check_'.$id.'" value="'.$object->$idProp.'" /><label id="label_'.$currentId.'" for="'.$currentId.'">' . _copix_utf8_htmlentities ($object->$captionProp) . '</label></div>';
	        }
	   }
	   //CopixSession::set(CopixRequest::get('class'),null);
	    CopixHTMLHeader::addJsCode ("
		window.addEvent('domready', function () {
			var input = $('$id');
			$$('.check_$id').each (function (el) {
				el.addEvent ('change', function () {
					var value = '';
					$('hidden_$id').setHTML(''); 
					$$('.check_$id').each ( function (el) {
						if (el.checked) {
							if (value!='') {
								value += ',';
							}
							value += $('label_'+el.getProperty('id')).innerHTML;
							$('hidden_$id').setHTML ($('hidden_$id').innerHTML+'<input type=\"hidden\" name=\"".$name."[]\" value=\"'+el.value+'\" />');
						}
					});
					input.value = value;
				});
			});
		});
		");
	   return _arDirectPPO(new CopixPPO(array('MAIN'=>$toReturn)),'generictools|blank.tpl');
	}
}
?>