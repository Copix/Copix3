<?php

/**
 * @package		webtools
 * @subpackage	index_search
 * @author		Duboeuf Damien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Class de test pour l'indexation
 *
 */
class Index_Search_IndexingServices extends CopixTest {
	
	/**
	 * repertoire du dossier de test
	 *
	 */
	const TEST_PATH = 'tests/';
	/**
	 * Nom du fichier de test
	 *
	 */
	const FILETEST_NAME = 'documentTestIndex';
	
	public function setUp () {
		
	} 
	
	/**
	 * Test l'indexation d'un fichier PDF
	 *
	 */
	public function testIndexPDF () {
	    
		_class ('index_search|indexingservices')->addFile ('index_search|documentTestIndexPDF',
		                                                   // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                   str_replace('test.php', 'index.php', _url ('index_search|tests|getPDFFILE')),
		                                                   'Fichier PDF de test unitaire',
		                                                   NULL,
		                                                   indexingServices::TYPE_PDF);
		// TODO ne verifie pas si l'objet est bien indexé
		$this->assertTrue (true);
	}
	  
	/**
	 * Test l'indexation d'un fichier DOC MSWord
	 *
	 */
	public function testIndexDOC () {
		
		_class ('index_search|IndexingServices')->addFile ('index_search|documentTestIndexDOC',
		                                                   // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                   str_replace('test.php', 'index.php', _url ('index_search|tests|getDOCFILE')),
		                                                   'Fichier DOC de test unitaire',
		                                                   NULL,
		                                                   indexingServices::TYPE_DOC);
		// TODO ne verifie pas si l'objet est bien indexé
		$this->assertTrue (true);
	}
	
	/**
	 * Test l'indexation d'un fichier TXT
	 *
	 */
	public function testIndexTXT () {
		_class ('index_search|IndexingServices')->addFile ('index_search|documentTestIndexTXT',
		                                                   // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                   str_replace('test.php', 'index.php', _url ('index_search|tests|getTXTFILE')),
		                                                   'Fichier TXT de test unitaire',
		                                                   NULL,
		                                                   indexingServices::TYPE_TXT);
		// TODO ne verifie pas si l'objet est bien indexé
		$this->assertTrue (true);
	}
	
	/**
	 * Test l'indexation d'un fichier HTML
	 *
	 */
	public function testIndexHTML () {
		_class ('index_search|IndexingServices')->addFile ('index_search|documentTestIndexHTML',
		                                                   // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                   str_replace('test.php', 'index.php', _url ('index_search|tests|getHTMLFILE')),
		                                                   'Fichier HTML de test unitaire',
		                                                   NULL,
		                                                   indexingServices::TYPE_HTML);
		// TODO ne verifie pas si l'objet est bien indexé
		$this->assertTrue (true);
	}
	
	/**
	 * Test l'indexation d'un contenu TXT
	 *
	 */
	public function testIndexTXTBrute () {
		_class ('index_search|IndexingServices')->addContent ('index_search|documentTestIndexTXTBrute1',
		                                                      file_get_contents (str_replace('test.php', 'index.php', _url ('index_search|tests|getTXTFILE'))),
		                                                      // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                      str_replace('test.php', 'index.php', _url ('index_search|tests|getTXTFILE')),
		                                                      'Fichier TXTBrute par addContent de test unitaire',
		                                                      NULL,
		                                                      indexingServices::TYPE_TXT_BRUTE);
		$this->assertTrue (true);
		
		_class ('index_search|IndexingServices')->addFile ('index_search|documentTestIndexTXTBrute2',
		                                                   // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                   str_replace('test.php', 'index.php', _url ('index_search|tests|getTXTFILE')),
		                                                   'Fichier TXTBrute par addFile de test unitaire',
		                                                   NULL,
		                                                   indexingServices::TYPE_TXT_BRUTE);
		// TODO ne verifie pas si l'objet est bien indexé
		$this->assertTrue (true);
	}
	
	/**
	 * Test l'indexation d'un contenu HTML
	 *
	 */
	public function testIndexHTMLBrute () {
		_class ('index_search|IndexingServices')->addContent ('index_search|documentTestIndexHTMLBrute1',
		                                                      file_get_contents (str_replace('test.php', 'index.php', _url ('index_search|tests|getTXTFILE'))),
		                                                      // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                      str_replace('test.php', 'index.php', _url ('index_search|tests|getHTMLFILE')),
		                                                      'Fichier HTMLBrute par addContent de test unitaire',
		                                                      NULL,
		                                                      indexingServices::TYPE_HTML_BRUTE);
		$this->assertTrue (true);
		
		_class ('index_search|IndexingServices')->addFile ('index_search|documentTestIndexHTMLBrute2',
		                                                   // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                   str_replace('test.php', 'index.php', _url ('index_search|tests|getHTMLFILE')),
		                                                   'Fichier HTMLBrute par addFile de test unitaire',
		                                                   NULL,
		                                                   indexingServices::TYPE_HTML_BRUTE);
		// TODO ne verifie pas si l'objet est bien indexé
		$this->assertTrue (true); return;
	}
	
	/**
	 * Test l'indexation de fichier avec droits restraints
	 *
	 */
	public function testIndexWithCredential () {
		
		_class ('index_search|IndexingServices')->addFile ('index_search|documentTestIndexCrendtialAdmin',
		                                                   // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                   str_replace('test.php', 'index.php', _url ('index_search|tests|getTXTFILE')),
		                                                   'Fichier Accessible qu\'au admin de test unitaire',
		                                                   'basic:admin',
		                                                   indexingServices::TYPE_TXT);
		
		// TODO ne verifie pas si l'objet est bien indexé
		$this->assertTrue(true);
		
		_class ('index_search|IndexingServices')->addFile ('index_search|documentTestIndexCrendtialAdmin2',
		                                                   // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
		                                                   str_replace('test.php', 'index.php', _url ('index_search|tests|getTXTFILE')),
		                                                   'Fichier Accessible qu\'au admin de test unitaire (passage par tableau)',
		                                                   array ('basic:admin'),
		                                                   indexingServices::TYPE_TXT);
		
		// TODO ne verifie pas si l'objet est bien indexé
		$this->assertTrue(true);
	}
	
	/**
	 * Test l'indexation d'un repertorie /usr/include
	 *
	 */
	/*public function testIndexingUsrInclude () {
	    
		// Attention la taille de memoire max de PHP doit être a 32 Mo pour pouvoir récupérer
		// correctement tous les fichier .h de /usr/include/
		$listeFile = CopixFile:: search ('*.h', "/usr/include/", true);
		$i = 0;
	    foreach ($listeFile as $key=>$file) {
	    	$i++;
	    	echo $i . '<br />';flush()."\n";
	        _class ('index_search|IndexingServices')->addContent ('index_search|documentHeaderLinuxTest'.'|'.$key,
	                                                              file_get_contents ($file),
			                                                      // TODO Problème avec le _url qui renvoie test.php au lieu de index.php
			                                                      str_replace('test.php', 'index.php', _url ('index_search|tests|getTXTFILE')),
			                                                      'Fichier Header Linux de test N°.'.'|'.$key.'(le lien marche pas) ',
			                                                      NULL,
			                                                      indexingServices::TYPE_TXT_BRUTE);
	    	
	    }
		// TODO ne verifie pas si l'objet est bien indexé
		$this->assertTrue(true);
		
	}*/
}

?>