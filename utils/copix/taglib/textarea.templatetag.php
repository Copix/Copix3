<?php
/**
* @package copix
* @subpackage taglib
* @author Steevan BARBOYON
* @copyright CopixTeam
* @link http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Génération d'un textarea
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagTextArea extends CopixTemplateTag {
	/**
	 * Construction du code HTML
	 *
	 * @param array $pParams Paramètres
	 * @return string
	 */
   public function process ($pContent = null) {
	   $pParams = $this->getParams ();
	   
		//input check
		if (empty ($pParams['name'])) {
			throw new CopixTemplateTagException ("[plugin textarea] parameter 'name' cannot be empty");
		}

		$pParams['id'] = $this->getParam ('id', $pParams['name']);
		$pParams['value'] = $this->getParam ('value', $pContent);
		$pParams['extra'] = $this->getParam ('extra', null);
		$pParams['error'] = $this->getParam ('error', false);
		$pParams['showerroricon'] = $this->getParam ('showerroricon', true);

		// recherche des paramètres uniquement HTML pour faciliter le template
		$badParams = array ('help', 'extra', 'disabled', 'showerroricon', 'error');
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
		return $tpl->fetch ('default|taglib/textarea.php');
	}
}