<?php
/**
* @package	copix
* @subpackage auth
* @version	$Id: theme.plugin.php,v 1.2 2007/03/21 21:31:52 metal3d Exp $
* @author	Croes Gérald see copix.org for other contributors.
* @copyright 2001-2005 CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/
class PluginTheme extends CopixPlugin {
    function beforeProcess (&$execParam){
    	if (($theme = $this->getUserTheme ()) !== null){
    		CopixTpl::setTheme ($theme);
    	}
    }
    
    /**
    * Defines the user theme
    */
    function setUserTheme ($theme){
    	switch ($this->config->themeLifeTime){
    		case 'session':
    		   $_SESSION['TEMPLATE']['userTheme'] = $theme;
    		   break;
    		case 'cookie':
    		   setcookie('TEMPLATE', 1, time()+60*60*24*30);
    		   $_COOKIE['TEMPLATE']['userTheme'] = $theme;
    		   break;
    	}
    }

    /**
    * Gets the user's theme
    */
    function getUserTheme (){    
    	switch ($this->config->themeLifeTime){
    		case 'session':
    		   $varName = '_SESSION';
    		   break;
    		case 'cookie':
    		   $varName = '_COOKIE';
    		   break;
    		default: $varName = null;
    	}
    	if ($varName !== null){
    		if (isset ($$varName['TEMPLATE']['userTheme'])){
    			return $$varName['TEMPLATE']['userTheme'];
    		}
    	}
    	return null;
    }
}
?>