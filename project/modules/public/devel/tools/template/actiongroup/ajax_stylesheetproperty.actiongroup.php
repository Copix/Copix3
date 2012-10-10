<?php
/**
 * @package template
 * @author Croes Gérald see copix.org for other contributors
 * @copyright 2001-2006 CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.htmlGNU Leser General Public Licence, see LICENCE file
 */
CopixClassesFactory::fileInclude ('template|CopixTemplateEditor');

class ActionGroupAjax_StyleSheetProperty extends CopixActionGroup {
	/**
    * récupère la liste des feuille de style existantes sur le site (dans www)
    * TODO Implémentation de la partie feuille de styles
    */
	function getStyleSheetList () {
		//return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('admin|edit', array ('editId' => $editId)));
	}
}
?>