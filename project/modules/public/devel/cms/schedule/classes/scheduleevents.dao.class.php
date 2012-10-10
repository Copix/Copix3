<?php
/**
* @package	cms
* @subpackage schedule
* @version	$Id: scheduleevents.dao.class.php,v 1.1 2007/04/08 18:08:14 gcroes Exp $
* @author	Bertrand Yan, Croes Gérald see copix.aston.fr for other contributors.
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage schedule
* DAOScheduleEvents
*/
class DAOScheduleEvents {
    /**
   * Move an event in a new heading
   * @param string $id_evnt
   * @param int $to
   */
   function moveHeading ($id_evnt, $to) {
      $query  = 'update scheduleevents set id_head='.($to === null ? 'NULL' : (is_numeric ($to) ? $to : intval ($to))).' where ';
      $query .= 'id_evnt = '.$id_evnt;

      CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
   }
   
    function findByDate ($date,$id_head) {
        $workflow = & CopixClassesFactory::create ('copixheadings|workflow');
        $query  = 'SELECT * FROM scheduleevents where ';
        $query .= 'status_evnt='.$workflow->getPublish ().' and ';
        if ($id_head == null) {
            $query .= 'id_head is NULL  and ';
        }else{
            $query .= 'id_head='.$id_head.' and ';
        }
/*        $query .= 'datedisplayto_evnt>=\''.date('Ymd').'\' and ';
        $query .= 'datedisplayfrom_evnt<=\''.date('Ymd').'\' and ((';
        $query .= 'datefrom_evnt<=\''.$date.'\' and ';
        $query .= 'dateto_evnt>=\''.$date.'\') or (';
        $query .= 'datefrom_evnt=\''.$date.'\' and ';
        $query .= 'dateto_evnt=\'\'))';*/
        $query .= 'datedisplayto_evnt>=\''.$date.'\' and ';
        $query .= 'datedisplayfrom_evnt<=\''.$date.'\'';

        $toReturn = CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
        return $toReturn;
    }

    function findByDateAndCat ($date,$idCat) {
        $workflow = & CopixClassesFactory::create ('copixheadings|workflow');
        $query  = 'SELECT * FROM scheduleevents where ';
        $query .= 'id_evtc='.$idCat.' and ';
        $query .= 'status_evnt='.$workflow->getPublish ().' and ';
        $query .= 'datedisplayto_evnt>=\''.date('Ymd').'\' and ';
        $query .= 'datedisplayfrom_evnt<=\''.date('Ymd').'\' and ((';
        $query .= 'datefrom_evnt<=\''.$date.'\' and ';
        $query .= 'dateto_evnt>=\''.$date.'\') or (';
        $query .= 'datefrom_evnt=\''.$date.'\' and ';
        $query .= 'dateto_evnt=\'\'))';
		$toReturn = & CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
        return $toReturn;
    }

    function getCountHeading ($id_head){
        $query = 'select COUNT(id_evnt) as count_evnt from scheduleevents where id_head='.intval ($id_head);
        //echo $query.'<br>';
        $arResult = CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
        $toReturn = $arResult[0]->count_evnt;
        return $toReturn;
    }

    /**
    * check
    */
    function check ($Evnt) {
        if (($result=$this->_compiled_check($Evnt))!==true) {
            return $result;
        }else{
            $errorObject = new CopixErrorObject ();
            if (strlen($Evnt->dateto_evnt) && $Evnt->datefrom_evnt > $Evnt->dateto_evnt){
                $errorObject->addError ('dateto_evnt', CopixI18N::get ('schedule.error.dao.fromsupto'));
            }
            if ($Evnt->datedisplayfrom_evnt > $Evnt->datedisplayto_evnt){
                $errorObject->addError ('dateto_evnt', CopixI18N::get ('schedule.error.dao.displayfromsupto'));
            }
            return $errorObject->isError () ? $errorObject->asArray () : true;
        }
    }

    function getEventByUser ($status, $id_head) {
        $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
        $user      = & $plugAuth->getUser();
        $sp        = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('id_head'    , '=', $id_head);
        $sp->addCondition ('status_evnt', '=', $status);
        $sp->addCondition ('author_evnt', '=', $user->login);
        return $this->_compiled->findBy ($sp);
    }

    function getEventByStatusAuthor ($status, $id_head, $login=null) {
        if ($login === null) {
            $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
            $user      = & $plugAuth->getUser();
            $login     = $user->login;
        }

        $sp = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('id_head'          , '=', $id_head);
        $sp->addCondition ('status_evnt'      , '=', $status);
        $sp->addCondition ('statusauthor_evnt', '=', $login);
        return $this->_compiled->findBy ($sp);
    }

    function getEventByStatus ($status, $id_head) {
        $sp = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('id_head'    , '=', $id_head);
        $sp->addCondition ('status_evnt', '=', $status);
        return $this->findBy ($sp);
    }
    
    function _getRestrictedListByHeading ($id_head, $date, $status) {
      $query  = 'select scheduleevents.id_evnt as id_evnt, scheduleevents.id_head as id_head, copixheadings.caption_head as caption_head, ';
      $query .= ' scheduleevents.title_evnt as title_evnt, scheduleevents.preview_evnt as preview_evnt, scheduleevents.content_evnt as content_evnt, ';
      $query .= ' scheduleevents.author_evnt as author_evnt, scheduleevents.status_evnt as status_evnt, scheduleevents.datefrom_evnt as datefrom_evnt, ';
      $query .= ' scheduleevents.dateto_evnt as dateto_evnt, scheduleevents.datedisplayfrom_evnt as datedisplayfrom_evnt, scheduleevents.datedisplayto_evnt as datedisplayto_evnt, ';
      $query .= ' scheduleevents.editionkind_evnt as editionkind_evnt, scheduleevents.statusdate_evnt as statusdate_evnt, scheduleevents.statusauthor_evnt as statusauthor_evnt, ';
      $query .= ' scheduleevents.statuscomment_evnt as statuscomment_evnt ';
      $query .= ' FROM scheduleevents, copixheadings ';
      //$query .= ' WHERE scheduleevents.id_head=copixheadings.id_head(+) ';
		$query .= ' WHERE scheduleevents.id_head=copixheadings.id_head OR scheduleevents.id_head is null ';
      $query .= ' AND ( scheduleevents.status_evnt = '.$status.' ';
      if(strlen($id_head)>0) {
      	$query .= ' and scheduleevents.id_head = '.$id_head;
      } else {
      	$query .= ' and scheduleevents.id_head IS NULL ';
      }
      $query .= ' and scheduleevents.datedisplayto_evnt >= \''.date('Ymd').'\' ';
      $query .= ' and scheduleevents.datedisplayfrom_evnt <= \''.date('Ymd').'\')';
      $toReturn = CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
      return $toReturn;
    }

   
}
?>
