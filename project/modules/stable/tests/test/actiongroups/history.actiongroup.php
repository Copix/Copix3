<?php
class ActionGroupHistory extends CopixActionGroup {

	/**
	 * @package standard
	 * @subpackage test
	 * @author		Julien Alexandre
	 * @copyright	CopixTeam
	 * @link		http://copix.org
	 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
	 */

	/**
	 * Actions pour définir des catégories de tests fonctionnels
	 *
	 * @package standard
	 * @subpackage test
	 */
	/**
	 * Droits d'administrateur requis
	 */
	public function beforeAction ($pAction){
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Affichage simple de l'historique
	 */
	public function processDefault () {
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = 'Historique des tests';
		if (_request('showfailuresonly') == true) {
			$parameters = _daoSP()->addCondition('result', '=', 0);
			$ppo->arData = _dao ('testhistory')->findBy ($parameters);
			$ppo->showErrors = true;
		} else {
			$ppo->arData = _dao ('testhistory')->findAll();
			$ppo->showErrors = false;
		}
		return _arPpo($ppo, 'history.view.php');
	}

}