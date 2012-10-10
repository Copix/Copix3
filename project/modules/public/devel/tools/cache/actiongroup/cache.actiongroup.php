<?php
/**
* @package		cache
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @licence		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Prise en charge de la lecture des données du cache
 */
class ActionGroupCache extends CopixActionGroup {
   /**
   * Affichage d'un contenu en cache
   */
   function processGetRead (){
      $plugin = CopixPluginRegistry::get ('cache|cache');
      $id     = $plugin->id;
      
      $cacheType = 'content_cache';
      if ($plugin->subCacheId !== null){
      	$cacheType .= '|'.$plugin->subCacheId;
      }

      echo CopixCache::read ($id, $cacheType);
      return new CopixActionReturn (CopixActionReturn::NONE, null);
   }
}
?>