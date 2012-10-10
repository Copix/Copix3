<?php
/**
* @package   plugins
* @author   Croes Gérald
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class PluginCache extends CopixPlugin {
	/**
    * Si positionné à false, désactive le cache pour une page donnée
    */
	var $_enabled = true;

	/**
    * Définition de l'identifiant
    *  Si l'idenfiant vaut "false", alors un élément du site à demandé à ne jamais
    *   mettre l'élément en cache
    */
	function _makeId ($pArId){
		ksort ($pArId);
		$groups   = CopixUserProfile::getGroups ();
		$user     = CopixPluginRegistry::get ('auth|auth')->getUser ();
		return array ($pArId, $groups, $user->login);
	}

	/**
    * Désactive le cache pour une page donnée
    */
	function disable (){
		$this->_enabled = false;
	}

	/**
    * Regarde s'il existe l'informatione en cache et la récupère depuis le cache si
    * tel est le cas.
    */
	function beforeProcess (& $execParam){
		if ($this->_handles()){
			$id = $this->_makeId (CopixRequest::asArray ());
			$subCacheId = $this->_getSubcacheId ();

			$this->id = $id;
			$this->subCacheId = $subCacheId;

			if ($subCacheId !== false){
				$cacheType = 'content_cache|'.$subCacheId;
			}else{
				$cacheType = 'content_cache';
			}

			$cache = new CopixCache ($id, $cacheType);
			if ($cache->isCached (CopixConfig::get ('cache|cacheDuration'))){
				$execParam = new CopixAction ('cache|Cache', 'getRead');
			}
		}
	}

	/**
    * Va mettre le contenu $content en cache
    * @param string $content le contenu a afficher
    */
	function beforeDisplay (& $content){
		if ($this->_enabled == false){
			return;
		}
		if ($this->_handles()){
			$id = $this->_makeId (CopixRequest::asArray ());
			$subCacheId = $this->_getSubcacheId ();
			if ($subCacheId !== false){
				$cacheType = 'content_cache|'.$subCacheId;
			}else{
				$cacheType = 'content_cache';
			}

			$cache = new CopixCache ($id, $cacheType);
			$cache->write ($content);
		}
	}

	/**
	* Retourne l'identifiant de sous cache
	* @return string ou false si aucun identifiant de sous cache
	*/
	function _getSubcacheId (){
		$parameters = $this->config->cacheParameters;
		$request    = CopixRequest::asArray ();
		$this->_updateRequestWithDefault ($request);
		$key = '';
		if (isset ($parameters[$request['module']][$request['desc']][$request['action']])){
			if (is_array ($parameters[$request['module']][$request['desc']][$request['action']])){
				foreach ($parameters[$request['module']][$request['desc']][$request['action']] as $key=>$value){
					if (isset ($request[$value])){
						$key.=$value;
					}else{
						$key.='-_null_-';
					}
				}
				return $key;
			}
		}
		return false;
	}

	/**
    * Indique si le plugin 
    */
	function _handles (){
		$parameters = $this->config->cacheParameters;
		$request    = CopixRequest::asArray ();
		$this->_updateRequestWithDefault ($request);
		if (isset ($parameters[$request['module']])){
			//le module est déclaré dans les éléments à mettre en cache
			if (is_array ($parameters[$request['module']])){
				if (isset ($parameters[$request['module']][$request['desc']])){
					//le fichier de description est déclaré
					if (is_array ($parameters[$request['module']][$request['desc']])){
						if (isset ($parameters[$request['module']][$request['desc']][$request['action']])){
							//l'action est déclarée
							return true;
						}else{
							return false;
						}
					}else{
						return $parameters[$request['module']][$request['desc']];
					}
				}else{
					return false;
				}
			}else{
				return $parameters[$request['module']];
			}
		}else{
			return false;
		}
	}

	/**
	* Fait en sorte de mettre les paramètres par défaut si jamais la requête n'était pas complète au niveau 
	* du trio action/group/module
	* @param array $pRequest la requête HTTP
	* @return void
	* @access private
	*/
	function _updateRequestWithDefault (& $pRequest){
		if (!isset ($pRequest['module'])){
			$pRequest['module'] = 'default';
		}
		if (!isset ($pRequest['action'])){
			$pRequest['action'] = 'default';
		}
		if (!isset ($pRequest['desc'])){
			$pRequest['desc'] = 'default';
		}
	}
}
?>