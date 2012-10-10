<?php
/**
 * Validateur d'un lien de sitemap
 * @author fredericb
 *
 */
class SiteMapLinkValidator extends CopixAbstractValidator{
	/**
	 * @param SiteMapLink $pSitemapLink
	 */
	protected function _validate ($pSitemapLink){
		$errors = array();
		if(strlen($pSitemapLink->getCaption()) == 0 ){
			$errors['caption'] = 'Le nom est obligatoire.';
		}
		if(($pSitemapLink->getUrlMode() == SiteMapLink::URL_MODE_CUSTOM) && !$pSitemapLink->getCustomUrl()){
			$errors['url'] = 'L\'url externe doit être saisie dans ce mode.';
		}
		if(($pSitemapLink->getUrlMode() == SiteMapLink::URL_MODE_CMS) && !$pSitemapLink->getCmsLink()){
			$errors['url'] = 'Vous devez choisir un url dans de cms.';
		}
		
		if($pSitemapLink->getId() && $pSitemapLink->getChildMode() == SiteMapLink::CHILD_MODE_HEADING && !$pSitemapLink->getCmsHeading()){
			$errors['cms_heading'] = 'Vous devez choisir une catégorie dans de cms.';
		}
		
		return (count($errors) != 0) ? $errors : true;
	}
}