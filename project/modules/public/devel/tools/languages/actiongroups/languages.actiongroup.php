<?php
/**
 * @package		languages
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		tools 
 * @subpackage	menu 
 */
class ActionGroupLanguages extends CopixActionGroup {
	
	public function beforeAction ($actionName){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		_class ('functions')->updateLockedFiles ();
	}
	
    /**
     * Affiche les informations sur les traductions possibles
     */
    public function processDefault () {
    	$ppo = new CopixPPO ();
    	$ppo->TITLE_PAGE = _i18n ('global.title.languagesList');
    	
    	// verifications des erreurs passées dans l'url
	    if (_request ('error') !== null) {
	    	$ppo->arErrors = array (_i18n ('global.error.' . _request ('error'))); 
	    } else {
	    	$ppo->arErrors = array (); 		
	    }
    	    	
    	// recherche des fichiers .properties dans les modules installés
       	$ppo->installedModules = _class ('functions')->getFiles (true, false);
    	
    	// recherche des fichiers .properties dans les modules non installés
    	$ppo->uninstalledModules = _class ('functions')->getFiles (false, true);
    	
    	return _arPPO ($ppo, 'languages.list.tpl');
    }
}
?>