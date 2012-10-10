<?php
/**
* @package   copix
* @subpackage core
* @author    David Derigent
* @copyright 2001-2006 CopixTeam
* @link      http://copix.org
* @license   http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Objet Cli permet de manipuler les paramètres recu en ligne de commande 
* Paramètres :
*              module :  Nom du module appelée (obligatoire)
* 					action : Nom de l'action appelée (obligatoire)
*              desc   : Nom du fichier de mapping methode-action (facultatif)
*              httpHost : Nom de domaine du serveur (facultatif)
*              pathInfo : Chemin web du script (facultatif)
*              serverName :  Nom du serveur hôte qui exécute le script, 
*                           s'il n'est pas indiqué, $_SERVER ['SERVER_NAME'] 
*                           est initialisé avec une chaine vide(facultatif)
*              autres paramètres : vous avez la possibilité de passer tous les paramètres utiles 
*              à vos traitements métier en suivant la syntaxe suivante param=value.
*              
*              
*  <code>
*  index_cli.php  module=valeur1 action=valeur2 desc=valeur3 param1=valeurParam1 param2=ValeurParam2
*  </code>
* 
* @package copix
* @subpackage core
*/
class CopixCLI {

	/**
	* message contient les messages d'erreur générer lors de la 
	* manipulation des paramètres
	* @var string
	* @access private
	*/
	 public  $message = null;

	/**
	* Met dans la requête les éléments passés en paramètre de l'appel en ligne de commande
	* @return boolean
	* @static
	*/
	 public function prepare() {
		$isOk = false;
		try {
			if ($_SERVER['argc'] < 3) {
				$this->message = 'Paramètres obligatoires manquants';
				return $isOk;
			}
			//on met dans la requete les éléments passés en paramètre 
			for ($i = 1; $i < count($_SERVER['argv']); $i++) {
				$params = explode("=", $_SERVER['argv'][$i], 2);
				$_REQUEST[$params[0]] = $params[1];
			}
			
			if(!isset($_REQUEST['httpHost'])  ||  $_REQUEST['httpHost']==''){
				$_SERVER ['HTTP_HOST']='';
			} else{
				$_SERVER ['HTTP_HOST']=$_REQUEST['httpHost'];
			}
			
			if(!isset($_REQUEST['serverName'])  ||   $_REQUEST['serverName']==''){
				$_SERVER ['SERVER_NAME']='';
			} else{
				$_SERVER ['SERVER_NAME']=$_REQUEST['serverName'];
			}
			
			
			if(!isset($_REQUEST['pathInfo'])  || $_REQUEST['pathInfo']==''){
				$_SERVER ['PATH_INFO']='';
			} else{
				$_SERVER ['PATH_INFO']=$_REQUEST['pathInfo'];
			}

			//on teste si les éléments indispensables pour le fonctionnement de copix sont bien renseignés
			if ($_REQUEST['module'] == '' || $_REQUEST['action'] == '') {
				$this->message = 'Les paramètres module et acion sont obligatoires';
				return $isOk;
			} else {
				$isOk = true;
			}
		} catch (Exception $e) {
			$this->message = $e->getMessage();
		}

		return $isOk;
	}
	
}
?>