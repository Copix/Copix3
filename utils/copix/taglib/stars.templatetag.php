<?php
/**
 * @package copix
 * @subpackage taglib
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Sélection d'un niveau via des étoiles
 *
 * Paramètres obligatoires :
 *   - name => nom et identifiant du champ caché pour la valeur
 *   - max => valeur maximum (nombre d'étoiles)
 *
 * Paramètres facultatifs :
 *   - value => valeur actuelle (0 par défaut)
 *   - star => resource pour l'image activée (img/tools/star.png par défaut)
 *   - starDisabled => resource pour l'image activée (img/tools/star_disabled.png par défaut)
 *   - captions => tableau de captions pour chaque valeur (ex : array (0 => 'Niveau 0', 1 => 'Niveau 1')
 *   - template => template à utiliser (default|taglib/stars.php)
 * 
 * @package copix
 * @subpackage taglib
 */
class TemplateTagStars extends CopixTemplateTag {
	/**
	 * Demande d'exécution du tag
	 *
	 * @param string $pContent Contenu HTML de base
	 */
	public function process ($pContent = null) {
		$this->assertParams ('name', 'max');
		
		$tpl = new CopixTPL ();
		$tpl->assign ('name', $this->getParam ('name'));
		$value = $this->getParam ('value', 0);
		// si on a passé une valeur nulle, on la définit à 0, pour être sur d'avoir une valeur et non pas null
		if ($value === null) {
			$value = 0;
		}
		$tpl->assign ('value', $value);
		$tpl->assign ('max', $this->getParam ('max', 0));
		$star = _resource ($this->getParam ('star', 'img/tools/star.png'));
		$starDisabled = _resource ($this->getParam ('starDisabled', 'img/tools/star_disabled.png'));
		$tpl->assign ('star', $star);
		$tpl->assign ('starDisabled', $starDisabled);
		$tpl->assign ('captions', $this->getParam ('captions', array ()));

		$js = <<<JS
taglibstars_states = new Array ();
function taglibstars_change (pName, pValue) {
	hasChanged = false;
	x = 1;
	element = $ (pName + '_' + x);
	if ($ (pName + '_caption_0') != null) {
		$ (pName + '_caption_0').setStyle ('display', 'none');
	}
	while (element != null) {
		caption = $ (pName + '_caption_' + x);

		// si on reclick sur une étoile déja activée, on la désactive
		if (x == pValue && taglibstars_states[pName] == pValue && element.src == '$star') {
			element.set ('src', '$starDisabled');
			$ (pName).set ('value', pValue - 1);
			hasChanged = true;
			if (caption != null) {
				caption.setStyle ('display', 'none');
				if ($ (pName + '_caption_' + (x - 1)) != null) {
					$ (pName + '_caption_' + (x - 1)).setStyle ('display', '');
				}
			}

		// étoile inférieure au max => activation
		} else if (x < pValue) {
			element.set ('src', '$star');
			if (caption != null) {
				caption.setStyle ('display', 'none');
			}

		// étoile de la valeur sélectionnée => activation + affichage texte
		} else if (x == pValue) {
		element.set ('src', '$star');
			if (caption != null) {
				caption.setStyle ('display', '');
			}
		
		// étoile supérieure au max => désactivation
		} else {
			element.set ('src', '$starDisabled');
			if (caption != null) {
				caption.setStyle ('display', 'none');
			}
		}
		x++;
		element = $ (pName + '_' + x);
	}
	if (!hasChanged) {
		$ (pName).set ('value', pValue);
	}
	taglibstars_states[pName] = pValue;
}
JS;
		CopixHTMLHeader::addJSCode ($js, 'taglibstars');

		return $tpl->fetch ($this->getParam ('template', 'default|taglib/stars.php'));
	}
}