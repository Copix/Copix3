<?php
/**
 * @package copix
 * @subpackage config
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Valide une valeur de configuration pour un module
 *
 * @package copix
 * @subpackage config
 */
class CopixConfigModuleValidator extends CopixAbstractValidator {
	/**
	 * Validation
	 *
	 * @param mixed $pValue Valeur à valider
	 * @return mixed
	 */
	protected function _validate ($pValue) {
		$errors = array ();

		$fullName = $this->requireParam ('name');
		list ($module, $name) = explode ('|', $fullName);
		$params = CopixConfig::getParams ($module);
		foreach ($params as $param) {
			if ($param['Name'] == $name) {
				// type int
				if ($param['Type'] == 'int') {
					// chiffre invalide
					if ($pValue != null && ((string)intval ($pValue) <> (string)$pValue)) {
						$errors[$fullName] = $param['Caption'] . ' : ' . _i18n ('copix:copix.error.parameter.typeInt');
					// chiffre trop petit
					} else if ($param['MinValue'] != null && $param['MinValue'] > intval ($pValue)) {
						$errors[$fullName] = $param['Caption'] . ' : ' . _i18n ('copix:copix.error.parameter.typeIntMin', $param['MinValue']);
					// chiffre trop grand
					} else if ($param['MaxValue'] != null && $param['MaxValue'] < intval ($pValue)) {
						$errors[$fullName] = $param['Caption'] . ' : ' . _i18n ('copix:copix.error.parameter.typeIntMax', $param['MaxValue']);
					}

				// type email
				} else if ($param['Type'] == 'email') {
					// email invalide
					try {
						CopixFormatter::getMail ($pValue);
					} catch (CopixException $e) {
						$errors[$fullName] = $param['Caption'] . ' : ' . _i18n ('copix:copix.error.parameter.typeEmail');
					}

					// e-mail trop long
					if (!is_null ($param['MaxLength']) && strlen ($pValue) > $param['MaxLength']) {
						$errors[$fullName] = $param['Caption'] . ' : ' . _i18n ('copix:copix.error.parameter.typeEmailMax', $param['MaxLength']);
					}

				// type text
				} else if ($param['Type'] == 'text') {
					// texte trop long
					if (!is_null ($param['MaxLength']) && strlen ($pValue) > $param['MaxLength']) {
						$errors[$fullName] = $param['Caption'] . ' : ' . _i18n ('copix:copix.error.parameter.typeTextMax', $param['MaxLength']);
					}
				}
				break;
			}
		}

		return (count ($errors) > 0) ? $errors : true;
	}
}