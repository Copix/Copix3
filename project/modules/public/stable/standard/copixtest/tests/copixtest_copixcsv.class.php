<?php
/**
 * @package standard
 * @subpackage copixtest
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Tests sur la classe CopixCSV
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_CopixCSV extends CopixTest {
	public function setUp (){
		CopixContext::push ('copixtest');
	}
    
	public function tearDown (){
		CopixContext::pop ();
	}
	
	/**
	 * Test de création d'un fichier CSV
	 */
	public function testCreateCsvFile (){
		//on test que le fichier est bien créé
		$csvFile = new CopixCSV (COPIX_TEMP_PATH.'file.csv');
        for ($i = 0 ; $i < 4; $i ++) {
            $csvFile->addLine(array("test", "test", "test"));
        }
        $this->assertTrue (file_exists (COPIX_TEMP_PATH.'file.csv'));
	}
    
	/**
	 * Parcours du fichier csv
	 */
    public function testCsvParse (){
		//on test que le fichier est rempli et est correctement parcouru
		$csvFile = new CopixCsv  (COPIX_TEMP_PATH.'file.csv');
        $itCsv = $csvFile->getIterator ();
        $nbLine = $itCsv->count();
        
        $i=0;
        foreach ($itCsv as $line) {
            $i++;
        }

        $this->assertEquals ($i, $nbLine);

	}
	
	/**
	 * Test des CSV en en-tête
	 */
	public function testCsvHeaded (){
	    $csvFile = new CopixCsv  (COPIX_TEMP_PATH.'file.csv');
	    $itCsv = $csvFile->getIterator (true);
	    $arInfo = $itCsv->current();
	    $arKeys = array_keys ($arInfo); 
	    $this->assertEquals ($arKeys[0], 'test');
	    $arInfo = $itCsv->next();
	    $arKeys = array_keys ($arInfo); 
	    $this->assertEquals ($arKeys[0], 'test');
	    
	}
	
}
?>