<?php
/**
* @package		copix
* @subpackage	taglib
* @authors		Steevan BARBOYON
* @copyright	CopixTeam
* @link			http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Affiche un icone pour afficher / cacher un div
 * @package copix
 * @subpackage taglib
 * @example {showdiv id="divId" show="true"}
 * Paramètre requis
 * 		id : id du div
 * Paramètre optionnel
 * 		show : bool, affiché ou non par défaut (ne modifie pas l'état du div)
 */
class TemplateTagShowDiv extends CopixTemplateTag {

	public function process ($pParams, $pContent = null) {
	    extract ($pParams);
	    
	    // paramètre id
	    if (empty ($id)){
	      throw new CopixTemplateTagException ('[showdiv] Missing id parameter');
	    }
	    
	    // paramètre show
	    if (empty ($show)) {
			$show = true;
	    } else {
	    	$show = ($show == 'true') ? true : false;
	    }
	    
	    // code javascript pour afficher / cacher un div
	    CopixHTMLHeader::addJsCode ('function smarty_showDiv (id, show) {
			if (show) {
				img = \'' . _resource ('img/tools/way_up.png') . '\';
				style = \'\';
			} else {
				img = \'' . _resource ('img/tools/way_down.png') . '\';
				style = \'none\';
			}
			
			document.getElementById (id).style.display = style;
			document.getElementById (\'img_\' + id).src = img;
		}

		function smarty_invertShow (id) {
			smarty_showDiv (id, (document.getElementById (id).style.display != \'\'));
		}', 'smarty_showDiv');
		
	    // création du code HTML
	    $imgName = ($show) ? 'way_up' : 'way_down';
	    $out = '<img id="img_' . $id . '" src="' . _resource ('img/tools/' . $imgName . '.png') . '" onclick="javascript: smarty_invertShow (\'' . $id . '\');" style="cursor:pointer" />';
	    
	    return $out;
	} 
}
?>