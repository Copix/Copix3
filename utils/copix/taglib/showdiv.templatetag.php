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
 *      title : string, sera mit dans le alt et le title de l'icone
 * 		icondown : string, indique quel est l'icone que l'on veut afficher quand on peut afficher le div. sera passé en paramètre à _resource.
 *		iconup : string, indique quel est l'icone que l'on veut afficher quand on peut cacher le div. sera passé en paramètre à _resource.
 * 		caption : string, affiche ce texte à droite de l'icone, si il y en a un, sinon seulement le texte.
 * 		captioni18n : string, affiche un texte i18n à droite de l'icone, si il y en a un, sinon seulement le texte.
 * 		captionup : string, affiche ce texte à droite de l'icone, si il y en a un, sinon seulement le texte, lorsque l'on peut cacher le div.
 * 		captionupi18n : string, affiche ce texte i18n à droite de l'icone, si il y en a un, sinon seulement le texte, lorsque l'on peut cacher le div.
 *		captiondown : string, affiche ce texte à droite de l'icone, si il y en a un, sinon seulement le texte, lorsque l'on peut afficher le div.
 * 		captiondowni18n : string, affiche ce texte i18n à droite de l'icone, si il y en a un, sinon seulement le texte, lorsque l'on peut afficher le div.
 *		clicker : identifiant de l'élément à clicker pour afficher / cacher le div demandé (si on n'en spécifie pas, un span avec l'icone et l ecaption sera généré)
 *      userpreference : string, nom de la préférence utilisateur à lire / sauvegarder pour afficher / cacher par défaut et sauvegarder le statut
 *      alternate : string, html d'un div affiché à la place du div à cacher, lorsque celui-ci est caché
 *		alternateelement : identifiant d'un élément HTML à afficher à la place du div à cacher, quand celui-ci est caché
 */
class TemplateTagShowDiv extends CopixTemplateTag {

	public function process ($pContent = null) {
		$pParams = $this->getParams ();

		// paramètre id
		$this->assertParams ('id');

		// paramètre show
		if (!isset ($pParams['show'])) {
			$pParams['show'] = (isset ($pParams['userpreference'])) ? CopixUserPreferences::get ($pParams['userpreference'], true) : true;
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
'if (!window.taglib_show_div_infos) {
	taglib_show_div_infos = new Array ();
}

function taglib_show_div (id, show) {
	if (show) {
		img = (window.taglib_show_div_infos[id] && window.taglib_show_div_infos[id][\'img_up\']) ? taglib_show_div_infos[id][\'img_up\'] : null;
		style = \'\';
		styleAlternate = \'none\';
		caption = (window.taglib_show_div_infos[id] && window.taglib_show_div_infos[id][\'caption_up\']) ? taglib_show_div_infos[id][\'caption_up\'] : null;
	} else {
		img = (window.taglib_show_div_infos[id] && window.taglib_show_div_infos[id][\'img_down\']) ? taglib_show_div_infos[id][\'img_down\'] : null;
		style = \'none\';
		styleAlternate = \'\';
		caption = (window.taglib_show_div_infos[id] && window.taglib_show_div_infos[id][\'caption_down\']) ? taglib_show_div_infos[id][\'caption_down\'] : null;
	}
			
	$ (id).setStyle (\'display\', style);
	
	$ (id).fireEvent (\'display\');
	
	if ($ (\'img_\' + id) != undefined) {
		$ (\'img_\' + id).src = img;
		if (caption != null) {
			$ (\'caption_\' + id).set (\'html\', caption);
		}
	}

	if (window.taglib_show_div_infos[id][\'alternateelement\']) {
		$ (window.taglib_show_div_infos[id][\'alternateelement\']).setStyle (\'display\', styleAlternate);
	} else if (window.taglib_show_div_infos[id][\'alternate\']) {
		$ (id + \'_alternate\').setStyle (\'display\', styleAlternate);
	}

	if (window.taglib_show_div_infos[id][\'userpreference\']) {
		Copix.savePreference (window.taglib_show_div_infos[id][\'userpreference\'], (show) ? 1 : 0);
	}
}

function taglib_invert_show (id) {
	taglib_show_div (id, ($ (id).getStyle (\'display\') == \'none\'));
}',

'taglib_show_div');

		// code JS pour créer le tableau des infos de cet ID
		$js = 'taglib_show_div_infos[\'' . $pParams['id'] . '\'] = new Array ();';

		// code JS pour les images
		if ($pParams['showicon'] && !is_null ($pParams['iconup']) && !is_null ($pParams['icondown'])) {
			$js .= 'taglib_show_div_infos[\'' . $pParams['id'] . '\'][\'img_up\'] = \'' . $pParams['iconup'] . '\';';
			$js .= 'taglib_show_div_infos[\'' . $pParams['id'] . '\'][\'img_down\'] = \'' . $pParams['icondown'] . '\';';
		}

		// code javascript pour les captions
		if (!is_null ($pParams['captionup']) && !is_null ($pParams['captiondown'])) {
			$js .= 'taglib_show_div_infos[\'' . $pParams['id'] . '\'][\'caption_up\'] = \'' . str_replace ("'", "\'", $pParams['captionup']) . '\';';
			$js .= 'taglib_show_div_infos[\'' . $pParams['id'] . '\'][\'caption_down\'] = \'' . str_replace ("'", "\'", $pParams['captiondown']) . '\';';
		}

		// préférence utilisateur
		if (isset ($pParams['userpreference'])) {
			$js .= 'taglib_show_div_infos[\'' . $pParams['id'] . '\'][\'userpreference\'] = \'' . $pParams['userpreference'] . '\';';
		}

		// texte alternatif
		if ($this->getParam ('alternateelement') != null) {
			$js .= 'window.taglib_show_div_infos[\'' . $pParams['id'] . '\'][\'alternateelement\'] = \'' . $this->getParam ('alternateelement') . '\'';
		} else if (isset ($pParams['alternate'])) {
			$jsDomReady = 'var taglib_show_div_alternate = new Element (\'div\');';
			$jsDomReady .= 'taglib_show_div_alternate.id = \'' . $pParams['id'] . '_alternate\';';
			$jsDomReady .= 'taglib_show_div_alternate.set (\'html\', \'' . str_replace ("'", "\'", $pParams['alternate']) . '\');';
			if ($pParams['show']) {
				$jsDomReady .= 'taglib_show_div_alternate.setStyle (\'display\', \'none\');';
			}
			$jsDomReady .= 'taglib_show_div_alternate.injectAfter ($ (\'' . $pParams['id'] . '\'));';
			CopixHTMLHeader::addJSDOMReadyCode ($jsDomReady);
			$js .= 'window.taglib_show_div_infos[\'' . $pParams['id'] . '\'][\'alternate\'] = \'' . str_replace ("'", "\'", $pParams['alternate']) . '\';';
		}

		CopixHTMLHeader::addJSCode ($js, 'taglib_show_div_' . $pParams['id']);

		// création du code HTML
		$clicker = $this->getParam ('clicker');
		$toReturn = null;
		// pas de clicker, une caption ou une icone
		if ($clicker == null && $pParams['showicon'] || (!is_null ($pParams['captionup']) && !is_null ($pParams['captiondown']))) {
			if ($pParams['show']) {
				$imgSrc = $pParams['iconup'];
				$caption = $pParams['captionup'];
			} else {
				$imgSrc = $pParams['icondown'];
				$caption = $pParams['captiondown'];
			}
			$toReturn = '<a href="javascript: taglib_invert_show (\'' . $pParams['id'] . '\');">';

			// si on veut afficher un icon
			if ($pParams['showicon']) {
				$toReturn .= '<img id="img_' . $pParams['id'] . '" src="' . $imgSrc . '" style="cursor:pointer" alt="' . $this->getParam ('title') . '" title="' . $this->getParam ('title') . '" />';
			}

			// si on veut afficher un caption
			if (!is_null ($caption)) {
				$toReturn .= ' <span id="caption_' . $pParams['id'] . '">' . $caption . '</span>';
			}
				
			$toReturn .= '</a>';

		// clicker spécifié
		} else if ($clicker != null) {
			CopixHTMLHeader::addJSDOMReadyCode ("$ ('" . $clicker . "').addEvent ('click', taglib_invert_show.pass ('" . $pParams['id'] . "'))");
		}
	  
		return $toReturn;
	}
}