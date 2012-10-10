<?php
class ListenerIndex_Search extends CopixListener {
	/**
	* Gestion des contenux qui sont plac�s en ligne
	* @param CopixEvent $event l'�v�nement et ses param�tres
	* @param CopixEventResposne $eventResponse la r�ponse que l'on va passer � l'�v�nement
	*/
	public function processContent ($pEvent, $pEventResponse){
		_class ('index_search|IndexingServices')->addContent (
			$pEvent->getParam ('title'),
			$pEvent->getParam ('content'),
			$pEvent->getParam ('url'),
			$pEvent->getParam ('title'),
			$pEvent->getParam ('credentials'),
			indexingServices::TYPE_HTML_BRUTE,
			$pEvent->getParam ('path')
		);
	}

	/**
	* Gestion des contenus qui sont supprim�s
	* @param CopixEvent $event l'�v�nement et ses param�tres
	* @param CopixEventResposne $eventResponse la r�ponse que l'on va passer � l'�v�nement
	*/
	public function processDeletedContent ($pEvent, $pEventResponse){
		_class ('index_search|IndexingServices')->delete ($pEvent->getParam ('url'));
	}

	public function processUpdateContent ($pEvent, $pEventResponse) {
		_class ('index_search|IndexingServices')->delete ($pEvent->getParam ('url'));
		$new = $pEvent->getParam ('new', array ());
		_class ('index_search|IndexingServices')->addContent (
			@$new['title'],
			@$new['content'],
			@$new['url'],
			@$new['title'],
			@$new['credentials'],
			indexingServices::TYPE_HTML_BRUTE,
			@$new['path']
		);
	}

	public function processBeforeIndexing ($pEvent, $pEventResponse) {
		_ioDAO ('search_domain')->deleteBy (_daoSp ());
		_ioDAO ('search_map')->deleteBy (_daoSp ());
		_ioDAO ('search_objectcredential')->deleteBy (_daoSp ());
		_ioDAO ('search_objectindex')->deleteBy (_daoSp ());
		_ioDAO ('search_wordlist')->deleteBy (_daoSp ());
	}

}