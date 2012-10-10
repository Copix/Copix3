<?php
/**
 * @package    standard
 * @subpackage test
 * @author     Alexandre JULIEN
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */
class Test_CopixImage extends CopixTest {

	/**
	 * Teste le chargement d'une image gif
	 *
	 */
	public function testLoad () {
		$this->assertTrue(CopixImage::load(_resourcePath('test|img/test/google_test.gif')) !== false);
	}
	
	/**
	 * Teste le redimensionnement en respectant les proportions
	 *
	 */
	public function testResizeWithKeepingProportions () {
		$this->assertTrue(($img = CopixImage::load(_resourcePath('test|img/test/google_test.gif'))) !== false);
		$this->assertTrue($img->resize(80, 80, true));
		$this->assertTrue($img->resize(160, 500, true));
		$this->assertTrue($img->resize(20, 10, true));
		$this->assertTrue($img->resize(1920, 1200, true));
	}
	
	/**
	 * Teste le redimensionnement en ne respectant pas les proportions
	 *
	 */	
	public function testResizeWithoutKeepingProportions () {
		$this->assertTrue(($img = CopixImage::load(_resourcePath('test|img/test/google_test.gif'))) !== false);
		$this->assertTrue($img->resize(80, 80, false));
		$this->assertTrue($img->resize(160, 500, false));
		$this->assertTrue($img->resize(20, 10, false));
		$this->assertTrue($img->resize(1920, 1200, false));	
		$this->assertTrue($img->resize(20, 10, false));
		$this->assertTrue($img->resize(1920, 1200, false));
	}
	
	/**
	 * Teste le retour de la largeur de l'image
	 *
	 */
	public function testGettingWidth () {
		$this->assertTrue(($img = CopixImage::load(_resourcePath('test|img/test/google_test.gif'))) !== false);
		$this->assertEquals($img->getWidth(), '276');
	}
	
	/**
	 * Teste le retour de la hauteur de l'image
	 *
	 */
	public function testGettingHeight () {
		$this->assertTrue(($img = CopixImage::load(_resourcePath('test|img/test/google_test.gif'))) !== false);
		$this->assertEquals($img->getHeight(), '110');
	}
	
	/**
	 * Test de superposition
	 *
	 */
	public function testSuperpose () {
		$this->assertTrue(($img = CopixImage::load(_resourcePath('test|img/test/google_test.gif'))) !== false);
		$this->assertTrue(($img->superpose(_resourcePath('test|img/test/google_test.gif'))) !== false);
	}
	
	/**
	 * Test sur un filtre de transparence avec la couleur verte #00ff00
	 *
	 */
	public function testTransparencyFilter () {
		$this->assertTrue (($img = CopixImage::load(_resourcePath('test|img/test/test.jpg'))) !== false);
		$this->assertTrue ($img->applyTransparencyFilter(_resourcePath('test|img/test/filter.png'), 0, 255, 0) !== false);
	}
	
}