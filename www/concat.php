<?php
/**
 * @package copix
 * @subpackage project
 * @author Steevan BARBOYON
 * @copyright Copix Team
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html Lesser GNU General Public Licence, see LICENCE file
 */

// fichier de configuration des chemins
require (dirname (__FILE__) . '/../project/config/path.conf.php');
require (COPIX_CACHE_PATH . 'copixconcat/concat.php');

if (array_key_exists ($_GET['id'], $_cache)){
	require (COPIX_CACHE_PATH . 'copixconcat/' . $_GET['id'] . '.headers.php');
	require (COPIX_CACHE_PATH . 'copixconcat/' . $_GET['id'] . '.php');	
}else{
	header ('404 Not Found', null, 404);
}