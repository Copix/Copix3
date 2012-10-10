<?php
/**
 * @package     tools
 * @subpackage  antispam
 * @author      Duboeuf Damien
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion d'un capcha
 * @package tools
 * @subpackage antispam
 */
class Captcha {
	
	/**
	 * methode permetant de creer l'objet captcha
	 * @param string $pMethod force la méthode de captcha à utiliser
	 */
	public final function create ($pMethod=NULL) {
		if ($pMethod === NULL) {
			$pMethod = strtolower (CopixConfig::get ('antispam|method'));
		}
		return _ioClass ('antispam|'.$pMethod);
	}
	
	/**
	 * HTML de la question qui sera demandé
	 * @param	string	$namespace permet de différencier les captchas quand il sont mutltiple dans la meme page
	 * @param	array	$params Listes de prametre spécifique
	 * @return string
	 */
	public function getHTMLQuestion ($namespace = NULL, $params=NULL) {
		return '';
	}
	
	/**
	 * HTML de la reponse qui sera demandé
	 * @param	string	$namespace permet de différencier les captchas quand il sont mutltiple dans la meme page
	 * @param	array	$params Listes de prametre spécifique
	 * @return string
	 */
	public function getHTMLResponse ($namespace = NULL, $params=NULL) {
		return '';
	}
	
	/**
	 * Fonction de vérification des captcha
	 * @param	string	$namespace permet de différencier les captchas quand il sont mutltiple dans la meme page
	 */
	public function check ($namespace = NULL){
		return true;
	}
	
}