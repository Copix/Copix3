<?php
/**
 * @package copix
 * @subpackage log
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Validateur d'un profil de log
 *
 * @package copix
 * @subpackage log
 */
class CopixLogProfileValidator extends CopixAbstractValidator {
	/**
	 * Valide les données d'un profil de log
	 *
	 * @param array $pProfile Profil à valider
	 */
	protected function _validate ($pProfile) {
		// vérification de la structure de $pProfile
		$nameOk = (array_key_exists ('name', $pProfile));
		$enabledOk = (array_key_exists ('enabled', $pProfile) && is_bool ($pProfile['enabled']));
		$handleOk = (array_key_exists ('handle', $pProfile) && (is_array ($pProfile['handle']) || $pProfile['handle'] == 'all'));
		$strategyOk = (array_key_exists ('strategy', $pProfile));
		$levelOk = (array_key_exists ('level', $pProfile) && is_array ($pProfile['level']));
		if (!is_array ($pProfile) || !$nameOk || !$enabledOk || !$handleOk || !$strategyOk || !$levelOk) {
			$extras = array ('profile' => $pProfile);
			throw new CopixLogException (_i18n ('copix:log.error.invalidProfileStructure'), CopixLogException::INVALID_PROFILE_STRUCTURE, $extras);
		}
		
		$errors = array ();

		// nom
		if ($pProfile['name'] == null || !preg_match ('/^([a-zA-Z0-9_]{3,})$/', $pProfile['name'], $match)) {
			$errors[] = _i18n ('copix:log.profileValidator.invalidName');
		} else if ($this->getParam ('isNew') && in_array ($pProfile['name'], array_keys (CopixLogConfigFile::getList ()))) {
			//$errors[] = _i18n ('copix:log.profileValidator.profileExists', $pProfile['name']);
		}

		// types de messages
		if (is_array ($pProfile['handle']) && count ($pProfile['handle']) == 0) {
			$errors[] = _i18n ('copix:log.profileValidator.noHandle');
		}

		// stratégie
		if (!in_array ($pProfile['strategy'], array_keys (CopixLog::getStrategies ()))) {
			$errors[] = _i18n ('copix:log.profileValidator.noStrategy');
		}

		// niveau
		if (count ($pProfile['level']) == 0) {
			$errors[] = _i18n ('copix:log.profileValidator.noLevel');
		} else {
			$levelsPPO = CopixLog::getLevels ();
			$levels = array ();
			foreach ($levelsPPO as $level) {
				$levels[] = $level->id;
			}
			foreach ($pProfile['level'] as $level) {
				if (!in_array ($level, $levels)) {
					$errors[] = _i18n ('copix:log.profileValidator.invalidLevel');
				}
			}
		}

		// configuration de la stratégie
		$config = (array_key_exists ('config', $pProfile)) ? $pProfile['config'] : array ();
		if (($result = CopixLog::isValidConfig ($pProfile, $config)) instanceof CopixErrorObject) {
			$errors = array_merge ($errors, $result->asArray ());
		}
		
		return (count ($errors) == 0) ? true : $errors;
	}
}