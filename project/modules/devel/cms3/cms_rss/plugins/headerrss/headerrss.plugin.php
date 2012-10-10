<?php

/**
 * Plugin de connexion à l'espace pro
 *
 */
class PluginHeaderRss extends CopixPlugin implements ICopixBeforeProcessPlugin {
	/**
	 * Retourne la description
	 *
	 * @return string
	 */
	public function getDescription () {
		return 'Ajoute les flux RSS du CMS qui doivent l\'être dans les header de la page';
	}

	/**
	 * Retourne le libellé
	 *
	 * @return string
	 */
	public function getCaption () {
		return 'Ajout des flux RSS du CMS dans les headers de la page';
	}
	
	/**
	 * Appelée avant l'exécution de l'action demandée
	 * 
	 * @param string $pAction Nom de l'action
	 */
	public function beforeProcess (&$pAction) {
		if (!CopixRequest::isAJAX()){
			$public_id = _request('public_id', _request('heading', 0));
			$arFlux = _ioClass('cms_rss|rssservices')->getInheritedHeadingElementListFlux ($public_id, $inhereted);
			foreach ($arFlux as $id_rss){
				try{
					$flux = _ioClass('cms_rss|rssservices')->getById($id_rss);
					CopixHTMLHeader::addOthers('<link href="'._url('heading||', array('public_id'=>$flux->public_id_hei)).'" title="'.htmlentities($flux->caption_hei).'" type="application/rss+xml" rel="alternate" />', 'rss_'.$flux->public_id_hei);
				} catch (CopixException $e){
					_log("Tentative d'appel d'un flux supprimé, identifiant : ".$id_rss." Le flux va être retiré de l'élément.", 'debug');
					DAOcms_rss_headingelementinformations::instance ()->delete($id_rss, $inhereted);
				}
			}
		}
	}

	
}