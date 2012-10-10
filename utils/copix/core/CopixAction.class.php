<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Croes Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Objet de description des actions classiques
 * 
 * @package		copix
 * @subpackage	core
 * 
 * <code>
 * // dans un fichier de description
 * $action = new CopixAction ('AGName', 'MethodName'); 
 * </code>
*/
class CopixAction {
	/**
	 * Identifiant de l'objet à utiliser
	 * @var string
	 */
	public $useObj = null;

	/**
	 * Nom de la méthode à utiliser de l'objet
	 * @var string
	 */
	public $useMeth = null;

	/**
	 * Tableau associatif de paramètres qui seront traités par les plugins
	 * @var array
	 */
	public $params = array ();

	/**
	 * Selecteur de fichier à exécuter
	 * @var CopixModuleFileSelector 
	 */
	public $file = null;

	/**
	 * Contructeur
	 *
	 * @param string $useObj Identifiant de l'objet à utiliser. 
	 *   (le nom de l'objet réel peut être complété par des préfixes / suffixes automatiques, 
	 *   cf le coordinateur de module)
	 * @param string $useMeth Identifiant de la méthode de l'objet à utiliser
	 * @param array $params Tableau associatif de paramètres qui seront traités par les plugins
	*/
	public function __construct ($useObj, $useMeth, $params = array ()) {
		$this->useMeth = $useMeth;
		$this->useObj = $useObj;
		$this->params = $params;
		$this->file = new CopixModuleFileSelector ($useObj);
	}
}

/**
 * Pour les redirections automatiques depuis les fichiers de description
 * 
 * @package		copix
 * @subpackage	core
 * 
 * <code>
 * // dans un fichier de description
 * $redirectAction = new CopixActionRedirect (CopixUrl::get ('module|desc|action', array ('param'=>'value')));
 * $redirectAction2 = new CopixActionRedirect ('http://www.copix.org'); 
 * </code>
 */
class CopixActionRedirect extends CopixAction {
	/**
	 * Nom du fichier à utiliser
	 * @var string
	 */
	public $url = null;
	
	/**
	 * Constructeur
	 *
	 * @param string $useUrl Nom du fichier à utiliser
	 * @param array $params Tableau associatif de paramètre qui seront traités par les plugins
	 */
	public function __construct ($useUrl, $params = null) {
		// current, we don't care, there's no use for that.
		$this->file = new CopixModuleFileSelector ('');
		$this->url = $useUrl;
		if ($params != null) {
			$this->params = $params;
		}
	}
}

/**
 * Pour les fichiers statiques (html souvent)
 * 
 * @package		copix
 * @subpackage	core
 * 
 * <code>
 * //dans un fichier de description
 * $static = new CopixActionStatic ('module|file.html');
 * </code>
 */
class CopixActionStatic extends CopixAction {
	/**
	 * Informations supplémentaires
	 * @var array
	 */
	public $more = null;

	/**
	 * Constructeur
	 * 
	 * @param string $UseFile Nom du fichier à utiliser
	 * @param array $more Informations supplémentaires
	 * @param array $params Tableau associatif de paramètres qui seront traités par les plugins
	 */
	public function __construct ($useFile, $more = array (), $params = null) {
		$this->file = new CopixModuleFileSelector ($useFile);
		$this->more = $more;
		$this->useFile = $useFile;
		if ($params != null) {
			$this->params = $params;
		}
	}
}

/**
 * Pour afficher directement des zones dans la zone principale du template du processus standard
 * 
 * @package		copix
 * @subpackage	core
 * 
 * <code>
 * // dans un fichier de description
 * $actionZone = new CopixActionZone ('module|ZoneId', array ('TITLE_PAGE'=>'titre de la page (facultatif)', 'TITLE_BAR'=>'Titre de la barre (facultatif)'));
 * </code>
 */
class CopixActionZone extends CopixAction {
	/**
	 * Titre de la page
	 * @var string
	 */
	public $titlePage = null;

	/**
	 * Titre de la barre
	 * @var string
	 */
	public $titleBar = null;

	/**
	 * Paramètres de la zone
	 * @var array
	 */
	public $zoneParams = array ();

	/**
	 * Identifiant de la zone
	 * @var mixed
	 */
	public $zoneId = null;

	/**
	 * Paramètres supplémentaires
	 * @var array
	 */
	public $more = array ();

	/**
	 * Constructeur
	 * @param mixed $zoneId Identifiant de la zone
	 * @param array $more Paramètres supplémentaires (Ex : TITLE_PAGE, TITLE_BAR, Params)
	 * @param array $params Paramètres pour les plugins
	 */
	public function __construct ($zoneId, $more = array (), $params = null) {
		$this->file = new CopixModuleFileSelector ($zoneId);

		if (isset ($more['TITLE_PAGE'])) {
			$this->titlePage = $more['TITLE_PAGE'];
		}
		if (isset ($more['TITLE_BAR'])) {
			$this->titleBar = $more['TITLE_BAR'];
		}
		if (isset ($more['Params'])) {
			$this->zoneParams = $more['Params'];
		}
		if ($params != null) {
			$this->params = $params;
		}
		$this->more = $more;
		$this->zoneId = $zoneId;
	}
}
?>