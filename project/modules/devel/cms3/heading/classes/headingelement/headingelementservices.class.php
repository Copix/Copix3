<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author VUIDART Sylvain
 */

/**
 * Classe à étendre pour créer un service d'élément
 *
 * @package cms
 * @subpackage heading
 */
abstract class HeadingElementServices {
	/**
	 * Cache de _ioClass ('heading|HeadingElementInformationServices') pour éviter d'avoir trop d'appels à _ioClass
	 * 
	 * @var HeadingElementInformationServices
	 */
	protected $_hei = null;

	/**
	 * Appelle une méthode d'un service d'élément
	 *
	 * @param string $pTypeHei Type de l'élément
	 * @param string $pMethod nom de la méthode
	 * @param array $pParams Paramètre à passer à la méthode
	 * @return mixed
	 */
	public static function call ($pTypeHei, $pMethod, $pParams = array ()) {
		$typeInformations = _ioClass ('heading|headingelementtype')->getInformations ($pTypeHei);
		$elementServices = _ioClass ($typeInformations['classid']);
		if (!is_array ($pParams)) {
			$pParams = array ($pParams);
		}
		return call_user_func_array (array ($elementServices, $pMethod), $pParams);
	}

	/**
	 * Ajoute un élément
	 *
	 * @param stdClass $pDescription Description de l'élément
	 */
	abstract public function insert ($pDescription);

	/**
	 * Modifie un élément
	 *
	 * @param stdClass $pDescription Description de l'élément
	 */
	abstract public function update ($pDescription);

	/**
	 * Retourne l'élément par son identifiant interne
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return HeadingElementServices
	 */
	abstract public function getById ($pIdHelt);

	/**
	 * Retourne un élément depuis son identifiant publique
	 *
	 * @param int $pPublicId Identifiant publique
	 * @return stdClass
	 */
	abstract public function getByPublicId ($pPublicId);

	/**
	 * Retourne une courte description pour l'affichage détaillé
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	abstract public function getDisplayDescription ($pIdHelt);

	/**
	 * Supprime le(s) élément(s) via le public_id
	 *
	 * @param int $pPublicId Identifiant publique
	 */
	abstract public function delete ($pPublicId);

	/**
	 * Supprime le(s) élément(s) via l'identifiant interne
	 *
	 * @param int $pIdHelt Identifiant interne
	 */
	abstract public function deleteById ($pIdHelt);

	/**
	 * Retourne les éléments qui sont liés à $pPublicId
	 *
	 * @param int $pPublicId Identifiant publique
	 * @return array
	 */
	abstract public function getDependencies ($pPublicId);

	/**
	 * Retourne la liste des contenus perdus (qui n'ont pas d'équivalent dans headingelementinformations)
	 *
	 * @return array
	 */
	abstract public function findGhosts ();

	/**
	 * 
	 * Retourne la description de l'élément
	 * @param int $pIdHelt
	 */
	public function getDescription ($pIdHelt){
		return '';
	}
	
	/**
	 * 
	 * Retourne l'adresse pointée par l'élément
	 *
	 * @param int $pIdHelt Identifiant de l'élément
	 * @return string
	 */
	public function getURL ($pIdHelt) {
		$elem = $this->getById($pIdHelt);
		return _url ('heading||', array ('public_id'=>$elem->public_id_hei));
	}
	

	/**
	 * Constructeur
	 */
	public function __construct () {
		$this->_hei = _ioClass ('heading|HeadingElementInformationServices');
	}

	/**
	 * Prévisualisation
	 *
	 * @param int $pId Identifiant
	 * @return string
	 */
	public function previewById ($pId) {
		return CopixZone::process ('heading|headingelement/headingelementpreview', array ('record' => $this->getById ($pId)));
	}

	/**
	 * Retourne un nouvel élément, pour la création
	 *
	 * @return mixed
	 */
	public function getNew () {
		return _ppo ();
	}

	/**
	 * Permet de changer les actions (couper, copier, etc) possibles sur un élément
	 * /!\ A ne pas appeler directement, passer par HeadingElementInformationServices::getActions ()
	 *
	 * @param stdClass $pElement Enregistrement de l'élément
	 * @param stdClass $pActions Actions déja prédéfinies par HeadingElementInformationServices::getActions
	 */
	public function getActions ($pElement, $pActions) { }
	
	/**
	 * Retourne les publicId des éléments qui contiennent $toSearch
	 * @param string $toSearch
	 */
	public function search ($toSearch){
		return array();
	}

	/**
	 * Supprime l'élément "perdu", qui n'existe plus dans headingelementinformation
	 *
	 * @param int $pIdHelt Identifiant de l'élément
	 */
	public function deleteGhost ($pIdHelt) {
		return $this->deleteById ($pIdHelt);
	}

	/**
	 * Retourne les éléments qui pointent vers des éléments qui n'existent pas ou plus
	 *
	 * @return array
	 */
	public function findDeadLinks () {
		return array ();
	}
	
	/**
	 * Methode indiquant si il existe une methode d'export des données pour le type d'element correspondant à la classe de service 
	 * A surcharger dans la classe fille par "return true" si la classe d'export est implémentée
	 */
	public function canExport(){
		return false;
	}
	
	/**
	 * 
	 * Methode d'export des elements correspondant à la classe de service dans un fichier zip
	 * @param String $pZipName
	 * @param HeadingElement $pElement
	 */
	public function export($pZipName, $pElement){ }
}