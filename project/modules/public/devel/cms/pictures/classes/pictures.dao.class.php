<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage pictures
* DAOPictures
*/

class DAOPictures {
   /**
   * Move a picture in a new heading
   * @param string $id_pict
   * @param int $to
   */
   function moveHeading ($id_pict, $to) {
      $query  = 'update pictures set id_head='.($to === null ? 'NULL' : (is_numeric ($to) ? $to : intval ($to))).' where ';
      $query .= 'id_pict = '.$id_pict;

      CopixDB::getConnection ($this->_connectionName)->doQuery ($query);
   }
   
   function getPictByNameAndIndex ($name, $index) {
      $sp         = & CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('name_pict', '=', $name);
      $sp->addCondition ('nameindex_pict', '=', $index);

      $result = $this->findBy ($sp);
      if (count($result) > 0) {
         return $result[0]->id_pict;
      }else{
         return false;
      }
   }

   function getNBPictures ($id_head, $status=null){
      $query  = 'select COUNT(id_pict) as count_pict from pictures where id_head';
      $query .= strlen($id_head) > 0 ? '='.$id_head : ' is NULL';
      if ($status !== null) {
         $query .= ' and status_pict='.$status;
      }
      $arResult = & CopixDB::getConnection ($this->_connectionName)->doQuery($query);
      

      $toReturn = $arResult[0]->count_pict;
      return $toReturn;
   }
   
    /**
    * Get pictures by heading and status
    * @param int $id_head heading identifier
    * @return int

    */
    function getPictures ($id_head, $status ) {
      $sp  = CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('id_head'    , '=', $id_head);
      $sp->addCondition ('status_pict', '=', $status);
      return $this->findBy($sp);
    }
    
    /**
    * Gets the next picture name sequence value
    * @param string $name_pict the picture name
    * @param string $format_pict the extension 
    * @return int
    */
    function getNextNameIndex($name_pict, $format_pict) {
      $ct = CopixDB::getConnection ($this->_connectionName);
      $query  = 'select MAX(nameindex_pict)+1 as maxindex from pictures where name_pict='.$ct->quote($name_pict).' and format_pict='.$ct->quote ($format_pict);

      $arResult = CopixDB::getConnection ($this->_connectionName)->doQuery($query);
      if (count($arResult)>0) {
      	return intval($arResult[0]->maxindex);
      }else{
          return 0;
      }
    }
    
    function getPictureByUser ($status, $id_head) {
      $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
      $user      = & $plugAuth->getUser();
      $sp        = CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('id_head'    , '=', $id_head);
      $sp->addCondition ('status_pict', '=', $status);
      $sp->addCondition ('author_pict', '=', $user->login);
      return $this->findBy ($sp);
   }

   function getPictureByStatusAuthor ($status, $id_head, $login=null) {
      if ($login === null) {
         $plugAuth  = & CopixController::instance ()->getPlugin ('auth|auth');
         $user      = & $plugAuth->getUser();
         $login     = $user->login;
      }

      $sp = CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('id_head'          , '=', $id_head);
      $sp->addCondition ('status_pict'      , '=', $status);
      $sp->addCondition ('statusauthor_pict', '=', $login);
      return $this->findBy ($sp);
   }

   function getPictureByStatus ($status, $id_head) {
      $sp = CopixDAOFactory::createSearchParams ();
      $sp->addCondition ('id_head'    , '=', $id_head);
      $sp->addCondition ('status_pict', '=', $status);
      return $this->findBy ($sp);
   }
}
?>