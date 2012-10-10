<?php
/**
 * @package webtools
 * @subpackage feed
 * @author Patrice FERLET
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Diverses actions de base
 *
 * @package webtools
 * @subpackage feed
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Génère un flux avec le contenu inséré via l'événement Content
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault(){
		$rss =  new Syndication ();
    	
		//add informations
    	$rss->title = CopixConfig::get ('feed|feedtitle');
    	$rss->link->uri = _url ();
    	$rss->description = CopixConfig::get ('feed|feeddesc');
		
    	$author = $rss->addAuthor ();
    	$author->name = CopixConfig::get ('feed|author');
    	$author->email = CopixConfig::get ('feed|email');
    	$author->webSite->uri = CopixConfig::get ('feed|site');
    	
    	$dao = _ioDao ('feeds');
    	$sp = _daoSP ();
    	if (_request ('category', false)) {
    		$sp->addCondition ('feed_category', '=', _request ('category'));
    	}
    	
    	if (_request ('ref',false)) {
    		$sp->addCondition ('feed_link', '=', _request ('ref'));
    	}
    	
    	$sp->orderBy (array ('feed_pubdate', 'DESC'));
    	
    	$feeds = $dao->findBy ($sp);
    	foreach ($feeds as $feed) {
    		$item = $rss->addItem ();
    		$item->title = html_entity_decode ($feed->feed_title);
    		$item->link->uri = '<![CDATA[' . $feed->feed_link . ']]>';
    		$item->content->encoded = $feed->feed_content;
    		$item->content->value = $feed->feed_desc;
    		$item->pubDate = $feed->feed_pubdate;
    		if (!empty ($feed->feed_author)) {
    			$author = $item->addAuthor ();
	    		$author->name = $feed->feed_author;
    		}
    	}
    	$ppo = new CopixPPO();
    	$ppo->TITLE_PAGE = $rss->title ;
    	$ppo->content = $rss->getContent (Syndication::RSS_2_0);
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl',array ('content-type' => 'application/rss+xml'));
	}
}