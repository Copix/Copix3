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
class CopixTest_CopixFilterTest extends CopixTest {
	public function testFilter (){
		$this->assertEquals (10, CopixFilter::getInt ('10'));
		$this->assertEquals (10, CopixFilter::getInt ('10.1'));
		$this->assertEquals (10, CopixFilter::getInt ('aaaa10.1'));
		
		$this->assertEquals (10.0, CopixFilter::getFloat ('10.0'));
		$this->assertEquals (10.1, CopixFilter::getFloat ('10.1'));
		$this->assertEquals (10.1, CopixFilter::getFloat ('a1d0.fg1'));

		$this->assertEquals ('Bonjour toi', CopixFilter::getAlpha ('B\\""\'o56njour toi'));
		$this->assertEquals ('Bonjourtoi', CopixFilter::getAlpha ('B\\""\'o56njour t\\oi', false));

		$this->assertEquals ('Bonjour 17', CopixFilter::getAlphaNum ('B\\""\'o-{}°njour 1__-()@7'));
		$this->assertEquals ('Bonjour17', CopixFilter::getAlphaNum ('B\\""\'o-{}°njour 1__-()@7', false));
	}
}
?>