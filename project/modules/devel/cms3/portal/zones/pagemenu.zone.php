<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author   Sylvain Vuidart
 */

/**
 * Menu affiché au dessus des pages en mode modification.
 */
class ZonePageMenu extends CopixZone {
	
	public function _createContent (&$toReturn) {
		CopixHTMLHeader::addJSLink (_resource ('heading|js/portalgeneralmenu.js'));
		$edition = $this->getParam ('edition');
		$renderContext = $this->getParam ('renderContext');
		$actions = array ('savedraft', 'savepublish', 'saveplanned');

		if (!$edition) {
			$actions[] = array ('img' => 'img/tools/update.png', 'caption' => 'Informations', 'url' => _url ('admin|edit', array ('editId' => _request ('editId'))));
		}
		if ($edition || $renderContext != RendererContext::UPDATED) {
			$actions[] = array ('img' => 'img/tools/update.png', 'caption' => 'Contenu', 'url' => _url ('admin|edit', array ('editId' => _request ('editId'), 'mode' => 'edit')));
		} 

		if ($renderContext == RendererContext::UPDATED) {
			$actions[] = array ('img' => 'img/tools/show.png', 'caption' => 'Aperçu', 'url' => _url ('portal|admin|DisplayPage', array ('editId' => _request ('editId'))));
		}

		if (!$edition) {
			$actions[] = 'cancel';
			$showBack = false;
		} else {
			$showBack = true;
		}
		
		$element = $this->getParam('element', null);

		$toReturn = CopixZone::process ('heading|headingelement/HeadingElementButtons', array ('form' => 'formPage', 'actions' => $actions, 'showBack' => $showBack, 'element'=>$element));
		return true;
	}
}