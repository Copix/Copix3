<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Alexandre JULIEN
 */
class heading_headingportletsxmlparse extends CopixTest {
	
	public function testArrayType () {
		$arData = array ();
		$arData['media'] = 'salut';
		$arData['image'] = 'bonjour';
		$this->assertTrue(_class('heading|HeadingElementType')->getList() == $arData);
	}
}
?>