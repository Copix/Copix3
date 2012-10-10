<?php
/**
 * @package copix
 * @subpackage project
 * @author Guillaume Perréal, Steevan BARBOYON
 * @copyright Copix Team
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html Lesser GNU General Public Licence, see LICENCE file
 */

require ('../project/config/path.conf.php');
require (COPIX_PATH . 'copix.inc.php');

$fetcher = new CopixResourceFetcher ();
try {
	$fetcher->setFromRequest ();
	$fetcher->fetch ();
} catch (CopixResourceNotFoundException $e) {
	header ('404 Not Found', null, 404);
} catch (CopixResourceForbiddenException $e) {
	header ('430 Forbidden', null, 430);
}