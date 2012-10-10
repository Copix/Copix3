<?php
/**
* @package		tools 
 * @subpackage	comments
 * @author	 	Fersing Estelle
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Surcharge du DAO commentaires
* @package		tools 
 * @subpackage	comments
 */
class DAOComments {
	/**
	 * Surcharge de la fonction de vérification de l'enregistrement pour avoir les bons libellés.
	 * @param	DAORecord	$pRecord	L'enregistrement à vérifier 
	 */
	function check ($pRecord){
		//vérifications standards.
		if (($arErrors = $this->_compiled_check ($pRecord)) === true){
			$arErrors = array ();
		}

		//vérification du format du mail
		try {
			if (isset ($pRecord->authoremail_comment)){
		   		CopixFormatter::getMail ($pRecord->authoremail_comment);
			}
		}catch (Exception $e){
			$arErrors[] = $e->getMessage ();			
		}
		
		// vérification de l'antispam
	    if (CopixConfig::get('comments|captcha') != 0 && !isset($pRecord->noCaptcha)) {
            $results = _ioDAO ('commentscaptcha')->findBy(_ioDAOSp()->addCondition("captcha_id", "=", $pRecord->captcha_id));
            if (! (isset ($results[0]) && ($results[0]->captcha_answer == $pRecord->captcha_answer))){
                $arErrors[] = _i18n ('comments.admin.captcha.error');
            }
	    }

		//on remplace avec les bons libellés
		foreach ($arErrors as $key=>$error){
			$arErrors[$key] = str_replace (array ('"content_comment"', '"authoremail_comment"', '"authorsite_comment"', '"authorlogin_comment"'),
			    array (_i18n ('comments.list.content'), _i18n ('comments.list.mail'), _i18n ('comments.list.site'), _i18n ('comments.list.author')),
				$error
			);			
		}

		//erreurs s'il en existe, true sinon
		return count ($arErrors) == 0 ? true : $arErrors; 
	}
	
	function nbComments() {
	  // Requêtes permettant de compter les commentaires
	  $query = 'select count(*) as count_comments from comments ';

      // On récupère la connection à la base
      $arResults = CopixDB::getConnection()->doQuery($query);;

      // On fait la requête
      $toReturn = $arResults[0]->count_comments;

      return $toReturn;
	}
}
?>