<?php
/**
 * @package 	webtools
 * @subpackage 	wiki
 * @author		Croës Gérald
 * @copyright	2001-2008, CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test des échanges de données wiki avec une base de données
 * @package webtools
 * @subpackage wiki
 */
class wiki_WikiDataDBTest extends CopixTest {
	/**
	 * Test de l'ajout et la récupération de contenus
	 *
	 */
	function testContent (){
		$pContent = '==WIKICONTENT==';
		$pNewContent = '==WIKICONTENT MODIFIED==';
		$pRevision = 1;
		$pWikiName = uniqid ('wiki');
		
		// On vérifie qu'un élément a été inséré
		$this->assertEquals (1, _ioClass ('wiki|wikidatadb')->addContent($pWikiName, $pContent));
		// Vérification de son contenu
		$this->assertEquals($pContent, _ioClass ('wiki|wikidatadb')->getContent($pWikiName));
		
		// Vérification de l'ajout d'un nouveau contenu
		$this->assertEquals (1,_ioClass ('wiki|wikidatadb')->addContent($pWikiName, $pNewContent));
		
		// Vérification du nouveau contenu
		$this->assertEquals ($pNewContent, _ioClass ('wiki|wikidatadb')->getContent($pWikiName));
		
		// Vérification de la récupération d'une revision antérieure
		$this->assertEquals ($pContent, _ioClass ('wiki|wikidatadb')->getContent($pWikiName, $pRevision));
	}
}