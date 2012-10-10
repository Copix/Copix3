<?php
/**
 * @package		tutorials
 * @subpackage 	news_7
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Présentation des tests unitaires avec le module de nouvelles
 * @package	tutorials
 * @subpackage	news_7
 */
class News_7_UrlTest extends CopixTest {
	public function testUrl (){
		$this->assertEquals (CopixUrl::getRequestedBaseUrl ().'test.php/news_7/1/titre_test_de_nouvelle', _url ('news_7|default|show', array ('id_news'=>1, 'title_news'=>'titre test de nouvelle')));
		$this->assertEquals (CopixUrl::getRequestedBaseUrl ().'test.php/news_7/1/titre_test_de_nouvelle_a', _url ('news_7|default|show', array ('id_news'=>1, 'title_news'=>'titre test de nouvelle à')));

		/*
		$parameters = CopixUrl::parse ('/news_7/rss', true);
		$this->assertEquals ($parameters['action'], 'default');
		$this->assertEquals ($parameters['rss'], '1');
		
		$parameters = CopixUrl::parse ('/news_7/1/rss', true);
		$this->assertEquals ($parameters['action'], 'show');
		$this->assertEquals ($parameters['id_news'], '1');
		*/
	}
}
?>