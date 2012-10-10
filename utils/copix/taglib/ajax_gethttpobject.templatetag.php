<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Gérald Croës
* @copyright	2000-2006 CopixTeam
* @link			http://www.copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe qui permet d'inclure dans l'en tête HTML la fonction getHTTPObject ()
* 
* La fonction getHttpObject () à pour objectif de crééer un "XmlHTTPRequest" Javascript sous
* Mozilla et IE pour réaliser des processus Ajax
*
* @see {ajax_gethttpobject_declare}
* @package		copix
* @subpackage	taglib
*/
class TemplateTagAjax_gethttpobject extends CopixTemplateTag {
	/**
	* Déclare la fonction getHTTPObject () dans l'en tête HTML.
	* @param mixed $pParams aucun paramètre attendu ici.
	*/
	public function process ($pContent=null){
		static $called = false;
		if (!$called){
			CopixHTMLHeader::addJSCode('function getHTTPObject()
{
  var xmlhttp = false;
  /*@cc_on
  @if (@_jscript_version >= 5)
     try{
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
     }catch (e){
        try{
           xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }catch (E) {
           xmlhttp = false;
        }
     }
  @else
     xmlhttp = false;
  @end @*/
  if (!xmlhttp && typeof XMLHttpRequest != "undefined"){
     try{
        xmlhttp = new XMLHttpRequest();
     }catch (e){
        xmlhttp = false;
     }
  }
  return xmlhttp;
}', 'getHTTPObject');
			$called = true;
		}
	}
}