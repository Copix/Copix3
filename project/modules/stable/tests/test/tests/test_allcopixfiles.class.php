<?php
/**
 * @package standard
 * @subpackage test
 * @author		Croës Gérald
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Essaye d'inclure l'ensemble des fichiers déclarés dans CopixClassPath.inc.php
 * @package standard
 * @subpackage test
 */
class Test_AllCopixFiles extends CopixTest {
   function testCopixLoadAllClasses (){
      $arPath = include (COPIX_CLASSPATHS_FILE);
      foreach ($arPath as $key=>$path){
      	CopixAutoloader::load ($key);
      }
   }
}