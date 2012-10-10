<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Réponse apportée par un handler sur une demande de type get
 * @package copix
 * @subpackage core
 */
class CopixUrlHandlerGetResponse {
	public $path;
	public $vars;
	public $scriptName;
	public $basePath;
	public $protocol;
	public $externUrl;
	public $anchor;

	/**
	 * Construction de la réponse avec les valeurs par défaut de l'url courante
	 */
	function __construct (){
		$this->vars = null;
		$this->path = null;
		$this->scriptName = CopixUrl::getRequestedScriptName ();
		$this->basePath = CopixUrl::getRequestedBasePath ();
		$this->protocol = CopixUrl::getRequestedProtocol ();
		$this->externUrl = null;
		$this->anchor = null;
	}
}