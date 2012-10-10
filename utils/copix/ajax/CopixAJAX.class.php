<?php
/**
 * @package    copix
 * @subpackage ajax
 * @author     Guillaume Perréal
 * @copyright  CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe d'aide à l'utilisation d'AJAX.
 * 
 * @package copix
 * @subpackage ajax
 */
class CopixAJAX {

	/**
	 * Clef du header HTTP dans le tableau $_SERVER.
	 *
	 */
	const SERVER_HEADER_KEY = 'HTTP_X_COPIX_AJAX_SESSION_ID';

	/**
	 * Nom du namespace de session utilisé.
	 */
	const SESSION_NAMESPACE = 'AJAX_SESSION';

	/**
	 * Identifiant de la session AJAX de la page.
	 *
	 * @var string
	 */
	private static $_sessionId;

	/**
	 * Session AJAX en cours.
	 *
	 * @var CopixAJAXSession
	 */
	private static $_session;

	/**
	 * Teste si on est en train de traiter une requête AJAX.
	 *
	 * @return boolean Vrai si on est en train de traiter une requête AJAX.
	 */
	public static function isAJAXRequest () {
		return isset ($_SERVER[self::SERVER_HEADER_KEY]);
	}

	/**
	 * Retourne l'identifiant de la page courante.
	 *
	 * @return string Identifiant.
	 */
	public static function getSessionId () {
		if (!isset (self::$_sessionId)) {
			if (isset ($_SERVER[self::SERVER_HEADER_KEY])) {
				self::$_sessionId = $_SERVER[self::SERVER_HEADER_KEY];
			} else {
				self::$_sessionId = uniqid ();
			}
		}
		return self::$_sessionId;
	}

	/**
	 * Retourne la session AJAX de la page courante.
	 *
	 * @return CopixAJAXSession
	 */
	public static function getSession () {
		if (!isset (self::$_session)) {

			// Récupère l'identifiant de session
			$sessionId = self::getSessionId ();

			// Récupère la session si elle existe
			self::$_session = CopixSession::get($sessionId, self::SESSION_NAMESPACE);
			
			if(self::$_session) {
				// Met à jour la session
				self::$_session->touch ();
				
			} else {
				self::$_session = new CopixAJAXSession ($sessionId);				
				CopixSession::set($sessionId, self::$_session, self::SESSION_NAMESPACE);
			}

			if (!self::isAJAXRequest ()) {
				// Charge le framework si nécessaire
				CopixHTMLHeader::addJSFramework ();
			
				// Nettoie les sessions qui traînent
				self::destroyStaleSessions ();
			}

			// N'oublie pas de demander un "ping" pour maintenir la session en vie
			CopixHTMLHeader::addJSDOMReadyCode ('Copix.sessionKeepalive ('.intval (CopixAJAXSession::TIMEOUT/3).');', 'CopixAJAXSessionKeepalive');
			
		}
		return self::$_session;
	}

	/**
	 * Détruit les sessions de page inutiles.
	 */
	public static function destroyStaleSessions () {
		if (isset ($_SESSION['COPIX'][self::SESSION_NAMESPACE])) {
			foreach ( (array)$_SESSION['COPIX'][self::SESSION_NAMESPACE] as $key=>$session) {
				if($session instanceof CopixSessionObject) {
					$session = $session->getSessionObject();
				}
				if (!$session instanceof CopixAJAXSession || $session->isStale ()) {
					unset ($_SESSION['COPIX'][self::SESSION_NAMESPACE][$key]);
				}
			}
		}
	}
}