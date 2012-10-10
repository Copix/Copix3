<?php
/**
* @package		copix
* @subpackage	taglib
* @author		Gérald Croës
* @copyright	2000-2006 CopixTeam
* @link			http://www.copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Classe de base pour gérer les exceptions dans les template
* @package		copix
* @subpackage	taglib
*/
class CopixTemplateTagException extends CopixException {}

/**
* Objet parent des balises développées dans Copix pour CopixTpl
* @package		copix
* @subpackage	taglib
*/
abstract class CopixTemplateTag {
    /**
     * Fonction qui sera en charge de créer le template
     * @param array $pParams la liste des paramètres envoyés au plugin
     * @return string le contenu de la fonction
     */
    abstract public function process ($pParams); 
}
?>