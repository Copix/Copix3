<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Gérald Croës
 */

/**
 * Actions de préparation à la modificiation / création sur les éléments de rubrique
 *
 * @package cms
 * @subpackage heading
 */
class ActionGroupElement extends CopixActionGroup {

    protected function _beforeAction ($pAction) {
        //Dans un premier temps, on regarde si l'on dispose d'un editId (pour pouvoir passer dans la méthode finalizeEdit)
        if (_request ('editId') !== null) {
            //On dispose d'un editId, on récupère la rubrique d'édition courante.
            $heading = CopixSession::get ('heading', _request ('editId'));
        }else {
            //Pas d'édition en cours, on regarde la rubrique que l'on souhaite éditer.
            $heading = _request ('heading', 0);
        }

        //On test si l'on est admin ou si l'on peut écrire dans la rubrique donnée
        if (! (CopixAuth::getCurrentUser ()->testCredential ('basic:admin') || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'.$heading))) {
            throw new CopixCredentialException ('basic:admin');
        }
        
        CopixPage::add ()->setIsAdmin (true);
        if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
        _ioClass ('HeadingElementInformationServices')->breadcrumbAdmin ();
    }

    /**
     * Ecran d'administration par défaut
     */
    public function processDefault () {
        //création des classes de service.
        $services = _ioClass ('HeadingElementInformationServices');
        $headingElementType = new HeadingElementType ();

        $ppo = _ppo ();
        $ppo->heading = _request ('heading', 0);
        $ppo->TITLE_PAGE = 'Administration des contenus du site';
        $ppo->arHeadingElementTypes = $headingElementType->getList ();
        $ppo->sort = CopixUserPreferences::get ('heading|elements|sort|' . $ppo->heading, CopixUserPreferences::get ('heading|defaultSort'));
        $ppo->arHeadingElementInformations = $services->getGroupByStatusMaxVersion ($ppo->heading, $ppo->sort);
        $ppo->prefDisplay = CopixUserPreferences::get ('heading|elements|display|' . $ppo->heading, CopixUserPreferences::get ('heading|defaultDisplay'));
        $ppo->prefThumbHeight = CopixUserPreferences::get ('heading|thumbnailHeight');
        $ppo->prefThumbWidth = CopixUserPreferences::get ('heading|thumbnailWidth');

        //on récupère la rubrique parent
        $ppo->parentHeading = $services->get ($ppo->heading);

        //on affiche le theme de la rubrique dans laquelle on est.
        $theme = $services->getTheme ($ppo->parentHeading->public_id_hei, $foo);
        if ($theme != null && CopixConfig::get ('heading|useDefinedThemeForAdmin') == 1) {
            CopixTpl::setTheme ($theme);
        }

        //Au cas ou on souhaite afficher directement un élément
        $ppo->id_helt = _request ('id_helt');
        $ppo->type_hei = _request ('type_hei');
        $ppo->selectedItems = array();
        $selectedItems = _request ('selected', array());
        if (!empty($selectedItems)) {
            foreach ($selectedItems as $infos) {
                list ($idHelt, $type) = explode ('|', $infos);
                // lors de l'annulation création d'un élément on n'a pas les identifiants
                try {
                    $ppo->selectedItems[] = $services->getById ($idHelt, $type);
                } catch (Exception $e) {

                }
            }
        }

        // On regarde s'il faut autoriser l'opération "coller"
        $ppo->canPaste = HeadingClipboard::canPaste ($ppo->heading);
        if ($ppo->canPaste) {
            $ppo->clipboard = HeadingClipboard::getElements ();
            $ppo->clipboardMode = HeadingClipboard::getMode ();
            $ppo->clipboardPath = HeadingClipboard::getPath ();
        }

		ZoneHeadingBookmarks::setHeading ($ppo->heading);
		ZoneHeadingAdminBreadcrumb::setHeading ($ppo->heading);

        return _arPpo ($ppo, 'headingelements.admin.php');
    }

    /**
     * Demande de création d'un élément de type donné dans une rubrique
     */
    public function processPrepareCreate () {
        CopixRequest::assert ('type', 'heading');

        //on vérifie que le type d'élément est connu
        $typeInformations = _ioClass ('heading|headingelementtype')->getInformations ($type = _request ('type'));

        //On crée le namespace de modification
        $editId = uniqid  ('record_');
        CopixSession::set ('type_hei', $type, $editId);
        CopixSession::set ('heading', _request ('heading'), $editId);
        if (_request('then', false)) {
            CopixSession::set ('then', _request ('then'), $editId);
        }

        //on va vers la page de création de l'élément
        return _arRedirect (_url ($typeInformations['adminurl'], array ('editId'=>$editId)));
    }

    /**
     * si l'on vient de l'elementCHooser, on ne connait pas le id_hei ni le heading mais seulement le public_id
     * On redirige alors vers prepareEdit
     *
     * @return unknown
     */
    public function processPrepareExplorerEdit () {
        CopixRequest::assert ('elementChooser', 'public_id_hei');
        $element = _ioClass ('headingelementinformationservices')->get (_request ('public_id_hei'));
        return _arRedirect(_url('heading|element|prepareedit', array('type'=>$element->type_hei, 'id'=>$element->id_helt, 'heading'=>$element->parent_heading_public_id_hei)));
    }

    /**
     * Demande la modification d'un élément de type donné (appel à une méthode dans le module conserné)
     *
     * @return unknown
     */
    public function processPrepareEdit () {
        CopixRequest::assert ('type', 'heading', 'id');

        //on vérifie qu'il existe (une exception est générée sinon)
        $element = _ioClass ('headingelementinformationservices')->getById (_request ('id'), _request ('type'));

        //on vérifie que le type d'élément est connu
        $typeInformations = _ioClass ('heading|headingelementtype')->getInformations ($type = _request ('type'));

        //Si on demande à modifier autre chose qu'un brouillon,
        // on vérifie s'il n'existe pas un brouillon en cours sur l'élément

        if (($element->status_hei != HeadingElementStatus::DRAFT && $element->status_hei != HeadingElementStatus::PLANNED) && ! _request('newDraft', 0)) {
            try {
                $drafts = _ioClass ('headingelementinformationservices')->getDrafts ($element->public_id_hei);
				
                $tpl= new CopixTpl();
                $tpl->assign ('drafts', $drafts);
                $tpl->assign ('element', $element);
                $ppo = _ppo();
                $ppo->TITLE_PAGE = "Modifier un brouillon existant ou travailler sur la version publiée ?";
                $ppo->MAIN = $tpl->fetch('headingelementsdraft.admin.php');
                
                return _arPPO($ppo, 'generictools|blank.tpl');
            } catch (HeadingElementInformationNotFoundException $e) {
                //il n'y a pas de brouillon, pas besoin de message de confirmation
            }
        }

        //On crée le namespace de modification
        $editId = uniqid ('record_');
        CopixSession::set ('id_helt', _request ('id'), $editId);
        CopixSession::set ('type_hei', $type, $editId);
        CopixSession::set ('heading', $element->parent_heading_public_id_hei, $editId);
		if (_request('then', false)) {
            CopixSession::set ('then', _request ('then'), $editId);
        }

        return _arRedirect (_url ($typeInformations['adminurl'], array ('editId'=>$editId)));
    }

    /**
     * Finalization de la modification d'un élément
     */
    public function processFinalizeEdit () {
        $heading = CopixSession::get ('heading', _request ('editId'));
        $redirect = CopixSession::get('then', _request ('editId'));
        CopixSession::destroyNamespace (_request ('editId'));
		$aParam =  array ('heading'=>$heading, 'selected'=>_request('selected', array()));
		if (CopixRequest::exists ('prevaction')) {
			$aParam['prevaction'] = _request ('prevaction');
		}
		if ($redirect){
			$redirect = CopixUrl::appendToUrl($redirect, array('selectedElementsFromCms'=>_request('selected')));
		}
        return _arRedirect ($redirect ? $redirect : _url ('heading|element|', $aParam));
    }

    /**
     * On déplace un element à une position donnée
     *
     * @return unknown
     */
    public function processMove () {
        CopixRequest::assert ('id', 'type', 'oldPosition', 'newPosition');
        $element = _ioClass('headingelementinformationservices')->getById (_request('id'), _request('type'));
        _ioClass('headingelementinformationservices')->reorderAndMoveWithOldPosition ($element->public_id_hei, _request('oldPosition'),_request('newPosition'));
        return _arRedirect (_url ('element|', array ('heading' => $element->parent_heading_public_id_hei)));
    }

    /**
     * "Coupe" un élément (enregistre l'identifiant en session)
     */
    public function processCut () {
        $elements = _request ('elements');
        $selected = array ();
        if (count ((array) $elements)) {
            $arCut = array ();
            foreach ($elements as $key => $element) {
                //on ne souhaite mettre dans le clipboard que les identifiants publics.
                $element = explode ('|', $element);
                $arCut[] = _ioClass ('headingelementinformationservices')->getPublicId ($element[0], $element[1]);
                $selected[] = $element[0] . '|' . $element[1];
            }

            // On enregistre l'élément public en session
            HeadingClipboard::set ($arCut);
            HeadingClipboard::setMode (HeadingClipboard::MODE_CUT);
        }
        return _arRedirect (_url ('element|', array ('heading' => _request ('heading'), 'selected' => $selected)));
    }

    /**
     * "Copie" un élément (enregistre l'identifiant en session)
     */
    public function processCopy () {
        $headingelementinformationservices = new HeadingElementInformationServices ();

        $selected = array ();
        if (count ((array) CopixRequest::get ('elements'))) {
            $elements = CopixRequest::get ('elements');
            $arCopy = array ();
            foreach ($elements as $key => $element) {
                $element = explode ('|', $element);
                // On récupère les identifiants publics
                $arCopy[] = $headingelementinformationservices->getPublicId ($element[0], $element[1]);
                $selected[] = $element[0] . '|' . $element[1];
            }

            // On enregistre l'élément public en session
            HeadingClipboard::set ($arCopy);
            HeadingClipboard::setMode (HeadingClipboard::MODE_COPY);
        }
        return _arRedirect (_url ('element|', array ('heading' => _request ('heading'), 'selected' => $selected)));
    }

    /**
     * Coller le contenu coupé ou collé dans la rubrique courante.
     */
    public function processPaste () {
        CopixRequest::assert ('heading');
        $headingElementInformationServices = new HeadingElementInformationServices ();

        $selected = array ();
        $arData = HeadingClipboard::getContent ();
        if (count ($arData) > 0) {
            foreach ($arData as $value) {
                if (HeadingClipboard::getMode () == HeadingClipboard::MODE_CUT) {
                    //Couper
                    $headingElementInformationServices->move ($value, _request ('heading'));
                    $element = $headingElementInformationServices->get ($value);
                    $selected[] = $element->id_helt . '|' . $element->type_hei;
                } else {
                    //Copier
                    $public_id = $headingElementInformationServices->copy ($value, _request ('heading'));
                    $element = $headingElementInformationServices->get ($public_id);
                    $selected[] = $element->id_helt . '|' . $element->type_hei;
                }
            }
            HeadingClipboard::clear ();
        } else {
            throw new CopixException ('Aucun élément a coller');
        }

        return _arRedirect (_url ('element|', array ('heading' => _request('heading'), 'selected' => $selected)));
    }

    /**
     * Vide le contenu du presse-papier
     *
     * @return CopixActionReturn
     */
    public function processClearClipboard () {
        HeadingClipboard::clear ();
        return _arRedirect (_url ('heading|element|', array ('heading' => _request ('heading'))));
    }

    /**
     * Actions groupées sur les éléments
     */
    public function processFormAction () {
        $fonctions = array ('archive', 'copy', 'cut', 'delete', 'move', 'paste', 'publish');
        $fonction = _request ('fonction');
        if (!in_array (strtolower ($fonction), $fonctions)) {
            throw new CopixException ('La fonction "' . $fonction . '" n\'existe pas.');
        }
        return $this->{'process'.$fonction}();
    }

    /**
     * Demande de suppression d'un élément.
     * Cette demande de suppression concerne toutes les version des documents et / ou rubriques sous rubriques
     * @TODO Faire en sorte que les éléments Brouillons et Planifiés faisant parti de la demande de suppression
     *  ne suppriment pas l'ensemble des versions des documents
     */
    public function processDelete () {
        //Récupération des paramètres
        if (count ((array) CopixRequest::get ('elements'))) {
            $arHeadings = $arAllVersion = $arElementsPublicId = $arOthers = $selected = array ();

            foreach (_request ('elements') as $elementsId) {
                $ids = explode ('|', $elementsId);
                $element = _ioClass ('HeadingElementInformationServices')->getById ($ids[0], $ids[1]);
                $selected[] = $element->id_helt . '|' . $element->type_hei;
                if ($element->type_hei == 'heading') {
                    $arHeadings[] = $element;
                    $arElementsPublicId[] = $element->public_id_hei;
                }elseif ($element->status_hei == HeadingElementStatus::PUBLISHED) {
                    $arAllVersion[] = $element;
                    $arElementsPublicId[] = $element->public_id_hei;
                }else {
                    $arOthers[] = $element;
                }
            }

            $nbToDelete = count ($arHeadings) + count ($arAllVersion);

            //Demande de confirmation si ce n'est pas déjà fait.
            if (! _request ('confirm')) {
                $text = '';
                if (count ($arHeadings)) {
                    $text .= 'Êtes vous sûr de vouloir supprimer '.(count ($arHeadings) == 1 ? 'la' : 'les '.count ($arHeadings)). ' rubrique'.(count ($arHeadings) == 1 ? '' : 's').' ';
                    $arCaptions = array ();
                    foreach ($arHeadings as $heading) {
                        $arCaptions[] = $heading->caption_hei;
                    }
                    $text .= implode (', ', $arCaptions).' ainsi que '.(count ($arHeadings) == 1 ? 'ses' : 'leurs').' sous-rubriques ?';
                }

                if (count ($arAllVersion)) {
                    if (count ($arHeadings)) {
                        $text .= '<br /> Vous supprimerez également toutes les versions ';
                    }else {
                        if (count($arAllVersion) == 1) {
                            $element = $arAllVersion[0];
                            if ($element->status_hei == HeadingElementStatus::PUBLISHED){
                            	$nbDependencies = count(_class("heading|headingelementinformationservices")->getDependencies ($element->public_id_hei));
                            	if ($nbDependencies){
									$text .= CopixZone::process("heading|headingelement/headingelementdependencies", array('public_id'=>$element->public_id_hei))."<br />";
									$text .= '<br /><br /><span style="color: red">';
									$text .= 'Attention : les références vers cet élément ne seront pas supprimées.';
									$text .= '<br />Par exemple, si vous supprimez un article qui est utilisé dans une page, la page fera encore référence à cet article.<br />';
									$text .= '</span><br />';
                            	} else {
                            		$text .= "Cet élément n'est pas utilisé.<br />";
                            	}
                            }
                        }
                        $text .= 'Êtes vous sûr de vouloir supprimer toutes les versions ';
                    }
                    if (count ($arAllVersion) > 1) {
                        $text .= 'des '.count ($arAllVersion).' éléments ';
                    }else {
                        $text .= 'de l\'élément ';
                    }
                    $arCaptions = array ();
                    foreach ($arAllVersion as $element) {
                        $arCaptions[] = $element->caption_hei;
                    }
                    $text .= implode (', ', $arCaptions);
                    if (count ($arHeadings)) {
                        $text .= '.';
                    }else {
                        $text .= ' ?';
                    }
                }

                if (count ($arOthers)) {
                    if (count($arOthers) == 1 && !(count ($arHeadings) || count ($arAllVersion))) {
                        $element = $arOthers[0];
                        if ($element->status_hei == HeadingElementStatus::PUBLISHED){
							$nbDependencies = count(_class("heading|headingelementinformationservices")->getDependencies ($element->public_id_hei));
                            if ($nbDependencies){
								$text .= CopixZone::process("heading|headingelement/headingelementdependencies", array('public_id'=>$element->public_id_hei))."<br />";
								$nbDependencies = count(_class("heading|headingelementinformationservices")->getDependencies ($element->public_id_hei));
								$text .= '<br /><br /><span style="color: red">';
								$text .= 'Attention : les références vers cet élément ne seront pas supprimées.';
								$text .= '<br />Par exemple, si vous supprimez un article qui est utilisé dans une page, la page fera encore référence à cet article.<br />';
								$text .= '</span><br />';
                            } else {
                            	$text .= "Cet élément n'est pas utilisé.<br />";
                            }
                        }
                    }

                    if (count ($arHeadings) || count ($arAllVersion)) {
                        $text .= '<br />Enfin, vous supprimerez également ';
                    }else {
                        $text .= 'Êtes vous sûr de vouloir supprimer ';
                    }
                    if (count ($arOthers) > 1) {
                        $text .= 'les '.count ($arOthers).' éléments ';
                    }else {
						if ($arOthers[0]->status_hei == HeadingElementStatus::DRAFT) {
							$text .= 'le brouillon ';
						} else {
							$text .= 'l\'élément ';
						}
                    }
                    $arCaptions = array ();
                    foreach ($arOthers as $element) {
                        $arCaptions[] = $element->caption_hei;
                    }
                    $text .= implode (', ', $arCaptions);
                    if (count ($arHeadings) || count ($arAllVersion)) {
                        $text .= '.';
                    }else {
                        $text .= ' ?';
                    }
                }

                return CopixActionGroup::process ('generictools|Messages::getConfirm', array (
                        'message' => $text,
                        'title' => 'Confirmez-vous la demande de suppression définitive ?',
                        'confirm' => _url ('element|delete', array ('confirm' => 1, 'elements' => _request ('elements'))),
                        'cancel' => _url ('element|', array ('heading' => $element->parent_heading_public_id_hei, 'selected' => $selected))
                ));
            }

            //on réalise la supression
            _ioClass ('headingelementinformationservices')->delete ($arElementsPublicId, $arOthers);
            return _arRedirect (_url ('element|', array ('heading'=>$element->parent_heading_public_id_hei)));
        }
        return _arRedirect (_url ('element|', array ('heading' => _request ('heading'))));
    }

    /**
     * Procède à la publication des éléments demandés.
     */
    public function processPublish () {
        //récupération des éléments a publier
        if (count ((array) CopixRequest::get ('elements'))) {
            $hei = _ioClass ('heading|HeadingElementInformationServices');
            $selected = array ();
            foreach (_request ('elements') as $elementsId) {
                $ids = explode ('|', $elementsId);
                $element = $hei->getById ($ids[0], $ids[1]);
                if ($element->type_hei !== 'heading') {
                    $arToPublish [] = $element;
                }
                $selected[] = $element->id_helt . '|' . $element->type_hei;
            }

            //lancement de la publication
            CopixDB::begin ();
            try {
                foreach ($arToPublish as $element) {
                    $hei->publishById ($element->id_helt, $element->type_hei);
                }
                CopixDB::commit ();
            }catch (CopixException $e) {
                CopixDB::rollback ();
                throw $e;
            }
            $aParam =  array ('heading'=>$element->parent_heading_public_id_hei, 'selected'=>$selected);
            // Possibilité de notifier par email qu'un contenu a été publié
			$headingElementType = new HeadingElementType ();
			$typeInformations = $headingElementType->getInformations ($element->type_hei);
			if (CopixUserPreferences::get($typeInformations['module'].'|'.$element->type_hei.'Notification') == '1') {
                // Previous Action
                $aParam['prevaction'] = 'publish';
            }
            return _arRedirect (_url ('element|', $aParam));
        }
        return _arRedirect (_url ('element|', array ('heading' => _request ('heading'))));
    }

    /**
     * 
     * Planification des éléments envoyés
     */
    public function processPlan () {
    	$published_date_hei = _request('published_date', false) ? CopixDateTime::DateTimeToyyyymmddhhiiss(_request('published_date')) : null;
		$end_published_date_hei = _request('end_published_date', false) ? CopixDateTime::DateTimeToyyyymmddhhiiss(_request('end_published_date')) : null;
			
        //récupération des éléments a publier
		if (count ((array) CopixRequest::get ('elements')) && ($published_date_hei || $end_published_date_hei)) {
            $hei = _ioClass ('heading|HeadingElementInformationServices');
            $selected = array ();
            foreach (_request ('elements') as $elementsId) {
                $ids = explode ('|', $elementsId);
                $element = $hei->getById ($ids[0], $ids[1]);
                if ($element->type_hei !== 'heading') {
                    $arToPublish [] = $element;
                }
                $selected[] = $element->id_helt . '|' . $element->type_hei;
            }

            //lancement de la publication
            CopixDB::begin ();
            try {
                foreach ($arToPublish as $element) {
                    $hei->planById ($element->id_helt, $element->type_hei, $published_date_hei, $end_published_date_hei);
                }
                CopixDB::commit ();
            }catch (CopixException $e) {
                CopixDB::rollback ();
                throw $e;
            }
            $aParam =  array ('heading'=>$element->parent_heading_public_id_hei, 'selected'=>$selected);
            return _arRedirect (_url ('element|', $aParam));
        }
        return _arRedirect (_url ('element|', array ('heading' => _request ('heading'))));
    }
    
    /**
     * Procède à l'archivage d'un élément
     */
    public function processArchive () {
        $headingElementInformationServices = new HeadingElementInformationServices ();

        // Récupération des paramètres
        $arSelectHeadingElementInformations =  _request ('elements');
        if (is_array ($arSelectHeadingElementInformations) && count ($arSelectHeadingElementInformations)) {
            $arElementsPublicId = array ();
            $selected = array ();
            $headings = array ();
            foreach ($arSelectHeadingElementInformations as $elementsId) {
                $ids = explode ('|', $elementsId);
                $element = $headingElementInformationServices->getById ($ids[0], $ids[1]);
                if ($element->type_hei == 'heading') {
                    $headings[] = $element->caption_hei;
                }
                $arElementsPublicId[] = $element->public_id_hei;
                $selected[] = $element->id_helt . '|' . $element->type_hei;
            }
            if (count ($headings) > 0 && _request ('headingsOk') != 'true') {
                return CopixActionGroup::process ('generictools|messages::getConfirm', array (
                        'message' => 'Si vous archivez une rubrique, tous les enfants de cette rubrique seront archivés.<br />Archiver les éléments sélectionnés ?',
                        'confirm' => _url ('heading|element|Archive', array ('elements' => $arSelectHeadingElementInformations, 'headingsOk' => 'true', 'heading' => _request ('heading'))),
                        'cancel' => _url ('heading|element|', array ('heading' => _request ('heading')))
                ));
            }
            $headingElementInformationServices->archive ($arElementsPublicId);
        }

        return _arRedirect (_url ('element|', array ('heading' => _request ('heading'), 'selected' => $selected)));
    }
    /**
     * Affiche une page avec la dernière activité sur les éléments.
     */
    public function processLastActivity () {
        $ppo = new CopixPpo ();
        $ppo->TITLE_PAGE = 'Dernières modifications';

        $headingElementInformationServices = new HeadingElementInformationServices ();
        $ppo->arLastUpdated = $headingElementInformationServices->find (array ('date_update_hei_from'=>date ('YmdHis', strtotime('-30 days')), 'order_by'=>'date_update_hei'));

        return _arPpo ($ppo, 'headingelementinformations.lastupdate.tpl');
    }

    public function processNotify () {
        $aRequestParams = CopixRequest::asArray ();
        unset ($aRequestParams['error']);
        $ppo = _ppo ();
        if( ($ppo->mail = CopixSession::get ('heading|email|notify')) == null) {
            $ppo->mail = array ();
        }
        $ppo->mail['send'] = true;
        $ppo->mail['dest'] = _request('emailNotification');
        $ppo->mail['subject'] = str_replace('{$TITLE_PAGE}', 'Notification de mise à jour du contenu', CopixConfig::get ('default|titleBar'));
        $ppo->mail['msg'] = 'Bonjour,<br />';
        $aSelected = (array) _request('selected');
        $ppo->mail['msg'] .= ((sizeof ($aSelected) > 1) ? 'Les contenus suivants ont' : 'Le contenu suivant a').' été mis à jour :<br />';
        foreach ($aSelected as $infos) {
			list ($idHelt, $type) = explode ('|', $infos);
            $element = _class ('heading|headingelementinformationservices')->getById ($idHelt, $type);
            $url = _url('heading||', array('public_id' => $element->public_id_hei));
			$ppo->mail['msg'] .= ' <a href="'.$url.'" title="'.$element->caption_hei.'">'.$url.'</a><br />';
        }
        CopixSession::set ('heading|email|notify', $ppo->mail);
        $oMail = new CopixHTMLEMail ($ppo->mail['dest'], null, null, $ppo->mail['subject'], $ppo->mail['msg']);
        $oError = $oMail->check ();
        if ($oError->isError ()) {
            $aRequestParams['error'] = $oError->asArray();
            $aRequestParams['prevaction'] = 'publish';
        }
        else if (!$oMail->send (CopixConfig::get ('default|mailFrom'), CopixConfig::get ('default|mailFromName'))) {
            $aRequestParams['error'] = $oMail->getErrors ();
            if (sizeof ($aRequestParams['error']) == 0) {
                $aRequestParams['error'][] = 'L\'email n\'a pas pu être envoyé';
            }
            $aRequestParams['prevaction'] = 'publish';
        }
        else {
            CopixSession::delete ('heading|email|notify');
        }
        unset ($aRequestParams['module'], $aRequestParams['group'], $aRequestParams['action'], $aRequestParams['emailNotification']);
        return _arRedirect (_url ('element|', $aRequestParams));
    }

}