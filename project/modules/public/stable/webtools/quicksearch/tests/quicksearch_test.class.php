<?php
/**
 * 
 */

/**
 * Test des services de recherche du moteur
 */
class QuickSearch_Test extends CopixTest {
	public function setup (){
		CopixContext::push ('quicksearch');
	}
	public function teardown (){
		CopixContext::pop ();
	}

	public function testService (){
//-- Test d'insertion
		_service ('quicksearch|quicksearch::addOrUpdateIndex', array ('id'=>10,
			'kind'=>'test',
			'keywords'=>'mot clef',
			'title'=>'titre',
			'summary'=>'résumé',
			'content'=>'contenu',
			'url'=>'http://www.copix.org'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>10, 'kind'=>'test'));
		$this->assertEquals ($element->keywords, 'mot clef');
		$this->assertEquals ($element->title, 'titre');
		$this->assertEquals ($element->summary, 'résumé');
		$this->assertEquals ($element->content, 'contenu');
		$this->assertEquals ($element->url, 'http://www.copix.org');
		
//-- Test d'insertion on test si ça marche aussi en contexte différent
		CopixContext::push ('copixtest'); 
		_service ('quicksearch|quicksearch::addOrUpdateIndex', array ('id'=>20,
			'kind'=>'test',
			'keywords'=>'mot clef 20',
			'title'=>'titre 20',
			'summary'=>'résumé 20',
			'content'=>'contenu 20',
			'url'=>'http://wiki.copix.org'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>20, 'kind'=>'test'));
		$this->assertEquals ($element->keywords, 'mot clef 20');
		$this->assertEquals ($element->title, 'titre 20');
		$this->assertEquals ($element->summary, 'résumé 20');
		$this->assertEquals ($element->content, 'contenu 20');
		$this->assertEquals ($element->url, 'http://wiki.copix.org');
		CopixContext::pop ();

//-- Test de mise à jour
		_service ('quicksearch|quicksearch::addOrUpdateIndex', array ('id'=>10,
			'kind'=>'test',
			'keywords'=>'mot clef maj',
			'title'=>'titre maj',
			'summary'=>'résumé maj',
			'content'=>'contenu maj',
			'url'=>'http://www.copix.org maj'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>10, 'kind'=>'test'));
		$this->assertEquals ($element->keywords, 'mot clef maj');
		$this->assertEquals ($element->title, 'titre maj');
		$this->assertEquals ($element->summary, 'résumé maj');
		$this->assertEquals ($element->content, 'contenu maj');
		$this->assertEquals ($element->url, 'http://www.copix.org maj');
		
//-- Test de mise à jour en contexte différent
		CopixContext::push ('copixtest'); 
		_service ('quicksearch|quicksearch::addOrUpdateIndex', array ('id'=>20,
			'kind'=>'test',
			'keywords'=>'mot clef 20 maj',
			'title'=>'titre 20 maj',
			'summary'=>'résumé 20 maj',
			'content'=>'contenu 20 maj',
			'url'=>'http://wiki.copix.org maj'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>20, 'kind'=>'test'));
		$this->assertEquals ($element->keywords, 'mot clef 20 maj');
		$this->assertEquals ($element->title, 'titre 20 maj');
		$this->assertEquals ($element->summary, 'résumé 20 maj');
		$this->assertEquals ($element->content, 'contenu 20 maj');
		$this->assertEquals ($element->url, 'http://wiki.copix.org maj');
		CopixContext::pop ();

//-- Test de supression
		_service ('quicksearch|quicksearch::deleteIndex', array ('id'=>10,
			'kind'=>'test'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>10, 'kind'=>'test'));
		$this->assertEquals ($element, false);
		
		_service ('quicksearch|quicksearch::deleteIndex', array ('id'=>20,
			'kind'=>'test'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>20, 'kind'=>'test'));
		$this->assertEquals ($element, false);
	}
	
	public function testEvent (){
//-- Test d'insertion
		_notify ('Content', array ('id'=>10,
			'kind'=>'test',
			'keywords'=>'mot clef',
			'title'=>'titre',
			'summary'=>'résumé',
			'content'=>'contenu',
			'url'=>'http://www.copix.org'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>10, 'kind'=>'test'));
		$this->assertEquals ($element->keywords, 'mot clef');
		$this->assertEquals ($element->title, 'titre');
		$this->assertEquals ($element->summary, 'résumé');
		$this->assertEquals ($element->content, 'contenu');
		$this->assertEquals ($element->url, 'http://www.copix.org');
		
//-- Test d'insertion on test si ça marche aussi en contexte différent
		CopixContext::push ('copixtest'); 
		_notify ('Content', array ('id'=>20,
			'kind'=>'test',
			'keywords'=>'mot clef 20',
			'title'=>'titre 20',
			'summary'=>'résumé 20',
			'content'=>'contenu 20',
			'url'=>'http://wiki.copix.org'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>20, 'kind'=>'test'));
		$this->assertEquals ($element->keywords, 'mot clef 20');
		$this->assertEquals ($element->title, 'titre 20');
		$this->assertEquals ($element->summary, 'résumé 20');
		$this->assertEquals ($element->content, 'contenu 20');
		$this->assertEquals ($element->url, 'http://wiki.copix.org');
		CopixContext::pop ();

//-- Test de mise à jour
		_notify ('Content', array ('id'=>10,
			'kind'=>'test',
			'keywords'=>'mot clef maj',
			'title'=>'titre maj',
			'summary'=>'résumé maj',
			'content'=>'contenu maj',
			'url'=>'http://www.copix.org maj'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>10, 'kind'=>'test'));
		$this->assertEquals ($element->keywords, 'mot clef maj');
		$this->assertEquals ($element->title, 'titre maj');
		$this->assertEquals ($element->summary, 'résumé maj');
		$this->assertEquals ($element->content, 'contenu maj');
		$this->assertEquals ($element->url, 'http://www.copix.org maj');
		
//-- Test de mise à jour en contexte différent
		CopixContext::push ('copixtest'); 
		_notify ('Content', array ('id'=>20,
			'kind'=>'test',
			'keywords'=>'mot clef 20 maj',
			'title'=>'titre 20 maj',
			'summary'=>'résumé 20 maj',
			'content'=>'contenu 20 maj',
			'url'=>'http://wiki.copix.org maj'));
		$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>20, 'kind'=>'test'));
		$this->assertEquals ($element->keywords, 'mot clef 20 maj');
		$this->assertEquals ($element->title, 'titre 20 maj');
		$this->assertEquals ($element->summary, 'résumé 20 maj');
		$this->assertEquals ($element->content, 'contenu 20 maj');
		$this->assertEquals ($element->url, 'http://wiki.copix.org maj');
		CopixContext::pop ();

//-- Test de supression
		_notify ('DeletedContent', array ('id'=>10,
				'kind'=>'test'));
				$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>10, 'kind'=>'test'));
		$this->assertEquals ($element, false);
		
		_notify ('DeletedContent', array ('id'=>20,
				'kind'=>'test'));
				$element = _service ('quicksearch|quicksearch::getIndex', array ('id'=>20, 'kind'=>'test'));
		$this->assertEquals ($element, false);
	}
}
?>