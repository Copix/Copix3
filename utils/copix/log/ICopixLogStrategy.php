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
 * Interface de base pour la gestion des logs
 *
 * @package copix
 * @subpackage log
 */
interface ICopixLogStrategy {
	/**
	 * Effectue un log
	 *
	 * @param string $pProfile Nom du profil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param string $pDate Date et heure du log, format YmdHis
	 * @param string $pMessage Message à loger
	 * @param array $pExtras Informations supplémentaires
	 */
	public function log ($pProfile, $pType, $pLevel, $pDate, $pMessage, $pExtras);

	/**
	 * Retourne les éléments qui correspondent aux paramètres de recherche indiqués
	 *
	 * @param string $pProfile Nom du profil
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner, null pour tous
	 * @return string[]
	 */
	public function get ($pProfile, $pStart = 0, $pCount = null);

	/**
	 * Supprime le contenu du log pour le profil demandé
	 *
	 * @param string $pProfile Nom du profil
	 */
	public function delete ($pProfile);

	/**
	 * Retourne le nombre d'éléments
	 *
	 * @param string $pProfile Nom du profil
	 * @return int
	 */
	public function count ($pProfile);

	/**
	 * Retourne la taille prise par les éléments logés
	 *
	 * @param string $pProfile Nom du profil
	 * @return int
	 */
	public function getSize ($pProfile);

	/**
	 * Indique si on peut lire le contenu du profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isReadable ($pProfile);

	/**
	 * Indique si on peut écrire dans le profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isWritable ($pProfile);

	/**
	 * Retourne l'HTML pour la configuration des informations spécifiques à la stratégie
	 *
	 * @param string $pProfile Nom du profil
	 * @return string
	 */
	public function getConfigEditor ($pProfile);

	/**
	 * Indique si la configuration de la stratégie est valide
	 *
	 * @param string $pProfile Nom du profil
	 * @param array $pConfig Configuration
	 * @return mixed
	 */
	public function isValidConfig ($pProfile, $pConfig);
}