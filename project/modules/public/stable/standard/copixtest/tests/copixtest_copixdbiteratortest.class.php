<?php
/**
 * @package 	standard
 * @subpackage 	copixtest
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test des requêtes, suite au passage dans Copix aux iterateurs en tant que résultat de requête
 * @package standard
 * @subpackage copixtest
 */
class CopixTest_CopixDBIteratorTest extends CopixTest {
   function testEmptyIsSet (){
   	   $results = _iDoQuery ('select * from copixmodule where 1=2');
   	   $this->assertFalse (isset ($results[0]));
   	   $this->assertEquals (count ($results), 0);
   	   $this->assertFalse (isset ($results[0]));
   	   
   	   $results = _iDoQuery ('select * from copixmodule');
   	   $this->assertTrue (isset ($results[0]));
   }
   
   function testOffsetExistsDAO (){
   	$results = _ioDao ('copix:copixconfig')->iFindBy (_daoSp ()->addCondition ('id_ccfg', '=', 'FOOOOOOOOOOOOVALUE'));
   	$this->assertFalse (isset ($results[0]));
   	
   	$results = _ioDAO ('copix:copixconfig')->iFindAll ();
   	$this->assertTrue (count ($results)  > 10);
   	$this->assertTrue (isset ($results[0]));
   }
}
?>