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
 * Affiche 3 input text pour une saisie de date
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagDate extends CopixTemplateTag {
	/**
    * Construction de l'input
	 *
	 * @param array $pParams
	 * @return string
	 */
    public function process ($pParams) {
        if (!isset ($pParams['id']) && !isset ($pParams['name'])) {
	   		throw new CopixTagException ("[CopixTagInput] Missing name parameter");
        }

		$pParams['error'] = $this->getParam ('error', false);
		$pParams['next'] = $this->getParam ('next', null);

		$pParams['value_year'] = null;
		$pParams['value_month'] = null;
		$pParams['value_day'] = null;
        if (isset ($pParams['value']) && !empty ($pParams['value'])) {
			if (strlen ($pParams['value']) != 8) {
				throw new CopixTagException ('[TemplateTagDate] La date "' . $pParams['value'] . '" doit être au format YYYYMMDD.');
			}
        	$pParams['value_year'] = substr ($pParams['value'], 0, 4);
			$pParams['value_month'] = substr ($pParams['value'], 4, 2);
			$pParams['value_day'] = substr ($pParams['value'], -2);
        }
        
		$tpl = new CopixTPL ();
		$tpl->assign ('params', $pParams);
		return $tpl->fetch ('default|taglib/date.php');
    }
}