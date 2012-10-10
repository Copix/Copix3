<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Alexandre JULIEN
 */
class heading_headingelementtype extends CopixTest {
	public function testParsingType () {
		$this->assertTrue(_class('heading|HeadingElementType')->getCaption('media') === 'salut');
	}
	
	public function testArrayType () {
		$arData = array ();
		$arData['media'] = 'salut';
		$arData['image'] = 'bonjour';
		$this->assertTrue(_class('heading|HeadingElementType')->getList() == $arData);
	}
}
?>