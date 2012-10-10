<?php
/**
 * @package		standard
 * @subpackage	test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		standard
 * @subpackage	test
 */
class Test_CopixServices extends CopixTest {
	public function setup (){
		CopixDB::getConnection ()->doQuery ('delete from testmain');
		CopixDB::getConnection ()->doQuery ("INSERT INTO testmain ( type_test , titre_test , description_test , date_test )
VALUES ('1', 'test1', 'test1desc', '20060202');");
		CopixDB::getConnection ()->doQuery ("INSERT INTO testmain ( type_test , titre_test , description_test , date_test )
VALUES ('2', 'test2', 'test2desc', '20050202');");
		CopixDB::getConnection ()->doQuery ("INSERT INTO testmain ( type_test , titre_test , description_test , date_test )
VALUES ('3', 'test3', 'test3desc', '20050202');") ;
		CopixDB::getConnection ()->doQuery ("INSERT INTO testmain ( type_test , titre_test , description_test , date_test )
VALUES ('4', 'test4', 'test4desc', '20050202'
);");
	}

	function testTransaction (){
		try {
		   CopixServices::process ('test|CopixTest::deleteCopixTestMain', array ('fail'=>true));
		   $this->assertTrue (false);//KO, il aurait du y avoir une exception
		}catch (Exception $e){
			$this->assertTrue (true);//ok, il y a bien eu exception			
		}
		//Comme le service a échoué, la transaction n'est pas validée
		$full = CopixDB::getConnection ()->doQuery ('select * from testmain');
		$this->assertEquals (count ($full), 4);
		
		try {
		   CopixServices::process ('test|CopixTest::deleteCopixTestMain');
		   $this->assertTrue (true);//OK, il n'y a pas d'exception
		}catch (Exception $e){
			echo $e->getMessage ();
			$this->assertTrue (false);//KO, il ne devrait pas y avoir d'exception
						
		}
		//Comme le service a échoué, la transaction n'est pas validée
		$full = CopixDB::getConnection ()->doQuery ('select * from testmain');
		$this->assertEquals (count ($full), 0);
		
	}
}