<?php
/**
 * @package		copix
 * @subpackage	cache
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Interface commune aux stratégies de cache
 * 
 * @package 	copix
 * @subpackage	cache
 */
interface ICopixCacheStrategy {
	/**
	 * Indique si la stratégie est active ou non (certaines stratégies peuvent demander la présence de librairies externes)
	 * 
	 * @param array $pExtra Informations supplémentaires (ex : 'monInfo' => 12)
	 * @return boolean
	 */
	public function isEnabled ($pExtra);
	
	/**
	 * Ecriture dans le cache
	 * 
	 * @param string $pId l'identifiant de l'élément à mettre dans le cache
	 * @param mixed $pContent le contenu à mettre dans le cache
	 * @param string $pType le type de cache dans lequel on souhaite stocker l'élément
	 * @param array $pExtra Informations supplémentaires (ex : 'monInfo' => 12)
	 */
	public function write ($pId, $pContent, $pType, $pExtra);

	/**
	 * Lecture depuis le cache
	 *
	 * @param string $pId Identifiant du cache que l'on souhaite récupérer
	 * @param string $pType	Type de cache depuis lequel on souhaite lire
	 * @param array $pExtra Informations supplémentaires (ex : 'duration' => 12)
	 * @return mixed Contenu du cache
	 * @throws CopixCacheException si l'élément n'est pas trouvé
	 */
	public function read ($pId, $pType, $pExtra);

	/**
	 * Supprime du contenu dans le cache
	 * 
	 * @param string $pId Identifiant de l'élément à supprimer du cache. Si null, tout le type est supprimé
	 * @param string $pType	Type de cache depuis lequel on va supprimer les éléments
	 * @param array $pExtra Informations supplémentaires (ex : 'monInfo' => 12)
	 */
	public function clear ($pId, $pType, $pExtra);

	/**
	 * Indique si un élément existe dans le cache
	 *
	 * @param string $pId Identifiant de l'élément dans le cache
	 * @param string $pType	Type de cache dans lequel on va tester la présence de l'élément
	 * @param array $pExtra Informations supplémentaires (ex : 'monInfo' => 12)
	 * @return boolean 
	 */
	public function exists ($pId, $pType, $pExtra);
}

/**
 * Exception du type CopixCache
 * 
 * @package		copix
 * @subpackage	cache
 */
class CopixCacheException extends CopixException {}

/**
 * Gestion du cache
 * 
 * @package		copix
 * @subpackage	cache
 */
class CopixCache {
	
	/**
	 * On conserve les éléments autorisés
	 * 
	 * @var array
	 */
	static private $_enabled = array ();

	/**
	 * Liste des stratégies instanciées
	 * 
	 * @var array
	 */
	static private $_strategy = array ();

	/**
	 * Instancie la stratégie associée à ce type de cache
	 *
	 * @param string $pName Nom de la stratégie à instancier
	 * @return object Instance de la stratégie demandée
	 */
	static private function _getStrategy ($pName) {
		$pName = strtolower ($pName);
		if (isset (self::$_strategy[$pName])) {
			return self::$_strategy[$pName];
		}

		switch ($pName) {
			case 'file' :
				require_once (COPIX_PATH . 'cache/CopixCacheFileStrategy.class.php');
				return self::$_strategy[$pName] = new CopixCacheFileStrategy ();

			case 'apc' :
				require_once (COPIX_PATH . 'cache/CopixCacheApcStrategy.class.php');
				return self::$_strategy[$pName] = new CopixCacheApcStrategy ();

			case 'system' :
				require_once (COPIX_PATH . 'cache/CopixCacheSystemStrategy.class.php');
				return self::$_strategy[$pName] = new CopixCacheSystemStrategy ();

			default :
				return self::$_strategy[$pName] = _ioClass ($pName);
		}
	}

	/**
	 * Renvoie la stratégie à utiliser pour le type donné en paramètre
	 * 
	 * @param string $pType Type de cache dont on veut connaitre la stratégie
	 * @return string Null si aucune stratégie n'est définie
	 */
	static private function _getStrategyNameFor ($pType) {
		if (($typeInformations = CopixConfig::instance()->copixcache_getType (self::_getMain ($pType))) !== null) {
			return $typeInformations['strategy'];
		}
		return null;
	}

	/**
	 * Lecture des informations en cache
	 *
	 * @param mixed $pId Identifiant des données en cache a retourner
	 * @param string $pType Type de cache
	 * @return mixed Les données (si pas de données renvoi false)
	 * @throws CopixCacheException
	 */
	static public function read ($pId, $pType = 'default') {
		// Type non activé, erreur (l'utilisateur est censé tester l'existence de la donnée avant)
		if (!self::isEnabled (self::_getMain ($pType))) {
			throw new CopixCacheException ('Impossible de lire depuis le cache');
		}
		return self::_getStrategy (self::_getStrategyNameFor (self::_getMain ($pType)))->read (
			serialize ($pId), $pType, CopixConfig::instance()->copixcache_getType (self::_getMain ($pType))
		);
	}

	/**
	 * Ecriture d'informations dans le cache
	 *
	 * @param mixed $pId Identifiant du cache à écrire
	 * @param string $pType Type de cache dans lequel écrire
	 * @param mixed $pContent Contenu à écrire dans le cache
	 * @return boolean
	 */
	static public function write ($pId, $pContent, $pType = 'default') {
		// Type non activé, on ne fait rien
		if (!self::isEnabled(self::_getMain ($pType))) {
			return false;
		}
		
		return self::_getStrategy(self::_getStrategyNameFor (self::_getMain ($pType)))->write (
			serialize($pId), $pContent, $pType, CopixConfig::instance()->copixcache_getType (self::_getMain ($pType))
		);
	}

	/**
	 * Permet de savoir si un élément existe dans le cache
	 *
	 * @param mixed $pId Identifiant de l'élément que l'on recherche
	 * @param string $pType Type de cache
	 * @return boolean
	 */
	static public function exists ($pId, $pType = 'default') {
		// Type non activé, existe pas
		if (!self::isEnabled(self::_getMain ($pType))) {
			return false;
		}

		return self::_getStrategy(self::_getStrategyNameFor (self::_getMain ($pType)))->exists (
			serialize($pId), $pType, CopixConfig::instance()->copixcache_getType (self::_getMain ($pType))
		);
	}

	/**
	 * Regarde si le cache du type spécifié est activé
	 *
	 * @param string $pType Type de cache
	 * @return boolean
	 */
	static public function isEnabled ($pType = 'default') {
		$config = CopixConfig::instance ();
		// On regarde si le type est pris en charge
		if (($typeInformations = $config->copixcache_getType (self::_getMain ($pType))) === null) {
			return self::$_enabled[self::_getMain ($pType)] = false;
		}

		// Si le cache global est activé et que le type 
		if ($config->cacheEnabled && $typeInformations['enabled']) {
			try { 
				return self::$_enabled[self::_getMain ($pType)] = self::_getStrategy (self::_getStrategyNameFor($pType))->isEnabled ($typeInformations);
			} catch (Exception $e) {
				//Si une erreur surviens, on marquera le cache comme inactif
			}
		}
		return self::$_enabled[self::_getMain ($pType)] = false;
	}

	/**
	 * Vidage du cache
	 *
	 * @param mixed $pId Identifiant du cache
	 * @param string $pType Type de cache
	 * @return boolean
	 */
	static public function clear ($pId = null, $pType = 'default') {
		if (self::isEnabled (self::_getMain ($pType))) {
			if ($pId == null) {
				if (count (explode ('|',$pType)) == 1) {
					CopixCache::_cascadeClear($pType);
				}
				return self::_getStrategy (self::_getStrategyNameFor (self::_getMain ($pType)))->clear (
					null, $pType, CopixConfig::instance ()->copixcache_getType (self::_getMain ($pType))
				);
			}
			return self::_getStrategy (self::_getStrategyNameFor (self::_getMain ($pType)))->clear (
				serialize ($pId), $pType, CopixConfig::instance ()->copixcache_getType (self::_getMain ($pType))
			);
		}
		return true;
	}

	/**
	 * Permet de faire le clear en cascade
	 *
	 * @param string $pType Type de cache
	 */
	static private function _cascadeClear ($pType) {
		if ($pType) {
			if (($cache = CopixConfig::instance ()->copixcache_getType (self::_getMain ($pType))) !== null) {
				$arTypeToClear = explode ('|', $cache['link']);
				foreach ($arTypeToClear as $type) {
					self::clear (null, $type);
				}
			}
		}
	}

	/**
	 * Récupère le type princal dont le type est passé en paramètre
	 * 
	 * @param string $pType Type de cache
	 * @return string
	 */
	static private function _getMain ($pType) {
		$parts = explode ('|', $pType);
		return $parts[0];
	}

	/**
	 * Retourne la liste des stratégies disponibles pour la gestion des caches
	 * 
	 * @return array of object (Propriétés : id et caption)
	 */
	static public function getStrategies () {
		$file = new StdClass ();
		$file->id = 'file';
		$file->caption = _i18n ('copix:cache.CopixCacheFileStrategy');

		$system = new StdClass ();
		$system->id = 'system';
		$system->caption = _i18n ('copix:cache.CopixCacheSystemStrategy');

		$apc = new StdClass ();
		$apc->id = 'Apc';
		$apc->caption = _i18n ('copix:cache.CopixCacheApcStrategy');

		return array ($file, $system, $apc);
	}
}
?>