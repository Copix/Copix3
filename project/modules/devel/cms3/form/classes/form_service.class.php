<?php
/**
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */

/**
 * Service du module Formulaire
 * @package cms
 * @subpackage form
 * @author Nicolas Bastien
 */
class Form_Service extends HeadingElementServices {

	const STR_SESSION = 'form|edit|record';
	const FORM_TYPE = 'form';
	
	const ORDER_STEP = 100;

    private $_currentForm = null;

	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$element = $this->getById ($pIdHelt);
		return $element->title_hei;
	}

	/**
	 * 
	 * Retourne la description de l'élément
	 * @param int $pIdHelt
	 */
	public function getDescription ($pIdHelt){
		$element = $this->getById ($pIdHelt);
		return $element->description_hei;
	}
	
	/**
	 * Récupère un enregistrement par son identifiant interne
	 *
	 * @param int $pIdHelt Identifiant interne de l'élément à récupérer
	 */
	public function getById ($pIdHelt){
		//on vérifie que l'élément existe
		if (! $record = DAOcms_form::instance ()->get ($pIdHelt)){
			throw new HeadingElementInformationNotFoundException ($pIdHelt);
		}
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->getById ($pIdHelt, self::FORM_TYPE);
		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($element);

		return $element;
	}

	/**
	 * Récupère un enregistrement par son identifiant publique
	 *
	 * @param int $pPublicId Identifiant interne de l'élément à récupérer
	 */
	public function getByPublicId ($pPublicId){
		$element = _ioClass ('heading|HeadingElementInformationServices')->get ($pPublicId, self::FORM_TYPE);
		if (($record = DAOcms_form::instance ()->get ($element->id_helt)) === false) {
			throw new HeadingElementInformationNotFoundException ($pPublicId);
		}
		_ppo ($record)->saveIn ($element);
		return $element;
	}
	
	/**
	 * Récupération ou création d'un formulaire
	 * Stockage en session
	 * @return unknown_type
	 */
	public function getOrCreateCmsForm() {
		//Récupération du numéro de modification
		$editId = _request ('editId', null);
				
		if (($id = CopixSession::get ('id_helt', $editId)) != null) {
			$toEdit = $this->getById($id);
			$toEdit->content = DAOcms_form::instance ()->getContent($id);
		} else {
			$toEdit = _ppo (DAORecordcms_form::create ());
			$toEdit->content = array();
			$toEdit->parent_heading_public_id_hei = CopixSession::get ('heading', $editId);
		}

		$toEdit->editId = $editId;
		$toEdit->contentOrder = false;

        //On mémorise les identifiants du contenu, ils serviront lors de la mise à jour pour supprimer les anciennes valeurs
        $arIdsContent = array();
        foreach ($toEdit->content as $cfc) {
            $arIdsContent[] = $cfc->cfc_id;
        }
        $toEdit->arIdsContent = $arIdsContent;

		//Mise en session
		CopixSession::set (self::STR_SESSION, $toEdit, $editId);
		
		return $toEdit;
	}
	
	/**
	 * Récupération du formulaire en cours de modification
	 * @return cms_form
	 */
	public function getCurrentForm() {
		if ($this->_currentForm != null) {
            return $this->_currentForm;
        }
        CopixRequest::assert ('editId');
        $element = CopixSession::get (self::STR_SESSION, _request ('editId'));
        if (!$element){
            throw new CopixException ('Element en cours de modification perdu');
        }

        $this->_currentForm = $element;

        return $element;
	}
	
	/**
	 * Récupère la liste des champs du formualire courant de type $pType
	 * @param $pType
	 * @return array
	 */
	public function getFormFieldByType ($pType) {
		$form = $this->getCurrentForm();
		$toReturn = array();
		
		foreach ($form->content as $field) {
			if ($field->cfe_type == $pType) {
				$toReturn[$field->cfc_id_element] = $field->cfe_label;
			}
		}
		return $toReturn;
	}
	
	/**
	 * Construction du formulaire CopixForm pour un CmsForm
	 * @return CopixForm
	 */
	public function getCmsFormForm() {

		$formConfig = new Form_Config();
		
		$form = new CopixFormLight('cms_form');
		$form->setTitle('CMS Formulaire');
		
		$form->setSubmitUrl('form|admin|submit')
			->attachField ('cf_id', _field ('hidden'), array ())
			->attachField ('editId', _field ('hidden'), array ())
			->attachField ('caption_hei', _field ('varchar', array('extra'=>'style="width:100%"')), 
							array ('label'=>'Nom :', 'require'=>true))
			->attachField ('description_hei', _field ('varchar', array('extra'=>'style="width:100%"')),
							array ('label'=>'Description :', 'require'=>false))
			->attachField ('cf_route', _field ('select', array('values'=>$formConfig->getRoutes(),'extra'=>'style="width:100%"', 'emptyShow'=>true)), 
							array ('label'=>'Validation :', 'require'=>true));
			
		return $form;
	}
	
	/**
	 * Sauvegarde du formulaire en base
	 * @param $arValues
	 * @return unknown_type
	 */
	public function insert($pValues) {
		HeadingCache::clear ();
		$ppoValues = _ppo($pValues);
		
		CopixDB::begin ();
		try {
			//création de l'enregistrement
			$record = DAORecordcms_form::create ()->initFromDbObject ($ppoValues);

			DAOcms_form::instance ()->insert ($record);
			
			$record->id_helt = $record->cf_id;
			$record->type_hei = self::FORM_TYPE;
			$record->caption_hei = $ppoValues['caption_hei'];
			$record->title_hei = $ppoValues['caption_hei'];
			$record->parent_heading_public_id_hei = $ppoValues['parent_heading_public_id_hei'];

			//dans $record->id_helt on a le nouvel identifiant
			_ioClass ('heading|HeadingElementInformationServices')->insert ($record);
			
			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_form::instance ()->update ($record);
			
			//On insère le contenu du formulaire
			$this->saveFormContent($record->cf_id,$ppoValues->content);
						
			CopixDB::commit ();
		} catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		HeadingCache::clear ();
	}
	
	public function update ($pValues){
		HeadingCache::clear ();
		$ppoValues = _ppo ($pValues);

		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($ppoValues['cf_id']);

			//on met a jour les données spécifiques
			$record->caption_hei = $ppoValues['caption_hei'];
			$record->description_hei = $ppoValues['description_hei'];
			$record->cf_theme = $ppoValues['cf_theme'];
			$record->cf_route = $ppoValues['cf_route'];
			$record->cf_route_params = $ppoValues['cf_route_params'];
			
			DAOcms_form::instance ()->update ($record);
				
			//On insère le contenu du formulaire
			$this->saveFormContent($record->cf_id,$ppoValues->content,$pValues->arIdsContent);
			
			
			//on met a jour les données génériques
			foreach (_ioClass ('heading|HeadingElementInformationServices')->getFields () as $propertyName){
				$record->$propertyName = $ppoValues[$propertyName];
			}
			_ioClass ('heading|HeadingElementInformationServices')->update ($record);
			CopixDB::commit ();
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		HeadingCache::clear ();
	}
	
	/**
	 * Sauvegarde en base du formulaire courant
	 * @param $idForm	identifiant du formulaire
	 * @param $arContent contenu du formulaire
     * @param $arIdsContent id de l'ancien contenu
	 * @return unknown_type
	 */
	public function saveFormContent($idForm, $arContent, $arIdsContent = array()) {
		HeadingCache::clear ();
		
		$arIdsNewContent = array();
		//Sauvegarde du contenu
		foreach ($arContent as $cmsFormContent) {
			$arIdsNewContent[] = $cmsFormContent->cfc_id;
            $cmsFormContent->cfc_id_form = $idForm;
			
			$dbAction = 'insert';
			if ($cmsFormContent->cfc_id != null) {
				$dbAction = 'update';
			}
			DAOcms_form_content::instance ()->{$dbAction}($cmsFormContent);
		}

        //Suppression des anciens contenus
        $arIdsToDelete = array_diff($arIdsContent, $arIdsNewContent);
		if (count($arIdsToDelete) > 0) {
			$sp = _daoSP()->addSQL ('cfc_id IN (' . implode(',', $arIdsToDelete) . ')')
						  ->addCondition('cfc_id_form', '=', $idForm);
			DAOcms_form_content::instance ()->deleteBy($sp);
		}

		HeadingCache::clear ();
	}
	
	/**
	 * Suppression d'un formulaire et de son contenu
	 * (Appelé par HeadingElementService)
	 * @param $pIdHelt id du formulaire
	 * @return void
	 */
	public function deleteById($pIdHelt) {
		HeadingCache::clear ();
		$record = DAOcms_form::instance ()->get($pIdHelt);
		DAOcms_form::instance ()->delete($record);
		HeadingCache::clear ();
	}
	
	/**
	 * Suppression d'un formulaire et de son contenu
	 * (Appelé par HeadingElementService)
	 * @param $pIdHelt id du formulaire
	 * @return void
	 */
	public function delete ($pArPublicId) {
		HeadingCache::clear ();
		$results = DAOcms_form::instance ()->findBy (_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId))->fetchAll();
		foreach ($results as $result){
			DAOcms_form::instance ()->delete($result);
		}
		HeadingCache::clear ();
	}

	
	/**
	 * Prévisualisation de l'élément
	 * @param string $pId
	 */
	public function previewById ($pId){
		_tag ('mootools', array ('plugins'=>'smoothbox'));
		
		$tpl = new CopixTpl();
		$tpl->assign('id', $pId);
		$tpl->assign('description_hei', _ioClass('heading|headingelementinformationservices')->getById($pId, 'form')->description_hei);
		
		//Activation du lien smoothbox
		CopixHTMLHeader::addJSDOMReadyCode('TB_init();');
		
		$toReturn = $tpl->fetch('form|heading.preview.tpl');
		
		return $toReturn;
	}
	
	/* *** Gestion du contenu du formulaire *** */
	
	/**
	 * Déplacement d'un élément vers le haut
	 * @param $pIdElement
	 * @return void
	 */
	public function moveUpElement($pIdElement) {
		HeadingCache::clear ();
		$content = $this->getCurrentForm()->content;
		$order_up = 0;
		$order_down = 0;
		foreach ($content as $key => $element) {
			if (intval($element->cfc_id_element) == $pIdElement) {
				if (isset($content[$key-1])) {
					$order_up = $content[$key-1]->cfc_order;
					$order_down = $element->cfc_order;
					
					//On inverse les ordres des deux éléments
					$element->cfc_order = $order_up;
					$content[$key-1]->cfc_order = $order_down;
					
					$this->_sortCurrentFormContent();
					break;
				}
			}
		}
		HeadingCache::clear ();
	}
	
	/**
	 * Déplacement d'un élément vers le bas
	 * @param $pIdElement
	 * @return void
	 */
	public function moveDownElement($pIdElement) {
		HeadingCache::clear ();
		$content = $this->getCurrentForm()->content;
		$order_up = 0;
		$order_down = 0;
		foreach ($content as $key => $element) {
			if (intval($element->cfc_id_element) == $pIdElement) {
				if (isset($content[$key+1])) {
					$order_up = $content[$key+1]->cfc_order;
					$order_down = $element->cfc_order;
					
					//On inverse les ordres des deux éléments
					$element->cfc_order = $order_up;
					$content[$key+1]->cfc_order = $order_down;
					
					$this->_sortCurrentFormContent();
					break;
				}
			}
		}
		HeadingCache::clear ();
	}
	
	/**
	 * Trie le contenu du formulaire courant par ordre croissant
	 * @return void
	 */
	private function _sortCurrentFormContent() {
		usort($this->getCurrentForm()->content, 'compareContent');
	}
	
	/**
	 * Initialisation de l'ordre des éléments
	 * @param $pArIdContentSorted
	 * @return void
	 */
	public function setContentOrder($pArIdContentSorted) {
		$this->getCurrentForm()->contentOrder = $pArIdContentSorted;
	}
	
	/**
	 * Mise à jour de l'ordre des éléments
	 * @param $pArIdContentSorted
	 * @return void
	 */
	public function updateContentOrder() {
		if ($this->getCurrentForm()->contentOrder === false) {
			return;
		}
		$arOrderedElementIds = array_flip(explode(',', $this->getCurrentForm()->contentOrder));
        
		foreach ($this->getCurrentForm()->content as $key => $element) {
			$element->cfc_order = ($arOrderedElementIds[$element->cfc_id_element] + 1) * self::ORDER_STEP;
		}
        
		$this->_sortCurrentFormContent();
		$this->getCurrentForm()->contentOrder = false;
	}
	
	/**
	 * Recupère la liste des champs d'un formulaire
	 *
	 * @param int $pId_form
	 * @return array
	 */
	public function getFormFields ($pId_form){
		$query = "SELECT * FROM cms_form_content, cms_form_element
				WHERE cfe_id = cfc_id_element
				AND cfc_id_form = :id_form
				ORDER BY cfc_order";
		$results = _doQuery($query, array(':id_form'=>$pId_form));
		return $results;
	}
	
	/**
	 * Retourne les dates d'envois de saisie du formulaire $pId_form
	 *
	 * @param int $pId_form
	 * @return array
	 */
	public function getDatesEnvois ($pId_form, $pDateDebut = false, $pDateFin = false){
		$query = "SELECT DISTINCT cfv_date FROM cms_form_values
					WHERE cfv_id_form = :id_form ";
		$params = array(':id_form'=>$pId_form);
		if($pDateDebut){
			$query .= " AND cfv_date > :dateDebut ";
			$params[':dateDebut'] = $pDateDebut;
		}
		if($pDateFin){
			$query .= " AND cfv_date < :dateFin ";
			$params[':dateFin'] = $pDateFin;
		}
		$query .= "ORDER BY cfv_date DESC";
		$results = _doQuery($query, $params);
		return $results;
	}
	
	/**
	 * Retourne les valeurs d'un envoi de formulaire à une date donnée.
	 *
	 * @param date $pDateEnvoi
	 * @return array
	 */
	public function getValues ($pDateEnvoi){
		$query = "SELECT * FROM cms_form_values WHERE cfv_date = :date";
		$results = _doQuery($query, array(':date'=>$pDateEnvoi));
		$toReturn = array();
		foreach ($results as $result){
			$toReturn[$result->cfv_id_element] = $result;
		}
		return $toReturn;
	}
	
	/**
	 * Retourne les éléments qui sont liés à $pPublicId
	 *
	 * @param int $pPublicId Identifiant publique
	 * @return array
	 */
	public function getDependencies ($pPublicId) {
		return array ();
	}

	/**
	 * Permet de changer les actions (couper, copier, etc) possibles sur un élément
	 * /!\ A ne pas appeler directement, passer par HeadingElementInformationServices::getActions ()
	 *
	 * @param stdClass $pElement Enregistrement de l'élément
	 * @param stdClass $pActions Actions déja prédéfinies par HeadingElementInformationServices::getActions
	 */
	public function getActions ($pElement, $pActions) {
		$pActions->copy = false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts (){
		$toReturn['specific'] = _doQuery ('select * from cms_form where cf_id not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'form', ':status'=>HeadingElementStatus::DELETED));
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select cf_id from cms_form)', array (':type'=>'form'));
		return $toReturn;
	}	
}

/**
 * Fonction de comparaison pour le tri du contenu d'un formulaire
 * @param $a
 * @param $b
 * @return int
 */
function compareContent($a, $b)
{
    if ($a->cfc_order == $b->cfc_order) {
        return 0;
    }
    return ($a->cfc_order < $b->cfc_order) ? -1 : 1;
}
