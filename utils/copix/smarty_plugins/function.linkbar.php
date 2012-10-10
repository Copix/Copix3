<?php
/**
* @package 	copix
* @subpackage	smarty_plugins
* @author		Croes Gérald,  yan bertrand <ybertrand@aston.fr>
* @copyright	2001-2006 CopixTeam
* @link			http://www.copix.org
* @license  	http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin smarty type fonction
 * Purpose:  LinkBar for navigation.
 *
 * Input:    pageNum  = (required)  the page number we wants to display
 *           nbLink = (obtional) number of link in the bar
 *           nbTotalPage = (required) total number of pages.
 *           url = (required) url which is called when you clic on a link
 *                 (for example url = 'index.php?pageNum='
 *                  if we click on the second page we'll go to :
 *                  index.php?pageNum=2)
 *
 *
 * Examples:
 * {linkbar url="index.php?showPage=" nbLink=2 pageNum=1 nbTotalPage=100}
 */
function smarty_function_linkbar($params, $this) {
   if (isset ($params['assign'])) {
      $this->assign($params['assign'], _tag ('linkbar', $params));
   }else {
      return _tag ('linkbar', $params);
   }
}