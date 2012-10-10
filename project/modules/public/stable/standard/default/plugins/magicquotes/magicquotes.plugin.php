<?php
/**
* @package		standard
* @subpackage	plugin_magicquotes
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin magicquotes qui permet de supprimer les slashes ajoutés
 * par PHP si magic_quotes est activé.
 * Il est recommandé pour plus de performances de ne pas enregistrer ce plugin
 * et de désactiver l'option magic_quotes dans le PHP.ini
 * @package standard
 * @subpackage plugin_magicquotes
 */
class PluginMagicQuotes extends CopixPlugin {
	/**
    * magic_quotes are on or off... ?*
    * @var boolean   $magic_quotes
    */
	var $magic_quotes;
	
	/**
    * @var  CopixController  $app
    * @param   class   $config      objet de configuration du plugin
    */
	public function __construct ($config){
		parent::__construct ($config);
		$this->magic_quotes = get_magic_quotes_gpc();
	}

	/**
    * surchargez cette methode si vous avez des traitements à faire, des classes à declarer avant
    * la recuperation de la session
    */
	public function beforeSessionStart(){
		foreach (CopixRequest::asArray () as $key=>$elem){
			CopixRequest::set ($key, $this->_stripSlashes ($elem));
		}
	}

	/**
    * enleve tout les slashes d'une chaine ou d'un tableau de chaine
    * @param string/array   $string
    * @return string/array   l'objet transformé
    */
	private function _stripSlashes ($string){
		if ($this->magic_quotes){
			if (is_array ($string)){
				$toReturn = array ();
				// c'est un tableau, on traite un à un tout les elements du tableau
				foreach ($string as $key=>$elem){
					$toReturn[$key] = $this->_stripSlashes ($elem);
				}
				return $toReturn;
			}else{
				return stripSlashes ($string);
			}
		}else{
			return $string;
		}
	}
}
?>