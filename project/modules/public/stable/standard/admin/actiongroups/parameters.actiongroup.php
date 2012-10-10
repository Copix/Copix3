<?php
/**
 * @package standard
 * @subpackage admin 
* 
* @author		Bertrand Yan
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Administration des paramètres
* @package standard
* @subpackage admin 
*/
class ActionGroupParameters extends CopixActionGroup {
	/**
	 * Executé avant toute action
	 *
	 * @param string $pActionName Nom de l'action
	 */
	public function beforeAction ($pActionName) {
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

   /**
   * Ecran par défaut
   */
   function processDefault () {
      $tpl = new CopixTpl ();
      $tpl->assign ('TITLE_BAR', CopixI18N::get ('params.title'));

      $choiceModule = CopixRequest::get ('choiceModule', false, true);
      $editParam    = CopixRequest::get ('editParam', false, true);

      $tpl->assign ('TITLE_PAGE', CopixI18N::get ('params.titlePage.admin'));
      $tpl->assignZone ('MAIN', 'ShowParams', array ('choiceModule'=>$choiceModule,
                                                     'editParam'=>$editParam));
      return _arDisplay ($tpl);
   }

   /**
   * Applique les changements sur le paramètre
   */
	function processValid () {
		CopixRequest::assert ('idFirst', 'idSecond', 'value');
   	  
		// si la config existe bien
		if (CopixConfig::exists (CopixRequest::get ('idFirst').'|'.CopixRequest::get ('idSecond'))){
			// initialisation de variables
			$id = CopixRequest::get ('idFirst').'|'.CopixRequest::get ('idSecond');
			$params = CopixConfig::getParams (CopixRequest::get ('idFirst'));
			$config = $params[$id];
			$value = CopixRequest::get ('value');
			$error = false;
			
			// type int
			if ($config['Type'] == 'int') {
				// chiffre invalide
				if ((string)intval ($value) <> (string)$value) {
					$error = 'typeInt';
				// chiffre trop petit
				} else if (!is_null ($config['MinValue']) && $config['MinValue'] > intval ($value)) {
					$error = 'typeIntMin';
				// chiffre trop grand
				} else if (!is_null ($config['MaxValue']) && $config['MaxValue'] < intval ($value)) {
					$error = 'typeIntMax';
				}
			
			// type email
			} else if ($config['Type'] == 'email') {
				// email invalide
				try {
					CopixFormatter::getMail ($value);
				} catch (CopixException $e) {
					$error = 'typeEmail';
				}
				
				// e-mail trop long
				if (!is_null ($config['MaxLength']) && strlen ($value) > $config['MaxLength']) {
					$error = 'typeEmailMax';
				}
				
			// type text
			} else if ($config['Type'] == 'text') {
				// texte trop long
				if (!is_null ($config['MaxLength']) && strlen ($value) > $config['MaxLength']) {
					$error = 'typeTextMax';
				}
			}
			
			// si il y a eu une erreur
			if ($error !== false) {
				return _arRedirect (_url ('admin|parameters|', array ('choiceModule'=>CopixRequest::get ('choiceModule'), 'editParam'=>CopixRequest::get ('idSecond'), 'error' => $error)));
			}
          
			// modification de la config
			CopixConfig::set ($id, $value);
		}
		return _arRedirect (_url ('admin|parameters|', array ('choiceModule'=>CopixRequest::get ('choiceModule'))));
	}

   /**
   * Sélection d'un module et redirection vers la page par défaut
   * (simplement pour éviter les problèmes de rafraichissement de page)
   */
   function processSelectModule () {
      return _arRedirect (_url ('admin|parameters|',
           (CopixRequest::get ('choiceModule') !== null)? array ('choiceModule'=>CopixRequest::get ('choiceModule')) : array ()));
   }
}
?>