<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Gestion du cache du CMS (cache CopixCache et interne à HeadingCache, pour un peu plus de gain)
 *
 * @package cms
 * @subpackage heading
 */
class HeadingCache {
	/**
	 * Cache interne pour ne pas appeler plusieurs fois CopixCache
	 *
	 * @var array
	 */
	private static $_cache = array ();

	/**
	 * Indique si le cache est activé
	 *
	 * @return boolean
	 */
	private static function _useCache () {
		static $_useCache = null;
		if ($_useCache === null) {
			$_useCache = CopixConfig::get ('heading|useCache');
		}
		return $_useCache;
	}

	/**
	 * Retourne l'identifiant de cache complet, avec le nom de domaine
	 * Le nom de domaine sert pour les caches de liens
	 *
	 * @param string $pId Identifiant
	 */
	private static function _getId ($pId, $pInternalOnly = false) {
		if ($pInternalOnly !== false){
			return CopixURL::getRequestedProtocol () . '|' . CopixURL::getRequestedDomain () . '|' . $pId;
		} else {
			return $pId;
		}
	}

	/**
	 * Indique si le cache existe
	 *
	 * @param string $pId Identifiant de cache
	 * @return boolean
	 */
	public static function exists ($pId, $pInternalOnly = false) {
		$pId = self::_getId ($pId, $pInternalOnly);		

		//Si déjà recherché, pas besoin d'aller plus loin
		if (array_key_exists ($pId, self::$_cache)) {
			return true;
		}
		
		//Si pas trouvé dans le cache interne et que l'on ne souhaite se concentrer que sur celui ci, fin
		if ($pInternalOnly){
			return false;
		}

		//On doit vérifier le cache en lui même, on regarde s'il est autorisé
		if (!self::_useCache ()) {
			return false;
		}

		//interrogation de CopixCache
		return CopixCache::exists ($pId, 'CMS3');
	}

	/**
	 * Retourne le contenu du cache si il existe, ou $pReturn sinon
	 *
	 * @param string $pId Identifiant de cache
	 */
	public static function get ($pId, $pInternalOnly = false) {
		//Si n'existe pas, on ne cherche pas plus loin
		if (! self::exists ($pId, $pInternalOnly)){
			return null;
		}

		//on regarde dans le cache interne (car exists aurait pu trouver l'élément dans le CopixCache)
		$pId = self::_getId ($pId, $pInternalOnly);
		if (array_key_exists ($pId, self::$_cache)) {
			return self::$_cache[$pId];
		}
		
		// Lecture de CopixCache (try / catch en cas de court circuit)
		try {
			return self::$_cache[$pId] = CopixCache::read ($pId, 'CMS3');
		}catch (CopixCacheException $ce){
			return null;	
		}
	}

	/**
	 * Définit une valeur du cache
	 *
	 * @param string $pId Identifiant de cache
	 * @param mixed $pValue Valeur
	 * @param boolean $pUseInterneCache Indique si on veut stocker dans le cache interne, évite de trop faire grossir la classe
	 */
	public static function set ($pId, $pValue, $pUseInterneCache = true, $pInternalOnly = false) {
		$pId = self::_getId ($pId, $pInternalOnly);
		if ($pUseInterneCache) {
			self::$_cache[$pId] = $pValue;
		}
		if ((!$pInternalOnly) && self::_useCache ()) {
			try {
				CopixCache::write ($pId, $pValue, 'CMS3');
			} catch (CopixException $e){}
		}
	}

	/**
	 * Vide les caches
	 */
	public static function clear () {
		self::$_cache = array ();
		CopixCache::clear (null, 'CMS3');
	}
}
