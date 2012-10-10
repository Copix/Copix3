<?php
/**
 * @package copix
 * @subpackage log
 * @author Landry Benguigui
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Envoi un log par e-mail
 * 
 * @package copix
 * @subpackage log
 */
class CopixLogEmailStrategy extends CopixLogAbstractStrategy {
	/**
	 * Remplace des variables par leur valeurs
	 * 
	 * @param string $pPattern Chaine avec les variables à remplacer
	 * @param array $pVars Tableau associatif clef = nom de varialbe ; valeur = valeur de la variable
	 * @return string 
	 */
	private function _replaceVars ($pPattern, $pVars) {
		$toReturn = $pPattern;
		foreach ($pVars as $clef => $value) {
			$toReturn = str_replace ('{$' . $clef . '}', $value, $toReturn);
		}
		
		return $toReturn;
	}
	
	/**
	 * Envoi le log par e-mail
	 *
	 * @param string $pProfile Nom du profil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param string $pDate Date et heure du log, format YmdHis
	 * @param string $pMessage Message à loger
	 * @param array $pExtras Informations supplémentaires
	 */
	public function log ($pProfile, $pType, $pLevel, $pDate, $pMessage, $pArExtra){
		$profile = CopixConfig::instance ()->copixlog_getProfile ($pProfile);
		
		$year = substr ($pDate, 0, 4);
		$month = substr ($pDate, 4, 2);
		$day = substr ($pDate, 6, 2);
		$hour = substr ($pDate, 8, 2);
		$min = substr ($pDate, 10, 2);
		$sec = substr ($pDate, 12, 2);
		$level = CopixLog::getLevel ($pLevel);
		$pattern = _i18n ('copix:log.email.bodyHTML');
		$vars = array (
			'MESSAGE' => $pMessage,
			'PROFIL' => $pProfile,
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
		$body = utf8_decode ($this->_replaceVars ($pattern, $vars));
		$subject = str_replace ('%MESSAGE%', substr (utf8_decode ($pMessage), 0, 80), utf8_decode ($this->_getConfig ($profile, 'mailSubject', '[CopixLogEmailStrategy] %MESSAGE%')));
		
		// envoi du / des mail(s)
		$destinataires = explode (';', $this->_getConfig ($profile, 'to', array ()));
		foreach ($destinataires as $destinataire) {
			$mail = new CopixHTMLEmail ($destinataire, null, null, $subject, $body);
			$mail->send ($this->_getConfig ($profile, 'from'), $this->_getConfig ($profile, 'fromname'));
		}
	}
	
	/**
	 * Indique si on peut écrire dans le profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isWritable ($pProfile) {
		return CopixConfig::get ('default|mailEnabled');
	}
	
	/**
	 * Indique si on peut lire le contenu du profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return boolean
	 */
	public function isReadable ($pProfile) {
		return false;
	}

	/**
	 * Retourne l'HTML pour la configuration des informations spécifiques à la stratégie
	 *
	 * @param string $pProfile Nom du profil
	 * @return string
	 */
	public function getConfigEditor ($pProfile) {
		$tpl = new CopixTPL ();
		$tpl->assign ('to', $this->_getConfig ($pProfile, 'to'));
		$tpl->assign ('mailSubject', $this->_getConfig ($pProfile, 'mailSubject', '[CopixLogEmailStrategy] %MESSAGE%'));
		$tpl->assign ('from', $this->_getConfig ($pProfile, 'from', CopixConfig::get ('default|mailFrom')));
		$tpl->assign ('fromname', $this->_getConfig ($pProfile, 'fromname', CopixConfig::get ('default|mailFromName')));
		return $tpl->fetch ('copix:templates/logs/emailstrategyeditor.php');
	}

	/**
	 * Indique si la configuration de la stratégie est valide
	 *
	 * @param string $pProfile Nom du profil
	 * @param array $pConfig Configuration
	 * @return mixed
	 */
	public function isValidConfig ($pProfile, $pConfig) {
		$errors = array ();

		// adresses du destinataire et de l'expéditeur
		$destinataire = (array_key_exists ('to', $pConfig)) ? $pConfig['to'] : null;
		$expediteur = (array_key_exists ('from', $pConfig)) ? $pConfig['from'] : null;
		$emails = array_merge (explode (';', $destinataire), explode (';', $expediteur));
		try {
			foreach ($emails as $toValid) {
				CopixFormatter::getMail ($toValid);
			}
		} catch (Exception $e) {
			$errors[] = $e->getMessage ();
		}

		// nom de l'expéditeur
		if (!isset ($pConfig['fromname']) || strlen ($pConfig['fromname']) == 0) {
			$errors[] = _i18n ('copix:log.error.emptyFromName');
		}

		// titre du mail
		if (!isset ($pConfig['mailSubject']) || strlen ($pConfig['mailSubject']) == 0) {
			$errors[] = _i18n ('copix:log.error.emptyMailSubject');
		}

		return (count ($errors) > 0) ? new CopixErrorObject ($errors) : true;
	}
}