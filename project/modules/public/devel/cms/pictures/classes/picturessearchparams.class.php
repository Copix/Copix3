<?php
/**
* @package	cms
* @subpackage pictures
* @author	Bertrand Yan
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
/**
* @package	cms
* @subpackage pictures
* Class qui gère les paramètres de recherche pour les images
*/
class PicturesSearchParams {
   var $category = array();
   var $theme    = array();
   var $keyWord;
   var $format;
   var $maxWeight;
   var $maxWidth;
   var $maxHeight;
   var $cols;
   var $rows;
   var $searchByTheme;

   function PicturesSearchParams ($category,$theme,$keyWord,$format,$maxWeight,$maxWidth,$maxHeight,$cols,$rows,$searchByTheme) {
      $this->category      = $category;
      $this->theme         = $theme;
      $this->keyWord       = $keyWord;
      $this->format        = $format;
      $this->maxWeight     = $maxWeight;
      $this->maxWidth      = $maxWidth;
      $this->maxHeight     = $maxHeight;
      $this->cols          = $cols;
      $this->rows          = $rows;
      $this->searchByTheme = $searchByTheme;
   }
}
?>