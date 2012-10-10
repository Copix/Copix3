<?php
/**
* @package	cms
* @author	Croës Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */
CopixClassesFactory::fileInclude ('cms|CMSPage');

/**
 * @package cms
* Services divers pour la manipulation des pages
*/
class ServicesCMSPage {
    /**
    * Supression d'une page donnée
    * 
    * Lance deux événements CMSPageDelete(id) & DeletedContent(id, type=cms)
    * @param	numeric	$id	l'identifiant de la page à supprimer (identifiant publique)
    */
    function deleteOnline ($id){
        $daoPage = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        $record  = $daoPage->deleteByPublicId ($id);
        CopixEventNotifier::notify (new CopixEvent ('CMSPageDelete', array ('id'=>$id)));
        CopixEventNotifier::notify (new CopixEvent ('DeletedContent', array ('id'=>$id, 'type'=>'cms')));
    }

    /**
    * Récupération d'une liste de page
    * @param	boolean	$draft	indique si l'on souhaite récupérer les brouillons
    * @param 	string	$user	login utilisateur pour limiter la recherche
    * @return	array	liste des pages qui correspondent
    */
    function getList ($draft=false, $user=null) {
        $dao      = CopixDAOFactory::getInstanceOf ('CMSPage');
    	if ($draft === false){
	        $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
	        return $dao->findByStatus ($workflow->getPublish ());
    	}else{
	        return $dao->findOnlineAndAuthorDraft ($user);
        }
    }

    /**
    * Recherche des pages dans une rubrique
    * @param	numeric	$id_head	identifiant de la rubrique
    * @param 	boolean $lastOnly	indique si l'on souhaite obtenir les dernières versions uniquement
    * @return 	array	liste d'enregistrements représentant des pages
    */
    function findByHeading ($id_head, $lastOnly=true){
        $daoPages = CopixDAOFactory::getInstanceOf ('CMSPage');
        $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        return $daoPages->findByHeading ($id_head, $workflow->getPublish (), $lastOnly);
    }

    /**
    * Récupération du modèle pour la rubrique donnée
    * @param	numeric	$head	la rubrique dont on souhaites connaitre la liste des modèles disponibles
    * @return 	array	liste des pages modèles disponibles pour la rubrique donnée
    */
    function getModelFor ($head){
        $dao      = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $toReturn = array ();
        foreach ($dao->findByHeading ($head, $workflow->getPublish (), true) as $element){
           if (($tmpPage    = ServicesCMSPage::getOnline ($element->publicid_cmsp)) != null){
              if ($tmpPage->hasPortletOfKind ('page')){
                 $toReturn[] = $element;
              }
           }
        }
        return $toReturn;
    }

    /**
    * récupération depuis la base de donénes.
    * retourne une CMSPage, du bon type (CMSWikiPage, CMSHTMLPage, ..., ...)
    */
    function getVersion ($publicId, $version) {
        $dao = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        //gets the main record.
        if (($record = $dao->getVersion ($publicId, $version)) === false){
            return null;
        }
        //creates the specific page we wants to deal with.
        return new CMSPage ($record);
    }

    /**
    * Création d'une nouvelle page vide
    * @param	string	$userLogin	le nom de l'utilisatuer qui a crée la page
    * @param 	id		$id_head	l'identifiant de la rubrique dans laquelle la page sera créée
    * @return 	CMSPage la nouvelle page
    */
    function create ($userLogin, $id_head=null){
        $page  = new CMSPage ();

        $page->id_head     = $id_head;
        $page->author_cmsp = $userLogin;

        //default status is draft
        $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $page->status_cmsp =  $workflow->getDraft ();

        $page->statusauthor_cmsp = $userLogin;
        $page->statusdate_cmsp = date('Ymd');

        return $page;
    }

    /**
    * Récupération d'un brouillon de page
    * @param	numeric	$publicId	l'identifiant de la page dont on souhaites obtenir le brouillon
    */
    function getDraft ($publicId){
        $dao = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        //gets the main record.
        if (($record = $dao->getDraft ($publicId)) === false){
            return null;
        }

        //creates the specific page we wants to deal with.
        return new CMSPage ($record);
    }

    /**
    * Récupération d'une page en ligne depuis son identifiant
    * @param 	int	$id	l'identifiant de la page (publicid_cmsp)
    * @return	CMSPage
    */
    public static function getOnline ($id){
    	$exists = false;
//    	Copix::RequireClass ('CopixMemoryCache');
//    	$object = CopixMemoryCache::get ('CMS', "Page - $id", $exists);
    	if ($exists){
    		return $object;
    	}

        $dao     = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        if ($record  = $dao->getOnline ($id)){
        	$return = new CMSPage ($record);
        	//15 minutes de cache.
        	//        	CopixMemoryCache::set ("CMS", "Page - $id", $return, 60*15);
        	return $return;
        }
        return null;
    }

    /**
    * insertion en base de données d'une CMSPage
    * utilise en interne les DAO du bon type.
    * On ne peut insérer que des brouillons...
    */
    function insert (& $CMSPage){
        $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $dao      = CopixDAOFactory::getInstanceOf ('cms|CMSPage');

        $record = $CMSPage->getRecord ();
        $record->status_cmsp = $workflow->getDraft ();
        $dao->insert ($record);
        //pas d'identifiant public ? alors on utilise l'identifiant automatique
        if ($record->publicid_cmsp == null){
        	$record->publicid_cmsp = $record->id_cmsp;
        }
        $dao->update ($record);
        $CMSPage->initFrom ($record);

        return $record->id_cmsp;
    }

    /**
    * mise à jour d'une CMSPage en base de données.
    * utilise les DAO en interne
    */
    function update ($CMSPage){
        $workflow    = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $record      = $CMSPage->getRecord ();//l'enregistrement commun

        $dao         = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        $dao->update ($record);
    }

    /**
    * Only drafts can be put in trash.
    */
    function setTrash ($id, $comment=null){
    	CopixClassesFactory::fileInclude('cms|cmsauth');
    	$user = CMSAuth::getUser();
        $workflow  = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        $dao = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        $record = $dao->getDraft ($id);
        $record->status_cmsp = $workflow->getTrash ();
        $record->statusauthor_cmsp = $user->login;
        $record->statusdate_cmsp = date('Ymd');
        $record->statuscomment_cmsp = $comment;
        $dao->update ($record);
    }

    /**
    * Only drafts can be called back in the created state
    * (generally cause of a trash rescue)
    */
    function setCreate ($id,$comment=null){
    	CopixClassesFactory::fileInclude('cms|cmsauth');
    	$user = CMSAuth::getUser();
    	$workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        $dao = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        $record= $dao->getDraft ($id);
        $record->status_cmsp = $workflow->getDraft ();
        $record->statusauthor_cmsp = $user->login;
        $record->statusdate_cmsp = date('Ymd');
        $record->statuscomment_cmsp = $comment;
        $dao->update ($record);
    }

    /**
    * Only drafts can be proposed
    */
    function setPropose ($id,$comment=null){
    	CopixClassesFactory::fileInclude('cms|cmsauth');
    	$user      = CMSAuth::getUser();
        $workflow  = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $dao       = CopixDAOFactory::getInstanceOf ('cms|CMSPage');

        $record = $dao->getDraft ($id);
        $record->status_cmsp = $workflow->getPropose ();
        $record->statusauthor_cmsp = $user->login;
        $record->statusdate_cmsp = date('Ymd');
        $record->statuscomment_cmsp = $comment;
        $dao->update ($record);

        CopixEventNotifier::notify (new CopixEvent ('CMSPagePropose',
            array ('draft'=>$record)));
    }

    /**
    * Only drafts can be refused.
    */
    function setRefuse ($id,$comment=null){
    	CopixClassesFactory::fileInclude('cms|cmsauth');
    	$user = CMSAuth::getUser();
        $workflow  = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $dao       = CopixDAOFactory::getInstanceOf ('cms|CMSPage');

        $record = $dao->getDraft ($id);
        $record->status_cmsp = $workflow->getRefuse ();
        $record->statusauthor_cmsp = $user->login;
        $record->statusdate_cmsp = date('Ymd');
        $record->statuscomment_cmsp = $comment;

        $dao->update ($record);
        CopixEventNotifier::notify (new CopixEvent ('CMSPageRefuse',
            array ('draft'=>$record)));
    }

    /**
    * Only drafts can be validate
    */
    function setValid ($id,$comment=null){
    	CopixClassesFactory::fileInclude('cms|cmsauth');
    	$user      = CMSAuth::getUser();
        $workflow  = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $dao       = CopixDAOFactory::getInstanceOf ('cms|CMSPage');

        $record = $dao->getDraft ($id);
        $record->status_cmsp = $workflow->getValid ();
        $record->statusauthor_cmsp = $user->login;
        $record->statusdate_cmsp = date('Ymd');
        $record->statuscomment_cmsp = $comment;

        $dao->update ($record);
        CopixEventNotifier::notify (new CopixEvent ('CMSPageValid',
            array ('draft'=>$record)));
    }

    /**
    * supression définitive d'une page de type brouillon.
    */
    function setDelete ($id){
        $dao = CopixDAOFactory::getInstanceOf ('cms|CMSPage');
        $dao->deleteDraft ($id);
    }

    /**
    * creates a new draft from the given page.
    */
    function newDraftFromPage ($id, $version, $keepId = false, $newUser = null){
        $page = ServicesCMSPage::getVersion ($id, $version);
        if ($page === null){
        	return null;
        }

      	$page->version_cmsp  = 0;
      	if ($newUser !== null){
            $page->author_cmsp = $newUser;
        }
        $page->id_cmsp = null;
        ServicesCMSPage::insert ($page);
        if ($keepId == false){
        	$page->publicid_cmsp = $page->id_cmsp;
        	$page->version_cmsp  = 0;
        	ServicesCMSPage::update ($page);
        }
        return $page->publicid_cmsp;
    }

    /**
    * publish the cmspage.
    * will create a news version if needed and set the old version as an archive.
    * $id - the draft page.
    * $to - the page id destination.
    */
    function setPublish ($id, $comment=null){
    	$oldRecord = null;
    	$newRecord = null;

    	CopixClassesFactory::fileInclude('cms|cmsauth');
    	$user = CMSAuth::getUser();
        $workflow  = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $dao = CopixDAOFactory::getInstanceOf ('cms|cmspage');

        //gets the page to put online.
        $newRecord = $dao->getDraft  ($id);
        $oldRecord = $dao->getOnline ($id);
        if ($newRecord === null){
        	return false;
        }

        //Calculate the version number
        if ($oldRecord){
        	$newRecord->version_cmsp = $oldRecord->version_cmsp + 1;
        }else{
        	$newRecord->version_cmsp = 1;
        }

        //sets the page online.
        $newRecord->status_cmsp = $workflow->getPublish ();
        $newRecord->statusauthor_cmsp = $user->login;
        $newRecord->statusdate_cmsp = date('Ymd');
        $newRecord->statuscomment_cmsp = $comment;
        try {
            $dao->update ($newRecord);
        } catch (CopixDAOCheckException $e) {
        	var_dump($dao);
        	var_dump($newRecord);
        	var_dump($e->getErrors());
        }

        $isNew = ($oldRecord === null);
        $page  = new CMSPage ($newRecord);
        CopixEventNotifier::notify (new CopixEvent ('CMSPagePublish', array ('page'=>new CMSPage ($newRecord), 'isNew'=>$isNew)));
        CopixEventNotifier::notify (new CopixEvent ('PublishedContent', array ('id'=>$newRecord->publicid_cmsp, 'type'=>'cms',
         'content'=>$page->getParsed (CMSParseContext::search),
         'summary'=>$newRecord->summary_cmsp,
         'keywords'=>$newRecord->keywords_cmsp,
         'title'=>$newRecord->title_cmsp,
         'url'=>CopixUrl::get ('cms|default|get', array ('id'=>$newRecord->publicid_cmsp)),
         'isNew'=>$isNew)));

        if (!$isNew) {
            CopixEventNotifier::notify (new CopixEvent ('CMSPageUpdated', array ('id'=>$newRecord->publicid_cmsp)));
        }
        return $newRecord->publicid_cmsp;
    }

    /**
    * moves the given page to the given heading
    * will move all the drafts and all the pages of the given id
    */
    function moveHeading ($pageId, $to){
        $daoPage  = CopixDAOFactory::getInstanceOf ('CMSPage');
        $daoPage->updatePageHeading   ($pageId, $to);
    }

    /**
    * Get the html content of a page
    * @param object $page page object
    * @param object $error the error object, will be filled if an error occurs
    * @return html or false if page doesn't exists
    */
    public static function getPageContent ($page, & $error, $showUpdateLink=true) {
    	CopixClassesFactory::fileInclude ('cms|CMSAuth');
        $error = null;
        if (($page->datemax_cmsp !== null) && ($page->datemax_cmsp !== '') && ($page->datemax_cmsp <= date ('Ymd'))){
            $error = CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('cms.message.expire'),
            'back'=>CopixUrl::get ('copixheadings||show', array ('id_head'=>$page->id_head))));
        }
        if (($page->datemin_cmsp !== null) && ($page->datemin_cmsp !== '') && ($page->datemin_cmsp >= date ('Ymd'))){
            $error = CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('cms.message.notYet'),
            'back'=>CopixUrl::get ('copixheadings||show', array ('id_head'=>$page->id_head))));
        }

        if (!CMSAuth::canRead ($page->id_head)) {
            $error = CopixActionGroup::process ('genericTools|Messages::getError',
            array ('message'=>CopixI18N::get ('cms.error.notAnAuthorizedHead'),
            'back'=>CopixUrl::get ('||')));
        }

        CopixEventNotifier::notify (new CopixEvent ('HeadingFrontBrowsing', array ('id'=>$page->id_head)));
        CopixEventNotifier::notify (new CopixEvent ('CMSPageShow', array ('id'=>$page->publicid_cmsp,'id_head'=>$page->id_head)));

        //gets the model page if any
        $response = CopixEventNotifier::notify (new CopixEvent ('HeadingModelRequest', array ('id'=>$page->id_head)));

        $modelPage = null;
        foreach ($response->getResponse () as $element) {
            //we will override if two model pages are found.
            if ($element['model'] !== null){
                $modelPage = $element['model'];
            }
        }

        $toShow = '';
        if ($modelPage !== null && ($modelPage->publicid_cmsp !== $page->publicid_cmsp)){
            $modelPage->addPortletMessage ('ModelPage', $page->getParsed (CMSParseContext::front));
            $toShow = $modelPage->getParsed (CMSParseContext::front);
        }else{
            $toShow = $page->getParsed (CMSParseContext::front);
        }

        if ($showUpdateLink) {
           //show update link if contributor
           if (CMSAuth::canWrite ($page->id_head)) {
               $toShow .= '<br /><p><a href="'.CopixUrl::get('cms|admin|newFromPage', array('version'=>$page->version_cmsp, 'id'=>$page->publicid_cmsp, 'update'=>'1')).'">';
               $toShow .= '<img src="'.CopixUrl::getResource('img/tools/update.png').'" alt="'.CopixI18N::get('cms|cms.buttons.updatePage').'" />'.CopixI18N::get ('cms|cms.buttons.updatePage').'</a></p>';
           }
        }
        return $toShow;
    }

    /**
    * Get the html content of a draft
    * @param object $page draft object
    * @param object $error the error object, will be filled if an error occurs
    * @return html or error if page doesn't exists
    */
    function getDraftContent ($draft, & $error) {
        $error    = null;
        $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

        //check if we *can* do it
        if (CopixUserProfile::getLogin () != $draft->author_cmsp) {
            //not our document, we have to be a publicator, validator or more case of status.
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_VALID && $draft->status_cmsp == $workflow->getPropose ()){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotViewDraft'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_PUBLISH && $draft->status_cmsp == $workflow->getValid ()){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotViewDraft'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
            if (CopixUserProfile::valueOf ('cms', $servicesHeading->getPath ($draft->id_head)) < PROFILE_CCV_MODERATE && ($draft->status_cmsp == $workflow->getDraft () || $draft->status_cmsp == $workflow->getRefuse ())){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotViewDraft'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
            if (CopixUserProfile::getLogin () != $draft->author_cmsp && $draft->statusauthor_cmsd == $workflow->getTrash ()){
                return CopixActionGroup::process ('genericTools|Messages::getError',
                array ('message'=>CopixI18N::get ('admin.error.cannotViewDraft'),
                'back'=>CopixUrl::get ('copixheadings|admin|', array ('id_head'=>$draft->id_head))));
            }
        }

        CopixEventNotifier::notify (new CopixEvent ('HeadingFrontBrowsing', array ('id'=>$draft->id_head)));
        CopixEventNotifier::notify (new CopixEvent ('CMSPageShow', array ('id' => $draft->publicid_cmsp,'id_head'=>$page->id_head)));

        //gets the model page if any
        $response = CopixEventNotifier::notify (new CopixEvent ('HeadingModelRequest',
        array ('id'=>$draft->id_head)));

        foreach ($response->getResponse () as $element) {
            //we will override if two model pages are found.
            if ($element['model'] !== null){
                $modelPage = $element['model'];
            }
        }

        $toShow = '';
        if ($modelPage !== null && ($modelPage->id_cmsp !== $draft->id_cmsp)){
            $modelPage->addPortletMessage ('ModelPage', $draft->getParsed (CMSParseContext::front));
            $toShow = $modelPage->getParsed (CMSParseContext::front);
        }else{
            $toShow = $draft->getParsed (CMSParseContext::front);
        }
        return $toShow;
    }
}
?>