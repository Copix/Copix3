<?php
/**
* @package cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* @package	cms
* @subpackage pictures
* DAOPicturesLinkThemes
*/

class DAOPictureslinkthemes {
   function deleteTheme ($id_theme){
      $query = 'delete from pictureslinkthemes where id_tpic='.intval ($id_theme);
      CopixDB::getConnection ()->doQuery ($query);
   }
   function getCountPictures ($id_theme){
      $query = 'select COUNT(id_pict) as count_pict from pictureslinkthemes where id_tpic='.intval ($id_theme);
      $arResult=CopixDB::getConnection ()->doQuery ($query);
      $toReturn = $arResult[0]->count_pict;
      return $toReturn;
   }
   function moveTheme ($from, $to){
      $query = 'update pictureslinkthemes set id_tpic='.intval ($to).' where id_tpic='.intval ($from);
      CopixDB::getConnection ()->doQuery ($query);
   }
   function deletePicture ($id_pict){
      $query = 'delete from pictureslinkthemes where id_pict='.intval ($id_pict);
      CopixDB::getConnection ()->doQuery ($query);
   }
}
?>