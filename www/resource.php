<?php
/**
 * @package  copix
 * @subpackage project
 * @author   Guillaume Perréal
 * @copyright Copix Team
 * @link     http://www.copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html Lesser GNU General Public Licence, see LICENCE file
 */

//includes copix files.
//will define constants, paths, relative to copix.
require (dirname (__FILE__).'/../utils/copix/copix.inc.php');
require (dirname (__FILE__).'/../project/project.path.inc.php');

// Récupère le PATH_INFO comme on peut
if (isset ($_SERVER['PATH_INFO'])){
	$pathInfo = isset ($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['PATH_INFO'];
}else{
	$pathInfo = $_SERVER["PHP_SELF"];
}
$pathInfo = preg_replace('@^'.preg_quote($_SERVER['SCRIPT_NAME']).'@', '', $pathInfo); 
	
$fetcher = new CopixResourceFetcher(dirname(__FILE__));
try {
	$fetcher->setPathInfo($pathInfo);
	$fetcher->fetch();
} catch(CopixResourceNotFoundException $e) {
	header('404 Not Found', null, 404);
} catch(CopixResourceForbiddenException $e) {
	header('430 Forbidden', null, 430);
}

?>