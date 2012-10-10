<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Gérald Croës
 */

/**
 * Test sur les rubriques
 */
class Heading_HeadingTest extends CopixTest {
	public function testCreationRubrique (){
		//création d'une rubrique test
		$ppo = _ppo ();
		$ppo->parent_heading_public_id_hei = 0;//rubrique parente = rubrique racine
		$ppo->caption_hei = 'Rubrique testée '.date ('YmdHis');
		$ppo->description_hei = 'Ceci est une rubrique testée unitairement, et ceci constitue la description longue de mon élément';
		_class ('heading|headingservices')->insert ($ppo);

		$this->assertFalse ($ppo->public_id_hei === null);
		$this->assertFalse ($ppo->id_heading === null);
		$this->assertTrue (is_object (DAOcms_headings::instance ())->get ($ppo->id_heading));
	}
	
	public function testCreationArbo (){
		$r1 = _ppo (array ('parent_heading_public_id_hei'=>0,
							'caption_hei'=>'R1'
					));
		_class ('heading|headingservices')->insert ($r1);

		$r2 = _ppo (array ('parent_heading_public_id_hei'=>0,
							'caption_hei'=>'R2'
					));
		_class ('heading|headingservices')->insert ($r2);

		$r3 = _ppo (array ('parent_heading_public_id_hei'=>0,
							'caption_hei'=>'R3'
					));
		_class ('heading|headingservices')->insert ($r3);

		$r11 = _ppo (array ('parent_heading_public_id_hei'=>$r1->public_id_hei,
							'caption_hei'=>'R11'
					));
		_class ('heading|headingservices')->insert ($r11);		

		$r12 = _ppo (array ('parent_heading_public_id_hei'=>$r1->public_id_hei,
							'caption_hei'=>'R12'
					));
		_class ('heading|headingservices')->insert ($r12);		
		
		$r121 = _ppo (array ('parent_heading_public_id_hei'=>$r12->public_id_hei,
							'caption_hei'=>'R121'
					));
		_class ('heading|headingservices')->insert ($r121);

		$r122 = _ppo (array ('parent_heading_public_id_hei'=>$r12->public_id_hei,
							'caption_hei'=>'R122',
							'description_hei'=>'Description de la rubrique 122'
					));
		_class ('heading|headingservices')->insert ($r122);

		return compact ('r1', 'r2', 'r3', 'r11', 'r12', 'r121');
	}
	
	public function testGetPath (){
		$headingCreated = $this->testCreationArbo ();
	}

	public function testModificationRubrique (){
	}
	
	public function testSupressionRubrique (){
	}
	
	public function testRecuperationRubrique (){
	}
}