<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain VUIDART
 */

/**
 * Actions d'administration sur les liens
 * @package cms
 * @subpackage heading
 */
class ActionGroupLinkFront extends CopixActionGroup {
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
	
	/**
	 * Demande d'affichage d'un lien
	 * 
	 * On va rediriger l'internaute sur le lien pour le cas des liens externes et demander a heading de prendre en charge
	 * l'affichage des autres éléments
	 */
	public function processDefault (){
		$editedElement = _class('heading|linkservices')->getByPublicId (_request('public_id'));

		if (!is_null ($editedElement->href_link)){
			return _arRedirect (_ioClass ('heading|LinkServices')->getURL ($editedElement->id_helt));
		} else if (!is_null ($editedElement->module_link)){
			if ($editedElement->not_rewritten_link == 1) {
				return _arRedirect (_ioClass ('heading|LinkServices')->getURL ($editedElement->id_helt));
			} else {
				$headingElementInformationServices = _ioClass('heading|headingelementinformationservices');
				//Création du fil d'ariane
				$tree = array_reverse( explode('-', $editedElement->hierarchy_hei), true );
				foreach ($tree as $public_id){
					$inherited = false;
					$visibility = $headingElementInformationServices->getVisibility($public_id, $inherited);
					if ($visibility){
						$element = $headingElementInformationServices->get($public_id);
						$breadcrumb[] = array (
							'url' => CopixURL::get ('heading||', array ('public_id' => $public_id, 'caption_hei' => $element->caption_hei)),
							'caption' => $element->caption_hei,
							'element' => $element
						);
					}
				}
				
				if (count ($breadcrumb)){
					$breadcrumb[] = array ('url' => CopixConfig::get ('default|homePage'), 'caption' => 'Accueil');
					$breadcrumb = array_values(array_reverse ($breadcrumb, true));
					$responses = _notify ('cms_getbreadcrumb', array ('complexpath' => $breadcrumb, 'element' => $editedElement));
					if (count ($responses->getResponse ()) > 0) {
						$newBreadcrumb = array ();
						foreach ($responses->getResponse () as $response) {
							$newBreadcrumb[(isset ($response['level'])) ? $response['level'] : 0] = $response['breadcrumb'];
						}
						ksort ($newBreadcrumb);
						$breadcrumb = array_pop ($newBreadcrumb);
					}
					// si la page d'accueil n'existe pas, on remplace l'url par #
					$headingservices = new HeadingServices();
					if(is_array($breadcrumb)){
						foreach ($breadcrumb as $key => $entry){
							if(isset($entry['element'])){
								$heading = null;
								try{
									$heading = $headingservices->getByPublicId($entry['element']->public_id_hei);
								}catch(HeadingElementInformationNotFoundException $e){}
								if($heading){	
									if(!isset($heading->home_heading) || !$heading->home_heading){
										$breadcrumb[$key]['showlink'] = false;
									}else{
										$breadcrumb[$key]['url'] = CopixURL::get ('heading||', array ('public_id' => $heading->home_heading, 'caption_hei' => $element->caption_hei));
									}
								}
							}
						}
					}
					_notify ('breadcrumb', array ('complexpath' => $cache['BREADCRUMB'] = $breadcrumb));
				}
				
				$moduleLinkInfos = explode ('?', $editedElement->module_link);
				
				$params = array();
				if (isset ($moduleLinkInfos[1])) {
					parse_str ($moduleLinkInfos[1], $params);
				}
				return CopixActionGroup::process ($moduleLinkInfos[0], $params);
			}
		}

		$arExtra = _class('heading|linkservices')->getArExtra ($editedElement->extra_link);
		
		$args = array ('public_id'=>$editedElement->linked_public_id_hei, 'origin_public_id'=>_request('public_id'));
		
		if (array_key_exists('anchor', $arExtra) && $arExtra['anchor'] != null){
			$args['anchor'] = $arExtra['anchor'];
		}

		// on ajoute en get le public_id de l'element d'origine pour faire la selection dans les menus de navigation.
		return CopixActionGroup::process ('heading|default::default', $args);
	}
}