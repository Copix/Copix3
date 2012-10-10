<?php
/**
 * @package standard
 * @subpackage test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Test des fonctionnalités d'envois de mail
 * @package standard
 * @subpackage test
 */
class Test_MailerTest extends CopixTest {
	function setUp (){
		CopixContext::push ('test');
	}
	function tearDown (){
		CopixContext::pop ();
	}

	/**
	 * Test simple de l'envois de mail
	 */
	function testMail (){
		//Pour le moment, on se contente de voir s'il est possible de créer un objet
		$mail = new CopixHTMLEMail ('g.croes@alptis.fr', 'gerald@phpside.org', 'gerald@copix.org', '[sujet]test de message', 'Contenu du message');
	}
}