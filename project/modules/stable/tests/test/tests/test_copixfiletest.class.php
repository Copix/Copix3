<?php
/**
* @package		standard
* @subpackage	test
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	test
 */
class Test_CopixFileTest extends CopixTest {
	/**
	 * Variable pour contenir un volume de donne "important
	 * @var string
	 */
	private $_bigData = '';

	/**
	 * Inclusion de la classe CopixFile, cration des noms de fichiers  tester
	 * mise en place de l'arborescence de test
	 */
	function setUp (){
		if (file_exists (COPIX_CACHE_PATH.'simpletest')){
			rmdir (COPIX_CACHE_PATH.'simpletest');
		}
		 
		//test avec des donnes importantes
		$this->_bigData = '';
		for ($i=0; $i<10000; $i++){
			$this->_bigData .= 'BIG_DATA_FEED';
		}
	}
	 
	/**
	 * On rétabli l'arborescence "normale"
	 */
	function tearDown (){
		if (file_exists (COPIX_CACHE_PATH.'simpletest')){
			rmdir (COPIX_CACHE_PATH.'simpletest');
		}
	}

	/**
	 * Test d'écriture dans COPIX_TEMP_PATH
	 */
	function testBasicReadWrite (){
		//cration du tableau des fichiers dont la prsence est a tester.
		$arFileNames[] = COPIX_TEMP_PATH . 'testfile.test';
		$arFileNames[] = COPIX_CACHE_PATH .'testfile.test';
		$arFileNames[] = COPIX_CACHE_PATH .'html/testfile.test';
		$arFileNames[] = COPIX_CACHE_PATH .'php/testfile.test';
		$arFileNames[] = COPIX_CACHE_PATH .'php/templates/testfile.test';
		$arFileNames[] = COPIX_CACHE_PATH .'html/default/testfile.test';
		$arFileNames[] = COPIX_CACHE_PATH .'html/zones/testfile.test';
		$arFileNames[] = COPIX_CACHE_PATH .'simpletest/testfile.test';		
		$arFileNames[] = COPIX_CACHE_PATH .'tests/'.uniqid ().'/rep/rep2/rep3/test.dat';

		//Supression des fichiers que l'on va tester
		foreach ($arFileNames as $fileName){
			if (file_exists ($fileName)){
				if (!unlink ($fileName)){
					$this->assertTrue (false);//impossible de supprimer le fichier de test, on ne peut pas procder
					//aux tests unitaires.
				}
			}
		}
		 
		//on teste maintenant les fichiers
		foreach ($arFileNames as $fileName){
			$this->_testFile ($fileName);
		}
	}

	/**
	 * Test de création d'un fichier avec une arborescence qui a peu de chance d'exister.
	 */
	function testCreateWithDir (){
		//génération du nom de fichier
		$fileName = COPIX_TEMP_PATH . 'test/'. uniqid ('rep').'/testfile.dat';
		//test d'criture du fichier avec des donnes importantes
		CopixFile::write ($fileName, $this->_bigData);
		$this->assertTrue (file_exists ($fileName));
		 
		//test de lecture du fichier pour s'assurer que tout va bien
		$this->assertTrue (CopixFile::read ($fileName) == $this->_bigData);
		
	}
	
	/**
	 * Test de la fonction de recherche
	 *
	 */
	function testSearch () {
		$dirName = COPIX_TEMP_PATH . 'test/'.uniqid ('rep').'/';
		$subDir  = $dirName.uniqid ('rep').'/';
		$fileName = 'fichierunique.dat';

		CopixFile::write ($dirName.$fileName, $this->_bigData);
		CopixFile::write ($subDir.$fileName, $this->_bigData);

		$this->assertEquals (2, count (CopixFile::search ($fileName, $dirName)));
		$this->assertEquals (1, count (CopixFile::search ($fileName, $dirName, false)));

		$this->assertEquals (2, count (CopixFile::search ('*.dat', $dirName)));
		$this->assertEquals (1, count (CopixFile::search ('*.dat', $dirName, false)));
		
		$this->assertEquals (2, count (CopixFile::search ('fichier*.dat', $dirName)));
		$this->assertEquals (1, count (CopixFile::search ('fichier*.dat', $dirName, false)));
		
		$this->assertEquals (0, count (CopixFile::search (COPIX_TEMP_PATH . 'test/', $dirName, false)));
	}

	/**
	 * Test de créations des répertoire
	 *
	 */
	function testCreateDir () {
		// On crée un répertoire unique avec la fonction write
		$dirName = COPIX_TEMP_PATH . 'test/'. uniqid ('rep').'/';
		$this->assertTrue (CopixFile::write ($dirName, $this->_bigData));

		// On crée un répertoire unique avec la fonction createDir
		$dirName = COPIX_TEMP_PATH . 'test/'. uniqid ('rep').'/';
		CopixFile::createDir ($dirName);
		$this->assertTrue (file_exists ($dirName));
		
	}
	
	/**
	 * Test de cration d'un fichier, d'criture puis de lecture
	 * @param string $pFileName le nom du fichier  tester
	 */
	function _testFile ($pFileName){
		$this->assertFalse (file_exists ($pFileName));
		$this->assertTrue (CopixFile::write ($pFileName, 'abcdef'));
		$this->assertTrue (file_exists ($pFileName));
		$this->assertTrue (CopixFIle::read ($pFileName) === 'abcdef');
		 
		$this->assertTrue (CopixFile::write ($pFileName, $this->_bigData));
		$this->assertTrue (CopixFile::read ($pFileName) === $this->_bigData);
		$this->assertTrue (CopixFile::delete ($pFileName));
		$this->assertFalse (file_exists ($pFileName));
	}

	/**
	 * Test de manipulations sur les noms de fichiers
	 */
	function testExtractions (){
	    $arFileNames  =  array ('/le/chemin/du/fichier.php',
	    'c:\\le\\chemin\\du\\fichier.php',
	    'c:\le\chemin\du\fichier.php',
	    'c:\\le/chemin\\du/fichier.php',
		'\le/chemin\\du/fichier.php',
		'/le\\chemin/du/fichier.php');

		foreach ($arFileNames as $fileName){
			$this->assertEquals (CopixFile::extractFileName ($fileName), 'fichier.php');
			$this->assertEquals (CopixFile::extractFileExt ($fileName), '.php');
		}
		
		$this->assertEquals ('/le/chemin/du/', CopixFile::extractFilePath ('/le\\chemin/du/fichier.php'));
		$this->assertEquals ('c:/le/chemin/du/', CopixFile::extractFilePath ('c:/le\\chemin/du/fichier.php'));
	}
	
	/**
	 * Test de suppression de répertoire
	 *
	 */
	function testRemoveDir() {
	    
	    // On écrit un fichier dans une arborescence
		CopixFile::write (COPIX_TEMP_PATH.'unitfiletest/unitfiletest/unitfiletest/abcdef','tsetset');
	    $this->assertTrue(file_exists(COPIX_TEMP_PATH.'unitfiletest/unitfiletest/unitfiletest/abcdef'));

	    // On efface le répertoire
	    CopixFile::removeDir (COPIX_TEMP_PATH.'unitfiletest');
	    $this->assertTrue(!file_exists(COPIX_TEMP_PATH.'unitfiletest/unitfiletest/unitfiletest/abcdef'));

	    // On écrit un fichier dans une arborescence
	    CopixFile::write (COPIX_TEMP_PATH.'unitfiletest/unitfiletest/unitfiletest/abcdef','tsetset');
	    
	    $this->assertTrue(file_exists(COPIX_TEMP_PATH.'unitfiletest'));
	    // On efface le contenu du répertoire
	    CopixFile::removeDir(COPIX_TEMP_PATH.'unitfiletest/');
	    
	    $this->assertTrue((count(glob(COPIX_TEMP_PATH.'unitfiletest/*'))==0));

	    CopixFile::removeDir(COPIX_TEMP_PATH.'unitfiletest');
	    
	    // On écrit un fichier dans une arborescence
		CopixFile::write (COPIX_TEMP_PATH.'unitfiletest/unitfiletest/unitfiletest/abcdef','tsetset');
		$this->assertTrue(file_exists(COPIX_TEMP_PATH.'unitfiletest'));
	    
		// On efface le répertoire avec la méthode removeFileFromPath
	    $this->assertTrue (CopixFile::removeFileFromPath(COPIX_TEMP_PATH.'unitfiletest/unitfiletest/'));
   	    CopixFile::removeDir(COPIX_TEMP_PATH.'unitfiletest');
	}
	
	function testOtherFunctions (){
		$this->assertEquals (CopixFile::extractFileExt ('file.ext'), '.ext');
		$this->assertEquals (CopixFile::extractFileExt ('filenoext'), null);
		$this->assertEquals (CopixFile::extractFileName ('/chemin/pour/geraldc/'), 'geraldc');
	}
}
?>