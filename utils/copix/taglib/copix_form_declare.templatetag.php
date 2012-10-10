<?php
/**
 * @package    copix
 * @subpackage taglib
 * @author     Gérald Croës
 * @copyright  CopixTeam
 * @link       http://www.copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Déclaration d'un formulaire
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCopix_Form_Declare extends CopixTemplateTag {
	/**
	 * Demande d'exécution du tag
	 */
	public function process ($pContent=null) {
		$pParams = $this->getParams ();
		//récupération du formulaire, création d'un nouveau formulaire si besoin
		$form = CopixFormFactory::get (isset ($pParams['id']) ? $pParams['id'] : null);

		//ajout d'un DAO au formulaire
		if (isset ($pParams['dao'])){
			$form->setDAOId ($pParams['dao']);
		}
		 
		//ajout de l'enregistrement à éditer
		if (isset ($pParams['record'])){
			$form->setRecord ($pParams['record']);
		}

		if (isset ($pParams['action'])){
			$form->setAction ($pParams['action']);
		}

		//retour du code HTML attendu
		return '<form id="'.$form->getId ().'" method="'.$form->getMethod ().'" action="'.$form->getAction ().'">'."\n";
	}
}