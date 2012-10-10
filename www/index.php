<?php
/**
* @package  copix
* @subpackage project
* @author   Croes Gérald
* @copyright Copix Team
* @link     http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html Lesser GNU General Public Licence, see LICENCE file
*/

//includes copix files.
//will define constants, paths, relative to copix.
$path = dirname (__FILE__);
require ($path.'/../utils/copix/copix.inc.php');
require ($path.'/../project/project.inc.php');

try {	
   $coord = new ProjectController ($path.'/../project/config/copix.conf.php');
   $coord->process ();
}catch (CopixCredentialException $e){
	header ('location: '.CopixUrl::get ('auth||', array ('noCredential'=>1, 'auth_url_return'=>_url ('#'))));
	exit;
}catch (Exception $e){
	$coord->showException ($e);
}
?>