<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author  Gérald Croës
 */

/**
 * Renderer HTML pour les pages de contenu
 */
class pageHTMLRenderer extends PageRenderer {

	public function render (Page $pPage, $pMode, $pContext, $pExtra = array ()){
		parent::render ($pPage, $pMode, $pContext, $pExtra);
		if (in_array ($pContext, array (RendererContext::DISPLAYED, RendererContext::DISPLAYED_ADMIN, RendererContext::SEARCHED))){
			return $this->_renderDisplay (array_merge ($pExtra, array ('renderer_context' => $pContext)));
		}else{
			return $this->_renderUpdate ($pExtra);
		}
	}

	protected function _renderUpdate ($pExtra){
		//Inclusion de la librarie JS pour le déplacement des portlets
		_tag ('mootools');
		CopixHtmlHeader::addJSLink (_resource ('|js/tools.js'));
		/*CopixHtmlHeader::addCSSLink (_resource ('|styles/pageedit.css'));*/

		//création du template qui va accueillir les portlets
		$tpl = new CopixTpl ();

		$vars = $this->_extractVarsFromTemplateFile (CopixTpl::getFilePath ("portal|pagetemplates/" . $this->_page->getTemplate ()));
		foreach ($vars as $varName){
			$columnContent = $this->_renderPortlet($varName, $pExtra, RendererContext::UPDATED);			
			$tpl->assign ($varName, $columnContent);
		}

		if (!file_exists (CopixTpl::getFilePath ("portal|pagetemplates/" . $this->_page->getTemplate ()))){
			return '<div id="CMSPage">'.'Impossible d\'afficher la page, le modèle n\'existe pas.['.$this->_page->getTemplate ().']</div>';
		}

		$pageErrors = _tag ('error', array ('message'=>$pExtra['errors']));

		$addPortletMenu = CopixZone::process('portal|addPortletMenu', array('editId'=>$this->_params['editId']));

		return '<div id="CMSPage">'.$pageErrors.$tpl->fetch ("portal|pagetemplates/" . $this->_page->getTemplate ()).$addPortletMenu.'<div class="clear"></div></div>';
	}

	/**
	 * Retourne le rendu de la page en mode display
	 *
	 * @param Array $pExtra
	 * @return String
	 */
	protected function _renderDisplay ($pExtra){
		//création du template qui va accueillir les portlets
		$tpl = new CopixTpl ();
		$vars = $this->_extractVarsFromTemplateFile (CopixTpl::getFilePath ("portal|pagetemplates/" . $this->_page->getTemplate ()));
		foreach ($vars as $varName){
			$columnContent = $this->_renderPortlet($varName, $pExtra);
			$tpl->assign ($varName, $columnContent);
		}

		//On regarde s'il faut afficher le bouton de modification
		if ($this->_page->getEtat () != Page::UPDATE){
			$pageMenu = "";
		}else{
			CopixHtmlHeader::addCSSLink (_resource ('|styles/pageedit.css'));
			CopixHtmlHeader::addJSLink (_resource ('|js/tools.js'));
			$editButton = '';
			$pageMenu = CopixZone::process('portal|pageupdateheadermenu', array('public_id_hei'=>$this->_page->public_id_hei, 'renderContext'=>RendererContext::DISPLAYED_ADMIN, 'page_id'=>$this->_page->id_page, 'parent_public_id'=>$this->_page->parent_heading_public_id_hei, 'caption_hei'=>$this->_page->caption_hei));
		}
		
		$strMore = '';

		$strHeader = '';
		//Gestion du rendu complexe des formulaires
		if (CopixRegistry::instance()->exists('partialMode', 'cms|form') === true) {

			$builder = new Form_Builder();
			$cmsForm = $builder->get(CopixRegistry::instance()->get('idCmsForm', 'cms|form'));

			//Ajout de l'entête de formulaire avec les éventuelles erreurs
			$strHeader = $cmsForm->getHeader() . _tag ('error', array ('message'=>$cmsForm->getFormErrors()));

			//Ajout du footer de formulaire
			$strMore .= $cmsForm->getFooter();
		}
		
		if ($pExtra['renderer_context'] == RendererContext::DISPLAYED && $this->_page->breadcrumb_type_page == PageServices::BREADCRUMB_TYPE_AUTO) {
			$hei = _ioClass ('heading|HeadingElementInformationServices');
			$path = $hei->getHeadingPath ($this->_page->public_id_hei);
			array_shift ($path);
			$path = array_reverse ($path);
			$breadcrumb = array ();
			foreach ($path as $public_id) {
				if (HeadingElementServices::call ('heading', 'getByPublicId', $public_id)->breadcrumb_show_heading == HeadingServices::BREADCRUMB_SHOW) {
					$heading = $hei->get ($public_id);
					$breadcrumb[_url ('heading||', array ('public_id' => $heading->public_id_hei))] = $heading->caption_hei;
				}
			}
			_notify ('breadcrumb', array ('path' => $breadcrumb));
		}

		//affichage des données
		return '<div id="CMSPage">'.$strHeader.$pageMenu.$tpl->fetch ("portal|pagetemplates/" . $this->_page->getTemplate ()).$strMore.'</div>';
	}

	/**
	 * Rendu des fenetres des boutons "ajouter" dans une page
	 *
	 * @param String $pVariableName
	 * @return String
	 */
	private function _renderAddPortletsFor ($pVariableName){
		return "<div class='ajoutPortlet clear' id='$pVariableName'></div>";
		
		$listContent = '';
		$groups = array();
		foreach (_class ('PortletServices')->getList () as $portletId=>$portletInformations){
			if (array_key_exists('group', $portletInformations)){
				if (!array_key_exists($portletInformations['group'], $groups)){
					$groups[$portletInformations['group']] = array();
				}
				$groups[$portletInformations['group']][$portletId] = $portletInformations;
			}
			else {
				if (!array_key_exists('special', $groups)){
					$groups['special'] = array();
				}
				$groups['special'][$portletId] = $portletInformations;
			}

		}

		$arPortletsInfos = array();
		foreach (_class ('PortletServices')->getList () as $informations){
			$arPortletsInfos[$informations['portlettype']] = $informations;
		}

		$tpl = new CopixTpl();
		$tpl->assign ('arPortletsInfos', $arPortletsInfos);
		$tpl->assign ('portletsInformations', _class('portletservices')->getList());
		$tpl->assign ('portletClipBoard', CopixSession::get('portletClipBoard', 'cms3'));
		$tpl->assign ('variableName', $pVariableName);
		$tpl->assign ('groups', $groups);
		$tpl->assign ('editId', $this->_params['editId']);
		return $tpl->fetch ('addportlet.php');
	}
	
	private function _renderPortlet ($pVarName, $pExtra, $pRenderContext = RendererContext::DISPLAYED){
		$columnContent = '';

		foreach ($this->_page->getPortletsIn ($pVarName) as $portlet){
            if ($pRenderContext !== RendererContext::DISPLAYED) {
                if(!CopixSession::exists('portal|'.$portlet->getRandomId(), _request ('editId'))){
                    CopixSession::set('portal|'.$portlet->getRandomId(), $portlet, _request ('editId'));
                }
                else{
                    $portlet = CopixSession::get('portal|'.$portlet->getRandomId(),  _request ('editId'));
                }
            }

			//permet de placer l'ecran face à la portlet qu'on en ajoute une au lieu de renvoyer constemment au haut de page.
			// Goulven : une ancre "a name" est inutile si le div de la portlet a un attribut id.
			$ancre = "<a name='".$portlet->getRandomId()."'></a>";

			//la portlet Colonne est une portlet particulière qui contient des portlets, comme une page
			//mais seule la page connait toutes les portlets affichées, on est obligé de faire le traitement de contenu à ce niveau, au niveau du renderer de la page.
			if (isset($portlet->type_portlet) && $portlet->type_portlet == "PortletColumns"){
				$portlet->setLeftUpdateContent ($this->_renderPortlet (PortletColumns::LEFT_COLUMN . $portlet->getRandomId(), $pExtra, $pRenderContext));
				$portlet->setRightUpdateContent ($this->_renderPortlet (PortletColumns::RIGHT_COLUMN . $portlet->getRandomId(), $pExtra, $pRenderContext));
				$columnContent .= $ancre.$portlet->render ($this->_rendererMode, $this->_rendererContext);
			} else {
				$columnContent .= $ancre.$portlet->render ($this->_rendererMode, $this->_rendererContext);
			}
			if ($pRenderContext == RendererContext::UPDATED){
				//création de la toolbar associée a la portlet
				$htmlToolBar = '<div class="editPortletInner"><table style="width:100%" cellspacing="0"><tr><td style="width:60px;">';
				switch ($portlet->getEtat ()){
					case Portlet::SAVED :
						$htmlToolBar  .= '<a id="update_link_'.$portlet->getRandomId().'" title="Aperçu" href="'._url ('admin|displayPortlet', array ('id'=>$portlet->getRandomId (), 'editId'=>$pExtra['editId'], 'etat'=>Portlet::DISPLAYED)).'"><img src="'._resource ('img/tools/show.png').'" alt="Aperçu" /></a>';
						$style = "display:none";
						break;
					case Portlet::DISPLAYED :
						$htmlToolBar  .= '<a id="update_link_'.$portlet->getRandomId().'" title="Modifier" href="'._url ('admin|displayPortlet', array ('id'=>$portlet->getRandomId (), 'editId'=>$pExtra['editId'], 'etat'=>Portlet::SAVED)).'"><img src="'._resource ('img/tools/update.png').'" alt="Modifier" /></a>';
						$style = "display:none";
						break;
					default :
						$htmlToolBar  .= '<a id="update_link_'.$portlet->getRandomId().'" title="Sauvegarder" href="'._url ('admin|savePortlet', array ('id'=>$portlet->getRandomId (), 'editId'=>$pExtra['editId'])).'"><img src="'._resource ('img/tools/save.png').'" alt="Sauvegarder" /></a>';
						$style = "";
				}
				$htmlToolBar .= '<a id="cancel_link_'.$portlet->getRandomId().'" title="Annuler" href="'._url ('admin|cancelPortlet', array ('id'=>$portlet->getRandomId (), 'editId'=>$pExtra['editId'])).'" style="'.$style.'"><img src="'._resource ('img/tools/undo.png').'" alt="Annuler" /></a>';			
				$htmlToolBar .= '<a title="Copier dans le presse papier. L\'élément copié sera accessible via le bouton Ajouter." href="'._url ('admin|copyPortlet', array ('id'=>$portlet->getRandomId (), 'editId'=>$pExtra['editId'])).'"><img src="'._resource ('img/tools/clone.png').'" alt="Copier dans le presse papier" /></a>';
				
				$handleId = 'handle_'.$portlet->getRandomId ();
				$htmlToolBar .= '</td><td id="'.$handleId.'" class="dragHandle">&nbsp;</td>';
				$htmlToolBar .= '<td style="width:20px;text-align:right;"><a id="clickerRemovePortlet'.$portlet->getRandomId().'" title="Supprimer" href="javascript:;"><img src="'._resource ('img/tools/close.png').'" alt="Supprimer" /></a></td></tr></table></div>';

				
				CopixHTMLHeader::addJSDOMReadyCode("	
					createPortletDivMenu ('".$portlet->getRandomId ()."', '".addslashes($htmlToolBar)."');
					createDraggablePortlet ('".$portlet->getRandomId ()."', '".$handleId."', '".$pExtra['editId']."');	
				");		
				
				$tplRemovePortlet = new CopixTpl();
				$tplRemovePortlet->assign('randomId', $portlet->getRandomId ());
				$tplRemovePortlet->assign('editId', $pExtra['editId']);	
				$columnContent .= _tag("copixwindow", array('id' => 'copixwindowremoveportlet'.$portlet->getRandomId(), 'modal'=>true, 'title'=>'Confirmation', 'fixed' => true, 'clicker'=>"clickerRemovePortlet".$portlet->getRandomId()), $tplRemovePortlet->fetch("removeportletwindow.php"));
					
			}						
		}
		if ($pRenderContext == RendererContext::UPDATED){
			$columnContent .= $this->_renderAddPortletsFor ($pVarName);
		}
		return $columnContent;
	}
}