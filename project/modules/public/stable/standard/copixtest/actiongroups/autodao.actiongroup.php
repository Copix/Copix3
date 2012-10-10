<?php
/**
 * @package standard
 * @subpackage copixtest
* @author		Croës Gérald
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Actiongroup simple pour présenter le processus d'édition d'une table en utilisant les DAO automatiques
 * @package standard
 * @subpackage copixtest
 */
class ActionGroupAutoDAO extends CopixActionGroup {
	/**
	 * Affichage de la liste des éléments en base de données
	 */
	function getList (){
		$ppo = new CopixPPO ();
		$ppo->arData = _ioDAO ('copixtestautodao')->findAll ();
		return _arPpo ($ppo, 'autodao.list.tpl');
	}

	/**
	 * Formulaire de modification ou de création d'un élément en base de données
	 * On édite soit a partir de données passées en paramètre
	 */
	function getEdit (){
       $ppo = new CopixPPO ();
       $ppo->TITLE_PAGE = "Modification d'un élément";
       if (! ($ppo->toEdit = _ioDAO ('copixtestautodao')->get (CopixRequest::get ('id_test', null, true)))){
          throw new Exception ("Impossible de retrouver l'élément demandé");
       }
       $ppo->errors = CopixRequest::get ('errors', array (), true);
       return _arPpo ($ppo, 'autodao.form.tpl');
	}

	/**
	 * Validation des éléments en base de données
	 */
	function doValid (){
		try {
			$record = _record ('copixtestautodao');
		}catch (Exception $e){
			return CopixActionGroup::process ();
		}
	} 
}
?>