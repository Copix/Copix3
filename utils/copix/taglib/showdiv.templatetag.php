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
 * Paramètres optionnels
 * 		show : bool, div affiché ou non par défaut (ne modifie pas l'état du div), true par défaut
 * 		caption : string, affiche ce texte au lieu de l'image
 * 		captioni18n : string, affiche un texte i18n au lieu de l'image
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
	    
	    if (!empty ($captioni18n)) {
	    	$caption = _i18n ($captioni18n);
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
			if (document.getElementById (\'img_\' + id) != undefined) {
				document.getElementById (\'img_\' + id).src = img;
			}
		}

		function smarty_invertShow (id) {
			smarty_showDiv (id, (document.getElementById (id).style.display != \'\'));
		}', 'smarty_showDiv');
		
	    // création du code HTML
	    $imgName = ($show) ? 'way_up' : 'way_down';
		
		if (!empty ($caption)) {
			$out = '<a href="javascript: smarty_invertShow (\'' . $id . '\');">' . $caption . '</a>';
		} else {
			$out = '<img id="img_' . $id . '" src="' . _resource ('img/tools/' . $imgName . '.png') . '" onclick="javascript: smarty_invertShow (\'' . $id . '\');" style="cursor:pointer" />';
		}
	    
	    return $out;
	} 
}
?>