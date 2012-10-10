<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://www.copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagInputText extends CopixTemplateTag {
	/**
    * Construction de l'input
    * @param	mixed	$pParams	tableau de paramètre ou clef
    * @param 	mixed	$pContent	
    * @return 	string	l'input fabriqué
    * 	Paramètres recommandés :
    * 		id : identifiant utile pour les labels, le javascript..
    * 		name : nom de l'input utile pour récupérer sa valeur avec php
    * 
    * 	Autres paramètres (liste non exhaustive)
    * 		value : valeur à afficher
    * 		maxlength : nombre de caractères maximals
    * 		size : taille de l'input affiché
    * 		next : zone suivante qui prendra le focus lorsque maxlenght sera atteind
    * 		previous : zone précédente qui prendra le focus lorsque tous les caratères seront effacés
    * 		(ces deux derniers paramètres sont gérés à l'aide de javascript)
    */
    public function process ($pParams) {
        if (!isset ($pParams['id']) && !isset ($pParams['name'])){
	   		throw new CopixTagException ("[CopixTagInput] Missing id or name parameter");
        }

		// gestion de id et name
        if (!isset ($pParams['id'])) {
        	$pParams['id'] = $pParams['name'];  
        } else if (!isset ($pParams['name'])) {
        	$pParams['name'] = $pParams['id'];
        }

		$pParams['type'] = (isset ($pParams['type'])) ? $pParams['type'] : 'text';

		// échappement de la valeur
        if (!empty ($pParams['value'])) {
        	$pParams['value'] = htmlspecialchars ($pParams['value'], ENT_QUOTES);
        }

		// autofocus
        if ((!empty ($pParams['next']) && !empty ($pParams['maxlength'])) || !empty ($pParams['previous'])) {
            CopixHTMLHeader::addJSLink (_resource ('js/taglib/tag_inputtext.js'));
            if (!isset ($pParams['previous'])) {
                $pParams['previous'] = 'null';
            }
            if (!isset ($pParams['next'])) {
                $pParams['next'] = '';
            }
            
            if ($pParams['next'] == 'true') {
                $autofocus = 'autofocus (this, ' . $pParams['maxlength'] . ', event, \'' . $pParams['previous'] . '\');';
            } else {
                $autofocus = 'focusid (this, ' . $pParams['maxlength'] . ', event, \'' . $pParams['next'] . '\', \'' . $pParams['previous'] . '\');';
            }

			if (isset ($pParams['onkeydown'])) {
				$pParams['onkeydown'] .= ' ' . $autofocus;
			} else {
				$pParams['onkeyup'] = 'javascript: ' . $autofocus;
			}
        }

		// recherche des paramètres uniquement HTML pour faciliter le template
		$badParams = array ('next', 'previous', 'help', 'extra', 'disabled', 'showerroricon', 'error');
		$htmlParams = array ();
		foreach ($pParams as $key => $value) {
			if (!in_array ($key, $badParams)) {
				$htmlParams[$key] = $value;
			}
		}

		// disabled peut être passé en boolean, on le gère pour le coté HTML
		if (isset ($pParams['disabled']) && ($pParams['disabled'] === true || $pParams['disabled'] == 'disabled')) {
			$htmlParams['disabled'] = 'disabled';
		}
        
		$tpl = new CopixTPL ();
		$tpl->assign ('params', $pParams);
		$tpl->assign ('htmlParams', $htmlParams);
		return $tpl->fetch ('default|taglib/inputtext.php');
    }
}