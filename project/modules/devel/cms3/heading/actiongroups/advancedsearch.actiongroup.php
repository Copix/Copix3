<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Recherche avancée du CMS
 * 
 * @package cms
 * @subpackage heading
 */
class ActionGroupAdvancedSearch extends CopixActionGroup {
	
    protected function _beforeAction ($pAction) {
    	if (! (CopixAuth::getCurrentUser ()->testCredential ('basic:admin') || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'._request ('heading', 0)) || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'._request ('inheading', 0)))) {
	   		throw new CopixCredentialException ('basic:admin');
    	}
		CopixPage::add ()->setIsAdmin (true);
		if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
		_ioClass ('HeadingElementInformationServices')->breadcrumbAdmin ();
    }
	
	
	/**
	 * Modification d'informations sur les pages pour le référencement
	 *
	 * @return CopixActionReturn
	 */
	public function processShowElements () {
		_notify ('breadcrumb', array ('path' => array ('heading|advancedsearch|' => 'Recherche avancée')));
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Recherche avancée d\'éléments';
		
		//listes des status
		$statusOptions = _class('HeadingElementStatus')->getList ();
		//liste des types d'éléments
		$typesOptions = array();
		foreach (_class('HeadingElementType')->getList () as $key => $type){
			$typesOptions[$key] = $type['caption'];
		}
		
		// Valeurs par défaut
		$options = _Ppo(array (
			'status_hei' => '',
			'caption_hei' => '',
			'type_hei' => '',
			'nbrParPage' => 20,
			'page' => CopixRequest::getInt ('page', 1),
			'inheading' => CopixRequest::getInt ('heading', 0),
			'resolve_public_id' => '',
			'content' => '',
			'sort' => 'caption_hei',
			'sortOrder' => 'DESC',
			'url_id_hei' => ''
		));
		$sessionOptionName = 'heading|advancedSearch|options';
		// Récupération des options depuis la session ou $_POST
		if (CopixSession::exists ($sessionOptionName)) {
			$options = _sessionGet ($sessionOptionName);
			$options->page = CopixRequest::getInt('page', $options->page);
		}
		$isSubmitted = !(array_keys (CopixRequest::asArray ()) === array('module', 'group', 'action') || array_keys (CopixRequest::asArray ()) === array('module', 'group', 'action', 'inheading', 'page'));
		if ($isSubmitted) {
			foreach ($options as $key => $value) {
				if (is_bool ($value)) {
					$options->$key = CopixRequest::exists ($key);
				} elseif (is_int($value)) {
					$options->$key = CopixRequest::getInt ($key, $value);
				} else {
					$options->$key = _request ($key, $value, false);
				}
			}
			if(!CopixRequest::exists('page')){
				$options->page = 1;
			}
		}
		if ($options->nbrParPage == 0) {
			$options->nbrParPage = 20;
		}
		_sessionSet ($sessionOptionName, $options);

		// Récupération des éléments
		$elements = _ioClass ('heading|advancedsearchservices')->search ($options, ($options->page - 1) * $options->nbrParPage, $options->nbrParPage);
		$ppo->export = false;
	
		if ($options->type_hei){
			$ppo->export = true;
			foreach ($options->type_hei as $type){
				if (!HeadingElementServices::call ($type, 'canExport')){
					$ppo->export = false;
					break;
				}
			}
		} 
		$ppo->nbElements = $elements['count'];
		$ppo->nbrPages = ceil ($elements['count'] / $options->nbrParPage);
		$ppo->errors = $elements['errors'];
		$ppo->elements = $elements['results'];
		
		// Passage des options au PPO
		$ppo->statusOptions = $statusOptions;
		$ppo->typesOptions = $typesOptions;
		$ppo->options = $options;
		
		return _arPPO ($ppo, 'heading|advancedsearch/advancedsearch.php');
	}

	/**
	 * Recherche l'identifiant public, ou le caption_hei de _request ('search')
	 *
	 * @return CopixActionReturn
	 */
	public function processSearch () {
		// identifiant public passé
		if (is_numeric (_request ('search'))) {
			try {
				$element = _ioClass ('heading|HeadingElementInformationServices')->get (_request ('search'));
			} 
			catch (CopixException $e){
				$ppo = new CopixPPO();
				$dependencies = _ioClass ('heading|HeadingElementInformationServices')->getDependencies (_request ('search'));
				$ppo->dependencies = $dependencies;
				$ppo->public_id = _request ('search');
				$ppo->heading = _request ('heading');
				$ppo->status = _ioClass ('heading|HeadingElementStatus')->getList ();
				return _arPPO($ppo, "advancedsearch/notfound.php");
			}
			return $this->_redirect ($element);

		// nom passé
		} else {
			$options = new CopixPPO (array ('caption_hei' => _request ('search')));
			$elements = _ioClass ('heading|advancedsearchservices')->search ($options);
			if (count ($elements) == 1) {
				return $this->_redirect ($elements[0]);
			} else {
				return _arRedirect (_url ('heading|advancedsearch|ShowElements', array ('caption_hei' => _request ('search'))));
			}
		}
	}

	/**
	 * Redirige sur la page d'admin de l'élément donné
	 *
	 * @param DAORecord $pElement Record de l'élément
	 * @return CopixActionReturn
	 */
	private function _redirect ($pElement) {
		if ($pElement->type_hei == 'heading') {
			return _arRedirect (_url ('heading|element|', array ('heading' => $pElement->public_id_hei)));
		} else {
			return _arRedirect (_url ('heading|element|', array ('heading' => $pElement->parent_heading_public_id_hei, 'selected' => array ($pElement->id_helt . '|' . $pElement->type_hei))));
		}
	}
	
	public function processSearchByPortletType (){
		CopixRequest::assert('portletType');
		$arPortlets = _dao('cms_portlets')->findBy(_daoSP()->addCondition('type_portlet', '=', _request('portletType')))->fetchAll();
		$arIdPage = array();
		$arPage = array();
		
		if (count($arPortlets)){
			foreach ($arPortlets as $portlet){
				if ($portlet->id_page){
					$arIdPage[$portlet->id_page] = $portlet->id_page;
				} 
			}
		}
		
		if (count($arIdPage)){
			foreach ($arIdPage as $id){
				$page = _ioClass('heading|headingelementinformationservices')->getById ($id, 'page');
				$infos = _ioClass('heading|headingelementinformationservices')->get($page->public_id_hei);
				if($infos->status_hei == HeadingElementStatus::PUBLISHED){
					$arPage[$infos->public_id_hei] = $infos;
				}  
			}
		}
		$ppo = _ppo();
		$ppo->TITLE_PAGE = "Liste des pages appelant des portlets de type : "._request('portletType');
		$ppo->arPage = $arPage;
		return _arPPO($ppo, 'advancedsearch/portletsearch.php');
	}
}