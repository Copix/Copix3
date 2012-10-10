<?php
/**
 * @package standard
 * @subpackage copixtest
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Test des fonctionnalités d'envois de mail
 * @package standard
 * @subpackage copixtest
*/
class CopixTest_MailerTest extends CopixTest {
	function setUp (){
		CopixContext::push ('copixtest');
	}
	function tearDown (){
		CopixContext::pop ();
	}

	/**
	 * Test simple de l'envois de mail
	 */
	function testMail (){
		$this->markTestIncomplete ('Mailer à tester');
		return;

        $mail = new CopixEMail ('g.croes@alptis.fr', 'gerald@phpside.org', 'gerald@copix.org', '[sujet]test de message', 'Contenu du message');     
		$this->assertTrue ($mail->send ('g.croes@alptis.fr'));		
	}
}
?>