<?php
/**
* @package 		copix
* @subpackage	profile
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Le profil de l'utilisateur courant
* @package copix
* @subpackage profile
*/
class CopixUserProfile {
	/**
    * Le profil de l'utilisateur courant
    * @var object CopixProfile
    */
	private $_profile = null;
	
	/**
	 * Singleton
	 */
	static private $_instance = false;

	/**
    * Singleton
    * 
    * On va gèrer le fait que l'on puisse conserver les informations  déjà chargées en session
    * 
    * @return CopixUserProfile
    */
	public static function instance () {
		if (self::$_instance === false) {
			$user = CopixPluginRegistry::get ('auth|auth')->getUser ();

			if ((intval (CopixConfig::get ('profile|keepProfileInSession')) === 1) 
			     && isset ($_SESSION['PLUGIN_SESSION_PROFILE']) && 
			        ($_SESSION['PLUGIN_SESSION_PROFILE']->user == $user->login)){
				self::$_instance = $_SESSION['PLUGIN_SESSION_PROFILE']->profile;
			}else{
				self::$_instance  = new CopixUserProfile ($user->login);
				if ((intval (CopixConfig::get ('profile|keepProfileInSession')) === 1)){
			        $_SESSION['PLUGIN_SESSION_PROFILE'] = new StdClass ();
					$_SESSION['PLUGIN_SESSION_PROFILE']->profile = self::$_instance;
					$_SESSION['PLUGIN_SESSION_PROFILE']->user = $user->login;
				}
			}
		}
		return self::$_instance;
	}

	/**
    * Récupèration du login de l'utilisateur
    * @return string the user login.
    */
 	public static function getLogin (){
		return CopixPluginRegistry::get ('auth|auth')->getUser ()->login;
	}

	/**
    * Indique le droit dont dispose une personne sur un élément donné.
    * 
    * <code>
    *  //Récupère la valeur max du droit document situé dans un sous domaine
    *  $value = CopixUserProfile::valueOfIn ('document', 'modules|copixheadings|');
    *  //ira chercher dans tous les sous domaine de modules|copixheadings la valeur du droit
    *  //document 
    * </code>
    * 
    * @param	string	$capability	L'élément sur lequel on veut tester la valeur du droit
    * @param	string	$pBasePath	le chemin à partir duquel nous allons rechercher la valeur du droit
    * @return int valeur du droit
    */
	public static function valueOfIn ($pCapability, $pBasePath){
		return self::instance ()->_profile->valueIn ($pCapability, $pBasePath);
	}

	/**
    * Récupère la valeur d'un droit de l'utilisateur courant
    * 
    * <code>
    *   //on regarde quel est la valeur du droit "forum" pour l'utilisaeur courant
    *   $value = CopixUserProfile::valueOf ('forum');
    * 
    *   //on regarde quel est la valeur du droit "nouvelles" dans le domaine "rubrique|sport" pour 
    *   //l'utilisateur courant
    *   $value = CopixUserProfile::valueOf ('nouvelles', 'rubrique|sport');
    * </code>
    * 
    * @param	string	$pPath		le chemin dans lequel on veut tester la valeur du droit sur $capability
    * @param	string	$pCapability	l'élément sur lequel on veut tester la valeur du droit
    * @return int 
    */
	public static function valueOf ($pCapability, $pPath = null){
		return self::instance ()->_profile->valueOf ($pCapability, $pPath);
	}

	/**
    * Indique si notre utilisateur appartient ou non à un groupe donné
    * 
    * <code>
    *    //On regarde si l'utilisateur courant appartient au groupe trouvé
    *    if (CopixUserProfile::belongsTo ($groupAdministrateurId)){
    *       //c'est bon, on peut aller ici
    *    }
    * </code>
    * 
    * @param	string	$group	l'identifiant du groupe
    * @return	boolean
    */
	public static function belongsTo ($pGroup) {
		return self::instance ()->_profile->belongsTo ($pGroup);
	}

	/**
    * Récupère la liste des groupes auxquels appartient l'utilisateur
    * @return array of CopixGroups
    */
	public static function getGroups (){
		return self::instance ()->_profile->getGroups ();
	}

	/**
    * Constructeur. 
    * Chargement du profil utilisateur.
    * @param	string	$pLogin	le login de l'utilisateur courant
    */
	private function __construct ($pLogin) {
		$this->_profile = new CopixProfile ($pLogin);
	}
}
?>