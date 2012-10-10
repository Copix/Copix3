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
 * Contient les infos de retour des actions d'un coordinateur de page *
 * Cet objet permet à CopixController de savoir quoi faire après une action.
 * Il contient un code retour, et des données associées à ce code retour.
 * Dans les traitements par défaut, ce code est un entier.
 *
 * <code>
 * $tpl= new CopixTpl ();
 * //...
 * return new CopixActionReturn (CopixActionReturn::PPO, $ppo, 'html');
 * </code>
 * @package		copix
 * @subpackage	core
 */
class CopixActionReturn {
	/**
	 * Code de retour. vaut une des constantes COPIX_AR_*
	 * 
	 * @var int
	 */
	public $code = null;

	/**
	 * Paramètre pour le traitement du retour. Sa nature dépend du code retour
	 * 
	 * @var mixed
	 */
	public $data = null;

	/**
	 * Paramètre supplémentaire pour le traitement du retour. Sa nature et sa présence dépend du code retour
	 * 
	 * @var mixed
	 */
	public $more = null;

	/**
	 * Affichage dans le template principal
	 */
	const DISPLAY = 1;
	
	/**
	 * Une erreur est survenue
	 */
	const ERROR = 2;
	
	/**
	 * Redirection à une url
	 */
	const REDIRECT = 3;

	/**
	 * Rien ne sera fait de plus
	 */
	const NONE = 6;
	
	/**
	 * Affichage dans un autre template que le template principal défini par défaut 
	 */
	const DISPLAY_IN = 7;
	
	/**
	 * Téléchargement d'un contenu à partir d'un fichier
	 */
	const FILE = 8;
	
	/**
	 * Affichage d'un contenu binaire à partir d'un fichier
	 */
	const CONTENT = 9;
	
	/**
	 * Code HTTP
	 */
	const HTTPCODE = 10;
	
	/**
	 * Système "MVC"
	 */
	const PPO = 11;

	/**
	 * Contruction et initialisation du descripteur.
	 * 
	 * @param int $pCode Code (Constante de cette même classe)
	 * @param mixed $pData Paramètres (template / url / ...)
	 * @param mixed $pMore Paramètres supplémentaires
	 */
	public function __construct ($pCode, $pData = null, $pMore = null) {
		$this->data = $pData;
		$this->more = $pMore;
		$this->code = $pCode;
	}
}