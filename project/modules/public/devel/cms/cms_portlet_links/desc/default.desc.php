<?php
/**
* @package	cms
* @subpackage cms_portlet_links
* @author	Bertrand Yan, Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * @ignore
 */

$edit       = new CopixAction ('LinksPortlet', 'getEdit');
$validedit  = new CopixAction ('LinksPortlet', 'doValidEdit');
$valid      = new CopixAction ('LinksPortlet', 'doValid');

$addLink    = new CopixAction ('LinksPortlet', 'doAddLink');
$removeLink = new CopixAction ('LinksPortlet', 'doRemoveLink');
$selectPage = new CopixActionZone('cms|SelectPage', array ('TITLE_PAGE'=>CopixI18N::get ('links.title.selectPage'), 
                                                             'Params'=>array ('onlyLastVersion'=>1,
                                                                              'select'=>CopixUrl::get ('cms_portlet_links||setPage'), 
                                                                              'back'=>CopixUrl::get ('cms_portlet_links||edit'))));
$setPage    = new CopixAction ('LinksPortlet', 'doSetPage');

$moveUp   = new CopixAction ('LinksPortlet', 'doMoveUp');
$moveDown = new CopixAction ('LinksPortlet', 'doMoveDown');
?>