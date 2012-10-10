<?php
/**
 * @package		webtools
 * @subpackage 	wiki
 * @author		Brice Favre
 * @copyright	2001-2008 CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Fonction spéciales du wiki (LastChanges)
 * @package		webtools
 * @subpackage 	wiki
 */

class ActionGroupSpecials extends CopixActionGroup {
    
    /**
     * Renvoie des dernières mofications
     * 
     * @return  CopixActionReturn
     */
    function processLastChanges (){
        // Requête de récupération  des informations
	    $nbItems = _request ('nbitems', 20);
	    $startItem = _request ('startitem', 0);
        $arLastChanges = _ioDao ('wikipages')->findBy (_daoSp()-> setLimit ($startItem,$nbItems)
		                                                       -> orderBy (array ('modificationdate_wiki','DESC')));
		                                                       
        // Création de la page Wiki
        $page = new StdClass ();
        $page->original_author = 'root';
        $page->contribs = array();
        $page->last_modifier = 'root';
        $page->content_wiki = '';
		foreach ($arLastChanges as $changes) {
			$page->content_wiki .= '<a href="'._url('wiki||', array ('title' => $changes->title_wiki)).'">'.CopixDateTime::yyyymmddhhiissToDateTime($changes->modificationdate_wiki).' '.$changes->title_wiki.'</a><br/>'."\n";
		}

		// Création de l'objet PPO
	    $ppo = new CopixPPO (); 
		$ppo->page = $page;
		$ppo->canedit = false;
		$ppo->TITLE_PAGE = _i18n ('wiki.specials.lastchanges');
		$ppo->TITLE_BAR = _i18n ('wiki.specials.lastchanges');
		return _arPpo ($ppo, 'show.wikipage.tpl');
    }
}
?>