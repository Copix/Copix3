<?php
/**
* @package copix
* @subpackage utils
* @author Steevan BARBOYON
* @copyright CopixTeam
* @link http://copix.org
* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Gestion d'un fichier de configuration
 *
 * @package copix
 * @subpackage utils
 */
abstract class CopixConfigFile implements ICopixConfigFile {
	/**
	 * Indique si le fichier de configuration a les droits d'écriture
	 *
	 * @return boolean
	 */
	protected static function _isWritable ($pPath) {
		if (!file_exists ($pPath)) {
			try {
				CopixFile::write ($pPath, '<?php ?>');
			} catch (Exception $e) {
				return false;
			}
		}
		return is_writable ($pPath);
	}

	/**
	 * Ecrit les variables dans le fichier de config
	 *
	 * @param array $pVars Variables à écrire dans le fichier
	 */
	protected static function _write ($pPath, $pVars) {
		if (!self::_isWritable ($pPath)) {
			throw new CopixConfigFileException (_i18n ('copix:configfile.error.notWritable', $pPath));
		}
		$generator = new CopixPHPGenerator ();
		$declarations = array ();
		foreach ($pVars as $var => $value) {
			$declarations[] = $generator->getVariableDeclaration ('$' . $var, $value);
		}
		$content = $generator->getPHPTags (implode ($generator->getEndLine (), $declarations));
		if (CopixFile::write ($pPath, $content) === false) {
			throw new CopixConfigFileException (_i18n ('copix:configfile.error.write', $pPath));
		}
	}

	/**
	 * Retourne la valeur d'une variable du fichier de config
	 *
	 * @param string $pPath Chemin vers le fichier de config
	 * @param string $pVar Nom de la variable (sans le $)
	 * @param mixed $pDefaultValue Valeur par défaut si la variable n'existe pas
	 * @return mixed
	 */
	protected static function _getValue ($pPath, $pVar, $pDefaultValue = null, $pExceptionIfNotSet = false) {
		if (file_exists ($pPath)) {
			require ($pPath);
			if (!isset (${$pVar}) && $pExceptionIfNotSet) {
				throw new CopixConfigFileException (_i18n ('copix:configfile.error.varNotFound', array ($pVar, $pPath)));
			}
			return (isset (${$pVar})) ? ${$pVar} : $pDefaultValue;
		}
		return $pDefaultValue;
	}

	/**
	 * Retourne la valeur d'une variable du fichier de config
	 *
	 * @param string $pPath Chemin vers le fichier de config
	 * @param string $pVar Nom de la variable (sans le $)
	 * @param string $pKey Nom de la clef
	 * @param mixed $pDefaultValue Valeur par défaut si la variable n'existe pas
	 * @param boolean $pExceptionIfNotSet Indique si on veut lever une exception si la clef n'existe pas
	 * @return mixed
	 */
	protected static function _getArrayValue ($pPath, $pVar, $pKey, $pDefaultValue = null, $pExceptionIfNotSet = false) {
		$values = self::_getValue ($pPath, $pVar, $pDefaultValue, $pExceptionIfNotSet);
		if (!is_array ($values) || !array_key_exists ($pKey, $values)) {
			throw new CopixConfigFileException (_i18n ('copix:configfile.error.keyNotFound', array ($pKey, $pVar, $pPath)));
		}
		return $values[$pKey];
	}

	/**
	 * Retourne les variables et les valeurs du fichier $pPath
	 *
	 * @param string $pPath Chemin vers le fichier de config
	 * @param array $pVars Nom des variables
	 */
	private static function _getVars ($pPath, $pVars) {
		if (file_exists ($pPath)) {
			require ($pPath);
		}
		$toReturn = array ();
		foreach ($pVars as $var) {
			$toReturn[$var] = (isset (${$var})) ? ${$var} : null;
		}
		return $toReturn;
	}

	/**
	 * Modifie ou ajoute une valeur pour la variable indiquée
	 *
	 * @param string $pPath Chemin vers le fichier de config
	 * @param string $pVar Nom de la variable (sans le $)
	 * @param mixed $pValue Valeur
	 * @param array $pVars Nom des variables disponibles dans le fichier
	 */
	protected static function _edit ($pPath, $pVar, $pValue, $pVars) {
		$vars = self::_getVars ($pPath, $pVars);
		$vars[$pVar] = $pValue;
		self::_write ($pPath, $vars);
	}

	/**
	 * Modifie ou ajoute une valeur pour la variable indiquée
	 *
	 * @param string $pPath Chemin vers le fichier de config
	 * @param string $pVar Nom de la variable (sans le $)
	 * @param string $pKey Clef du tableau dont on veut modifier la valeur
	 * @param mixed $pValue Valeur
	 * @param array $pVars Nom des variables disponibles dans le fichier
	 */
	protected static function _editArray ($pPath, $pVar, $pKey, $pValue, $pVars) {
		$vars = self::_getVars ($pPath, $pVars);
		if (!is_array ($vars[$pVar])) {
			$vars[$pVar] = array ();
		}
		$vars[$pVar][$pKey] = $pValue;
		self::_write ($pPath, $vars);
		return true;
	}

	/**
	 * Supprime la clef du tableau indiqué
	 *
	 * @param string $pPath Chemin vers le fichier de config
	 * @param string $pVar Nom de la variable (sans le $)
	 * @param string $pKey Clef du tableau dont on veut supprimer la valeur
	 * @param array $pVars Nom des variables disponibles dans le fichier
	 * @param boolean $pExceptionIfNotSet Indique si on veut lever une exception si la clef n'existe pas
	 */
	protected static function _deleteArray ($pPath, $pVar, $pKey, $pVars, $pExceptionIfNotSet = false) {
		$vars = self::_getVars ($pPath, $pVars);
		if (is_array ($vars[$pVar]) && array_key_exists ($pKey, $vars[$pVar])) {
			unset ($vars[$pVar][$pKey]);
			self::_write ($pPath, $vars);
		} else if ($pExceptionIfNotSet) {
			throw new CopixConfigFileException (_i18n ('copix:configfile.error.keyNotFound', array ($pKey, $pVar, $pPath)));
		}
	}
}