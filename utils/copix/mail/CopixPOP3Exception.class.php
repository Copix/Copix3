<?php
/**
 * @package copix
 * @subpackage mail
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Exception d'utilisation pour POP3
 *
 * @package copix
 * @subpackage mail
 */
class CopixPOP3Exception extends CopixException {
	/**
	 * Les fonctions imap ne sont pas installées
	 */
	const IMAP_NOT_INSTALLED = 1;
	
	/**
	 * Connexion impossible
	 */
	const CONNECTION_ERROR = 2;
	
	/**
	 * Non connecté à unserveur POP3
	 */
	const NOT_CONNECTED = 4;
	
	/**
	 * Constructeur
	 *
	 * @param CopixPOP3Connection $pPOPConnection Connexion qui a généré une erreur
	 * @param string $pMessage Message d'erreur
	 * @param int $pCode Code d'erreur
	 * @param array $pExtras Informations supplémentaires
	 */
	public function __construct ($pPOPConnection, $pMessage, $pCode = 0, $pExtras = array ()) {
		$extras = array (
			'pop3_host' => $pPOPConnection->getHost (),
			'pop3_port' => $pPOPConnection->getPort (),
			'pop3_user' => $pPOPConnection->getUser (),
			'pop3_password' => $pPOPConnection->getPassword (),
			'pop3_connectionString' => $pPOPConnection->getConnectionString (),
			'pop3_alerts' => $pPOPConnection->getAlerts (),
			'pop3_errors' => $pPOPConnection->getErrors ()
		);
		$extras = array_merge ($extras, $pExtras);
		$pPOPConnection->disconnect ();
		parent::__construct ($pMessage, $pCode, $extras);
	}
}