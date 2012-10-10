<?php
class ActionGroupRssFront extends CopixActionGroup {
	
	/**
	 * On vérifie que Heading|| a lancé l'ordre d'affichage des éléments demandés.
	 *
	 * @param string $pActionName
	 */
	public function processDefault (){
		//HeadingFront utilise Copixregistry pour indiquer les public_id dont il a demandé l'affichage
		$front = CopixRegistry::instance ()->get ('headingfront');

		if ($front !== _request('public_id')){
			throw new CopixCredentialException ('basic:admin'); 
		}
		$editedElement = _class('cms_rss|rssservices')->getByPublicId (_request('public_id'));
		return $this->_getRss($editedElement);
	}	
	
	public function processPreview(){
		$editedElement = _class('cms_rss|rssservices')->getById(_request('id_rss'));
		return $this->_getRss($editedElement, true);
	}
		
	private function _getRss($pRssElement, $preview = false){
		if ($pRssElement){
			$rss = new Syndication ();
	
	    	$rss->title = $pRssElement->caption_hei;
	    	$rss->link->uri = _url('heading||', array('public_id'=>$pRssElement->public_id_hei));
	    	$rss->description = $pRssElement->description_hei;
	    	$types = split(',', $pRssElement->element_types_rss);
	    	$elements = _ioClass('cms_rss|rssservices')->getListElement ($pRssElement->heading_public_id_rss,$types,$pRssElement->order_rss, $pRssElement->recursive_flag);
	    	
	    	// si on est pas en preview, on filtre sur les groupes accessibles
			if(!$preview){
				$hashedGroups = array();
				// pour raison de compatibilité, si on a pas de groupe, on met les groupes base de données qui sont publics
				if(!_request('groups')){
					$sp = _daoSP()->addCondition('public_dbgroup', '=', 1);
					foreach (DAOdbgroup::instance ()->findBy ($sp) as $groupInfo){
						$hashedGroups[] = md5('auth|dbgrouphandler~'.$groupInfo->id_group);
					}
				}else{
					$hashedGroups = preg_split('/-/', _request('groups'));
				}
				$groupList = CopixGroupHandlerFactory::getAllGroupList();
				$handlersGroups = array();
				foreach ($groupList as $handlerName => $handlerGroups){
					foreach ($handlerGroups as $group => $label){
						if(in_array(md5($handlerName.'~'.$group), $hashedGroups)){
							if(!isset($handlersGroups[$handlerName])){
								$handlersGroups[$handlerName] = array();
							}
							$handlersGroups[$handlerName][] = $group; 
						}
					}
				}
	    	}
	    	foreach ($elements as $element) {
	    		if(!$preview){
	    			$canAccess = false;
	    			foreach ($handlersGroups as $handlerName => $handlerGroups){
	    				foreach ($handlerGroups as $groups){
	    					$credentials = _ioClass('heading|HeadingElementInformationServices')->getHeadingElementCredential ($groups,$handlerName, $element->public_id_hei);
	    					if($credentials->value_credential >= HeadingElementCredentials::READ){
	    						$canAccess = true;
	    						break;
	    					}
	    				}
	    				if($canAccess){
	    					break;
	    				}
	    			}
	    			if(!$canAccess){
	    				continue;
	    			}
	    		}
	    		try{
		    		$title = $element->caption_hei;
		    		$url = (HeadingElementServices::call($element->type_hei, 'getUrl', array($element->id_helt)));
		    		$url = str_replace('&', '&#38;', $url);
		    		$desc = HeadingElementServices::call($element->type_hei, 'getDescription', array($element->id_helt));
		    		if(!$desc){
		    			$desc = $title; 
		    		}
	    		
		    		if($url && $title && $desc){
			    		$item = $rss->addItem ();
			    		$item->title = $title;
			    		$item->link->uri = $url;
						$item->content->value = $desc;
		    			$item->pubDate = CopixDateTime::yyyymmddhhiissToTimeStamp(str_replace(array(' ', '-', ':'), '', $element->date_update_hei));
		    		}
	    		}catch(Exception $e){
	    			_log($e->getMessage(), CopixLog::EXCEPTION);
	    		}
	    	}
	    	
	    	$ppo = new CopixPPO (array ('MAIN' => $rss->getContent (Syndication::RSS_2_0)));
			return _arDirectPPO ($ppo, 'generictools|blanknohead.tpl');
		}
		_log('Erreur lors de l\'affichage d\'un flux RSS', "errors");
		return _arNone();
	}
}