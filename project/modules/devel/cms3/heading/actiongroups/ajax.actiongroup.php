<?php
/**
 * @package     cms3
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain VUIDART
 */

/**
 * Ajax
 * 
 * @package cms
 * @subpackage heading
 */
class ActionGroupAjax extends CopixActionGroup {
	
	/**
	 * Classe de services sur les Elements de rubriques
	 *
	 * @var HeadingElementInformationServices
	 */
	protected $headingElementInformationServices;
	
	/**
	 * Création des classes utilitaires
	 *
	 * @param string $pActionName
	 */
	public function beforeAction ($pActionName){
		//création des classes de services utiles.
		$this->headingElementInformationServices = new HeadingElementInformationServices ();
	}
	
	public function processGetElementChooserNode (){
		$public_id = _request('public_id_hei');
		$formId = _request('formId');
		$selectedIndex = _request('searchIndex', _request('selectedIndex'));
		$open = _request('open');
		$options = _request('options');
		$options['openElement'] = (isset ($options['openElement'])) ? $options['openElement'] : null;
		$filter = $options['filter'] == '' ? array() : explode(":", $options['filter']);;
		$showAnchor = array_key_exists('anchor', $options) && $options['anchor'] ? $options['anchor'] : 'false';
		
		
		$arHeadingElementTypes = _class ('heading|HeadingElementType')->getList ();
		$toReturn = "<script type='text/javascript'>";

		//open on affiche toute la branche
		if ($open == 'true'){
			//construction de l'arbre
			$children = $this->headingElementInformationServices->getElementChooserTree ($public_id);
			$selectedElement = $this->headingElementInformationServices->get ($selectedIndex ? $selectedIndex : 0);
			$selectedPath = explode ('-', $selectedElement->hierarchy_hei);
			//$openPath est le chemin que l'on décide d'ouvrir à l'appel de l'element Chooser : par exemple quand à l'appel, on veut ouvrir une rubrique sans la selectionner
			$openPath = array();
			if ($options['openElement']){
				$openElement = $this->headingElementInformationServices->get ($options['openElement']);
				$openPath = explode('-', $openElement->hierarchy_hei);
			}
			$element = $this->headingElementInformationServices->get ($public_id);
			// on n'ouvre pas la branche de l'element selectionné.
			unset($selectedPath[sizeof($selectedPath) - 1]);
			$toReturn .= "nodeArray = new Array();\n";
			if (_ioClass('heading|headingelementchooserservices')->checkFilter($children, $filter)){
				//on ordonne les enfants selon leur display_order_hei
				$order = _ioClass('heading|headingelementchooserservices')->orderChildren ($children);				
				foreach ($order as $child){
					if (_ioClass('heading|headingelementchooserservices')->checkFilter (array($child), $filter)){
						$hasChildren = isset($child->children) && _ioClass('heading|headingelementchooserservices')->checkFilter ($child->children, $filter);
						if ($showAnchor == 'true'  && $child->type_hei == 'page'){
							$arAnchors = _class ('portal|pageservices')->getPageAnchors ($child->id_helt);
							$hasChildren = $hasChildren || !empty($arAnchors);
						}
						$toReturn .= "node".$child->public_id_hei." = tree" . $formId . ".get ('node_" . $formId . "_" . $child->public_id_hei . "');";
						// si le noeud existe de ja on ne le créé pas on l'ouvre
						$toReturn .= "if (!node".$child->public_id_hei."){";
						$toReturn .= "nodeArray.push({" . ($hasChildren ? "'children':true," : "") . "'public_id_hei':'" . $child->public_id_hei . "','open':" . (in_array($child->public_id_hei, $selectedPath)  || in_array($child->public_id_hei, $openPath) ? 'true' : 'false') . ",'caption_hei':'" . addslashes($child->caption_hei) . "'" . ($child->type_hei != "heading" ? ",'icon':'"._resource ($arHeadingElementTypes[$child->type_hei]['icon'])."'" : '') . ",'type_hei':'" . $child->type_hei . "', 'searchIndex':'"._request('searchIndex')."'});\n";
						$toReturn .= "} else {";
						//on ouvre le noeud existant
						if (in_array($child->public_id_hei, $selectedPath)){
							//si le noeud fait partie de la selection on lui specifie le selectedIndex pour qu'il le passe à ses enfants.
							$toReturn .= "node".$child->public_id_hei.".data.searchIndex='$selectedIndex';";
							$toReturn .= "if (!node".$child->public_id_hei.".open){node".$child->public_id_hei.".toggle();}";
						}
						$toReturn .= '}';
					}
				}
			}
			
			//gestion des ancres
			if ($showAnchor == 'true' && $element->type_hei == 'page'){
				$arAnchors = _class ('portal|pageservices')->getPageAnchors ($element->id_helt);
				foreach ($arAnchors as $anchor){
					$portlet = CopixXMLSerializer::unserialize($anchor->serialized_object);
					$params = new CopixParameterHandler();
					$params->setParams($portlet->getOptions ());
					if ($params->getParam('name', null) != null){
						$toReturn .= "nodeArray.push({'public_id_hei':'" . $element->public_id_hei . "#".$params->getParam('name')."','open':false,'caption_hei':'".$params->getParam('name')."','icon':'". _resource('portal|img/icon_anchor.png')."','type_hei':'page'});\n";
					}
				}
			}

			$toReturn .= ($public_id == 0 ? "node = tree" . $formId . ".root;" : "node = tree" . $formId . ".get ('node_" . $formId . "_" . $public_id . "');\n");
			$toReturn .= "createTree (tree" . $formId . ", node, nodeArray, '" . $formId . "', {'filter':'" . $options['filter'] . "','anchor':".$showAnchor.", 'selectedIndex':'"._request('selectedIndex')."', 'searchIndex':'"._request('searchIndex')."', 'openElement':'".$options['openElement']."'});\n";
		} else {
			//on n'affiche pas la branche, celle ci sera appelé plus tard en ajax sur l'evenement onExpand
			$toReturn .= "var node = tree" . $formId . ".get ('node_" . $formId . "_" . $public_id . "');\n";
			$toReturn .= "node.insert({text:\"Chargement...\",icon:Copix.getResourceURL('') + '/js/mootools/img/mootree_loader.gif'});";
		}
		$toReturn .= "</script>";
		
		$ppo = _ppo();
		$ppo->MAIN = $toReturn;
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * 
	 * Preview pour les images dans le imageChooser
	 */
	public function processGetImagePreview (){
		$public_id = _request('public_id_hei', false);
		$view = _request('view');
			
		$ppo = _ppo();
		
		if ($public_id != null){
			
			$results = $this->headingElementInformationServices->getChildrenByType ($public_id, 'image');
			if (empty($results)){
				$results[] = $this->headingElementInformationServices->get($public_id);
			}
			$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);
		
			switch ($view){
				case HeadingElementChooserServices::AFFICHAGE_MINIATURES :
			
					$toReturn = "";
					foreach ($children as $image){
						if (CopixAuth::getCurrentUser()->testCredential("basic:admin") || HeadingElementCredentials::canWrite($image->public_id_hei)){
							$toReturn .= '<div class="' .(sizeof($results) == 1 ? 'elementchooserfileselected elementchooserfileselectedstate' : 'elementchooserfile elementchooserfilenoselectedstate') .'" pih="'.$image->public_id_hei.'" libelle="'.$image->caption_hei.'">';
							$toReturn .= '<img class="imgelementchooserfile" title="'.$image->caption_hei.'" src="'._url('images|imagefront|GetImage', array('id_image'=>$image->id_helt, 'width'=>75, 'height'=>75, 'keepProportions'=>true, 'resizeIfNecessary'=>true), true).'" alt="'.$image->caption_hei.'"/>';
							$toReturn .= '</div>';
						}
					}
							
					$ppo->MAIN = $toReturn;
					return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
				default:
					$ppo->children = $children;
					return _arPPO($ppo, 'images|headingelementimagechooserdetails.php');
			}
		}
		$ppo->MAIN = "Selectionnez une rubrique ou un élément";
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * 
	 * Preview pour les documents dans le documentChooser
	 */
	public function processGetDocPreview (){
		$public_id = _request('public_id_hei', null);
		$view = _request('view');
		
		$ppo = _ppo();
		
		if ($public_id != null){
			
			$results = $this->headingElementInformationServices->getChildrenByType ($public_id, 'document');
			if (empty($results)){
				$results[] = $this->headingElementInformationServices->get($public_id);
			}
			$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);
		
			switch ($view){
				case HeadingElementChooserServices::AFFICHAGE_MINIATURES :
					$toReturn = "";
					$arDocIcons = ZoneHeadingElementChooser::getArDocIcons();
					foreach ($children as $document){
						if (CopixAuth::getCurrentUser()->testCredential("basic:admin") || HeadingElementCredentials::canWrite($document->public_id_hei)){
							$documentInfo = _ioClass('document|documentservices')->getByPublicId($document->public_id_hei);
							$toReturn .= '<div style="line-height: normal;" class="' .(sizeof($results) == 1 ? 'elementchooserfileselected elementchooserfileselectedstate' : 'elementchooserfile elementchooserfilenoselectedstate') .'" pih="'.$document->public_id_hei.'" libelle="'.$document->caption_hei.'" title="'.$document->caption_hei.'">';
							$extension = pathinfo($documentInfo->file_document, PATHINFO_EXTENSION);
							$toReturn .= '<img class="docelementchooserfile" src="'._resource('heading|'.(array_key_exists($extension, $arDocIcons) ? $arDocIcons[$extension] : 'img/docicons/unknow.png')).'" />';
							$toReturn .= '<span style="font-size: 0.9em;line-height: 10px;">';
							$toReturn .= strlen($document->caption_hei) > 15 ? substr($document->caption_hei, 0 ,12) . '...' : $document->caption_hei;
							$toReturn .= '</span>';
							$toReturn .= '</div>';
						}
					}
										
					$ppo->MAIN = $toReturn;
					return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
				default:
					$ppo->children = $children;
					$ppo->arDocIcons = ZoneHeadingElementChooser::getArDocIcons();
					return _arPPO($ppo, 'document|headingelementdocumentchooserdetails.php');
			}
		
		}
		$ppo->MAIN = "Selectionnez une rubrique ou un élément";
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * 
	 * Preview pour les articles dans le articlechooser
	 */
	public function processGetArticlePreview (){
		$public_id = _request('public_id_hei', null);
		
		$ppo = _ppo();
		
		if ($public_id != null){		
			$results = $this->headingElementInformationServices->getChildrenByType ($public_id, 'article');
			if (empty($results)){
				$results[] = $this->headingElementInformationServices->get($public_id);
			}
			$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);

			$ppo->formId = _request('formId');
			$ppo->children = $children;
			return _arPPO($ppo, 'articles|headingelementarticlechooserdetails.php');		
		}
		$ppo->MAIN = "Selectionnez une rubrique ou un élément";
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * 
	 * Preview pour les articles dans le articlechooser
	 */
	public function processGetMediaPreview (){
		$public_id = _request('public_id_hei', null);
		
		$ppo = _ppo();
		
		if ($public_id != null){		
			$results = $this->headingElementInformationServices->getChildrenByType ($public_id, _request('type', 'media'));
			if (empty($results)){
				$results[] = $this->headingElementInformationServices->get($public_id);
			}
			$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);

			$ppo->formId = _request('formId');
			$ppo->children = $children;
			return _arPPO($ppo, 'medias|headingelementmediachooserdetails.php');		
		}
		$ppo->MAIN = "Selectionnez une rubrique ou un élément";
		return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * Action group de recherche d'element du elementChooser
	 *
	 * @return unknown
	 */
	public function processQuickSearchAutoCompleter (){
		if(($search = _request('search', false)) != false){
			
			$query = "SELECT * FROM cms_headingelementinformations
						WHERE caption_hei LIKE :search and ( status_hei = ".HeadingElementStatus::PUBLISHED." OR status_hei = 0)";
		
			$args = array(':search'=>$search.'%');
				
			if (($filter = _request('filter')) != null){
				$query .= " AND type_hei = :filter";
				$args [':filter'] = $filter;
			}
				
			$results = _doQuery($query, $args);

			$toReturn = array();
			foreach ($results as $item){
				if ((!array_key_exists($item->public_id_hei, $toReturn) || (array_key_exists($item->public_id_hei, $toReturn) &&
				$toReturn[$item->public_id_hei]->status_hei != HeadingElementStatus::PUBLISHED))){
					$toReturn[$item->public_id_hei] = $item->caption_hei . ' - ' . $item->type_hei;
				}
			}
			$ppo = _ppo();
			$ppo->MAIN = json_encode(array_values($toReturn));
			return _arDirectPPO($ppo, 'generictools|blanknohead.tpl');
		}
		return _arNone();
	}
	
	public function processSelectNode (){
		if (($public_id = _request('public_id')) == null){
			$result = _ioClass ('heading|headingelementinformationservices')->find(CopixRequest::asArray('caption_hei', 'type_hei'))->fetchAll();
			$public_id = $result[0]->public_id_hei;
		}
		CopixRequest::set('searchIndex', $public_id);
		CopixRequest::set('public_id_hei', 0);
		CopixRequest::set('open', 'true');
		$options = array();
		$options['filter'] = _request('filter');
		CopixRequest::set('options', $options);
		return $this->processGetElementChooserNode();
	}
	
	/**
	 * Actiongroup permettant d'ajouter un bookmark
	 *
	 * @return none
	 */
	public function processAddBookMark (){
		if (($newBookmark = _request ('newBookmark', null)) != null) {
			$bookmarks = CopixUserPreferences::get ('heading|bookmark', '');
			$toReturn = false;
			if ($bookmarks != '') {
				$arBookmarks = explode (';', $bookmarks);
				if (array_search ($newBookmark, $arBookmarks) === false){
					$toReturn = true;
					$bookmarks .= ($bookmarks != '') ? ';' . $newBookmark : $newBookmark;
				}
				CopixUserPreferences::set ('heading|bookmark', $bookmarks);
			} else {
				$toReturn = true;
				CopixUserPreferences::set ('heading|bookmark', $newBookmark);
			}
			if ($toReturn) {
				$element = _ioClass('heading|headingelementinformationservices')->get ($newBookmark);
				$ppo = _ppo ();
				$ppo->MAIN = CopixZone::process ('heading|HeadingBookMark', array ('element' => $element));
				return _arDirectPPO ($ppo, 'generictools|blank.tpl');
			}			
		}
		return _arNone ();
	}
	
	/**
	 * Actiongroup permettant de supprimer un bookmark
	 *
	 * @return none
	 */
	public function processDeleteBookMark (){
		if (($bookmarkToDelete = _request('bookmarkToDelete', null)) != null){
			$bookmarks = CopixUserPreferences::get('heading|bookmark', '');
			$arBookmarks = explode (';', $bookmarks);
			
			
			if (!empty($arBookmarks)){
				foreach ($arBookmarks as $key => $bookmark){
					if ($bookmark == $bookmarkToDelete){
						unset($arBookmarks[$key]);
					}
				}
			}
			$bookmarks = implode(';', $arBookmarks);
			if ($bookmarks){
				CopixUserPreferences::set('heading|bookmark', $bookmarks);			
			} else {
				CopixUserPreferences::delete('heading|bookmark');
			}
			return _arString("OK");
		}
		return _arNone();
	}
	
	
	public function processUpdateSiteMapId (){
		$ppo = new CopixPPO ();
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = $this->_getEditedElement();
		}
		$portlet->setEtat (Portlet::UPDATED);
		$options = CopixRequest::asArray('sitemapId');
		$portlet->setOptions ($options);
		
		return _arNone();
	}
	
	protected function _getEditedElement (){
		CopixRequest::assert ('editId');
		if (!$element =	CopixSession::get ('portlet|edit|record', _request ('editId'))){
			throw new CopixException ('Portlet en cours de modification perdu');
		}
		return $element;
	}

	/**
	 * Affiche les dépendances
	 *
	 * @return CopixActionReturn
	 */
	public function processGetDependencies () {
		$ppo = new CopixPPO (array ('MAIN' => CopixZone::process ('heading|headingelement/headingelementdependencies', array ('public_id' => _request ('public_id')))));
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
    
    public function processMoveWidget(){
    	$arPositions = (array)json_decode(CopixUserPreferences::get('heading|dashboard|positions'), true);
    	_dump($arPositions, CopixUserPreferences::get('heading|dashboard|positions'));
    	/*
    	     	$column = (array)$arPositions[_request('from')];
    	unset($column[array_search(_request("id"), $column)]);   
    	$arPositions[_request('from')] = $column;
    	 */
    	unset($arPositions[_request('from')][array_search(_request("id"), $arPositions[_request('from')])]);   	
    	if (!_request('position')){  		
    		array_push($arPositions[_request('column')], _request("id"));
    	} else {
    		$newColumn = array();
    		foreach ($arPositions[_request('column')] as $widget){
    			if ($widget == _request("positionid")){
    				if (_request('position') == "after"){
    					$newColumn[] = $widget;
    					$newColumn[] = _request("id");
    				} else {
    					$newColumn[] = _request("id");
    					$newColumn[] = $widget;
    				}
    			} else {
    				$newColumn[] = $widget;
    			}
    		}
    		$arPositions[_request('column')] = $newColumn;
    	}
    	_dump($arPositions);
    	CopixUserPreferences::set('heading|dashboard|positions', json_encode($arPositions));
    	return _arNone();
    }
}