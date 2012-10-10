<?php
/**
 * @package     copix
 * @subpackage  taglib
 * @author      Steevan BARBOYON
 * @copyright   CopixTeam
 * @link        http://www.copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Balise capable d'afficher une liste d'onglets
 * @package    copix
 * @subpackage taglib
 */
class TemplateTagCopixFormField extends CopixTemplateTag {

	public function process ($pContent = null) {
		$params = $this->getParams ();
		$assign = '';
		if(isset($params['assign'])){
			$assign = $params['assign'];
			unset($params['assign']);
		}
	
		if (!isset($params['form'])) {
			$params['form'] = null;
		}
		
		if ($params['form'] instanceof  CopixForm) {
			$form = $params['form'];
		} else {
			$form = CopixFormFactory::get ($params['form']);
		}
		unset ($params['form']);
		
		
		if (!isset ($params['name'])) {
			throw new CopixTemplateTagException ("[plugin copixform_field] parameter 'name' cannot be empty");
		}
		$name = $params['name'];
		unset ($params['name']);
		
		$toReturn = $form->getRenderer ()->field ($name, $params);
		
		return $toReturn;
	}
}