<?php
/**
 * @package     cms
 * @subpackage  document
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * ActionGroup gerant les appels ajax de la portlet article 
 *
 */
class ActionGroupAjax extends CopixActionGroup {

	/**
	 * Retourne le document demandé avec les options envoyées
	 *
	 */
	public function processGetDocument (){
		$ppo = new CopixPPO ();
		$toReturn = '';
		$portletElement = null;
		$public_id = _request ('id_document');
		$document = $public_id != null ?_ioClass('heading|headingelementinformationservices')->get ($public_id) : null;
		$portlet = $this->_getEditedElement ();
		$portlet->setEtat (Portlet::UPDATED);
		
		//on ajoute un document à la portlet, on renvoie le contenu du document en fonction des options
		if ($public_id != null){
			$options = array (
							'content_disposition'=>(_request ('inline') == 'true') ? 'inline' : 'attachement',
							'template'=>_request ('template')
							);
			
			$listeDocs = array();
			if ($document->type_hei == "heading"){
				$results = _ioClass('heading|headingelementinformationservices')->getChildrenByType ($public_id, 'document');
				$children = _ioClass('heading|headingelementchooserservices')->orderChildren ($results);
				foreach ($children as $child){
					$listeDocs[] = _ioClass('document|documentservices')->getByPublicId ($child->public_id_hei);
				} 				
			} else {
				$listeDocs[] = _ioClass('document|documentservices')->getByPublicId ($document->public_id_hei);			
			}
			
			$tpl = new CopixTpl();
			$tpl->assign('listeDocs', $listeDocs);
			$tpl->assign('identifiantFormulaire', _request('formId'));
			$tpl->assign('arDocIcons', ZoneHeadingElementChooser::getArDocIcons());
			$toReturn = $tpl->fetch('documentformadminview.php');
			
			//si le document n'est pas encore ajouté
			if(($portletElement = $portlet->getPortletElementAt (_request('position'))) == null){
				$portletElement = $portlet->attach ($document->public_id_hei);
			}
			$portletElement->setOptions ($options);	
		}
		
		//si oldDocument = new Document on est en modification on ne supprime pas, sinon on supprime
		if ($portletElement == null || ($portletElement != null && $portletElement->getHeadingElement ()->id_hei != $public_id)){
			if($document == null){
				try{
					$portlet->dettach (_request('position'));
				}catch (CopixException $e){
					//si on arrive ici c'est qu'on a voulu faire des modifs dans les options alors qu'on n'a pas selectionné 
					CopixLog::log($e->getMessage(), 'errors', CopixLog::EXCEPTION);
				}
			}
			else{
				$portletElement->setHeadingElement ($document);
			}
		}
			
		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * Ajoute un document vide à la portlet
	 *
	 */
	public function processAddEmptyDocument (){
		$portlet = $this->_getEditedElement ();
		$identifiantFormulaire = $portlet->getRandomId ()."_pos_"._request ('position');
		//on renvoie le template en indiquant un newDocumentVide : on n'affichera pas la div qui contient l'document => deja cree par l'appel javascript
		$tpl = new CopixTpl ();		
		$tpl->assign ('portlet', $portlet);
		$tpl->assign ('position', _request ('position'));
		$tpl->assign ('newDocumentVide', true);
		$tpl->assign ('justAddDocument', false);
		$tpl->assign ('documentNotFound', false);
		
		$toReturn = $tpl->fetch ('document|portletdocument.form.php');			
		
		$ppo = new CopixPPO ();
		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}
	
	public function processAddDocument (){
		$id = _request ('id_document');
		$ppo = new CopixPPO ();
		try{
			$document = _class ('document|documentservices')->getByPublicId ($id);
			if($this->_getEditedElement () instanceof Page){
				$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
				if ($portlet== null){
					$portlet = $this->_getEditedElement ()->findPortletById (_request('portletId'));
				}
			}else{
				$portlet = $this->_getEditedElement ();
			}
			$portlet->setEtat (Portlet::UPDATED);
			
			//on ajoute un document à la portlet, on renvoie le contenu du document en fonction des options
			if ($id!= null){
				$options = array (
								'caption_hei'=>true,
								'description_hei'=>true,
								'file_document'=>true,
								'template'=> ''
								);

				$portletElement = $portlet->attach ($document->id_hei, _request('position'));
				$portletElement->setOptions ($options);	
			}
			
			//si oldDocument = new Document on est en modification on ne supprime pas, sinon on supprime
			if ($portletElement == null || ($portletElement != null && $portletElement->getHeadingElement ()->id_hei != $id)){
				if($document == null){
					$portlet->dettach (_request('position'));
				}
				else{
					$portletElement->setHeadingElement ($document);
				}
			}
		
			//$portlet = $this->_getEditedElement ()->findPortletById (_request('portletId'));
			//on renvoie le template en indiquant un newDocumentVide : on n'affichera pas la div qui contient le document => deja cree par l'appel javascript
			$tpl = new CopixTpl ();		
			$tpl->assign ('portlet', $portlet);
			$tpl->assign ('position', _request ('position'));
			$tpl->assign ('justAddDocument', false);
			$tpl->assign ('document', $document);
			$tpl->assign ('newDocumentVide', true);
			
			$toReturn = $tpl->fetch ('document|portletdocument.form.php');		
			$ppo->MAIN = $toReturn;	
		}
		catch (HeadingElementInformationNotFoundException $e){
			$ppo->MAIN = '';
		}	
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}
	
	/**
	 * Retourne la page en cours d'edition
	 *
	 * @return Page
	 */
	protected function _getEditedElement (){
		CopixRequest::assert ('editId');
		$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
		if ($portlet == null){
			$portlet = CopixSession::get('portlet|edit|record', _request('editId'));			
		}
		if (!$portlet){
			throw new CopixException ('Portlet en cours de modification perdu');
		}
		return $portlet;
	}
	
}

?>