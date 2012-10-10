<?php
/**
 * @package		copix
 * @subpackage	core
 * @author		Ferlet Patrice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des cookies
 */
class CopixCookie {
	/**
	 * Create the cookie
	 * @param $pName string
	 * @param $pValue mixed
	 * @param $pNameSpace
	 * @param $ptime timestamp time to live
	 */
	public static function set($pName, $pValue, $pNameSpace=null, $pTime=null){	    
	    $pNameSpace = is_null ($pNameSpace) ? "default" : $pNameSpace;	    
	    $pTime = is_null ($pTime) ? time ()+ 6307200000 : time () + $pTime; //+200 years
	    
	    //the cookie name
	    $name = "COPIX_NAMESPACE_".$pNameSpace.'__:'.$pName;
	    
		if ($pValue === null){
			unset ($_COOKIE[$pName]);
			setcookie($name,null,time()-1,'/');
		}else{
			if (is_object ($pValue) && !($pValue instanceof CopixCookieObject)){
				$pValue = new CopixCookieObject ($pValue);
			}
			setcookie ($name,serialize ($pValue), $pTime,'/');
			$_COOKIE[$name]=$pValue;
		}
	}
	
	/**
	 * get the cookie value
	 * @param $pName string name
	 * @return mixed value
	 */
	public static function get($pName,$pNameSpace='default'){
	    $name = "COPIX_NAMESPACE_".$pNameSpace.'__:'.$pName;
		if (isset ($_COOKIE[$name])){
		    $_COOKIE[$name] = unserialize($_COOKIE[$name]);
			if ((is_object ($_COOKIE[$name]))  
			    && ($_COOKIE[$name] instanceof CopixCookieObject)) {
				return $_COOKIE[$name]->getCookieObject ();
			}else{
				return $_COOKIE[$name];
			}
		}
		return null;
	}
	
	
	/**
	 * Delete value
	 */
	public static function delete($pName,$pNameSpace=null){	    
	    CopixCookie::set ($pName, null, $pNameSpace, time()-1);
	}
}

/**
 * Objet pouvant être mis dans un cookie
 */
class CopixCookieObject extends CopixSerializableObject {
	public function getCookieObject (){
   		return $this->getRemoteObject ();
   	}
	
}
?>