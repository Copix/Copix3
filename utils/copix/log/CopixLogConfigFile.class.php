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
 * Gère le fichier de configuration des logs
 *
 * @package copix
 * @subpackage logs
 */
class CopixLogConfigFile extends CopixConfigFile {
	/**
	 * Variables définies dans le fichier de config
	 * 
	 * @var array
	 */
	private static $_vars = array ('_log_profiles');

	/**
	 * Retourne le chemin vers le fichier de configuration
	 *
	 * @return string
	 */
	public static function getPath () {
		return COPIX_VAR_PATH . 'config/log_profiles.conf.php';
	}

	/**
	 * Supprime le profil de log
	 *
	 * @param string $pName Nom
	 */
	public static function delete ($pName) {
		self::_deleteArray (self::getPath (), '_log_profiles', $pName, self::$_vars, true);
	}

	/**
	 * Ajoute le profil de log
	 *
	 * @param array $pProfil
	 * @return mixed
	 */
	public static function add ($pProfile) {
		return self::_editProfile ($pProfile, true);
	}

	/**
	 * Modifie le profil de log
	 *
	 * @param array $pProfile Informations sur le profil
	 * @return mixed
	 */
	public static function edit ($pProfile) {
		return self::_editProfile ($pProfile, false);
	}

	/**
	 * Modifie ou ajoute u nprofil de log
	 *
	 * @param array $pProfile Profil
	 * @param boolean $pIsNew Indique si c'est un nouveau profil
	 * @return mixed
	 */
	private static function _editProfile ($pProfile, $pIsNew) {
		if (($result = _validator ('CopixLogProfileValidator', array ('isNew' => $pIsNew))->check ($pProfile)) instanceof CopixErrorObject) {
			return $result;
		}
		return self::_editArray (self::getPath (), '_log_profiles', $pProfile['name'], $pProfile, self::$_vars);
	}

	/**
	 * Active un profil de log
	 *
	 * @param string $pName Nom
	 */
	public static function enable ($pName) {
		$profile = self::get ($pName);
		$profile['enabled'] = true;
		self::edit ($profile);
	}

	/**
	 * Désactive un profil de log
	 *
	 * @param string $pName Nom
	 */
	public static function disable ($pName) {
		$profile = self::get ($pName);
		$profile['enabled'] = false;
		self::edit ($profile);
	}

	/**
	 * Retourne un profil le profil de log demandé
	 *
	 * @param string $pName Nom du profil
	 * @return array
	 */
	public static function get ($pName) {
		return self::_getArrayValue (self::getPath (), '_log_profiles', $pName, null, true);
	}

	/**
	 * Retourne la liste des profils de log
	 *
	 * @return array
	 */
	public static function getList () {
		return self::_getValue (self::getPath (), '_log_profiles', array ());
	}
}