#!/usr/bin/php -q 
<?php
/**
* @package  copix
* @subpackage project
* @author   David Derigent
* @copyright Copix Team
* @link     http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
//includes copix files.
//will define constants, paths, relative to copix.
$path = dirname (__FILE__);
require ($path.'/../utils/copix/copix.inc.php');
require ($path.'/../project/project.inc.php');

require_once (COPIX_UTILS_PATH  .'CopixCLI.class.php');

//CopixInstall
$cli =   new  CopixCLI();
$is = $cli->prepare();
if($is){
    try {	
       $coord = new ProjectController ($path.'/../project/config/copix.conf.php');
       $coord->process ();
    }catch (CopixCredentialException $e){
    	echo "You're not authorized to do that, please try to auth";
    	exit;
    }catch (Exception $e){
    	$coord->showException ($e);
    }
}else {
	echo($cli->message);
}




?>