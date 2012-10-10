<?php
/**
* @package  copix
* @author   Croes Gérald
* @copyright Copix Team
* @link     http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

//Inclusion et lancement du framework
require_once ('../utils/copix/copix.inc.php');
require_once ('../project/project.inc.php');
require_once ('../utils/copix/tests/CopixTestController.class.php');

//set_memory_limit (0);
error_reporting (E_ALL);
set_time_limit (null);
$testController = new CopixTestController ();
$testController->process (array ('report_path'=>COPIX_TEMP_PATH.'testreport/', 
						'config'=>'../project/config/copix.conf.php', 
						'xml'=>isset ($_REQUEST['xml']) ? $_REQUEST['xml'] : false));
?>