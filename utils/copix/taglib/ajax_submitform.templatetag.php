<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Steevan BARBOYON
* @copyright	2000-2006 CopixTeam
* @link			http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Balise de vérification des données d'un formaulaire en ajax
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagAjax_SubmitForm extends CopixTemplateTag {
	
	public function process ($pParams) {
		
		// paramètres requis
		$requestedParameters = array ('form', 'submit', 'divErrors', 'urlVerif', 'urlSubmit');
		foreach ($requestedParameters as $param) {
			if (!isset ($pParams[$param])) {
				throw new CopixTemplateTagException (_i18n ('copix:copix.smarty.badTagParamValue', array ('null', $param, 'ajax_submitform')));
			}
		}
		
		// on a besoin de mootools
		_tag ('mootools');
		
		// code javascript
		$jsCode = '
			window.addEvent(\'domready\', function(){
				$(\'' . $pParams['submit'] . '\').addEvent(\'click\', function(e) {
					$(\'' . $pParams['submit'] . '\').disabled = true;
					$(\'' . $pParams['form'] . '\').action = \'' . _url ($pParams['urlVerif']) . '\';
					new Event (e).stop ();
					$(\'' . $pParams['form'] . '\').send ({
						update: $(\'formErrors\'),
						onComplete: function (response) {
							if (response == \'true\') {
								$(\'' . $pParams['form'] . '\').action = \'' . _url ($pParams['urlSubmit']) . '\';
								$(\'' . $pParams['form'] . '\').submit ();
							} else {
								$(\'' . $pParams['submit'] . '\').disabled = false;
							}
						},
					});
				});
			});';
		
		CopixHTMLHeader::addJSCode($jsCode, 'ajax_submitform_' . $pParams['form']);
	}
}
?>