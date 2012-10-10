<?php
/**
* @package	cms
* @subpackage news
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @package	cms
 * @subpackage news
 * NewsService
 */
class NewsService {
   function getOnlineNewsList(){
     $DAOnews = & CopixDAOFactory::getInstanceOf ('News');
     return $DAOnews->findAll ();
   }

   function getAllNews(){
     $DAOnews = & CopixDAOFactory::getInstanceOf ('News');
     return $DAOnews->findAll ();
   }

   function getNews($id){
     $DAOnews = & CopixDAOFactory::getInstanceOf ('News');
     return $DAOnews->get($id);
   }
	
	function getLevel($id_head){
		$DAOnews = & CopixDAOFactory::getInstanceOf ('news|News');
		return $DAOnews->getCountHeading($id_head);
	}
}
?>