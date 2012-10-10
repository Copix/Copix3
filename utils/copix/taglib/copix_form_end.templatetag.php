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
 * Fin d'une section formulaire
 * @package		copix
 * @subpackage	taglib
 */
 class TemplateTagCopix_Form_End extends CopixTemplateTag {
    /**
     * Demande d'exécution du tag
     */
    public function process ($pParams, $pContent=null) {
    	//récupération du formulaire, création d'un nouveau formulaire si besoin
    	$form = CopixFormFactory::get (isset ($pParams['id']) ? $pParams['id'] : null);
    	return '</form>';
 	} 	
 } 
?>