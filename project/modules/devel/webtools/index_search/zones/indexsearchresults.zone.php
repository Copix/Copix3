<?php
/**
 * @package webtools
 * @subpackage index_search
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Effectue la recherche et retourne le résultat
 *
 * @package webtools
 * @subpackage index_search
 */
class ZoneIndexSearchResults extends CopixZone {
    /**
	 * Génère le contenu
	 *
	 * @param string $pToReturn HTML généré
	 * @return boolean
	 */
    protected function _createContent (&$pToReturn) {
		$page = $this->getParam ('page', 1) - 1;

		$search = _class ('index_search|researcher');
		$criteria = $this->_sanitize( $this->getParam ('criteria') );
		$timer = new CopixTimer (); // Calcule le temps d'execution
		$timer->start ();
		$search->search ($criteria, $this->getParam ('path'));
		$time = $timer->stop ();

		// Lancement de la recherche
		$ppo = new CopixPPO ();
		$ppo->listResults = $search->getResult ($page);
		$ppo->nbResult= $search->getNumberResults ();
		$ppo->criteria	= $criteria;
		$ppo->time = $time;
		$ppo->TITLE_PAGE = _i18n ('index_search|index_search.title.show');
		$ppo->currentPage = $page + 1;
		$ppo->nextPage = $page + 2;
		$ppo->prevPage = $page;
		$ppo->maxPage = $search->pageNumber ();
		$ppo->similarWord = ($this->getParam ('showSimilars', true)) ? $search->getSimilarSearchString () : array ();
		$ppo->params = $this->getParam ('params');
		$ppo->url = $this->getParam ('url', _url ('index_search||'));

		$x = 3;
		$ppo->loopStart = ($ppo->currentPage - $x) < 1 ? 1 : $ppo->currentPage - $x;
		$ppo->loopEnd = ($ppo->currentPage + $x) > $ppo->maxPage ? $ppo->maxPage : $ppo->currentPage + $x;

		$ppo->showCachedAndText = CopixConfig::get ('index_search|showCachedAndText');

		//On a besoin de ces informations si xiti est activé
		if( CopixModule::isEnabled('xiti') ){
			$ppo->xiti_params = '?xtmc='.$this->_sanitize( $criteria, true ).'&xtcr=';
			$ppo->xiti_offset = $page * 10;
		}

		$pToReturn = $this->_usePPO ($ppo, 'index_search|search.results.tpl');
		return true;
    }

	private function _sanitize($str, $forXiti = false){
		$charset = 'UTF-8'; // ISO8859-1';
		$str= htmlentities($str, ENT_NOQUOTES, $charset);
		$str = preg_replace('/&(.)(acute|caron|cedil|circ|grave|ring|slash|tilde|uml);/', '$1', $str);
		$str = html_entity_decode($str, ENT_NOQUOTES, $charset);
		if( $forXiti ){
			$str = preg_replace('"[^a-z0-9_:~\\\/\-]"i','_',$str);
		}
		$str = strtolower($str); // En dernier, sinon renvoie une chaîne vide
		return $str;
	}
}