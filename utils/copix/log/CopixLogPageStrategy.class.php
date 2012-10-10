<?php
/**
 * @package		copix
 * @subpackage	log
 * @author		Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Log affiché directement via un echo, et sauvegardés en internet pour pouvoir les relire
 * 
 * @package		copix
 * @subpackage	log
 */
class CopixLogPageStrategy implements ICopixLogStrategy {
	/**
	 * Tableau des logs affichés
	 *
	 * @var array
	 */
	static private $_logs = array ();
	
	/**
	 * Affiche directement le log via un echo
	 *
	 * @param string $pProfil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau du log
	 * @param string $pDate Date au format YYYYMMDDHHMMSS
	 * @param string $pMessage Message à logger
	 * @param array $pArExtra Informations supplémentaires
	 */
	public function log ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pArExtra) {
		echo '<div style="background-color: white; border: solid red 1px; padding: 5px; margin: 5px;">';
		echo '<font color="#969696">' . _i18n (
			'copix:log.page.title',
			array (
				'CopixLogPageStrategy',
				CopixLog::getLevel ($pLevel),
				$pType
			)
		) . '<br />';
		if (isset ($pArExtra['file'])) {
			echo _i18n ('copix:log.page.file', $pArExtra['file']);
			if (isset ($pArExtra['line'])) {
				echo ' | ' . _i18n ('copix:log.page.line', $pArExtra['line']);
			}
			echo '<br />';
		}
		echo '</font>';
		echo '<b>' . $pMessage . '</b>';
		echo '</div>';
	}
	
	/**
	 * Supprime le contenu du log pour le profil demandé
	 * 
	 * @param string $pProfil Nom du profil dont on souhaite supprimer lee contenu
	 * @throws CopixLogException
	 */
	public function deleteProfile ($pProfil) {
		throw new CopixLogException (_i18n ('copix:log.error.cantDelete')); 		
	}
	
	/**
	 * Retourne les logs sous forme d'itérateur
	 * 
	 * @param string $pProfil Profil dont on veut retourner les logs
	 * @throws CopixLogException
	 */
	public function getLog ($pProfil) {
		throw new CopixLogException (_i18n ('copix:log.error.cantGet'));
	}
}
?>