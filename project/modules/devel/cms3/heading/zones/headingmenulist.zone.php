<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * Affiche un menu hiérarchique
 * @package     cms
 * @subpackage  heading
 */
class ZoneHeadingMenuList extends CopixZone {
	
	function _createContent (& $toReturn) {
		//On récupère les valeurs des éléments critiques.
		$typeHEM = $this->getParam ('type_hem');
		$paramMenu = $this->getParam ('menu', null);
        $type_hei = $this->getParam('type_hei', array());

		//public_id de l'element à patir duquel on fabrique le menu
		// soit le public_id en request, sinon le public_id en parametre,
		//sinon en edition ou creation d'element, le public_id de la rubrique de l'element en request
		//sinon en edition ou creation d'element, le public_id de la rubrique de l'element en session
		$publicId = _request('public_id', $this->getParam('public_id',_request('heading', CopixSession::get('heading', _request('editId')))));
		$templateFile = $this->getParam('template', 'heading|menu/headingmenulistnavigation.php');
		
		$cacheEnabled = !$this->getParam ('noCache', 0);

		$cacheId = CopixURL::getRequestedProtocol ().'|'.$typeHEM.'|'.serialize ($paramMenu).'|'.$publicId.'|'.$templateFile.serialize (_currentUser ()->getGroups ());
		if ($cacheEnabled && HeadingCache::exists ($cacheId)){		
			$cache = HeadingCache::get ($cacheId);
			CopixHTMLHeader::applyChanges ($cache['HTMLHEADER']);
			$toReturn = $cache['CONTENT'];
			_notify('cms_display', array('type'=>'menu', 'element'=>$cache, 'type_menu'=>$typeHEM));
			return true; 
		}
		
		CopixHTMLHeader::startListeningForChanges ();
		
		/****init des variables****/
		//niveau
		$level = 0;
		//profondeur de menu
		$depth = 1;
		//classe css pour les style du menu
		$classe = null;
		//element à patir duquel on fabrique le menu
		$heading = null;
		//template d'affichage du menu
		$template = null;
		//arbre des elements du menu
		$tree = null;
		
		$menu = null;

		//on verifie qu'un type de menu est passé en parametre : NAVIGATION, FOOTER ...
		if ($this->getParam ('type_hem') != null){
			//si on n'est pas dans le CMS, si on n'a pas de publicId
			if ($publicId == null){
				//on recherche si un menu est défini pour le module en cours
				$menu = _ioClass('heading|headingelementmenuservices')->getModuleMenu ($this->getParam ('type_hem'));
			}
		
			//pas de menu definit pour le module en cours, on prends alors la racine du CMS
			if ($menu == null){
				$publicId = $publicId == null ? 0 : $publicId;
				//on peut directement passer à la zone un menu à afficher, menu de type Record
				try {
					$menu = ($paramMenu === null ? _ioClass('heading|headingelementmenuservices')->getHeadingElementMenu ($publicId, $typeHEM) : $paramMenu);
				} catch (CopixException $e) {
					_log("Impossible d'afficher le menu ".$this->getParam ('type_hem'), 'errors', CopixLog::WARNING);
					$toReturn = '';
					CopixHTMLHeader::stopListeningForChanges ();					
					return false;
				}
			}
			
			if ($menu){
				//si on ne veut pas de menu pour cet element.
				if($menu->is_empty_hem){
					$toReturn = '';
					CopixHTMLHeader::stopListeningForChanges ();
					return false;
				}
				
				// si on affiche directement une portlet
				if ($menu->portlet_hem){
					// Le try/catch se fait dans l'autre méthode
					$toReturn = $this->_getPortletMenu ($menu->public_id_hem);
					CopixHTMLHeader::stopListeningForChanges ();
					return true;
				}
				$publicId = $menu->public_id_hem;
				$depth = $menu->depth_hem;
				$level = $menu->level_hem;
				$classe = $menu->class_hem;
				$template = $menu->template_hem;
				try {
					//si le niveau est différent de 0 c'est qu'on prends un element au dessus de l'element de départ
					if ($level != 0) {
						$heading = _ioClass('HeadingElementInformationServices')->getParentAtLevel($publicId, $level);
						$publicId = $heading->public_id_hei;
					} else {
						$heading = $this->_getHeadingElement ($publicId);
					}
					$tree = _ioClass('HeadingElementInformationServices')->getTree ($publicId, $depth, true, $type_hei);
				} catch (CopixException $e) {
					_log("Impossible de récupérer l'arborescence pour la portlet de menu ".$this->getParam ('type_hem'), 'errors', CopixLog::WARNING);
					$toReturn = '';
					CopixHTMLHeader::stopListeningForChanges ();
					return false;
				}
			}
		}
		
		$currentActionGroup = _request('module').'/'._request('group').'/'._request('action');
		
		//Si aucun arbre de menu n'a été défini on retourne vide.
		if ($tree == null && !($currentActionGroup == 'heading/element/default' || $currentActionGroup == 'heading/element/')) {
			$toReturn = '';
			CopixHTMLHeader::stopListeningForChanges ();
			return false;
		} else if ($tree == null){
			$tree = array();
		}
		
		// les liens peuvent avoir des options spéciales
		$hei = _ioClass ('heading|HeadingElementInformationServices');
		foreach ($tree as &$item) {
			if ($item->type_hei == 'link') {
				$completeItem = _ioClass ('heading|LinkServices')->getByPublicId ($item->public_id_hei);
				if ($completeItem->linked_public_id_hei != null) {
					$linkedItem = $hei->get ($completeItem->linked_public_id_hei);
					if ($completeItem->caption_link == LinkServices::CAPTION_ELEMENT) {
						$item->caption_hei = $linkedItem->caption_hei;
					}
					if ($completeItem->url_link == LinkServices::URL_ELEMENT) {
						$item->path = _url ('heading||', array ('public_id' => $linkedItem->public_id_hei));
					}
				}
			}
		}

		//on affiche le menu
		$tpl = new CopixTpl ();
		$tpl->assign ('tree', $tree);
		$tpl->assign ('class', $classe);
		$tpl->assign ('heading', $heading);
		$tpl->assign ('type', $this->getParam ('type_hem', false) && ($currentActionGroup == 'heading/element/default' || $currentActionGroup == 'heading/element/') ? _class('heading|headingelementmenuservices')->getCaption ($this->getParam ('type_hem')) : "");
		//origin_public_id : correspond si l'on vient d'un lien de l'identifiatn du lien, on le passe au template pour qu'il puisse selectionner le bon menu.
		$tpl->assign ('currentPublicIdHei', _request('origin_public_id', _request('public_id', null)));
		$toReturn = $tpl->fetch (($template) ? $template : $this->getParam('template', 'heading|menu/headingmenulistnavigation.php'));
		
		//notification d'affichage
		_notify('display', array('type'=>'menu', 'element'=>$tree, 'type_menu'=>$typeHEM));
		
		$cache = array ();
		$cache['CONTENT'] = $toReturn;
		$cache['HTMLHEADER'] = CopixHTMLHeader::stopListeningForChanges ();
		if ($cacheEnabled){
			HeadingCache::set($cacheId, $cache);
		}
		return true;	
	}
	
	/**
	 * Retroune l'element d'identifiant $pPublicId en gérant les erreurs
	 *
	 * @param int $pPublicId
	 * @return record
	 */
	private function _getHeadingElement (&$pPublicId){
		try {
			$heading = _ioClass('HeadingElementInformationServices')->get ($pPublicId);
		} catch (CopixException $e){
			$message = $pPublicId == -1 ? "Vous n'avez pas défini d'element de base de menu. " : '';
			_log($message."Element de menu introuvable remplacé par la racine du site. ".$e->getMessage(), "errors", CopixLog::WARNING);
			$pPublicId = 0;
			$heading = _ioClass('HeadingElementInformationServices')->get ($pPublicId);
		}
		return $heading;
	}
	
	/**
	 * Récupère le contenu d'une portlet
	 *
	 * @param int $pPublicId
	 * @return String
	 */
	private function _getPortletMenu ($pPublicId){		
		try{
			$portlet = _ioClass('portal|portletservices')->getHeadingElementPortletByPublicId ($pPublicId);
			if ($portlet->status_hei == HeadingElementStatus::PUBLISHED && HeadingElementCredentials::canShow($pPublicId)){
				return $portlet->render (RendererMode::HTML, RendererContext::DISPLAYED);
			} else {
				if($portlet->status_hei != HeadingElementStatus::PUBLISHED){
					_log ("La portlet \"".$portlet->caption_hei."\" de public_id_hei $pPublicId n'est pas publiée et ne sera pas affichée dans le menu.", "errors", CopixLog::INFORMATION, array ('public_id' => $pPublicId));
				} else {
					_log ("Pas de droits suffisants pour afficher la portlet de public_id_hei $pPublicId", "errors", CopixLog::INFORMATION, array ('public_id' => $pPublicId));
				}
				return "";
			}
		}catch (CopixException $e){
			//la portlet a été supprimée ou la base n'est pas bonne, on ne lance pas d'exception afin de pouvoir utiliser quand meme le site.
			_log ("Portlet de menu introuvable. ".$e->getMessage(), "errors", CopixLog::WARNING, array ('public_id' => $pPublicId));
		}	
	}
}
