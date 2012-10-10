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
class ActionGroupAdminFlux extends CopixActionGroup {
	
	public function beforeAction ($actionName){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

    /**
     * Liste les fichiers sauvegardés
     */
    public function processDefault () {
    	$rss = _class ('syndication|syndication');
    	
    	$rss->title = 'Mon flux RSS 2.0';
    	$rss->link = 'http://www.google.fr';
    	$rss->description = 'Oula, bin on dirait un flux RSS 2.0 hein éèàù';
    	
    	for ($boucle = 0; $boucle < 5; $boucle++) {
    		$item = $rss->addItem ();
    		$item->title = 'test ' . $boucle;
    		$item->link = 'http://www.google.fr' . $boucle;
    		$item->description = 'bin, on dirait bien une description, hein';
    	}
    	
    	//$rss->compress = true;
    	return $rss->arDirectContent ('Mon flux RSS');
    }
}
?>