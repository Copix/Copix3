<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @authors		Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Affiche un icone et / ou un texte, qui permet d'afficher / cacher un div quand on click dessus
 * 
 * @package		copix
 * @subpackage	taglib
 * @example		{showdiv id="divId" show="true"}
 * Paramètre requis
 * 		id : id du div
 * Paramètres optionnels
 * 		show : bool, indique si le div est affiché ou non par défaut (ne modifie pas l'état du div), true par défaut.
 * 		showicon : bool, indique si on veut afficher un icone ou non. par défaut, true, et l'icone est _resource ('img/tools/way_up(ou down).png').
 * 		icondown : string, indique quel est l'icone que l'on veut afficher quand on peut afficher le div. sera passé en paramètre à _resource.
 *		iconup : string, indique quel est l'icone que l'on veut afficher quand on peut cacher le div. sera passé en paramètre à _resource.
 * 		caption : string, affiche ce texte à droite de l'icone, si il y en a un, sinon seulement le texte.
 * 		captioni18n : string, affiche un texte i18n à droite de l'icone, si il y en a un, sinon seulement le texte.
 * 		captionup : string, affiche ce texte à droite de l'icone, si il y en a un, sinon seulement le texte, lorsque l'on peut cacher le div.
 * 		captionupi18n : string, affiche ce texte i18n à droite de l'icone, si il y en a un, sinon seulement le texte, lorsque l'on peut cacher le div.
 *		captiondown : string, affiche ce texte à droite de l'icone, si il y en a un, sinon seulement le texte, lorsque l'on peut afficher le div.
 * 		captiondowni18n : string, affiche ce texte i18n à droite de l'icone, si il y en a un, sinon seulement le texte, lorsque l'on peut afficher le div.
 */
class TemplateTagShowDiv extends CopixTemplateTag {

	public function process ($pParams, $pContent = null) {
	    // paramètre id
	    if (!isset ($pParams['id'])) {
			throw new CopixTemplateTagException (_i18n ('copix:taglib.showdiv.missingIdParameter'));
	    }
	    
	    // paramètre show
	    if (!isset ($pParams['show'])) {
			$pParams['show'] = true;
	    } else {
	    	$pParams['show'] = ($pParams['show'] == 'true' || $pParams['show'] == 1);
	    }
	    
	    // paramètre captioni18n fourni, qui vaut dans le cas up et le cas down
	    if (isset ($pParams['captioni18n'])) {
	    	$pParams['captionup'] = _i18n ($pParams['captioni18n']);
	    	$pParams['captiondown'] = $pParams['captionup'];
		// si on a un paramètre caption qui s'occupe de tout les cas
	    } else if (isset ($pParams['caption'])) {
	    	$pParams['captionup'] = $pParams['caption'];
	    	$pParams['captiondown'] = $pParams['caption'];
	    // paramètres captionupi18n et captiondowni18n, qui valent chacun pour leur cas
	    } else if (isset ($pParams['captionupi18n']) && isset ($pParams['captiondowni18n'])) {
	    	$pParams['captionup'] = _i18n ($pParams['captionupi18n']);
	    	$pParams['captiondown'] = _i18n ($pParams['captiondowni18n']);	   
	    // pas de paramètre captionup ou captiondown
	    } else if (!isset ($pParams['captionup']) || !isset ($pParams['captiondown'])) {
	    	$pParams['captionup'] = null;
	    	$pParams['captiondown'] = null;
	    }
	    
	    // paramètre showicon
		$pParams['showicon'] = (!isset ($pParams['showicon']) || (isset ($pParams['showicon']) && ($pParams['showicon'] == 'true' || $pParams['showicon'] == 1)));
		
		// paramètre iconup
		$pParams['iconup'] = (isset ($pParams['iconup'])) ? _resource ($pParams['iconup']) : _resource ('img/tools/way_up.png');
		
		// paramètre icondown
		$pParams['icondown'] = (isset ($pParams['icondown'])) ? _resource ($pParams['icondown']) : _resource ('img/tools/way_down.png');
		
		// code javascript pour afficher / cacher un div		
		CopixHTMLHeader::addJsCode (
'if (!window.smarty_show_div_infos) {
	smarty_show_div_infos = new Array ();
}

function smarty_show_div (id, show) {
	if (show) {	
		img = (window.smarty_show_div_infos[id] && window.smarty_show_div_infos[id][\'img_up\']) ? smarty_show_div_infos[id][\'img_up\'] : null;
		style = \'\';
		caption = (window.smarty_show_div_infos[id] && window.smarty_show_div_infos[id][\'caption_up\']) ? smarty_show_div_infos[id][\'caption_up\'] : null;
	} else {
		img = (window.smarty_show_div_infos[id] && window.smarty_show_div_infos[id][\'img_down\']) ? smarty_show_div_infos[id][\'img_down\'] : null;
		style = \'none\';
		caption = (window.smarty_show_div_infos[id] && window.smarty_show_div_infos[id][\'caption_down\']) ? smarty_show_div_infos[id][\'caption_down\'] : null;
	}
			
	document.getElementById (id).style.display = style;
	if (document.getElementById (\'img_\' + id) != undefined) {
		document.getElementById (\'img_\' + id).src = img;
		if (caption != null) {
			document.getElementById (\'caption_\' + id).innerHTML = caption;
		}
	}
}

function smarty_invert_show (id) {
	smarty_show_div (id, (document.getElementById (id).style.display != \'\'));
}',
'smarty_show_div');
		
		// code JS pour créer le tableau des infos de cet ID
		CopixHTMLHeader::addJsCode ('smarty_show_div_infos[\'' . $pParams['id'] . '\'] = new Array ();', 'smarty_show_div_' . $pParams['id']);
		
		// code JS pour les images
		if (!is_null ($pParams['iconup']) && !is_null ($pParams['icondown'])) {
			CopixHTMLHeader::addJsCode (
				'smarty_show_div_infos[\'' . $pParams['id'] . '\'][\'img_up\'] = \'' . $pParams['iconup'] . '\';' . "\n" . 
				'smarty_show_div_infos[\'' . $pParams['id'] . '\'][\'img_down\'] = \'' . $pParams['icondown'] . '\';',
				'smarty_show_div_captions_' . $pParams['id']
			);
		}
		
		// code javascript pour les captions
		if (!is_null ($pParams['captionup']) && !is_null ($pParams['captiondown'])) {
			CopixHTMLHeader::addJsCode (
				'smarty_show_div_infos[\'' . $pParams['id'] . '\'][\'caption_up\'] = \'' . str_replace ("'", "\'", $pParams['captionup']) . '\';' . "\n" . 
				'smarty_show_div_infos[\'' . $pParams['id'] . '\'][\'caption_down\'] = \'' . str_replace ("'", "\'", $pParams['captiondown']) . '\';',
				'smarty_show_div_captions_' . $pParams['id']
			);
		}
		
	    // création du code HTML
	    if ($pParams['show']) {
	    	$imgSrc = $pParams['iconup'];
	    	$caption = $pParams['captionup'];
	    } else {
	    	$imgSrc = $pParams['icondown'];
	    	$caption = $pParams['captiondown'];
	    }
		$out = '<a href="javascript: smarty_invert_show (\'' . $pParams['id'] . '\');">';
	    
	    // si on veut afficher un icon
	    if ($pParams['showicon']) {
	    	$out .= '<img id="img_' . $pParams['id'] . '" src="' . $imgSrc . '" style="cursor:pointer" />';
	    }
	    
	    // si on veut afficher un caption
		if (!is_null ($caption)) {
			$out .= ' <span id="caption_' . $pParams['id'] . '">' . $caption . "</span>";
		}
		
		$out .= '</a>';	    
	    return $out;
	} 
}
?>