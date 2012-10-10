<?php
/**
* @package	cms
* @author	Croes Gérald, see copix.org for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package cms
 * DAOCMSpage
 */
class DAOCMSpage {
	function findByAuthorStatusIn ($author, $status, $id_head){
        $sp = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('id_head'    , '=', $id_head);
        $sp->addCondition ('status_cmsp', '=', $status);
        $sp->addCondition ('author_cmsp', '=', $author);
        return $this->_distinct($this->findBy ($sp));
    }
    function findByStatusIn ($status, $id_head) {
        $sp = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('id_head'   , '=', $id_head);
        $sp->addCondition ('status_cmsp', '=', $status);
        return $this->_distinct($this->findBy ($sp));
    }
    function findByStatus ($status) {
        $sp = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('status_cmsp', '=', $status);
        return $this->_distinct($this->findBy ($sp));
    }
    function findOnlineAndAuthorDraft ($author){
        $workflow = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');

    	//online
        $sp = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('status_cmsp', '=', $workflow->getPublish ());
        $pages = $this->findBy ($sp);

        //drafts for the author
        $sp = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('author_cmsp', '=', $author);
        $sp->addCondition ('status_cmsp', '=', array ($workflow->getDraft (), $workflow->getValid (), $workflow->getPropose ()));
        $drafts = $this->findBy ($sp);
        return $this->_distinct(array_merge($drafts, $pages));
    }
    
    /**
    * Gets the pages for a given heading and (optionnaly) status.
    * @param int $heading the heading id
    * @param int $status the status of the pages we're looking for
    * @return array of DAORecordcmspage
    */
    function findByHeading ($heading, $status = null, $lastOnly = false){
        // Used in contribheading.listener.class.php
        $sp = CopixDAOFactory::createSearchParams ();
        if ($status !== null){
            $sp->addCondition ('status_cmsp', '=', $status);
        }
        $sp->addCondition ('id_head', '=', $heading);

        if ($lastOnly){
            $toReturn = array ();
            foreach ($this->findBy ($sp) as $element){
                if (isset ($toReturn[$element->publicid_cmsp])){
                    if ($toReturn[$element->publicid_cmsp]->version_cmsp < $element->version_cmsp){
                        $toReturn[$element->publicid_cmsp] = $element;
                    }
                }else{
                    $toReturn[$element->publicid_cmsp] = $element;
                }
            }
            return $toReturn;
        }else{
            return $this->findBy ($sp);
        }
    }

    /**
    * show the history of a given page.
    * @param int $id the page id
    * @return array of record

    */
    function getHistoryOf ($id) {
        $sp = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('publicid_cmsp', '=', $id);
        $sp->orderBy (array ('version_cmsp', 'DESC'));
        return $this->findBy ($sp);
    }

    /**
    * Gets the headings count
    */
    function getCountHeading ($id_rubrique){
        $query = 'select COUNT(id_cmsp) as count_pages from cmspage where id_head='.$id_rubrique;
		$results = CopixDB::doQuery ($query, $this->_connectionName);
		return $results[0]->count_pages;
    }
    
    /**
    * moves headings
    */
    function moveHeading ($from, $to){
        $query = 'update cmspage set id_head='.$to.' where id_head='.$from;
        CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
    }

    /**
    * updates the page heading
    */
    function updatePageHeading ($pageId, $to){
        $query  = 'update cmspage set id_head='.($to === null ? 'NULL' : (is_numeric ($to) ? $to : intval ($to))).' where ';
        $query .= 'id_cmsp = '.(is_numeric ($pageId) ? $pageId : intval ($pageId));
        CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
    }

    /**
    * @deprecated
    */
    function deleteByHeading ($id){
        $query = 'delete from cmspage where id_head='.(is_numeric ($id) ? $id : intval ($id));
        CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
    }

    /**
    * gets the last version of the given page that is online.
    * @param int $id the page id
    * @return int
    */
    function getLastOnlineVersionById ($id){
        $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $query = 'select max(version_cmsp) as version_cmsp from cmspage where publicid_cmsp='.intval ($id).' and status_cmsp='.$workflow->getPublish ();
        $results = CopixDb::getConnection ($this->_connectionName)->doQuery ($query);
        return isset ($results[0]) ? $results[0]->version_cmsp : null;  
    }
    
    /**
    * retourne la rubrique de la page en fonctino de son identifiant public
    */
    function getHeadingForPublicIdCmsp ($publicIdCmsp){
        $workflow         = CopixClassesFactory::getInstanceOf ('copixheadings|workflow');
        $query = 'select id_head from cmspage where publicid_cmsp='.intval ($publicIdCmsp);
        $results = CopixDb::getConnection ($this->_connectionName)->doQuery ($query);
        return isset ($results[0]) ? $results[0]->id_head : null;
    }
    
    /**
    * delete the given page
    * @param int $id the page id
    */
    function deleteByPublicId ($id){
        $query = 'delete from cmspage where publicid_cmsp='.intval ($id);
        CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
    }

    /**
    * _distinct
    * @param $records
    */
    function _distinct ($records){
        $toReturn = array ();
        foreach ($records as $record){
            if (! isset ($toReturn[$record->publicid_cmsp])){
                $toReturn[$record->publicid_cmsp] = $record;
            }else{
                if ($toReturn[$record->publicid_cmsp]->version_cmsp < $record->version_cmsp){
                    $toReturn[$record->publicid_cmsp] = $record;
                }
            }
        }
        return $toReturn;
    }

    function getDraft ($draftPubliId){
    	$sp = CopixDAOFactory::createSearchParams();
    	$sp->addCondition ('publicid_cmsp', '=', $draftPubliId);
    	$sp->addCondition ('version_cmsp', '=', 0);
    	$results = $this->findBy ($sp);
    	if (count ($results)){
    		return $results[0];
    	}else{
    		return false;
    	}
    }

    function getVersion ($publicId, $version){
    	$sp = CopixDAOFactory::createSearchParams();
    	$sp->addCondition ('publicid_cmsp', '=', $publicId);
    	$sp->addCondition ('version_cmsp', '=', $version);
    	$results = $this->findBy ($sp);
    	if (count ($results)){
    		return $results[0];
    	}else{
    		return null;
    	}
    }

    function deleteDraft ($publicId){
    	$query = "delete from cmspage where publicid_cmsp = ".intval ($publicId)." and version_cmsp=0";
    	CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
    }
}
?>
