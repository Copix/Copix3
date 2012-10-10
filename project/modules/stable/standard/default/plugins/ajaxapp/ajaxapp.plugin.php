<?php
/**
 * @package standard
 * @subpackage default
 * @author Salleyron Julien
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Change le code HTML à afficher si on fait un appel ajax
 *
 * @package standard
 * @subpackage default
 */
class PluginAjaxApp extends CopixPlugin implements ICopixBeforeDisplayPlugin, ICopixCatchExceptionsPlugin, ICopixAfterProcessPlugin {
	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Application AJAX';
	}

	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Remplace document.location.href, href, type="submit", type="image" par des appels javascripts.';
	}

	/**
	 * Appelée avant l'affichage
	 *
	 * @param string $toDisplay HTML à afficher
	 */
	public function beforeDisplay (&$toDisplay) {
		if (CopixAJAX::isAJAXRequest() && _request('instanceId') != null) {
			list ($zone, $url, $request) = CopixAJAX::getSession ()->get(_request('instanceId'));
			$toDisplay = preg_replace ('/document.location.href=\"([^\"]*)\"/','$(\''.$zone.'\').fireEvent (\'goto\', \'\1\');"', $toDisplay); 
			$toDisplay = preg_replace ('/document.location.href=\'([^\']*)\'/','$(\''.$zone.'\').fireEvent (\'goto\', \'\1\');"', $toDisplay);
			$toDisplay = preg_replace ('/href=\"([^\"]*)\"/', 'href="javascript:void (null);" onclick="$(\''.$zone.'\').fireEvent (\'goto\', \'\1\');"', $toDisplay);
			$toDisplay = preg_replace ('/href=\'([^\']*)\'/', 'href="javascript:void (null);" onclick="$(\''.$zone.'\').fireEvent (\'goto\', \'\1\');"', $toDisplay);
			$toDisplay = str_replace ('type="submit"', 'type="button" onclick="$(\''.$zone.'\').fireEvent (\'submit\', this);"', $toDisplay);
			$toDisplay = str_replace ('type="image"', 'type="button" onclick="$(\''.$zone.'\').fireEvent (\'submit\', this);"', $toDisplay);
			$toDisplay = str_replace ('type=\'image\'', 'type="button" onclick="$(\''.$zone.'\').fireEvent (\'submit\', this);"', $toDisplay);
		}
	}

	/**
	 * Catch les exceptions
	 *
	 * @param Exception $e Exception
	 * @return boolean
	 */
	public function catchExceptions ($e) {
		if (CopixAJAX::isAJAXRequest() && _request('instanceId') != null) {
			if ($e instanceof CopixCredentialException) {
				list ($zone, $url, $request) = CopixAJAX::getSession ()->get(_request('instanceId'));
				echo '<script>$("'.$zone.'").fireEvent("goto", "'.CopixUrl::get ('auth||', array ('noCredential' => 1, 'auth_url_return' => _url ('#'), 'instanceId'=>_request('instanceId'))).'");</script>';
				exit;
			}
		}
		return false;
	}

	/**
	 * Appelée après l'action
	 *
	 * @param CopixActionReturn $actionreturn Retourn de l'action
	 */
	public function afterProcess ($actionreturn) {
		if (CopixAJAX::isAJAXRequest() && _request('instanceId') != null) {
			if ($actionreturn->code == CopixActionReturn::REDIRECT) {
				list ($zone, $url, $request) = CopixAJAX::getSession ()->get(_request('instanceId'));
				echo '<script>$("'.$zone.'").fireEvent("goto", "'._url ($actionreturn->data, array ('instanceId'=>_request('instanceId'))).'");</script>';
				exit;
			}
		}
	}
}