<?php
/**
 * @package webtools
 * @subpackage feed
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package webtools
 * @subpackage feed
 */
class ActionGroupExample extends CopixActionGroup {
	/**
	 * Executée avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	protected function _beforeAction ($pActionName) {
		_currentUser ()->assertCredential ('basic:admin');
	}

    /**
     * Exemple de génération de flux
	 *
	 * @return CopixActionReturn
     */
    public function processDefault () {
		// génération du flux
		// la génération sera la même quel que soit le format de sortie
    	$rss = new Syndication ();

    	$rss->title = 'Mon flux RSS';
    	$rss->link->uri = 'http://www.copix.org';
    	$rss->description = 'Oula, bin on dirait un flux éèàù';

    	for ($boucle = 0; $boucle < 3; $boucle++) {
    		$author = $rss->addAuthor ();
    		$author->name = 'Steevan ' . $boucle;
    		$author->email = 's.barboyon' . $boucle . '@truc.fr';
    		$author->webSite->uri = 'http://www.copix' . $boucle . '.org';
    	}

    	for ($boucle = 0; $boucle < 5; $boucle++) {
    		$item = $rss->addItem ();
    		$item->title = 'test ' . $boucle;
    		$item->link->uri = 'http://forum.copix' . $boucle . '.fr';
    		$item->content->value = 'bin, on dirait bien une description';
    		$item->pubDate = time ();

    		for ($boucle2 = 0; $boucle2 < 3; $boucle2++) {
	    		$author = $item->addAuthor ();
	    		$author->name = 'Steevan ' . $boucle2;
	    		$author->email = 's.barboyon_author' . $boucle2 . '@bidule.com';
	    		$author->webSite->uri = 'http://doc.copix' . $boucle2 . '.org';
    		}
    	}
    	
    	// si on ne veut pas de tabulations / retours à la ligne
    	//$rss->compress = true;

		// retour du flux en XML
    	$ppo = new CopixPPO (array ('MAIN' => $rss->getContent (Syndication::RSS_2_0)));
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
    }
}