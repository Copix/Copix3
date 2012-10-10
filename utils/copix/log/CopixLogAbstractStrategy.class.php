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
 * Facilite la création d'une stratégie de log en implémentant les méthodes de ICopixLogStrategy avec quelques méthodes déja codées
 *
 * @package copix
 * @subpackage log
 */
abstract class CopixLogAbstractStrategy implements ICopixLogStrategy {
	/**
	 * Retourne les éléments qui correspondent aux paramètres de recherche indiqués
	 *
	 * @param string $pProfile Nom du profil
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner, null pour tous
	 * @return string[]
	 */
	public function get ($pProfile, $pStart = 0, $pCount = null) {
		throw new CopixLogException (_i18n ('copix:log.error.cantGet', CopixLogException::NOT_READABLE));
	}
	
	/**
	 * Supprime le contenu du log pour le profil demandé
	 *
	 * @param string $pProfile Nom du profil
	 */
	public function delete ($pProfile) {
		throw new CopixLogException (_i18n ('copix:log.error.cantDelete', CopixLogException::NOT_WRITABLE));
	}
	
	/**
	 * Retourne le nombre d'éléments
	 *
	 * @param string $pProfile Nom du profil
	 * @return int
	 */
	public function count ($pProfile) {
		return count ($this->get ($pProfile));
	}
	
	/**
	 * Indique si on peut lire le contenu du profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isReadable ($pProfile) {
		return true;
	}
	
	/**
	 * Indique si on peut écrire dans le profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isWritable ($pProfile) {
		return true;
	}
	
	/**
	 * Retourne la taille en octets prise par les logs
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function getSize ($pProfile) {
		if (!$this->isReadable ($pProfile)) {
			throw new CopixLogException (_i18n ('copix:log.error.cantGet', CopixLogException::NOT_READABLE));
		}
	}
	
	/**
	 * Retourne l'HTML contenant des champs de config spécifiques à cette stratégie
	 *
	 * @param string $pProfile Nom du profil
	 * @return string
	 */
	public function getConfigEditor ($pProfile) {
		return null;
	}

	/**
	 * Indique si la configuration de la stratégie est valide
	 *
	 * @param string $pProfile Nom du profil
	 * @param array $pConfig Configuration
	 * @return mixed
	 */
	public function isValidConfig ($pProfile, $pConfig) {
		return true;
	}

	/**
	 * Retourne forcément une chaine de caractère, quel que soit le type de $pValue ($pValue passé dans var_export dans certains cas)
	 *
	 * @param mixed $pValue Valeur
	 * @return string
	 */
	protected function _getStringValue ($pValue) {
		return (is_string ($pValue) || is_numeric ($pValue)) ? $pValue : var_export ($pValue, true);
	}

	/**
	 * Retourne la valeur de la configuration demandée
	 *
	 * @param array $pProfile Ijnformations sur le profil
	 * @param string $pName Nom de la configuration
	 * @param mixed $pDefault Valeur par défaut
	 */
	protected function _getConfig ($pProfile, $pName, $pDefault = null) {
		return (isset ($pProfile['config'][$pName])) ? $pProfile['config'][$pName] : $pDefault;
	}
}