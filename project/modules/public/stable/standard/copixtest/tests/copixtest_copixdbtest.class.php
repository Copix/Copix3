<?php
/**
* @package		standard
* @subpackage	copixtest
* @author		Croës Gérald
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package		standard
 * @subpackage	copixtest
 */
class CopixTest_CopixDBTest extends CopixTest {
	/**
	 * On va tester les connexions définies dans l'application
	 */
	public function testConnection (){
    	foreach (CopixConfig::instance ()->copixdb_getProfiles () as $name){
    		try {
    			CopixDB::getConnection ($name);
    			$this->assertTrue (true);//ok
    		}catch (Exception $exception){
    			$this->assertTrue (false);//n'a pas fonctionné    			
    		}
    	}
	}
	
	public function setup (){
		CopixDB::getConnection ()->doQuery ('delete from copixtestmain');
		CopixDB::getConnection ()->doQuery ("INSERT INTO copixtestmain ( type_test , titre_test , description_test , date_test )
VALUES ('1', 'test1', 'test1desc', '20060202');");
		CopixDB::getConnection ()->doQuery ("INSERT INTO copixtestmain ( type_test , titre_test , description_test , date_test )
VALUES ('2', 'test2', 'test2desc', '20050202');");
		CopixDB::getConnection ()->doQuery ("INSERT INTO copixtestmain ( type_test , titre_test , description_test , date_test )
VALUES ('3', 'test3', 'test3desc', '20050202');") ;
		CopixDB::getConnection ()->doQuery ("INSERT INTO copixtestmain ( type_test , titre_test , description_test , date_test )
VALUES ('4', 'test4', 'test4desc', '20050202'
);");
	}
	
	function testUpdate (){
		$count = CopixDB::getConnection ()->doQuery ('update copixtestmain set titre_test = :titre where type_test = 1', array (':titre'=>'nouveau titre'));
		$this->assertEquals (1, $count);		

		$count = CopixDB::getConnection ()->doQuery ('update copixtestmain set titre_test = :titre', array (':titre'=>'nouveau titre'));
		$this->assertEquals (3, $count);

		$count = CopixDB::getConnection ()->doQuery ('update copixtestmain set titre_test = :titre', array (':titre'=>'nouveau titre encore'));
		$this->assertEquals (4, $count);
	}
	
	/**
	 * Requête limitée
	 */
	function testLimitedQuery (){
		//récupèration de tout
		$full = CopixDB::getConnection ()->doQuery ('select * from copixtestmain');
		//Les tests sont prévus pour fonctionner avec 4 enregistrements
		$this->assertTrue (count ($full) === 4);

		//récupèration des deux premières lignes
		$first2 = CopixDB::getConnection ()->doQuery ('select * from copixtestmain', array (), 0, 2); 
		$this->assertTrue (count ($first2) == 2);
		for ($i=0; $i<2; $i++){
			$this->assertTrue ($first2[$i]->id_test == $full[$i]->id_test);
		}

		//récupèration des 2ème et 3ème enregistrements
		$middle2 = CopixDB::getConnection ()->doQuery ('select * from copixtestmain', array (), 1, 2);
		$this->assertTrue (count ($middle2) == 2);
		for ($i=0; $i<2; $i++){
			$this->assertTrue ($middle2[$i]->id_test == $full[$i+1]->id_test);
		}

		//récupèration des derniers enregistrements à partir de la 2ème ligne
		$offset2 = CopixDB::getConnection ()->doQuery ('select * from copixtestmain', array (), 2);
		$this->assertTrue (count ($offset2) == 2);
		for ($i=0; $i<2; $i++){
			$this->assertTrue ($offset2[$i]->id_test == $full[$i+2]->id_test);
		}
	}

	function testTransaction (){
		CopixDB::begin ();
		CopixDB::getConnection ()->doQuery ('delete from copixtestmain');
		CopixDB::rollback ();
		$full = CopixDB::getConnection ()->doQuery ('select * from copixtestmain');
		$this->assertTrue (count ($full) === 4);

		CopixDB::begin ();
		$query = 'INSERT INTO copixtestmain (type_test,titre_test,description_test,date_test,version_test) 
VALUES (1, \'Titre temp\', \'Description de lelement temporaire\', \'20060201\', 0)';
		CopixDB::getConnection ()->doQuery ($query);
		
		CopixDB::commit ();
		
		$full = CopixDB::getConnection ()->doQuery ('select * from copixtestmain');
		$this->assertTrue (count ($full) === 5);
		
		$tId = CopixDB::begin ();
		//création des enregistrements de l'autre table
		$record = _record ('copixtest|copixtestmain');
		//Création de la dao de l'autre table
		$dao = _dao ('copixtest|copixtestmain');  
		//insertion d'enregistrements exemples
		$record->type_test  = 1;//catégorie 
		$record->title_test = 'Titre temp';
		$record->description_test = 'Description de lelement temporaire';
		$record->date_test = '20060201';
		$record->version_test = 0;
		$dao->insert ($record);
		CopixDB::commit ($tId);
	
		$full = CopixDB::getConnection ()->doQuery ('select * from copixtestmain');
		$this->assertTrue (count ($full) === 6);
   		
		CopixDB::begin ();
		//création des enregistrements de l'autre table
		$record = _record ('copixtest|copixtestmain');

		//Création de la dao de l'autre table
		$dao = _dao ('copixtest|copixtestmain');
   
		//insertion d'enregistrements exemples
		$record->type_test  = 1;//catégorie 
		$record->title_test = 'Titre temp';
		$record->description_test = 'Description de lelement temporaire';
		$record->date_test = '20060201';
		$record->version_test = 0;
		$dao->insert ($record);
		CopixDB::rollback ();
		$full = CopixDB::getConnection ()->doQuery ('select * from copixtestmain');
		$this->assertTrue (count ($full) === 6);
   
		CopixDB::begin ();
		$count = CopixDB::getConnection ()->doQuery('delete from copixtestmain');
		$this->assertEquals (6, $count);
		CopixDB::begin ();
		$count = count (CopixDB::getConnection ()->doQuery ('select * from copixtestforeignkeytype'));
		CopixDB::getConnection ()->doQuery("INSERT INTO copixtestforeignkeytype ( caption_typetest ) VALUES ('test')");     
		$countNow = count (CopixDB::getConnection ()->doQuery ('select * from copixtestforeignkeytype'));
		CopixDB::commit ();
		CopixDB::rollback ();

		$full = CopixDB::getConnection ()->doQuery ('select * from copixtestmain');
		$this->assertTrue (count ($full) >= 5);
   
		$this->assertTrue ($count == $countNow - 1);
   
		$dao->delete ($record->id_test);
	}

	function testSpecialChar (){
		$ct = CopixDB::getConnection ();
		$value = "test ' avec une quote et un antislash \\";
		$query = "insert into copixtestmain (type_test , titre_test , description_test , date_test ) VALUES ('1', ".$ct->quote($value).", 'test1desc', '20060202')";

		$countNow = count (CopixDB::getConnection ()->doQuery ('select * from copixtestmain'));
		try {
			CopixDB::getConnection ()->doQuery ($query);
		}catch (Exception $exception){
		}
		$this->assertTrue ($countNow == count (CopixDB::getConnection ()->doQuery ('select * from copixtestmain')) - 1);
		/*
		 $value = "test:antislash\\";
		 $query = "insert into copixtestmain (id_test , type_test , titre_test , description_test , date_test ) VALUES (NULL , '1', ".$ct->quote($value).", 'test1desc', '20060202')";
		 $countNow = count (CopixDB::getConnection ()->doQuery ('select * from copixtestmain'));
		 try {
		 CopixDB::getConnection ()->doQuery ($query);
		 }catch (Exception $exception){
		 }
		 $this->assertTrue ($countNow == count (CopixDB::getConnection ()->doQuery ('select * from copixtestmain')) - 1);

		 $value = 'test:doublequote"';
		 $query = "insert into copixtestmain (id_test , type_test , titre_test , description_test , date_test ) VALUES (NULL , '1', ".$ct->quote($value).", 'test1desc', '20060202')";
		 $countNow = count (CopixDB::getConnection ()->doQuery ('select * from copixtestmain'));
		 try {
		 CopixDB::getConnection ()->doQuery ($query);
		 }catch (Exception $exception){
		 }
		 $this->assertTrue ($countNow == count (CopixDB::getConnection ()->doQuery ('select * from copixtestmain')) - 1);
		 */
		$value = 'test:doublequote"';
		$query = "insert into copixtestmain (type_test , titre_test , description_test , date_test ) VALUES ('1', :parameter, 'test1desc', '20060202')";
		$countNow = count (CopixDB::getConnection ()->doQuery ('select * from copixtestmain'));
		try {
			CopixDB::getConnection ()->doQuery ($query, array (':parameter'=>$value));
		}catch (Exception $exception){
		}
		$this->assertTrue ($countNow == count (CopixDB::getConnection ()->doQuery ('select * from copixtestmain')) - 1);
	}


} 
?>