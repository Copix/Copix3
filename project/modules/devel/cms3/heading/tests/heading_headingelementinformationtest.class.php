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
 * Tests des services sur les elements de rubriques
 */
class Heading_HeadingElementInformationTest extends CopixTest {
	public function testCreationHeadingElementInformation (){
	}

	public function testModificationHeadingElementInformation (){
	}

	public function testSupressionHeadingElementInformation (){
	}
	
	/**
	 * Teste la recherche de la future version d'un element
	 *
	 */
	public function testGetNextVersion () {
		$this->assertTrue(_ioClass('heading|HeadingElementInformationServices')->getNextVersion(20) === 1);
	}
	
	/**
	 * Procédure de création d'une arborescence
	 *
	 * @return unknown
	 */
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
	
	/**
	 * Teste la suppression d'un element à partir de son identifiant et de son type
	 *
	 */
/*	public function testDeleteById () {
		$this->testCreationArbo ();
		$this->assertTrue (_class('heading|HeadingElementInformationServices')->deleteById(15, true) !== false);
	}
	
	/**
	 * Teste la suppression d'un element à partir de son identifiant public
	 *
	 */
/*	public function testDeleteByPublicId () {
		$this->testCreationArbo ();
		$this->assertTrue (_class('heading|HeadingElementInformationServices')->deleteByPublicId (16, true) !== false);
	}
	*/
	
	/**
	 * Test de la recherche
	 *
	 */
	public function testFindByParameters () {
		$test1 = count(_class('heading|HeadingElementInformationServices')->find(array('parent_heading_public_id_hei' => 27)));
		$test2 = count(_class('heading|HeadingElementInformationServices')->find(array('author_id_update_hei' => 1)));
		$test3 = count(_class('heading|HeadingElementInformationServices')->find(array('caption_hei' => 'Hardware')));
		$test4 = count(_class('heading|HeadingElementInformationServices')->find(array('type_hei' => 'heading')));
		$test5 = count(_class('heading|HeadingElementInformationServices')->find(array('status_hei' => 3)));
		$test6 = count(_class('heading|HeadingElementInformationServices')->find(array('version_hei' => 0)));		
		$this->assertTrue ($test1 === 1);
		$this->assertTrue ($test2 === 18);
		$this->assertTrue ($test3 === 1);
		$this->assertTrue ($test4 !== 0);
		$this->assertTrue ($test5 !== 0);
		$this->assertTrue ($test5 !== 0);
		$this->assertTrue ($test6 !== 0);
	}

	/**
	 * Teste si un element à un fils à partir de son identifiant et de son type
	 *
	 */
	public function testHasChild() {
		$this->assertTrue(_class('heading|HeadingElementInformationServices')->hasChild (28) == true);
	}

	/**
	 * Teste si un element à un fils à partir de son identifiant public
	 *
	 */
	public function testHasChildById () {
		$this->assertTrue(_class('heading|HeadingElementInformationServices')->hasChildById (28, 'heading') == true);
	}
	
	/**
	 * test sur l'accesseur de theme à partir de l'identifiant et du type
	 *
	 */
	public function testGetTheme () {
		$arData = array ('theme' => 5, 'providedBy' => 27);
		if (_class('heading|HeadingElementInformationServices')->getTheme (27) == $arData) {
			$this->assertTrue(_class('heading|HeadingElementInformationServices')->getTheme (27) == $arData);
		}
	}
	
	/**
	 * test sur l'accesseur de theme à partir de l'identifiant public
	 *
	 */
	public function testGetThemeById () {
		$arData = array ('theme' => 5, 'providedBy' => 27);
        $this->fail("getThemeById n'existe plus!");
        //$this->assertTrue(_class('heading|HeadingElementInformationServices')->getThemeById (27, 'heading') !== $arData);
	}
	
	/**
	 * test sur l'url de base à partir de l'identifiant et du type
	 *
	 */
	public function testGetBaseUrl () {
		$arData = array ('baseUrl' => 'http://www.test.com', 'providedBy' => 27);
		$this->assertTrue (_class('heading|HeadingElementInformationServices')->getBaseUrl (27) == $arData);
	}
	
	/**
	 * test sur l'url de base à partir de l'identifiant public
	 *
	 */
	public function testGetBaseUrlById () {
		$arData = array ('baseUrl' => 'http://www.test.com', 'providedBy' => 27);
        $this->fail("getBaseUrlById n'existe plus!");
		//$this->assertTrue (_class('heading|HeadingElementInformationServices')->getBaseUrlById (28, 'heading') == $arData);
	}
	
	/**
	 * test sur le retour d'un root menu à partir de l'identifiant et du type
	 *
	 */
	public function testGetRootMenu () {
		$arData = array ('rootMenu' => 8, 'providedBy' => 27);
        $this->fail("getRootMenu n'existe plus!");
		//$this->assertTrue (_class('heading|HeadingElementInformationServices')->getRootMenu (27) == $arData);
	}
	
	/**
	 * Test sur le retour d'un root menu à partir de l'identifiant public
	 *
	 */
	public function testGetRootMenuById () {
		$arData = array ('rootMenu' => 8, 'providedBy' => 27);
        $this->fail("getRootMenuById n'existe plus!");
		$this->assertTrue (_class('heading|HeadingElementInformationServices')->getRootMenuById (28, 'heading') == $arData);
	}
	
	/**
	 * Test sur le retour d'un menu contextuel
	 *
	 */
	public function testGetContextualMenu () {
		$arData = array ('contextualMenu' => 7, 'providedBy' => 27);
        $this->fail("getContextualMenu n'existe plus!");
		//$this->assertTrue (_class('heading|HeadingElementInformationServices')->getContextualMenu (27) == $arData);
	}
}