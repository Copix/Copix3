<?php
class ActionGroupArticleFront extends CopixActionGroup {
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
		$ppo = _ppo ();
		$editedElement = _class('articles|articleservices')->getByPublicId (_request('public_id'));
		$ppo->TITLE_PAGE = $editedElement->caption_hei;
				
		$ppo->article = $editedElement;
		$ppo->article->content_article = _class('cms_editor|cmswysiwygparser')->transform($ppo->article->content_article);
		//Si on ne veut pas afficher l'article : on redirige vers le parent et on genere une 301
		if (CopixConfig::get('articles|redirect')){
			return _arRedirect(_url('heading||', array('public_id'=>$editedElement->parent_heading_public_id_hei)), array('301'=>true));
		}
		return _arPpo ($ppo, 'articles|frontarticle.tpl');
	}
}