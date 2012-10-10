<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain VUIDART
 */

/**
 * ActionGroup Search engine optimization
 * @package cms
 * @subpackage heading
 */
class ActionGroupSEO extends CopixActionGroup {
	
    protected function _beforeAction ($pAction) {
		_ioClass ('HeadingElementInformationServices')->breadcrumbAdmin ();
		CopixPage::add ()->setIsAdmin (true);
		if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
    }

	/**
	 * Renvoie le sitemap des pages publiées du cms.
	 *
	 * @return File
	 */
	public function processSitemap (){
		$xml = new DOMDocument('1.0', 'utf-8');
		
		//urlset
		$urlset = $xml->createElement('urlset');
		$urlset = $xml->appendChild($urlset);
		
		$xmlns = $xml->createAttribute('xmlns');
   		$urlset->appendChild($xmlns); 
   		
		$urlset_text = $xml->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
    	$xmlns->appendChild($urlset_text); 
		
    	//urls
		$pages = _ioClass('portal|pageservices')->getPublishedPagesList ( null, array('inheading' => _request('heading')));
		foreach ($pages as $page) {
			// On ne liste pas les pages nécessitant un login
			if (! HeadingElementCredentials::canRead ($page->public_id_hei)){
				continue;
			}
			// On ne liste pas non plus les éléments si la page indique noindex, nofollow ou noarchive
			if ($page->robots_hei !== null) {
				continue;
			}
			$url = $xml->createElement('url');
			$url = $urlset->appendChild($url);
			
			$loc = $xml->createElement('loc');
			$loc = $url->appendChild($loc);
			
			$page_url = $xml->createTextNode(_url('heading||', array('public_id'=>$page->public_id_hei)));
			$page_url = $loc->appendChild($page_url);
			
			$lastmod = $xml->createElement('lastmod');
			$lastmod = $url->appendChild($lastmod);

			$dateParts = explode (' ', $page->date_update_hei);
			$page_last_mod = $xml->createTextNode($dateParts[0]);
			$page_last_mod = $lastmod->appendChild($page_last_mod);
		}
		return _arContent($xml->saveXML(), array ('filename'=>'sitemap.xml'));
	}

	/**
	 * Modification d'informations sur les pages pour le référencement
	 *
	 * @return CopixActionReturn
	 */
	public function processEditElements () {
		if (! (CopixAuth::getCurrentUser ()->testCredential ('basic:admin') || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'._request ('heading', 0)) || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'._request ('inheading', 0)))) {
	   		throw new CopixCredentialException ('basic:admin');
    	}
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Modification des éléments';
		
		// Modification des éléments envoyés par $_POST
		if (CopixRequest::Exists('elements')) {
			$ppo->message = $this->_doEditElements();
		}
		// Ordres de tri possibles
		$sortOptions = array (
			'caption_hei' => 'Titre',
			'url_id_hei' => 'URL',
			'date_create_hei' => 'Date de création',
			'date_update_hei' => 'Date de modification',
			'published_date_hei' => 'Date de publication'
		);
		$statusOptions = _class('HeadingElementStatus')->getList ();
		// Valeurs par défaut
		$options = _Ppo(array (
			'searchTitle' => 'TOUT',
			'searchTitleMenu' => 'TOUT',
			'searchURL' => 'TOUT',
			'searchDescription' => 'TOUT',
			'displayName' => false,
			'displayTitle' => true,
			'displayTitleMenu' => true,
			'displayURL' => true,
			'displayDescription' => true,
			'sortBy' => 'caption_hei',
			'inheading' => CopixRequest::getInt('heading', 0),
			'status_hei' => HeadingElementStatus::PUBLISHED,
			'nbrParPage' => 10,
			'page' => CopixRequest::getInt ('page', 1)
		));
		$sessionOptionName = 'heading|seo|options';
		// Récupération des options depuis la session ou $_POST
		if (CopixSession::exists ($sessionOptionName)) {
			$options = _sessionGet ($sessionOptionName);
			$options->page = CopixRequest::getInt('page', $options->page);
		}
		if (_request('submitfilter')) {
			foreach ($options as $key => $value) {
				if (is_bool ($value)) {
					$options->$key = CopixRequest::exists ($key);
				} elseif (is_int($value)) {
					$options->$key = CopixRequest::getInt ($key, $value);
				} else {
					$options->$key = _request ($key, $value);
				}
			}
			$options->page = 1;
		}
		_sessionSet ($sessionOptionName, $options);
		
		
		// Si tous les champs sont décochés, on force le premier pour éviter une page vide
		if (((int)$options->displayTitle + (int)$options->displayURL + (int)$options->displayDescription) == 0) {
			$options->displayTitle = true;
		}
		
		// La clé de recherche peut être forgée
		if (!in_array ($options->sortBy, array_keys ($sortOptions))) {
			$options->sortBy = $defaults['sortBy'];
		}
		// La clé de statut peut être forgée
		if (!in_array ($options->status_hei, array_keys ($statusOptions))) {
			$options->status_hei = $defaults['status_hei'];
		}
		// Récupération des pages
		$search = array ();
		if ($options->searchName != 'TOUT') {
			$search['name'] = ($options->searchName == 'OUI');
		}
		if ($options->searchTitle != 'TOUT') {
			$search['title'] = ($options->searchTitle == 'OUI');
		}
		if ($options->searchURL != 'TOUT') {
			$search['url'] = ($options->searchURL == 'OUI');
		}
		if ($options->searchDescription != 'TOUT') {
			$search['description'] = ($options->searchDescription == 'OUI');
		}
		$search['inheading'] = $options->inheading;
		$search['status'] = $options->status_hei;
		$pages = _ioClass ('portal|pageservices')->getPublishedPagesList ($options->sortBy, $search);
		
		if (_request('export') == 'csv'){
			return $this->_getCsv($pages);
		}
		
		$ppo->nbrPages = ceil (count ($pages) / $options->nbrParPage);
		// Extraction des éléments à afficher parmi les pages obtenues
		$ppo->elements = array_slice ($pages, ($options->page - 1) * $options->nbrParPage, $options->nbrParPage);
		
		// Passage des options au PPO
		$ppo->sortOptions = $sortOptions;
		$ppo->statusOptions = $statusOptions;
		$ppo->options = $options;
		
		return _arPPO ($ppo, 'heading|seo/pages.php');
	}

	/**
	 * Effectue la modification des pages
	 * 
	 * @return message de confirmation ou d'erreur
	 */
	private function _doEditElements () {
		// Tableau des éléments renvoyés par la page (donc non disabled)
		$elements = _request ('elements', array());
		
		$pagesServices = _ioClass ('PageServices');
		$elementServices = _ioClass ('HeadingElementInformationServices');
		
		$toReturn = '';
		$modified = array ();
		$lockedElements = array ();
		
		// boucle sur les pages à modifier
		foreach ($elements as $id_helt => $data) {
			$page = $pagesServices->getById ($id_helt);
			
			// Si quelqu'un a modifié l'élément, la version max sera différente
			$isLocked = ($elementServices->getMaxVersion ($page->public_id_hei) != $page->version_hei);
			if ($isLocked) {
				$lockedElements[] = $page->caption_hei;
				continue;
			}
			
			// Chaque $key est un nom de propriété de l'objet page
			foreach ($data as $field => $value) {
				if ($page->$field != $value) {
					$modified[$id_helt] = true;
					$page->$field = $value;
				}
			}
			if (array_key_exists ($id_helt, $modified)) {
				// Quand on crée une version, l'élément passe en archive
				$pagesServices->version ($page);
				$elementServices->publishById ($page->id_helt, 'page');
			}
		}
		
		if ($lockedElements) {
			$toReturn .= _tag ('error', array ('message' => $lockedElements, 'title'=>(count($lockedElements)>1 ? 'Les pages suivantes ont été modifiées' : 'La page suivante a été modifiée')." par un autre utilisateur entre-temps, les valeurs saisies n'ont pas pu être enregistrées."));
		}
		$modified = count ($modified);
		if ($modified){
			$toReturn .= _tag("notification", array("title"=>"Enregistrement effectué", "message"=>$modified ." élément".( count($modified)>1 ? 's' : '')." modifié".( count($modified)>1 ? 's' : '')."."));
		}

		return $toReturn;
	}
	
	private function _getCsv ($pPages){
		CopixFile::createDir(COPIX_CACHE_PATH."seo/");
		try{
			CopixFile::delete(COPIX_CACHE_PATH."seo/pages.csv");
		} catch (Exception $e){
			_log('Fichier d\'extract urls '.COPIX_CACHE_PATH."seo/pages.csv supprimé avant nouvelle création", 'debug');
		}
		$csv = new CopixCsv(COPIX_CACHE_PATH."seo/pages.csv");		

		if (!empty($pPages)){
			foreach ($pPages as $page){
				$arLine = array();
				$arLine[] = $page->caption_hei;
				$arLine[] = $page->url_id_hei;
				$csv->addLine($arLine);
			}
		}
		return _arFile(COPIX_CACHE_PATH."seo/pages.csv");
	}
}