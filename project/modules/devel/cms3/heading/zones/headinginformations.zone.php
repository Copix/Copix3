<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author Gérald Croës
 */

/**
 * Affichage des informations de base pour un élément
 *
 * @package cms
 * @subpackage heading
 */
class ZoneHeadingInformations extends CopixZone {
	/**
	 * Identifiant pour avoir plusieurs zones dans la même page
	 * 
	 * @var string
	 */
	private $_uniqId = null;

	/**
	 * Cache de HeadingElementInformationServices pour éviter trop d'appels au singleton
	 *
	 * @var HeadingElementInformationServices
	 */
	private $_hei = null;

	/**
	 * Création du contenu
	 *
	 * @param string $toReturn HTML à afficher
	 * @return boolean
	 */
	protected function _createContent (&$toReturn) {
		$record = $this->getParam ('record');
		$this->_uniqId = $this->getParam ('uniqId');
		$this->_hei = _ioClass ('HeadingElementInformationServices');

		$toReturn['_previsualisation'] = $this->_preview ($record);
		$toReturn['versions'] = $this->_versions ($record);
		$toReturn['themes'] = $this->_themes ($record);
		$toReturn['utilisations'] = $this->_dependencies ($record);
		$toReturn['url'] = $this->_url ($record);
		$toReturn['menus'] = $this->_menus ($record);
		$toReturn['elementlies'] = $this->_linkedHeadingElements ($record);
		$toReturn['filariane'] = $this->_breadcrumb ($record);
		
		if (CopixUserPreferences::get('heading|cms_mode') == "advanced"){
			$toReturn['notes'] = $this->_comments ($record);
			$toReturn['referencement'] = $this->_referencing ($record);
			$toReturn['developper'] = $this->_developper ($record);
			$toReturn['droits'] = $this->_credentials ($record);
			$toReturn['cible'] = $this->_target ($record);
		}
		
		return true;
	}

	/**
	 * Retourne un objet CopixTPL pré rempli
	 *
	 * @param mixed $pRecord
	 * @return CopixTPL
	 */
	private function _getTPL ($pRecord) {
		$tpl = new CopixTPL ();
		$tpl->assign ('record', $pRecord);
		$tpl->assign ('uniqId', $this->_uniqId);
		$tpl->assign ('uniqueElement', !is_array ($pRecord));
		return $tpl;
	}

	/**
	 * Prévisualisation de l'élément
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _preview ($pRecord) {
		$tpl = $this->_getTPL ($pRecord);
		$preview = array ();
		if (is_array ($pRecord)) {
			foreach ($pRecord as $record) {
				$preview[] = $this->_hei->previewById ($record->id_helt, $record->type_hei);
			}
		} else {
			$preview[] = $this->_hei->previewById ($pRecord->id_helt, $pRecord->type_hei);
		}
		$tpl->assign ('preview', $preview);
		return $tpl->fetch ('heading|informations/preview.php');
	}
	
	/**
	 * Informations sur les versions
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _linkedHeadingElements ($pRecord) {
		if (is_array ($pRecord)) {
			return null;
		}
		
		$tpl = $this->_getTPL ($pRecord);
		$tpl->assign ('linkedHeadingElements', $this->_hei->getLinkedHeadingElements ($pRecord->public_id_hei));
		$types = new HeadingElementType ();
		$tpl->assign ('elementTypes', $types->getList ());
		return $tpl->fetch ('heading|informations/linkedheadingelements.php');
	}

	/**
	 * Informations sur les versions
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _versions ($pRecord) {
		if (is_array ($pRecord)) {
			return null;
		}
		
		$tpl = $this->_getTPL ($pRecord);
		$tpl->assign ('versions', $this->_hei->find (array ('public_id_hei' => $pRecord->public_id_hei, 'order_by' => 'date_create_hei')));
		$tpl->assign ('arElementStatus', _class ('headingelementstatus')->getList ());
		return $tpl->fetch ('heading|informations/versions.php');
	}

	/**
	 * Informations sur les thèmes
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _themes ($pRecord) {
		if (is_array ($pRecord)) {
			return null;
		}
		return CopixZone::process("heading|themechooser", array('public_id'=>$pRecord->public_id_hei, 'tpl'=>$this->_getTPL ($pRecord)));
	}

	/**
	 * Informations sur l'adresse
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _url ($pRecord) {
		if (is_array ($pRecord)) {
			return null;
		}

		$base_url_inherited_from = false;
		$base_url = $this->_hei->getBaseUrl ($pRecord->public_id_hei, $base_url_inherited_from);
		if ($base_url_inherited_from != null) {
			$base_url_inherited_from = $this->_hei->get ($base_url_inherited_from)->caption_hei;
		}
		$tpl = $this->_getTPL ($pRecord);
		$tpl->assign ('base_url', $base_url);
		$tpl->assign ('base_url_inherited_from', $base_url_inherited_from);
		return $tpl->fetch ('heading|informations/url.php');
	}
	
	/**
	 * Fil d'ariane
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _breadcrumb ($pRecord) {
		if (is_array ($pRecord)) {
			return null;
		}
		
		// type dossier
		if ($pRecord->type_hei == 'heading') {
			$tpl = $this->_getTPL ($pRecord);
			$tpl->assign ('heading', HeadingElementServices::call ('heading', 'getById', $pRecord->id_helt));
			return $tpl->fetch ('heading|informations/breadcrumb.heading.php');
			
		// type page
		} else if ($pRecord->type_hei == 'page') {
			$tpl = $this->_getTPL ($pRecord);
			$tpl->assign ('page', HeadingElementServices::call ('page', 'getById', $pRecord->id_helt));
			return $tpl->fetch ('heading|informations/breadcrumb.page.php');
		}
	}

	/**
	 * Commentaire sur un élément
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _comments ($pRecord) {
		if (is_array ($pRecord)) {
			return null;
		}
		
		$tpl = $this->_getTPL ($pRecord);
		return $tpl->fetch ('heading|informations/comment.php');
	}

	/**
	 * Dépendances d'un élément
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _dependencies ($pRecord) {
		if (is_array ($pRecord)) {
			return null;
		}
		$tpl = $this->_getTPL ($pRecord);
		return $tpl->fetch ('heading|informations/dependencies.php');
	}

	/**
	 * Menus d'un élément
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _menus ($pRecord) {
		return CopixZone::process("heading|configurationmenus", array("record"=>$pRecord, "uniqid"=>$this->_uniqId));
	}

	/**
	 * Référencement d'un élément
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _referencing ($pRecord) {
		if (!CopixConfig::get ('heading|robotsActivated') || is_array ($pRecord)) {
			return null;
		}

		if (!empty ($pRecord->robots_hei)) {
			$aRobots = explode (',', $pRecord->robots_hei);
			$pRecord->robots_hei = new stdClass ();
			foreach ($aRobots as $sRobot) {
				$pRecord->robots_hei->$sRobot = 1;
			}
		}
		$robots_inherited = false;
		if ($pRecord->parent_heading_public_id_hei !==  null) {
			$inherited = false;
			$robots_inherited = $this->_hei->getRobots ($pRecord->parent_heading_public_id_hei, $inherited);
			if ($robots_inherited === '') {
				$robots_inherited = 'Pas de valeur définie';
			}
		}

		$tpl = $this->_getTPL ($pRecord);
		$tpl->assign ('robots_inherited', $robots_inherited);
		return $tpl->fetch ('heading|informations/referencing.php');
	}

	/**
	 * Droits d'un élément
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _credentials ($pRecord) {
		if (is_array ($pRecord) || !HeadingElementCredentials::canModerate ($pRecord->public_id_hei)) {
			return null;
		}
		$arRights = _ioClass ('HeadingElementCredentials')->getList ();
		$allCredentials = $this->_hei->getAllHeadingElementCredential ($pRecord->public_id_hei);
		$groupHandlerLabels = CopixGroupHandlerFactory::getGroupLabels();
		 asort($allCredentials);
		// on ajoute les groupes ayant des droits
		$groups = array();
		foreach ($allCredentials as $groupHandler => $arRecord){
			asort($arRecord);
			$label = $groupHandlerLabels[$groupHandler];
			$groups[$label] = array();	
			foreach ($arRecord as $crRecord){
				$group = new stdClass();
				$group->group_handler = $crRecord->group_handler;
				$group->id_group = $crRecord->id_group;
				$group->right = $crRecord->value_credential;
				if($group->right !== ''){
					$group->right = (int)$group->right;
				}
				$groups[$label][] = $group;
			}
		}
		
		$parentGroups = array();
		// puis les groupes ayant droit à la rubrique parente
		if($pRecord->public_id_hei > 0){
			$parentPublicId = $this->_hei->get($pRecord->public_id_hei)->parent_heading_public_id_hei;
			$parentCredentials = $this->_hei->getAllHeadingElementCredential ($parentPublicId);
			asort($parentCredentials);	
			foreach ($parentCredentials as $groupHandler => $arRecord){
				$label = $groupHandlerLabels[$groupHandler];
				asort($arRecord);
				$parentGroups[$label] = array();	
				foreach ($arRecord as $crRecord){
					$group = new stdClass();
					$group->group_handler = $crRecord->group_handler;
					$group->id_group = $crRecord->id_group;
					$group->right = $crRecord->value_credential;
					if($group->right !== ''){
						$group->right = (int)$group->right;
					}
					$parentGroups[$label][] = $group;
				}
			}
		}
		
		$arListGroups = CopixGroupHandlerFactory::getAllGroupList();
		
		$tpl = $this->_getTPL ($pRecord);
		$tpl->assign ('groups', $groups);
		$tpl->assign ('parentGroups', $parentGroups);
		$tpl->assign ('arListGroups', $arListGroups);
		$tpl->assign ('arRights', $arRights);
		return $tpl->fetch ('heading|informations/credentials.php');
	}

	/**
	 * Dépendances d'un élément
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _target ($pRecord) {
		$tpl = $this->_getTPL ($pRecord);
		if (is_array ($pRecord)) {
			return null;
		}
		if ($pRecord->target_params_hei) {
			$params = explode (';', $pRecord->target_params_hei);
			foreach ($params as $param){
				list ($key, $value) = explode ('=', $param);
				$tpl->assign ('target_' . $key, $value);
			}
		} else {
			$tpl->assign ('target_height', null);
			$tpl->assign ('target_width', null);
		}
		return $tpl->fetch ('heading|informations/target.php');
	}

	/**
	 * Informations pour le développeur
	 *
	 * @param mixed $pRecord Elément dont on veut les infos
	 * @return string
	 */
	private function _developper ($pRecord) {
		if (!CopixUserPreferences::get ('heading|showDevelopper', false) || is_array ($pRecord)) {
			return null;
		}
		$tpl = $this->_getTPL ($pRecord);
		$sections = array (
			'Générale' => array ('type_hei'),
			'Identifiants' => array ('id_hei', 'id_helt', 'public_id_hei'),
			'Position' => array ('order_hei', 'display_order_hei', 'hierarchy_hei', 'hierarchy_level_hei'),
			'Auteur' => array (
				'author_id_create_hei', 'author_handler_create_hei', 'author_caption_create_hei',
				'author_id_update_hei', 'author_handler_update_hei', 'author_caption_update_hei'
			)
		);
		$tpl->assign ('sections', $sections);
		return $tpl->fetch ('heading|informations/developper.php');
	}
	
	private function _getMultipleElementInformation (&$toReturn){
		//préparation du PPO avec dans record l'élément en cours d'affichage
		$ppo = _rPPO (array ('record'=>$this->requireParam ('record')));
		//permet definir plusieurs affichages du meme element.
		$ppo->uniqId = uniqid();
		
		//on modifie plusieurs elements.
		$ppo->uniqueElement = false;
					
		$arCaption = array();
		$visibility = "";
		$classe = "";
		$theme = "";
		$target_hei = "";
		$target_params_hei = "";
		$ppo->target_width = "";
		$ppo->target_height = "";
		$ppo->visibility_inherited_from = false;
		$ppo->base_url_inherited_from = false;
		$url = "";
		$arPublicId = array();
		
		foreach ($ppo->record as $element){
			//titre : concatenation des caption
			$arCaption[] = $element->caption_hei;
			//ids
			$arPublicId[] = $element->id_helt .';' . $element->type_hei;
			//visibilité
			$ppo->visibility = _ioClass ('headingelementinformationservices')->getVisibility ($element->public_id_hei, $ppo->visibility_inherited_from);	
			if ($visibility != "" && $visibility != $element->show_in_menu_hei){
				$visibility = -1;
			} else {
				$visibility = $element->show_in_menu_hei;
			}
			if ($ppo->visibility_inherited_from != null){
				$ppo->visibility_inherited_from = _ioClass ('headingelementinformationservices')->get ($ppo->visibility_inherited_from)->caption_hei;
			}
				
			//classe de menu
			if ($classe != "" && $classe != $element->menu_html_class_name_hei){
				$classe = '******';
			} else {
				$classe = $element->menu_html_class_name_hei;
			}
			
			//cible
			$element->target_hei = $element->target_hei == null ? 0 : $element->target_hei;
			if ($target_hei != "" && $target_hei != $element->target_hei){
				$target_hei = -1;
			} else {
				$target_hei = $element->target_hei;
			}
			if ($target_params_hei != "" && $target_params_hei != $element->target_params_hei){
				$ppo->target_width = "******";
				$ppo->target_height = "******";
				$target_params_hei = -1;
			} else if ($element->target_params_hei){
				$params = explode(';', $element->target_params_hei);
				foreach ($params as $param){
					list ($key, $value) = explode("=", $param);
					$ppo->{'target_' . $key} = $value;
					if ($ppo->{'target_' . $key} != "" && $ppo->{'target_' . $key} != $value){
						$ppo->{'target_' . $key} = '******';
					} else {
						$ppo->{'target_' . $key} = $value;
					}
				}
				$target_params_hei = $element->target_params_hei;
			}
			
			
			
			//récupération des droits
			$ppo->rights->activated=false;
			if (HeadingElementCredentials::canModerate($element->public_id_hei)) {
				$ppo->rights->activated=true;
				
				$ppo->rights->arGroups = DAOdbgroup::instance ()->findAll ()->fetchAll ();
				$ppo->rights->arRights = _class ('HeadingElementCredentials')->getList ();
				
				foreach ($ppo->rights->arGroups as $group){
					$credential = _ioClass ('HeadingElementInformationservices')->getHeadingElementCredential ($group->id_group, $element->public_id_hei);
					$group->right =  $credential ? $credential->value_credential : null;
				} 
			}

			$ppo->mvtesting->activated = false;
		}
	
		//on redefini l'enregistrement pour acceuillir les infos de plusieurs elements.
		$ppo->record = DAORecordcms_headingelementinformations::create ();
		//passage des informations au record
		$ppo->record->caption_hei = implode(', ', $arCaption);
		$ppo->arPublicId = implode('|' ,$arPublicId);
		$ppo->record->target_hei = $target_hei;
		$ppo->multiple_visibility = $visibility;
		$ppo->record->show_in_menu_hei = $visibility; 
		$ppo->record->menu_html_class_name_hei = $classe; 
		
		//DROITS DE SAUVEGARDE
		$ppo->rightsToSave = HeadingElementCredentials::canModerate ($ppo->record->public_id_hei);

		// RSS désactivé
		$ppo->rss->activated = false;
		$toReturn = $this->_usePPO ($ppo, 'headingelementinformation.tpl');
		return true;
	}
}