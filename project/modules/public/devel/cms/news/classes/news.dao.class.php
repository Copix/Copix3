<?php
/**
* @package	cms
* @subpackage news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage news
 * DAONews
 */
class DAONews {
   /**
   * Move a news in a new heading
   * @param string $id_news
   * @param int $to
   */
   function moveHeading ($id_news, $to) {
      $query  = 'update news set id_head='.($to === null ? 'NULL' : (is_numeric ($to) ? $to : intval ($to))).' where ';
      $query .= 'id_news = '.$id_news;

      CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
   }

   function getCountHeading ($id_head){
      $query = 'select COUNT(ID_NEWS) as count_news from news where id_head='.intval ($id_head);
      $arResult = CopixDB::getConnection ($this->_connectionName)->doQuery ($query);

      $toReturn = $arResult[0]->count_news;
      return $toReturn;
   }

   function deleteByHeading ($id_head){ // Not tested yet
      $query = 'delete from news where id_head='.intval ($id_head);
      CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
   }

   function getRestrictedListByCat ($id_head, $numToShow, $status, $asc = false){
      $daoSearchParams = & CopixDAOFactory::createSearchParams ();
      $daoSearchParams->addCondition ('status_news', '=', $status);
      $daoSearchParams->addCondition ('id_head', '=', $id_head);
      $daoSearchParams->orderBy (array ('datewished_news', $asc ? 'asc' : 'desc'));

      $daoNews = & CopixDAOFactory::getInstanceOf ('News');
      $arNews  = $daoNews->findBy ($daoSearchParams);

      $arNews = array_slice ($arNews, 0, $numToShow);

      return $arNews;
   }

   function getRestrictedList ($numToShow, $status){
      $daoSearchParams = & CopixDAOFactory::createSearchParams ();
      $daoSearchParams->addCondition ('status_news', '=', $status);
      $daoSearchParams->orderBy ('datewished_news', 'desc');

      $daoNews = & CopixDAOFactory::getInstanceOf ('News');
      $arNews  = $daoNews->findBy ($daoSearchParams);

      $arNews = array_slice ($arNews, 0, $numToShow);

      return $arNews;
   }

   function getNewsByUser ($status, $id_head) {
      $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
      $user      = & $plugAuth->getUser();
      $sp        = CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('id_head'    , '=', $id_head);
      $sp->addCondition ('status_news', '=', $status);
      $sp->addCondition ('author_news', '=', $user->login);
      return $this->findBy ($sp);
   }

   function getNewsByStatusAuthor ($status, $id_head, $login=null) {
      if ($login === null) {
         $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
         $user      = & $plugAuth->getUser();
         $login     = $user->login;
      }

      $sp = CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('id_head'          , '=', $id_head);
      $sp->addCondition ('status_news'      , '=', $status);
      $sp->addCondition ('statusauthor_news', '=', $login);
      return $this->findBy ($sp);
   }

   function getNewsByStatus ($status, $id_head) {
      $sp = CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('id_head'    , '=', $id_head);
      $sp->addCondition ('status_news', '=', $status);
      return $this->findBy ($sp);
   }


}
?>