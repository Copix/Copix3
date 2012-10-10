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
	private static $_enabled = array ();

	/**
	 * Liste des stratégies instanciées
	 * 
	 * @var array
	 */
	private static $_strategy = array ();

	/**
	 * Instancie la stratégie associée à ce type de cache
	 *
	 * @param string $pName Nom de la stratégie à instancier
	 * @return object Instance de la stratégie demandée
	 */
	private static function _getStrategy ($pType) {
		if (isset (self::$_strategy['type'][$pType])) {
			return self::$_strategy['type'][$pType];
		}

		$mainType = self::_getMain ($pType);

		if (isset (self::$_strategy['type'][$mainType])) {
			return self::$_strategy['type'][$mainType] = self::$_strategy['type'][$pType];
		}
		
		if (($typeInformations = CopixConfig::instance()->copixcache_getType ($mainType)) !== null) {
			$name = strtolower ($typeInformations['strategy']);
		}

		if (strpos ($name, '|') === false) {
			$name = 'CopixCache' . $name . 'Strategy';
		}

		return self::$_strategy['name'][$name] = self::$_strategy['type'][$mainType] = self::$_strategy['type'][$pType] = new $name ($typeInformations);
	}

	/**
	 * Lecture des informations en cache
	 *
	 * @param mixed $pId Identifiant des données en cache a retourner
	 * @param string $pType Type de cache
	 * @return mixed Les données (si pas de données renvoi false)
	 * @throws CopixCacheException
	 */
	public static function read ($pId, $pType = 'default') {
		// Type non activé, erreur (l'utilisateur est censé tester l'existence de la donnée avant)
		if (!self::isEnabled (self::_getMain ($pType))) {
			throw new CopixCacheException ('Impossible de lire depuis le cache');
		}
		return self::_getStrategy ($pType)->read ($pId, $pType);
	}

	/**
	 * Ecriture d'informations dans le cache
	 *
	 * @param mixed $pId Identifiant du cache à écrire
	 * @param mixed $pContent Contenu à écrire dans le cache
	 * @param string $pType Type de cache dans lequel écrire
	 * @return boolean
	 */
	public static function write ($pId, $pContent, $pType = 'default') {
		// Type non activé, on ne fait rien
		if (!self::isEnabled(self::_getMain ($pType))) {
			return false;
		}
		return self::_getStrategy($pType)->write ($pId, $pContent, $pType);
	}

	/**
	 * Permet de savoir si un élément existe dans le cache
	 *
	 * @param mixed $pId Identifiant de l'élément que l'on recherche
	 * @param string $pType Type de cache
	 * @return boolean
	 */
	public static function exists ($pId, $pType = 'default') {
		// Type non activé, existe pas
		if (!self::isEnabled(self::_getMain ($pType))) {
			return false;
		}
		return self::_getStrategy($pType)->exists ($pId, $pType);
	}

	/**
	 * Regarde si le cache du type spécifié est activé
	 *
	 * @param string $pType Type de cache
	 * @return boolean
	 */
	public static function isEnabled ($pType = 'default') {
		if (array_key_exists ($mainType = self::_getMain ($pType), self::$_enabled)){
			return self::$_enabled[$mainType]; 			
		}

		// On regarde si le type est pris en charge
		if (($typeInformations = CopixConfig::instance ()->copixcache_getType ($mainType)) === null) {
			return self::$_enabled[$mainType] = false;
		}

		// Si le cache global est activé et que le type 
		if (CopixConfig::instance ()->cacheEnabled && $typeInformations['enabled']) {
			try { 
				return self::$_enabled[$mainType] = self::_getStrategy ($pType)->isEnabled ($typeInformations);
			} catch (Exception $e) {
				//Si une erreur surviens, on marquera le cache comme inactif
			}
		}
		return self::$_enabled[$mainType] = false;
	}

	/**
	 * Vidage du cache
	 *
	 * @param mixed $pId Identifiant du cache
	 * @param string $pType Type de cache
	 * @return boolean
	 */
	public static function clear ($pId = null, $pType = 'default') {
		if (self::isEnabled (self::_getMain ($pType))) {
			if ($pId == null) {
				if (count (explode ('|',$pType)) == 1) {
					CopixCache::_cascadeClear($pType);
				}
				return self::_getStrategy ($pType)->clear (null, $pType);
			}
			return self::_getStrategy ($pType)->clear ($pId, $pType);
		}
		return true;
	}

	/**
	 * Permet de faire le clear en cascade
	 *
	 * @param string $pType Type de cache
	 */
	private static function _cascadeClear ($pType) {
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
	private static function _getMain ($pType) {
		$parts = explode ('|', $pType);
		return $parts[0];
	}

	/**
	 * Retourne la liste des stratégies disponibles pour la gestion des caches
	 * 
	 * @return array of object (Propriétés : id et caption)
	 */
	public static function getStrategies () {
		$temp = array ();
		foreach (CopixFile::glob (COPIX_PATH . 'cache/strategies/*.class.php') as $file) {
			$class = substr (CopixFile::extractFileName ($file), 0, -10);
			$caption = _i18n ('copix:copixcache.' . $class);
			$description = _i18n ('copix:copixcache.' . $class . 'Description');
			$strategy = new CopixCacheStrategyDescription ('copix:' . $class, $caption, $description);
			$temp[$strategy->getId ()] = $strategy;
		}

		// stratégies ajoutées via des modules
		foreach (CopixModule::getList () as $module) {
			$temp = array_merge ($temp, CopixModule::getInformations ($module)->getCacheStrategies ());
		}

		// tri
		$tri = array ();
		foreach ($temp as $strategy) {
			$tri[$strategy->getCaption ()] = $strategy->getId ();
		}
		ksort ($tri);
		$toReturn = array ();
		foreach ($tri as $id) {
			$toReturn[$id] = $temp[$id];
		}

		return $toReturn;
	}
}