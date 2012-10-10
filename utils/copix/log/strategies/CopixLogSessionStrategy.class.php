<?php
/**
 * @package copix
 * @subpackage log
 * @author Landry Benguigui, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Log dans la session
 * 
 * @package	copix
 * @subpackage log
 */
class CopixLogSessionStrategy extends CopixLogAbstractStrategy {	
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
	public function log ($pProfile, $pType, $pLevel, $pDate, $pMessage, $pExtras) {
		$log = array ('date' => $pDate, 'message' => $pMessage, 'level' => $pLevel, 'type' => $pType);
		//on concatene le tableau info avec le tableau issu du backtrace
		$log = $log + $pExtras;
		CopixSession::push ('copix|log|session|' . $pProfile, $log);
	}
	
	/**
	 * Supprime le contenu du log pour le profil demandé
	 *
	 * @param string $pProfile Nom du profil
	 */
	public function delete ($pProfile) {
		CopixSession::set ('copix|log|session|' . $pProfile, null);
	}

	/**
	 * Retourne les éléments qui correspondent aux paramètres de recherche indiqués
	 *
	 * @param string $pProfile Nom du profil
	 * @param int $pStart Index du premier élément à retourner
	 * @param int $pCount Nombre d'éléments à retourner, null pour tous
	 * @return CopixLogData[]
	 */
	public function get ($pProfile, $pStart = 0, $pCount = null) {
		if (is_array ($profile = CopixSession::get ('copix|log|session|' . $pProfile))) {
	        $arLog = array ();
	        foreach ($profile as $log) {
   				$extras = $log;
	        	unset ($extras['type'], $extras['level'], $extras['date'], $extras['message']);
        		$arLog[] = new CopixLogData ($pProfile, $log['type'], $log['level'], $log['date'], $log['message'], $extras);
	        }
	        $arrayObject = new ArrayObject (array_reverse ($arLog));
	        return $arrayObject->getIterator ();
		}
		return new ArrayObject ();
	}
}