<?php
/**
 * @package     cms
 * @subpackage  portal
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Front office sur les pages 
 */
class ActionGroupPortalFront extends CopixActionGroup {
	/**
	 * On vérifie que Heading|| a lancé l'ordre d'affichage des éléments demandés.
	 *
	 * @param string $pActionName
	 */
	public function beforeAction ($pActionName){
		//HeadingFront utilise Copixregistry pour indiquer les public_id dont il a demandé l'affichage
		$front = CopixRegistry::instance ()->get ('headingfront');

		if ($front !== _request('public_id')){
			throw new CopixCredentialException ('basic:admin'); 
		}
	}

	public function processDefault (){
		$editedElement = _ioClass('portal|pageservices')->getByPublicId (_request('public_id'));
		if ($editedElement->type_hei == 'page'){
			return $this->processPage ();
		}
		return $this->processPortlet ();
	}

	public function processPage (){
		//On regarde si le cache existe.
		if ($cacheExists = HeadingCache::exists ($cacheId = '2portal|front|'._request ('public_id').'_'.serialize (_currentUser ()->getGroups ()))){
			$cache = HeadingCache::get ($cacheId);			
		}else{
			$cache = array ();
		}
		if (!$cacheExists){
			CopixHTMLHeader::startListeningForChanges ();			
		}

		$headingElementInformationServices = new HeadingElementInformationServices ();
		$pageServices = new PageServices ();
		$editedElement = $pageServices->getByPublicId (_request('public_id'));
		
		//Calcul du fil d'ariane à partir de hierarchy level.
		$breadcrumb = array();
		if (!$cacheExists){
			$tree = array_reverse( explode('-', $editedElement->hierarchy_hei), true );
			foreach ($tree as $public_id){
				$inherited = false;
				$visibility = $headingElementInformationServices->getVisibility($public_id, $inherited);
				if ($visibility){
					$element = $headingElementInformationServices->get($public_id);
					$breadcrumb[] = array (
						'url' => CopixURL::get ('heading||', array ('public_id' => $public_id, 'caption_hei' => $element->caption_hei)),
						'caption' => $element->caption_hei,
						'element' => $element
					);
				}
			}
			$cache['BREADCRUMB'] = $breadcrumb;
		}

		if (!$cacheExists){
			if (count ($breadcrumb)){
				$breadcrumb[] = array ('url' => CopixConfig::get ('default|homePage'), 'caption' => 'Accueil');
				$breadcrumb = array_values(array_reverse ($breadcrumb, true));
				$responses = _notify ('cms_getbreadcrumb', array ('complexpath' => $breadcrumb, 'element' => $editedElement));
				if (count ($responses->getResponse ()) > 0) {
					$newBreadcrumb = array ();
					foreach ($responses->getResponse () as $response) {
						$newBreadcrumb[(isset ($response['level'])) ? $response['level'] : 0] = $response['breadcrumb'];
					}
					ksort ($newBreadcrumb);
					$breadcrumb = array_pop ($newBreadcrumb);
				}
				// si la page d'accueil n'existe pas, on remplace l'url par #
				$headingservices = new HeadingServices();
				if(is_array($breadcrumb)){
					foreach ($breadcrumb as $key => $entry){
						if(isset($entry['element'])){
							$heading = null;
							try{
								$heading = $headingservices->getByPublicId($entry['element']->public_id_hei);
							}catch(HeadingElementInformationNotFoundException $e){}
							if($heading){	
								if(!isset($heading->home_heading) || !$heading->home_heading){
									$breadcrumb[$key]['showlink'] = false;
								}else{
									$breadcrumb[$key]['url'] = CopixURL::get ('heading||', array ('public_id' => $heading->home_heading, 'caption_hei' => $element->caption_hei));
								}
							}
						}
					}
				}
				_notify ('breadcrumb', array ('complexpath' => $cache['BREADCRUMB'] = $breadcrumb));
			}
		}else{
			//le cache existe, on lance l'évènement attendu
			if (count ($cache['BREADCRUMB'])){
				_notify ('breadcrumb', array ('complexpath' => $cache['BREADCRUMB']));
			}
		}
		
		if (! $cacheExists){
			//description
			$description = str_replace (array("\r", "\n", '"'), array('', ' ', ''), $editedElement->description_hei);
			CopixHTMLHeader::addOthers('<META name="description" content="'. $description .'" />', 'description');
			//keywords			
			$tags_parent_public_id_hei;
			$tags = $headingElementInformationServices->getTags (_request('public_id'), $tags_parent_public_id_hei);
			CopixHTMLHeader::addOthers('<META name="keywords" content="'. implode(" ", $tags) .'" />', 'keywords');
		}
		


		//calcul du breadcrumb
		//récupère le chemin pour aller a la rubrique
		//notification au breadcrumb pour chaque element.
		$ppo = _ppo ();
		
		if (!$cacheExists){
			$ppo->MAIN = $cache['MAIN'] = $editedElement->render (RendererMode::HTML, RendererContext::DISPLAYED);
			$ppo->TITLE_PAGE = $cache['TITLE_PAGE'] = $editedElement->title_hei ? $editedElement->title_hei : $editedElement->caption_hei;
	        $ppo->TITLE_BAR = $cache['TITLE_BAR'] = $editedElement->browser_page ? $editedElement->browser_page : $ppo->TITLE_PAGE;
		}else{
			$ppo->MAIN = $cache['MAIN'];
			$ppo->TITLE_PAGE = $cache['TITLE_PAGE'];
	        $ppo->TITLE_BAR = $cache['TITLE_BAR'];
		}
		
		if (!$cacheExists){
			$cache['HTMLHEADER'] = CopixHTMLHeader::stopListeningForChanges ();
		}else{
			CopixHTMLHeader::applyChanges ($cache['HTMLHEADER']);			
		}

        if ($cacheExists === false){
        	//inutile de refaire un cache s'il existait.
        	if ($editedElement->isCachable ()){
        		HeadingCache::set($cacheId, $cache);        		
        	}
        }

        return _arPpo ($ppo, 'generictools|blanknohead.tpl');
	}
	
	public function processPortlet (){
		$editedElement = _ioClass('portal|portletservices')->getHeadingElementPortletByPublicId (_request('public_id'));		

		//calcul du breadcrumb
		//récupère le chemin pour aller a la rubrique
		//notification au breadcrumb pour chaque element.
		$ppo = _ppo ();
		$ppo->MAIN = $editedElement->render (RendererMode::HTML, RendererContext::DISPLAYED);
		$ppo->TITLE_PAGE = $editedElement->caption_hei;
		return _arPpo ($ppo, 'generictools|blanknohead.tpl');
	}
}