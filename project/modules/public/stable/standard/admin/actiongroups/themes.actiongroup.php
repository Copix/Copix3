<?php
/**
 * @package standard
 * @subpackage admin 
 * 
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions de sélection du thème graphiques
 * @package standard
 * @subpackage admin 
 */
class ActionGroupThemes extends CopixActionGroup {
	/**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction (){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
	* Affiche la liste des thèmes disponibles
	*/
	function processDefault (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('themes.titlePage.selectTheme');
		$arThemes = CopixTpl::getThemesList ();
		
		$arThemesInfos = array();
		$ppo->arThemes = array ();
		foreach ($arThemes as $theme) {
			$ppo->arThemes[] = CopixTpl::getThemeInformations ($theme);
		}
		
        $ppo->selectedTheme = CopixConfig::get ('defaultThemeId');
		return _arPPO ($ppo, 'theme.list.tpl');
	}

	/**
	* Définition du thème à utiliser
	*/
	function processDoSelectTheme (){
		$theme = _request ('id_ctpt', null, true);
		CopixConfig::set ('defaultThemeId', $theme);
		return _arRedirect (_url ('admin|themes|'));
	}
	
	/**
	 * Fonction qui renvoi l'image associé à un theme
	 * @return retour du contenu d'un fichier image
	 * @param id l'identifiant du theme ou l'on va chercher l'image
	 * @param name nom de l'image à utiliser
	 */
	function processGetImage (){
		$id = _request ('id',null,true);
		$name = _request ('name',null,true);
		if ($id != null && $name != null) {
			echo file_get_contents (COPIX_PROJECT_PATH.'themes/'.$id.'/'.$name);
		}
		exit;
	}
}
?>