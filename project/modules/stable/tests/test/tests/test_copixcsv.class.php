<?php
/**
 * @package standard
 * @subpackage test
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Tests sur la classe CopixCSV
 * @package standard
 * @subpackage test
 */
class Test_CopixCSV extends CopixTest {
	public function setUp (){
        if (file_exists (COPIX_TEMP_PATH.'file.csv')) {
            unlink (COPIX_TEMP_PATH.'file.csv');
        }
        //création d'un fichier CSV simple 
        CopixFile::write (COPIX_TEMP_PATH.'file.csv',
"Titre 0,Titre 1,Titre 2
test 0 0,test 1 0,test 2 0
test 0 1,test 1 1,test 2 1
test 0 2,test 1 2,test 2 2
test 0 3,test 1 3,test 2 3");

        CopixFile::write (COPIX_TEMP_PATH.'file_2.csv',
"Titre 0,Titre 1,Titre 2
test 0 0,test 1 0,test 2 0
test 0 1,test 1 1,test 2 1
test 0 2,test 1 2,test 2 2
test 0 3,test 1 3,test 2 3
");
        CopixFile::write (COPIX_TEMP_PATH.'file_empty.csv', "");

        CopixFile::write (COPIX_TEMP_PATH.'file_empty_headed.csv', 
"Titre 0,Titre 1,Titre 2");
	}
    
	public function tearDown (){
		CopixContext::pop ();
	}
	
	public function testCreateFile (){
	}
	
	/**
	 * Test de création d'un fichier CSV
	 */
	public function testCountCsvFile (){
		$this->_testCount (COPIX_TEMP_PATH.'file.csv');
		$this->_testCount (COPIX_TEMP_PATH.'file_2.csv');
	}
	
	private function _testCount ($pFilename){
		$csvFile = new CopixCSV ($pFilename);

        // On test le nombre de lignes (en considérant la première comme le titre)
        $itCsv = $csvFile->getIterator (true);
        $this->assertEquals ($itCsv->count (), 4);

        // On test le nombre de lignes (en considérant la première comme étant des données)
        $itCsv = $csvFile->getIterator ();
        $this->assertEquals ($itCsv->count (), 5);        
	}
	
	public function testEmpty (){
		$csvFile = new CopixCSV (COPIX_TEMP_PATH.'file_empty.csv');
		$this->assertEquals ($csvFile->getIterator ()->count (), 0);
		$this->assertEquals (count ($csvFile->getIterator ()), 0);

		$this->assertEquals (count ($csvFile->getIterator (true)), 0);
		$this->assertEquals (count ($csvFile->getIterator (true)), 0);

		$csvFile = new CopixCSV (COPIX_TEMP_PATH.'file_empty_headed.csv');
		$this->assertEquals ($csvFile->getIterator (true)->count (), 0);
		$this->assertEquals (count ($csvFile->getIterator (true)), 0);
	}

	/**
	 * Parcours du fichier csv
	 */
    public function testForeachIterator (){
    	$this->_testForeach (COPIX_TEMP_PATH.'file.csv');
    	$this->_testForeach (COPIX_TEMP_PATH.'file_2.csv');
    }
    
    private function _testForeach ($pFileName){
    	$csvFile = new CopixCSV ($pFileName);
    	$i = 0;
    	$toReturn = array ();
    	foreach ($csvFile->getIterator (true) as $key=>$element){
    		$this->assertEquals ("test 0 $i", $element['Titre 0']);
    		$this->assertEquals ("test 1 $i", $element['Titre 1']);
    		$this->assertEquals ("test 2 $i", $element['Titre 2']);
    		$this->assertEquals ($i, $key);
    		$i++;
    		$toReturn[] = $element;
    	}
    	$this->assertEquals ($i, $csvFile->getIterator (true)->count ());

    	$i = 0;
    	foreach ($csvFile->getIterator () as $key=>$element){
    		if ($i === 0){
				$this->assertEquals ("Titre 0", $element[0]);
				$this->assertEquals ("Titre 1", $element[1]);
				$this->assertEquals ("Titre 2", $element[2]);    			    			
    		}else{
    			$j = $i-1;
	    		$this->assertEquals ("test 0 $j", $element[0]);
	    		$this->assertEquals ("test 1 $j", $element[1]);
	    		$this->assertEquals ("test 2 $j", $element[2]);
	    		$this->assertEquals ($i, $key);
    		}
    		$i++;    		
    	}
    	$this->assertEquals ($i, $csvFile->getIterator ()->count ());
	}
	
	/**
	 * Test des CSV avec en-tête
	 */
	public function testCsvHeaded (){
	    $csvFile = new CopixCsv  (COPIX_TEMP_PATH.'file.csv');
	    $itCsv = $csvFile->getIterator (CopixCSV::HEADED);
	    $arInfo = $itCsv->current ();

	    $arKeys = array_keys ($arInfo);
	    $this->assertEquals ($arKeys[0], 'Titre 0');
	    $this->assertEquals ($arInfo['Titre 0'], 'test 0 0');
	}
}
?>