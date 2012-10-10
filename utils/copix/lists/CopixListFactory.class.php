<?php
/**
 * @package		copix
 * @subpackage	lists
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Classe principale pour CopixList
 * @package		copix
 * @subpackage	lists
 */
class CopixListFactory {

	/**
	 * Trace les dernières listes crées par CopixListFactory
	 *
	 * @var array
	 */
	private static $_currentId = array();

	/**
	 * Définition de l'identifiant de liste actuellement utilisée
	 *
	 * @param string $pId l'identifiant a définir
	 */
	public static function setCurrentId ($pId) {
		CopixListFactory::$_currentId = $pId;
	}

	/**
	 * Récupération de l'identifiant de formulaire actuellement utilisé
	 *
	 * @return string / null si aucun formulaire n'est actuellement en cours de manipulation
	 */
	public static function getCurrentId () {
		return CopixListFactory::$_currentId;
	}

	/**
	 * Récupération / création d'un formulaire
	 *  
	 * @param string $pId l'identifiant du formulaire à créer. Si rien n'est donné, un nouveau formulaire est créé
	 * @return CopixList
	 */
	public static function get ($pId = null){
		//Aucun identifiant donné ? bizarre, mais créons lui un identifiant
		if ($pId === null){
			if (CopixListFactory::getCurrentId () === null) {
				//@TODO I18N
				throw new CopixException ("Aucun ID en cours, vous devez en spécifier un pour votre liste");
			} else {
				$pId = CopixListFactory::getCurrentId ();
			}
		}

		if ($pId != CopixListFactory::getCurrentId ()) {
			CopixListFactory::setCurrentId ($pId);
		}

		//le formulaire existe ?
		$list = CopixSession::get ($pId, 'COPIXLIST');
		if ($list != null){
			return $list;
		}
		$list = new CopixList ($pId);
		CopixSession::set ($pId, $list, 'COPIXLIST');

		//Création du nouveau formulaire
		return $list;
	}

	/**
	 * Supression du CopixList d'identifiant demandé
	 *
	 * @param string $pId l'identifiant du CopixList a supprimer.
	 */
	public static function delete ($pId) {
		CopixSession::set ($pId, null, 'COPIXLIST');
	}
}