<?php
/**
 * Actions pour réparer les données du CMS.
 */
class ActionGroupRepair extends CopixActionGroup {
	protected function _beforeAction ($pAction) {
		CopixPage::add ()->setIsAdmin (true);
		if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
	}

	/**
	 * S'occupe de réparer les hierarchies précalculées.
	 */
	public function processFillHierarchy (){
		_currentUser ()->assertCredential ('basic:admin');
		ini_set ('max_execution_time', 18000);
		ini_set ('display_errors', 1);

		_currentUser ()->assertCredential ('basic:admin');

		$services = new HeadingElementInformationServices ();
		$list = $services->getChildren (0);

		if (_request ('confirm') != 'true'){
			//On va récupérer les rubriques dont on va réparer les hierarchies
			_dump ($list);
			echo '<a href="'._url ('heading|repair|fillHierarchy', array ('confirm'=>'true')).'">Confirmer</a>';
			return _arNone ();
		}

		//on va passer par une méthode non standard pour pouvoir flusher au fur et à mesure
		foreach ($list as $element){
			$this->_fillHierarchy($element);
		}

		echo '<a href="'._url ('admin|').'">Retourner sur le site</a>'; 
		return _arNone ();
	}
	
	private function _fillHierarchy (& $pElement){
		$services = new HeadingElementInformationServices ();
        if ($pElement->parent_heading_public_id_hei === null) {
            return 0;
        }
        
        $parent = $services->get ($pElement->parent_heading_public_id_hei);

        $oldHierarchy = $pElement->hierarchy_hei;
        $oldHierarchyLevel = $pElement->hierarchy_level_hei;

        $pElement->hierarchy_hei =  $parent->hierarchy_hei . "-" . $pElement->public_id_hei;
        $pElement->hierarchy_level_hei =  $parent->hierarchy_level_hei + 1;

        if (($oldHierarchy != $pElement->hierarchy_hei)
        	|| ($oldHierarchyLevel != $pElement->hierarchy_level_hei)){

        	echo '<h1>', $pElement->caption_hei, '('.$pElement->public_id_hei.')', '</h1>';        	
        	if ($oldHierarchy != $pElement->hierarchy_hei){
				echo '<b>', $oldHierarchy ,'</b>', ' =====> <b>', $pElement->hierarchy_hei, '</b><br />';				
        	}
        	if ($oldHierarchyLevel != $pElement->hierarchy_level_hei){
        		echo '<b>', $oldHierarchyLevel, '</b> =====> <b>', $pElement->hierarchy_level_hei, '</b><br />';
        	}
			DAOcms_headingelementinformations::instance ()->update ($pElement);
        }else{
        	echo $pElement->public_id_hei, ', ';
        }
        flush ();
        
        foreach ($services->getChildren ($pElement->public_id_hei) as $element) {
            $this->_fillHierarchy ($element);
        }
	}
	
	/**
	 * Retrouve les éléments qui sont orphelins
	 */
	public function processFindGhosts (){
		_currentUser ()->assertCredential ('basic:admin');
		_notify ('breadcrumb', array ('path' => array ('heading|repair|FindLostElements' => 'Eléments fantomes')));
		ini_set ('display_errors', 1);
		$ppo = _ppo ();
		$ppo->TITLE_PAGE = 'Données orphelines'; 
		$ppo->ghosts = HeadingRepair::findGhosts ();
		return _arPpo ($ppo, 'repair/ghosts.php');
	}

	/**
	 * Confirmation de suppression d'un ghost
	 *
	 * @return CopixActionReturn
	 */
	public function processDeleteGhost () {
		_currentUser ()->assertCredential ('basic:admin');
		_notify ('breadcrumb', array ('path' => array ('heading|repair|FindLostElements' => 'Eléments fantomes', '#' => 'Suppression')));
		CopixRequest::assert ('id_helt', 'type_hei');
		$id_helt = _request ('id_helt');
		return CopixActionGroup::process ('generictools|Messages::getConfirm',
			array (
				'message' => 'Etes-vour sur de vouloir supprimer l\'élément "' . $id_helt . '" dans la table spécifique ?',
				'confirm' => _url ('heading|repair|DoDeleteGhost', array ('id_helt' => $id_helt, 'type_hei' => _request ('type_hei'))),
				'cancel' => _url ('heading|repair|FindLostElements')
			)
		);
	}

	/**
	 * Suppression d'un ghost
	 *
	 * @return CopixActionreturn
	 */
	public function processDoDeleteGhost () {
		_currentUser ()->assertCredential ('basic:admin');
		CopixRequest::assert ('id_helt', 'type_hei');
		HeadingElementServices::call (_request ('type_hei'), 'deleteGhost', _request ('id_helt'));
		return _arRedirect ('heading|repair|FindLostElements');
	}

	/**
	 * Affiche les liens morts
	 *
	 * @return CopixActionReturn
	 */
	public function processFindDeadLinks () {
		_currentUser ()->assertCredential ('cms:write@0');
		_notify ('breadcrumb', array ('path' => array ('heading|repair|FindDeadLinks' => 'Liens morts')));
		$ppo = _ppo (array ('TITLE_PAGE' => 'Liens morts'));
		$ppo->deadLinks = HeadingRepair::findDeadLinks ();
		return _arPPO ($ppo, 'repair/deadlinks.php');
	}
} 