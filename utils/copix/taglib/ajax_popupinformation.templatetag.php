<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Balise capable d'afficher une "infobulle"
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagAjax_PopupInformation extends CopixTemplateTag {
	public function process ($pContent = null) {
		$this->assertParams ('zone');

		CopixSession::set ($sessionZone = uniqid (), $this->getParam ('zone'));
		$this->removeParam ('zone');
		CopixSession::set ($sessionVar = uniqid (), $this->getParams ());

		$id  = uniqid ('popupInformation');
		switch ($this->getParam ('handler', 'onmouseover')) {
			case 'click':
				$toReturn  = '<a rel="'.$id.'" id="div'.$id.'" href="javascript:void(null);">';
				$close = '</a>';
				break;
			default:
				//prend également en charge onmouseover qui est le handler par défaut.
				$toReturn  = '<div rel="'.$id.'" id="div'.$id.'" class="divpopup" style="display:inline;">';
				$close = '</div>';
				break;
		}
		$toReturn .= $this->getParam ('displayimg', true, 'boolean')  === true ? '<img src="'.$this->getParam ('img', _resource ('img/tools/information.png')).'" title="'.$this->getParam ('imgtitle', '').'" alt="'.$this->getParam ('alt', '').'" />' : '';
		$toReturn .= strlen ($this->getParam ('text', '')) ? $this->getParam ('text', '') : '';
		$toReturn .= strlen ($this->getParam ('imgnext', '')) ? '<img src="'.$this->getParam ('imgnext').'" />' : '';
		$toReturn .= $close;
		$width = 'auto';
		$toReturn .= '<div class="'.$this->getParam ('divclass', 'popupInformation').'" id="'.$id.'" style="width:'.$this->getParam ('width').';display:none;" >';
		//$toReturn .= $pContent;
		$toReturn .= '</div>';


		$jsCode = "
var ajax_zone = { 
	getZone : function (pZone,pSession,pDiv) {
		new Ajax('"._url ('generictools|ajax|getZone')."', {
        					method: 'post',
        					update: pDiv,
        					evalScripts : true,
							data: {'zone':pZone,'sessionvar':pSession}
        				}).request();
	}
};
";
		CopixHTMLHeader::addJSCode($jsCode,'ajaxpopup');
		switch ($this->getParam ('handler', 'onmouseover')) {
			case 'click':
				$jsCode = "
				window.addEvent('domready',function () {
				var el = $('div$id');
				var rel = $(el.getProperty('rel'));
				var save$id = {
				click : false,
				flag : false,
				hide : function () {
				if (!save$id.flag) {
				rel.fireEvent('hide');
		}
		}
		};
		el.addEvent ('click', function (e) {
		save$id.click = true;
		rel.fireEvent('display');
		});
		el.addEvent('mouseleave', function () {
		if (save$id.click) {
		save$id.hide.delay(1000);
		}
		});
		rel.addEvent ('mouseleave', function () {
		rel.fireEvent('hide');
		});
		rel.addEvent ('mouseenter', function () {
		save$id.flag = true;
		});
		rel.addEvent ('display', function (e) {
		if (rel.getStyle('display') == 'none') {
		if (rel.innerHTML=='') {
		rel.set('html', '<img src=\""._resource ('img/tools/load.gif')."\" />');
		ajax_zone.getZone('$sessionZone','$sessionVar',rel);
		}
		rel.setStyles({'left':(el.getLeft()+el.getSize().x)+'px','zIndex':'10'});
			
		rel.setStyle('display','');
		rel.fixdivShow.delay(200,rel);
		} else {
		rel.fixdivHide();
		rel.setStyle('display','none');
		}
		});
		rel.addEvent ('hide', function () {
		save$id.flag = false;
		save$id.click = false;
		rel.fixdivHide();
		rel.setStyle('display','none');
		});
		});
				";
		break;
			default:
				$jsCode = "
				window.addEvent('domready',function () {
				el = $('div$id');
				var rel = $(el.getProperty('rel'));
				/*
				el.addEvent('trash',function () {
				rel.remove();
		});
		*/

		rel.injectInside(document.body);
		el.addEvent('mouseenter', function (e) {
		if (rel.innerHTML=='') {
		rel.set('html', '<img src=\""._resource ('img/tools/load.gif')."\" />');
		ajax_zone.getZone('$sessionZone','$sessionVar',rel)
		}
		var e = new Event(e);
		rel.fixdivShow();
		rel.setStyle('visibility','visible');
		});

		el.addEvent('mousemove', function (e) {
		var e = new Event(e);
		rel.setStyles({
		'position':'absolute',
		'top' : (e.page.y+5)+'px',
		'left' : (e.page.x+5)+'px'
		});
		});

		el.addEvent('mouseleave', function (e) {
		var e = new Event(e);
		rel.fixdivHide();
		rel.setStyle('visibility','hidden');
		});
			

		});
				 ";
		}
		CopixHTMLHeader::addJSCode ($jsCode);

		$tag = new TemplateTagMootools (array('plugin'=>array('overlayfix')));
		return $tag->process ();
	}
}