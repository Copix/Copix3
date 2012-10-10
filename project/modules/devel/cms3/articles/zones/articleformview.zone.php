<?php
/**
 * Zone d'affichage du contenu de l'article
 *
 */
class ZoneArticleFormView extends CopixZone {
	
	public function _createContent (&$toReturn){
		$options = $this->getParam('options');
		$article = $this->getParam('article');
		
		$tpl = new CopixTpl ();
		$tpl->assign('article', $article);
		$tpl->assign('options', $options);

		$toReturn = $tpl->fetch ('articleformview.php');		
		return true;
	}
}
?>