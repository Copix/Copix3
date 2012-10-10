<?php
/**
 * @package		webtools
 * @subpackage 	news
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Présentation des tests unitaires avec le module de nouvelles
 * @package	webtools
 * @subpackage	news
 */
class News_UrlTest extends CopixTest {
	public function testUrl (){
		$this->assertEquals (CopixUrl::getRequestedBaseUrl ().'test.php/news/1/titre_test_de_nouvelle', _url ('news|default|show', array ('id_news'=>1, 'title_news'=>'titre test de nouvelle')));
		$this->assertEquals (CopixUrl::getRequestedBaseUrl ().'test.php/news/1/titre_test_de_nouvelle_a', _url ('news|default|show', array ('id_news'=>1, 'title_news'=>'titre test de nouvelle à')));

		/*
		$parameters = CopixUrl::parse ('/news/rss', true);
		$this->assertEquals ($parameters['action'], 'default');
		$this->assertEquals ($parameters['rss'], '1');
		
		$parameters = CopixUrl::parse ('/news/1/rss', true);
		$this->assertEquals ($parameters['action'], 'show');
		$this->assertEquals ($parameters['id_news'], '1');
		*/
	}
}
?>