<?php
/**
* @package   plugins
* @author   Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class PluginConfigCache { 
   var $cacheParameters;
   function PluginConfigCache (){
      //cached['module']['desc']['action'] = tableau d'identifiants pour les sous groupe de cache
      //Chaque élément est un tableau. Si pas un tableau, tout le groupe est actif et en cache.
   	  //$this->cacheParameters = array ('bench_news'=>array ('default'=>array ('get'=>array ('id'))));
   	  $this->cacheParameters = array ('bench_news'=>array ('default'=>array ('optimized'=>array ())));
   }
}
?>