<?php
/**
 * @package     cms
 * @subpackage  article
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
	 * Retourne l'article demandé avec les options envoyées
	 *
	 */
	public function processGetArticle (){
		$ppo = new CopixPPO ();
		$toReturn = '';
		$portletElement = null;
		$public_id = _request ('id_article');	
		$portlet = $this->_getEditedElement ();
		$portlet->setEtat (Portlet::UPDATED);	
		$article = $public_id != null ?_ioClass('heading|headingelementinformationservices')->get ($public_id) : null;
		
		//on ajoute un article à la portlet, on renvoie le contenu de l'article en fonction des options
		if ($public_id != null){
			$options = array ('template'=>_request ('template'));		

			$options['date_create'] = _request ('date_create', false);
			$options['date_update'] = _request ('date_update', false);
			$options['summary'] = _request ('check_summary', false);				
			$options['content'] = _request ('check_content', false);	
			$options['order'] = _request ('order', 'display_order_hei');			
			
			$listeArticles = array();
			if ($article->type_hei == "heading"){
				$results = _ioClass('heading|headingelementinformationservices')->getChildrenByType ($public_id, 'article');
				$children = _ioClass('heading|headingelementinformationservices')->orderElements ($results, $options['order']);
				foreach ($children as $child){
					$listeArticles[] = _ioClass('articles|articleservices')->getByPublicId ($child->public_id_hei);
				} 				
			} else {
				$listeArticles[] = _ioClass('articles|articleservices')->getByPublicId ($article->public_id_hei);			
			}
			
			$tpl = new CopixTpl();
			$tpl->assign('listeArticles', $listeArticles);
			$tpl->assign('identifiantFormulaire', _request('formId'));
			$tpl->assign('options', $options);
			$toReturn = $tpl->fetch('articleformadminview.php');
			
			//si l'article n'est pas encore ajouté
			if(($portletElement = $portlet->getPortletElementAt (_request('position'))) == null){
				$portletElement = $portlet->attach ($article->public_id_hei);
			}
			$portletElement->setOptions ($options);	
		}
		
		//si oldArticle = new Article on est en modification on ne supprime pas, sinon on supprime
		if ($portletElement == null || ($portletElement != null && $portletElement->getHeadingElement ()->id_hei != _request ('id_article'))){
			if($article == null){
				try{
					$portlet->dettach (_request('position'));
				}catch (CopixException $e){
					//si on arrive ici c'est qu'on a voulu faire des modifs dans les options alors qu'on n'a pas selectionné 
					CopixLog::log($e->getMessage(), 'errors', CopixLog::EXCEPTION);
				}
			}
			else{
				$portletElement->setHeadingElement ($article);
			}
		}

		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
	}
	
	/**
	 * Ajoute un article vide à la portlet
	 *
	 */
	public function processAddEmptyArticle (){
	
		if($this->_getEditedElement () instanceof Page){
			$portlet = $this->_getEditedElement ()->findPortletById (_request('portletId'));
		}else{
			$portlet = $this->_getEditedElement ();
		}
		$identifiantFormulaire = $portlet->getRandomId ()."_pos_"._request ('position');
		//on renvoie le template en indiquant un newArticleVide : on n'affichera pas la div qui contient l'article => deja cree par l'appel javascript
		$tpl = new CopixTpl ();		
		$tpl->assign ('portlet', $portlet);
		$tpl->assign ('newArticleVide', true);
		$tpl->assign ('position', _request ('position'));	
		$tpl->assign ('justAddArticle', false);
		$tpl->assign ('articleNotFound', false);
		$tpl->assign ('arTemplates', CopixTpl::find ('articles', '.article.tpl'));
		$tpl->assign ('identifiantFormulaire', $identifiantFormulaire);
		$toReturn = $tpl->fetch ('articles|portletarticle.form.php');			
		
		$ppo = new CopixPPO ();
		$ppo->MAIN = $toReturn;
		return _arDirectPPO ($ppo, 'generictools|blank.tpl');
	}
	
	public function processAddArticle (){
		$id = _request ('id_article');
		$ppo = new CopixPPO ();
		try{
			$article = _ioClass('articles|articleservices')->getByPublicId ($id);
			if($this->_getEditedElement () instanceof Page){
				$portlet = CopixSession::get('portal|'._request ('portletId'), _request('editId'));
				if ($portlet== null){
					$portlet = $this->_getEditedElement ()->findPortletById (_request('portletId'));
				}
			}else{
				$portlet = $this->_getEditedElement ();
			}
			$portlet->setEtat (Portlet::UPDATED);
			
			//on ajoute un article à la portlet, on renvoie le contenu de l'article en fonction des options
			if ($id!= null){
				$options = array ('template'=>_request ('template'));		

				$options['date_create'] = _request ('date_create', false);
				$options['date_update'] = _request ('date_update', false);
				$options['summary'] = _request ('check_summary', false);				
				$options['content'] = _request ('check_content', false);	
				$options['order'] = _request ('order', 'display_order_hei');	

				$portletElement = $portlet->attach ($article->id_hei, _request('position'));
				$portletElement->setOptions ($options);	
			}
			
			if ($portletElement == null || ($portletElement != null && $portletElement->getHeadingElement ()->id_hei != $id)){
				if($article == null){
					$portlet->dettach (_request('position'));
				}
				else{
					$portletElement->setHeadingElement ($article);
				}
			}
		
			$tpl = new CopixTpl ();		
			$tpl->assign ('portlet', $portlet);
			$tpl->assign ('position', _request ('position'));
			$tpl->assign ('justAddArticle', false);
			$tpl->assign ('article', $article);
			$tpl->assign ('newArticleVide', true);
			
			$toReturn = $tpl->fetch ('articles|portletarticle.form.php');		
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