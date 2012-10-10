<?php
/**
 * @package standard
 * @subpackage default
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Vérifie les pré-requis avant d'afficher le site
 *
 * @package standard
 * @subpackage default
 */
class PluginRequirements extends CopixPlugin implements ICopixBeforeProcessPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Vérifie les pré-requis (type et version du navigateur, etc)';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Vérifie les pré-requis (type et version du navigateur, etc)';
	}

	/**
	 * Indique si le niveau du navigateur est assez élevé pour le site
	 *
	 * @return boolean
	 */
	private function _testUserAgentLevel () {
		// user agents récupérés sur http://fr.wikipedia.org/wiki/User-Agent
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos ($userAgent, 'MSIE') != false) {
			$ie = array ('MSIE 1', 'MSIE 2', 'MSIE 3', 'MSIE 5.0', 'MSIE 5.2', 'MSIE 5.5', 'MSIE 6', 'MSIE 7', 'MSIE 8', 'MSIE 9');
			foreach (array_reverse ($ie, true) as $level => $version) {
				if (strpos ($userAgent, $version) !== false) {
					if ($level < $this->getConfig ()->getMinIEVersion ()) {
						$posMSIE = strpos ($userAgent, 'MSIE');
						$version = substr ($userAgent, $posMSIE + 5, strpos ($userAgent, ';', $posMSIE) - $posMSIE - 5);
						return 'La version de votre navigateur (Internet Explorer ' . $version . ') est trop ancienne, et ne vous permettra pas de profiter de toutes les fonctionnalités du site.';
					}
					return true;
				}
			}
		}

		return true;
	}

	/**
	 * CallBack pour array_filter
	 *
	 * @param string $pItem
	 * @return boolean
	 */
	public function testURL ($pItem) {
		return strpos (CopixUrl::getRequestedUrl (), $pItem) !== false;
	}

	/**
	 * Appelée avant le début de la session
	 *
	 * @param string $pAction Nom de l'action
	 */
	public function beforeProcess (&$pAction) {
		if (CopixSession::get ('tested', 'requirements') || CopixRequest::isAJAX ()) {
			return null;
		}

		$config = $this->getConfig ();
		$urls = $config->getURLS ();
		if (
			(count ($urls) == 0 && strtolower (_request ('module') . '|' . _request ('group') . '|' . _request ('action')) != 'default|default|requirements')
			|| count (array_filter ($urls, array ($this, 'testURL'))) > 0
		) {
			CopixSession::set ('tested', true, 'requirements');
			$errors = array ();
			if ($config->getTestUserAgent ()) {
				$test = $this->_testUserAgentLevel ();
				if ($test !== true) {
					$errors['ie'] = $test;
				}
			}

			if (count ($errors) > 0) {
				CopixSession::set ('errors', $errors, 'requirements');
				CopixSession::set ('theme', $config->getErrorTheme (), 'requirements');
				$params = array ();
				if ($config->getAllowRedirect ()) {
					$params['redirect'] = CopixUrl::getRequestedUrl ();
				}
				header ('Location: ' . _url ('||requirements', $params));
				exit ();
			}
		}
	}
}