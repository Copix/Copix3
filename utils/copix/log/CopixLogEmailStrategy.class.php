<?php
/**
 * @package   copix
 * @subpackage log
 * @author    Landry Benguigui
 * @copyright 2001-2006 CopixTeam
 * @link      http://copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Log en base de données
 * 
 * @package   copix
 * @subpackage log
 */
class CopixLogEmailStrategy implements ICopixLogStrategy {
	
	/**
	 * Remplace des variables par leur valeurs
	 * 
	 * @param string $pattern Chaine avec les variables à remplacer
	 * @param array $vars Tableau associatif clef = nom de varialbe ; valeur = valeur de la variable
	 * @return string 
	 */
	private function _replaceVars ($pattern, $vars) {
		$toReturn = $pattern;
		foreach ($vars as $clef => $value) {
			$toReturn = str_replace ('%' . $clef . '%', $value, $toReturn);
		}
		
		return $toReturn;
	}
	
	/**
	 * Envoi le log par mail
	 *
	 * @param string $pProfil Profile qui renvoie ce log
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log
	 * @param int $pDate Date
	 * @param string $pMessage Message à logger
	 * @param array $pArExtra Tableaux de paramètres
	 */
	public function log ($pProfil, $pType, $pLevel, $pDate, $pMessage, $pArExtra){
		$profile = CopixConfig::instance ()->copixlog_getProfile ($pProfil);
		
		$year  = substr ($pDate, 0, 4);
		$month = substr ($pDate, 4, 2);
		$day   = substr ($pDate, 6, 2);
		$hour  = substr ($pDate, 8, 2);
		$min   = substr ($pDate, 10, 2);
		$sec   = substr ($pDate, 12, 2);
		
		$levels = CopixLog::getLevels ();
		$level = $levels[$pLevel]->caption;
		
		$pattern = _i18n ('copix:log.email.bodyHTML');
		$vars = array (
			'MESSAGE' => $pMessage,
			'PROFIL' => $pProfil,
			'TYPE' => $pType,
			'LEVEL' => $level,
			'YEAR' => $year,
			'MONTH' => $month,
			'DAY' => $day,
			'HOUR' => $hour,
			'MIN' => $min,
			'SEC' => $sec,
			'EXTRAS' => CopixDebug::getDump ($pArExtra)
		);		
		$body = utf8_decode ($this->_replaceVars($pattern, $vars));
		$subject = str_replace ('%MSG%', substr (utf8_decode ($pMessage), 0, 80), utf8_decode (CopixConfig::get ('default|logMailTitle')));
		
		// envoi du / des mail(s)
		$destinataires = explode (';', $profile['email']);
		foreach ($destinataires as $destinataire) {
			$mail = new CopixHTMLEmail ($destinataire, null, null, $subject, $body);
			$mail->send ();
		}
	}
	
	/**
	 * Supprimer tous les log de ce profil
	 * @param	string	$pProfil	Le nom du profil dont on souhaite supprimer les contenus
	 * @return int	nombre de logs supprimés 
	 */
	public function deleteProfile ($pProfil){		
		return 0;
	}
	
	/**
	 * Retourne les logs sous forme d'itérateur
	 */
	public function getLog ($pProfil){
		throw new Exception (_i18n ('copix:log.email.getLog')); 
	}
}
?>