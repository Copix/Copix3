<?php
/**
 * @package		webtools
 * @subpackage	quicksearch
* @author	Gérald Croës
* @copyright CopixTeam
* @link      http://copix.org
* @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Actions de recherche
 * @package		webtools
 * @subpackage	quicksearch
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Fonction exécutée par défaut / formulaire / résultats
	 */
	public function processDefault (){
		if (CopixRequest::get ('criteria') !== null){
			return $this->processResults ();
		}
		return $this->processForm ();
	}
	
	/**
	 * Ecran de saisie pour le moteur de recherche
	 */
	public function processForm (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('quicksearch.search');
		return new CopixActionReturn (CopixActionReturn::PPO, $ppo, 'search.form.tpl');
	}

	/**
    * Lance la recherche et affiche les résultats.
    */
	public function processResults (){
		CopixRequest::assert ('criteria');

		//Création de l'objet de recherche.
        $objLook = _class ('QuickSearch');
		$objLook->clearAll ();

		//Définition des paramètres de la recherche.
		$objLook->AdvancedSearch = true;
		$objLook->TableName   = "quicksearchindex";
		$objLook->FieldIdName = "idobj_srch";

		//Définition des poids de rcherche
		$objLook->AssignLookParams (array ("keywords_srch"=>CopixConfig::get ('keywords_srch'), "title_srch"=>CopixConfig::get ('quicksearch|title_srch'), "summary_srch"=>CopixConfig::get ('resume_srch'), "content_srch"=>CopixConfig::get ('content_srch')));
		$objLook->AddFields (array ("title_srch", "kind_srch", "summary_srch", "url_srch" ));

		//Lancement de la recherche
		$tpl = new CopixTpl ();
		$tpl->assign ('TITLE_PAGE', _i18n ('quicksearch.title.show'));
		$nbResult = $objLook->ExecuteRequest (CopixRequest::get ('criteria'));
		if ( $nbResult > 0) {
			$tpl->assign ('MAIN', CopixZone::process ('QuickSearchResults', array ('results'=>$objLook, 'nbResult'=>$nbResult)));
		}else{
			$tpl->assign ('MAIN', _i18n ('quicksearch.noresult'));
		}
		return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
	}
}
?>