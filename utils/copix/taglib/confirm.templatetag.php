<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Génération de fenetre confirm
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagConfirm extends CopixTemplateTag {
	public function process ($pContent=null){
		$toReturn = '    '.$pContent.'<br /><br />';
		$toReturn .= '    <a href="'._url ($this->requireParam ('yes')).'">'._i18n('copix:common.buttons.yes').'</a>';
		$toReturn .= '    <a href="'._url ($this->requireParam ('no')).'">'._i18n('copix:common.buttons.no').'</a>';
		_tag('mootools');
		CopixHTMLHeader::addJsCode ("
		window.addEvent('domready', function () {
		var elem = new Element('div');
		elem.setStyles({'z-index':99999,'background-color':'white','border':'1px solid black','width':'200px','height':'100px','top': window.getScrollTop().toInt()+window.getHeight ().toInt()/2-100+'px','left':window.getScrollLeft().toInt()+window.getWidth ().toInt()/2-100+'px','position':'absolute','text-align':'center'});
		elem.set ('html', '$toReturn');
		elem.injectInside(document.body);
			
	});
		");
	return null;
	}
}