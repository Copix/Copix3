<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Demande d'affichage en frontoffice d'un élément de type Heading
 * 
 * @package cms
 * @subpackage heading
 */
class ActionGroupHeadingFront extends CopixActionGroup {
	/**
	 * On vérifie que Heading|| a lancé l'ordre d'affichage des éléments demandés.
	 *
	 * @param string $pActionName
	 */
	public function beforeAction ($pActionName){
		//HeadingFront utilise Copixregistry pour indiquer les public_id dont il a demandé l'affichage
		$front = CopixRegistry::instance ()->get ('headingfront');

		if ($front !== _request('public_id')){
			throw new CopixCredentialException ('basic:admin'); 
		}
	}

	public function processDefault (){
		$ppo = _ppo ();
		$heading = _ioClass ('heading|headingservices')->getByPublicId (_request('public_id'));
		//si on a une page d'accueil pour la rubrique
		if ($heading->home_heading){
			CopixHTMLHeader::addOthers('<link rel="canonical" href="'._url('heading||', array('public_id' => $heading->home_heading)).'" />');
			return CopixActionGroup::process ('heading|default::default', array ('public_id'=>$heading->home_heading, 'origin_public_id'=>_request('public_id')));
		} elseif (HeadingElementCredentials::canWrite (_request('public_id'))) {
			$ppo = new CopixPPO ();
			$ppo->TITLE_PAGE = "Rubrique sans page d'accueil";
			$ppo->element = $heading;
			return _arPPO ($ppo, 'status.nohomepage.php');
		}
		
		// Sinon on renvoie vers l'accueil général
		return _arRedirect (_url (), array('301'=>true));
	}
}