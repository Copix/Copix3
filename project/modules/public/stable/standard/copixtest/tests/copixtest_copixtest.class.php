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
class CopixTest_CopixTest extends CopixTest {
	public function testInclusion (){
		// Copix::RequireClass ('CopixListenerFactory');
		$this->assertTrue (class_exists ('CopixListenerFactory'));

		// Copix::RequireClass ('CopixFile');
		$this->assertTrue (class_exists ('CopixFile'));

		// Copix::RequireClass ('CopixTimer');
		$this->assertTrue (class_exists ('CopixTimer'));

		// Copix::RequireClass ('CopixEMailer');
		$this->assertTrue (class_exists ('CopixEMailer'));

		// Copix::RequireClass ('CopixErrorObject');
		$this->assertTrue (class_exists ('CopixErrorObject'));

		// Copix::RequireClass ('CopixFormFactory');
		$this->assertTrue (class_exists ('CopixFormFactory'));

		// Copix::RequireClass ('CopixDateTime');
		$this->assertTrue (class_exists ('CopixDateTime'));

		// Copix::RequireClass ('CopixUser');
		$this->assertTrue (class_exists ('CopixUser'));

		// Copix::RequireClass ('CopixI18NBundle');
		$this->assertTrue (class_exists ('CopixI18NBundle'));

		// Copix::RequireClass ('CopixHTTPHeader');
		$this->assertTrue (class_exists ('CopixHTTPHeader'));

		// Copix::RequireClass ('CopixFilter');
		$this->assertTrue (class_exists ('CopixFilter'));

		// Copix::RequireClass ('CopixMimeTypes');
		$this->assertTrue (class_exists ('CopixMimeTypes'));

		try {
			$this->assertFalse (Copix::RequireClass ('SomeFooClass'));
			$this->fail();//Un exception aurait du être générée
		}catch (Exception $e){
			$this->assertTrue (true);
		}
	} 
}
?>