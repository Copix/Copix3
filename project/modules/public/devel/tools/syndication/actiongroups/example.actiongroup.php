<?php
/**
 * @package		rssevent
 * @author		Steevan BARBOYON
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		rssevent 
 * @subpackage	adminflux 
 */
class ActionGroupExample extends CopixActionGroup {
	
	public function beforeAction ($actionName){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

    /**
     * Liste les fichiers sauvegardés
     */
    public function processDefault () {
    	
    	$rss = _class ('syndication|syndication');
    	
    	$rss->title = 'Mon flux RSS 2.0';
    	$rss->link->uri = 'http://www.google.fr';
    	$rss->description = 'Oula, bin on dirait un flux RSS 2.0 hein éèàù';
    	
    	for ($boucle = 0; $boucle < 3; $boucle++) {
    		$author = $rss->addAuthor ();
    		$author->name = 'Steevan ' . $boucle;
    		$author->email = 's.barboyon' . $boucle . '@alptis.org';
    		$author->webSite->uri = 'http://www.opensofts' . $boucle . '.org';
    	}
    	
    	for ($boucle = 0; $boucle < 5; $boucle++) {
    		$item = $rss->addItem ();
    		$item->title = 'test ' . $boucle;
    		$item->link->uri = 'http://www.google' . $boucle . '.fr';
    		$item->content->value = 'bin, on dirait bien une description, hein';
    		$item->pubDate = mktime ();
    		
    		for ($boucle2 = 0; $boucle2 < 3; $boucle2++) {
	    		$author = $item->addAuthor ();
	    		$author->name = 'Steevan ' . $boucle2;
	    		$author->email = 's.barboyon_author' . $boucle2 . '@alptis.org';
	    		$author->webSite->uri = 'http://www.opens' . $boucle2 . '.org';
    		}
    	}
    	
    	//$rss->compress = true;
    	$rss->writeToFile (COPIX_TEMP_PATH . 'syndication_atom_1_0.xml', Syndication::ATOM_1_0);
    	$rss->writeToFile (COPIX_TEMP_PATH . 'syndication_rss_2_0.xml', Syndication::RSS_2_0);
    	$rss->writeToFile (COPIX_TEMP_PATH . 'syndication_rss_1_0.xml', Syndication::RSS_1_0);
    	$ppo = new CopixPPO ();
    	$ppo->TITLE_PAGE = 'test';
    	$ppo->content = $rss->getContent (Syndication::RSS_1_0);
		return _arDirectPPO ($ppo, 'content.tpl');
    	//return _arContent ($rss->getContent (Syndication::ATOM_1_0));
    }
}
?>