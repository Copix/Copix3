<?php
/**
 * @package copix
 * @subpackage project
 * @author Croës Gérald, Steevan BARBOYON
 * @copyright Copix Team
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html Lesser GNU General Public Licence, see LICENCE file
 */
require ('../project/config/path.conf.php');
require (COPIX_PATH . 'copix.inc.php');

$coord = new CopixController (COPIX_CONFIG_FILE);
$coord->process ();