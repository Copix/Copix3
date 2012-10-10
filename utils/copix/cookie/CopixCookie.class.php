<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Ferlet Patrice, Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion de la Cookie
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixCookie {

	/**
	 * Tableau contenant les cookies qui seront enregistré
	 */
	private static $_cookies = array ();

	/**
	 * Tableau contenant les cookies a supprimé
	 */
	private static $_deleteCookies = array ();
	
	/**
     * Boolean qui permet de savoir si on as deja fait l'enregistrement des cookies
     */
	private static $_register = false;
	
	
	/**
	 * Indique si les cookies sont activés
	 *
	 * Ne fonctionne qu'après l'appel à session_start
	 */
	public static function enabled (){
		if (! CopixSession::started ()){
			throw new CopixException ('[CopixCookie] Session must be started to know if cookies are enabled');
		}
		
		//Si la constante SID est définie, c'est que session_start n'est pas arrivé a créer son cookie de session
		return SID == '';
	}
	
	/**
	 * Destruction de toutes les informations qui ont été rajoutées dans le namespace indiqué
	 *
	 * @param string $pNamespace Namespace à supprimer
	 */
	public static function destroyNamespace ($pNamespace) {
		if (isset (self::$_cookies[$pNamespace])) {
			foreach (self::$_cookies[$pNamespace] as $cookie) {
				$_deleteCookies[] = $cookie;
			}
			
		}
		unset (self::$_cookies[$pNamespace]);
	}
	
	/**
	 * Prépare une valeur pour etre stockée, si c'est un objet ou un tableau, il est encapsulé dans un CopixCookieObject.
	 *
	 * @param mixed $toStore
	 */
	private static function _prepareForStorage (&$pToStore) {
		if ((is_object ($pToStore) && !($pToStore instanceof CopixCookieObject)) || is_array ($pToStore)) {
			$pToStore = new CopixCookieObject ($pToStore);
		}
	}

	/**
	 * Définition d'une variable dans la Cookie
	 *
	 * @param string $pVar	Nom de la variable
	 * @param mixed $pValue Valeur de la variable
	 * @param string $pNamespace Namespace dans lequel on veut placer la variable
	 */
	public static function set ($pVar, $pValue, $pNamespace = 'default', $pTime = null, $pPath = '/') {
                //On remplace le | par des --- sinon ca plante le cookie
                $pVar = str_replace('|', '---', $pVar);
		self::assertIsAlreadyRegistered ();
		if ($pNamespace === null) {
			$pNamespace = 'default';
		}

                //Si on ne precise pas le temps on le mets a j+30
		if ($pTime === null) {
			$pTime = time ()+ 3600*24*30;
		}
		
		if ($pValue === null) {
			if (self::get ($pVar, $pNamespace) != null) {
				self::$_deleteCookies[] = self::$_cookies[$pNamespace][$pVar];
				unset (self::$_cookies[$pNamespace][$pVar]);
			}
			
			if (isset (self::$_cookies[$pNamespace]) && count (self::$_cookies[$pNamespace]) == 0) {
				self::destroyNamespace ($pNamespace);
			}
		} else {
			self::_prepareForStorage ($pValue);
			self::$_cookies[$pNamespace][$pVar] = new StdClass ();
			self::$_cookies[$pNamespace][$pVar]->value = $pValue;
			self::$_cookies[$pNamespace][$pVar]->name = '--'.$pNamespace.'--'.$pVar;
			self::$_cookies[$pNamespace][$pVar]->time = $pTime;
			self::$_cookies[$pNamespace][$pVar]->path = $pPath;
		}
	}
	
	/**
	 * Destruction d'une variable en Cookie
	 *
	 * @param string $pVar	Nom de la variable
	 * @param string $pNamespace Namespace dans lequel est la variable à supprimer
	 */
	public static function delete ($pVar, $pNamespace = 'default') {
                //On remplace le | par des --- sinon ca plante le cookie
                $pVar = str_replace('|', '---', $pVar);
		self::set ($pVar, null, $pNamespace);
	}

	/**
	 * Retourne la valeur d'une variable en Cookie
	 *
	 * @param string $pVar	Nom de la variable
	 * @param string $pNamespace Namespace dans lequel on veut lire la variable
	 * @param mixed $pDefaultValue Valeur par défaut si la variable n'existe pas
	 * @return mixed
	 */
	public static function &get ($pVar, $pNamespace = 'default', $pDefaultValue = null) {
                //On remplace le | par des --- sinon ca plante le cookie
                $pVar = str_replace('|', '---', $pVar);
		self::assertIsAlreadyRegistered ();
		$value = $pDefaultValue;
		if (isset (self::$_cookies[$pNamespace][$pVar])) {
			if (self::$_cookies[$pNamespace][$pVar]->value instanceof CopixCookieObject) {
				$value = self::$_cookies[$pNamespace][$pVar]->value->getCookieObject();
			} else {
				return self::$_cookies[$pNamespace][$pVar]->value;
			}
		} elseif (isset ($_COOKIE['--'.$pNamespace.'--'.$pVar])) {
			$cookieValue = @unserialize(@base64_decode($_COOKIE['--'.$pNamespace.'--'.$pVar]));
			if(is_object($cookieValue)) {
				self::$_cookies[$pNamespace][$pVar] = $cookieValue;
				if ($cookieValue->value instanceof CopixCookieObject) {
					$value = self::$_cookies[$pNamespace][$pVar]->value->getCookieObject ();
				} else {
					return self::$_cookies[$pNamespace][$pVar]->value;
				}
			}
		}
		return $value;
	}
	
	/**
	 * Ajoute un élément au tableau $pVar, ou le créé si il n'existe pas
	 *
	 * @param string $pVar	Nom de la variable
	 * @param mixed $pValue Valeur de la variable
	 * @param string $pNamespace Namespace dans lequel on veut placer la variable
	 */
	/*
	public static function push ($pVar, $pValue, $pNamespace = 'default') {
		if (!isset ($_SESSION[self::_getKey ()][$pNamespace][$pVar])) {
			$_SESSION[self::_getKey ()][$pNamespace][$pVar] = new CopixSessionObject (array ());
		}
		$arrayRef = &$_SESSION[self::_getKey ()][$pNamespace][$pVar]->getSessionObject ();
		array_push ($arrayRef, $pValue);
	}
	*/
	
	/**
	 * Indique si une variable existe en Cookie
	 *
	 * @param string $pVar Nom de la variable
	 * @param string $pNamespace Namespace
	 */
	public static function exists ($pVar, $pNamespace = 'default') {
                //On remplace le | par des --- sinon ca plante le cookie
                $pVar = str_replace('|', '---', $pVar);
		return (isset (self::$_cookies[$pNamespace][$pVar]->value) || isset ($_COOKIE['--'.$pNamespace.'--'.$pVar]));
	}
	
	/**
	 * Retourne la liste des namespaces
	 *
	 * @return array
	 */
	public static function getNamespaces () {
		$toReturn = array ();
		foreach (self::$_cookies as $namespace => $value) {
			$toReturn[] = $namespace;
		}
		sort ($toReturn);
		return $toReturn;
	}
	
	/**
	 * Retourne les variables définies dans le namespace $pNamespace
	 *
	 * @param string $pNamespace Namespace dont on veut les variables
	 * @return array
	 */
	public static function getVariables ($pNamespace = 'default') {
		$toReturn = array ();
		if (self::namespaceExists ($pNamespace)) {
			foreach (self::$_cookies[$pNamespace] as $var => $value) {
				$toReturn[$var] = self::get ($var, $pNamespace);
			}
		}
		ksort ($toReturn);
		return $toReturn;
	}
	
	/**
	 * Indique si le namespace $pNamespace existe
	 *
	 * @param string $pNamespace Namespace dont on veut vérifier l'existance
	 * @return boolean
	 */
	public static function namespaceExists ($pNamespace) {
		return isset (self::$_cookies[$pNamespace]);
	}
	
	public static function assertIsAlreadyRegistered () {
		if (self::$_register) {
			throw new CopixException (_i18n ('copix:copixcookie.isAlreadyRegistered'));
		}
	}
	
	/**
     * Méthode qui permet d'enregistrer réellement les cookies
     *
     */
	public static function setCookies () {
		foreach (self::$_cookies as $namespace=>$cookies) {
			foreach ($cookies as $name=>$cookie) {
				$value = base64_encode (serialize ($cookie));
				if (!isset ($_COOKIE[$cookie->name]) || ($value != $_COOKIE[$cookie->name])) {
					setcookie ($cookie->name, $value, $cookie->time, $cookie->path);
				}
			}
		}
		
		foreach (self::$_deleteCookies as $name=>$cookie) {
			setcookie ($cookie->name, null, time()-1, $cookie->path);
		}
		
		self::$_register = true;
	}
}