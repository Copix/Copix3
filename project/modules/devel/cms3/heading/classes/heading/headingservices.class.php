<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Gérald Croës
 */

/**
 * Classe de gestion des rubriques
 */
class HeadingServices extends HeadingElementServices {
	const BREADCRUMB_SHOW = 1;
	const BREADCRUMB_HIDE = 2;
	
	/**
	 * Cache des chemins
	 *
	 * @var array
	 */
	static private $_path = array (0=>array ());

	/**
	 * Retourne une courte description pour l'affichage détails
	 *
	 * @param int $pIdHelt Identifiant interne
	 * @return string
	 */
	public function getDisplayDescription ($pIdHelt) {
		$element = _ioClass ('HeadingElementInformationServices')->getById ($pIdHelt, 'heading');
		$headingsCount = count (_ioClass ('HeadingElementInformationServices')->getChildrenByType ($element->public_id_hei, 'heading'));
		$pagesCount = count (_ioClass ('HeadingElementInformationServices')->getChildrenByType ($element->public_id_hei, 'page'));
		$articlesCount = count (_ioClass ('HeadingElementInformationServices')->getChildrenByType ($element->public_id_hei, 'article'));
		$imagesCount = count (_ioClass ('HeadingElementInformationServices')->getChildrenByType ($element->public_id_hei, 'image'));

		$infos = array ();
		$infos[] = ($headingsCount > 1) ? $headingsCount . ' rubriques' : $headingsCount . ' rubrique';
		$infos[] = ($pagesCount > 1) ? $pagesCount . ' pages' : $pagesCount . ' page';
		$infos[] = ($articlesCount > 1) ? $articlesCount . ' articles' : $articlesCount . ' article';
		$infos[] = ($imagesCount > 1) ? $imagesCount . ' images' : $imagesCount . ' image';
		$toReturn = implode (' - ', $infos);
		return $toReturn;
	}
	
	/**
	 * Retourne la description de l'élément
	 * 
	 * @param int $pIdHelt
	 */
	public function getDescription ($pIdHelt){
		$heading = $this->getById ($pIdHelt);
		return $heading->description_hei;
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
		if($elem->home_heading){
			return _url ('heading||', array ('public_id'=>$elem->public_id_hei));
		}else{
			return false;
		}
	}

	/**
	 * Création d'une nouvelle rubrique
	 * @param array / object $pHeadingDescription
	 */
	public function insert ($pHeadingDescription){
		HeadingCache::clear ();
		$headingDescription = _ppo ($pHeadingDescription);
		CopixDB::begin ();
		try {
			//création de l'enregistrement
			$record = DAORecordcms_headings::create ()->initFromDbObject ($pHeadingDescription);
			if (!$record->home_heading){
				$record->home_heading = null;
			} else {
				$record->home_heading = (int)$record->home_heading;
			}
			$record->breadcrumb_show_heading = self::BREADCRUMB_SHOW;
			DAOcms_headings::instance ()->insert ($record);

			foreach (_ioClass ('heading|headingelementinformationservices')->getFields () as $propertyName){
				$record->$propertyName = $headingDescription[$propertyName];
			}
			$record->id_helt = $record->id_heading;
			$record->type_hei = 'heading';

			//dans $record->id_hei on a le nouvel identifiant
			_ioClass ('heading|headingelementinformationservices')->insert ($record);

			//on met maintenant a jour l'élément $record avec les informations publiques mises à jour
			DAOcms_headings::instance ()->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pHeadingDescription);
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e; 
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/**
	 * Mise à jour d'une rubrique
	 * @param array / object $pHeadingDescription
	 */
	public function update ($pHeadingDescription){
		HeadingCache::clear ();
		$headingDescription = _ppo ($pHeadingDescription);
		
		CopixDB::begin ();
		try {
			//on récupère l'enregistrement actuel
			$record = $this->getById ($headingDescription['id_heading']);

			//on met a jour les données spécifiques
			$record->home_heading = $headingDescription['home_heading'] ? (int)$headingDescription['home_heading'] : null;
			$record->breadcrumb_show_heading = $headingDescription['breadcrumb_show_heading'];
			DAOcms_headings::instance ()->update ($record);

			//on met a jour les données génériques
			foreach (_ioClass ('heading|headingelementinformationservices')->getFields () as $propertyName){
				$record->$propertyName = $headingDescription[$propertyName];
			}
			_ioClass ('headingelementinformationservices')->update ($record);

			//Application des changements
			_ppo ($record)->saveIn ($pHeadingDescription);
		}catch (CopixException $e){
			CopixDB::rollback ();
			HeadingCache::clear ();
			throw $e;
		}
		CopixDB::commit ();
		HeadingCache::clear ();
	}
	
	/**
	 * Supprime une ou plusieurs rubriques données en fonction du public_id
	 *
	 * @param int $pArPublicId le ou les identifiants 
	 */
	public function delete ($pArPublicId) {
		HeadingCache::clear ();
		DAOcms_headings::instance ()->deleteBy (_daoSp ()->addCondition ('public_id_hei', '=', $pArPublicId));
		HeadingCache::clear ();
	}

	/**
	 * Supprime le(s) élément(s) via l'identifiant interne
	 *
	 * @param int $pIdHelt Identifiant interne
	 */
	public function deleteById ($pIdHelt) {
		HeadingCache::clear ();
		DAOcms_headings::instance ()->deleteBy (_daoSp ()->addCondition ('id_heading', '=', $pIdHelt));
		HeadingCache::clear ();
	}

	/**
	 * Retourne le chemin des parents à partir d'un identifiant public
	 *
	 * @param int $pPublicId : identifiant public
	 * @return array () : Identifiants des parents
	 */
	public function getPath ($pPublicId){
		//dans le cas de l'element racine, on renvoie directement le path
		if ($pPublicId == 0){
			return array(0 => 0);
		}
		
		if (array_key_exists ($pPublicId, self::$_path)){
			return self::$_path[$pPublicId];
		}
	
		$path = array ($public_id = $pPublicId);
		$heiServices = _ioClass ('headingelementinformationservices');	
		do {
			$element = $heiServices->get ($public_id);
			if (array_key_exists ($element->parent_heading_public_id_hei, self::$_path)){
				$path = $path + self::$_path[$element->parent_heading_public_id_hei]; 				
				break;
			}elseif ($public_id = $element->parent_heading_public_id_hei){
				$path[] = $public_id;
			}else{
				break;
			}
			
		} while ($public_id);
		return self::$_path[$pPublicId] = $path;
	}

	/**
	 * Récupération d'une rubrique en fonction de son identifiant
	 * 
	 * @param int $pPublicId l'identifiant public de l'élément
	 * @throws HeadingElementInformationNotFoundException
	 */
	public function getById ($pId){
		$record = DAOcms_headings::instance ()->get ($pId);
		if (!$record) {
			throw new HeadingElementInformationNotFoundException ($pId);
		}

		//on récupère les informations "communes"
		$element = _ioClass ('headingelementinformationservices')->getById ($pId, 'heading');

		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($element);

		return $element;
	}

	public function getByPublicId ($pPublicId){
		//on récupère les informations "communes"
		$element = _ioClass ('heading|HeadingElementInformationServices')->get ($pPublicId);
		if (! $record = DAOcms_headings::instance ()->get ($element->id_helt)){
			throw new HeadingElementInformationNotFoundException ($pPublicId);
		}

		//fusion des informations communes et spécifiques
		_ppo ($record)->saveIn ($element);

		return $element;
	}	
	
	/**
	 * Retourne les rubriques faisant référence à l'element de publicId $pPublicId en page d'accueil
	 *
	 * @param int $pPublicId
	 */
	public function getDependencies ($pPublicId) {
		$query = 'SELECT DISTINCT id_heading FROM cms_headings WHERE home_heading = :public_id';
		$toReturn = array ();
		foreach (_doQuery ($query, array (':public_id' => $pPublicId)) as $result) {
			$toReturn[] = $this->getById ($result->id_heading);
		}
		return $toReturn;
	}

	/**
	 * Prévisualisation
	 *
	 * @param int $pId Identifiant
	 * @return string
	 */
	public function previewById ($pId) {
		$record = $this->getById ($pId);
		$infos = array ();
		if ($record->home_heading > 0) {
			$recordAccueil = _ioClass ('HeadingElementInformationServices')->get ($record->home_heading);
			$url = _url ('heading|element|', array ('heading' => $recordAccueil->parent_heading_public_id_hei, 'selected' => array ($recordAccueil->id_helt . '|' . $recordAccueil->type_hei)));
			$infos['accueil'] = array ('caption' => 'Page d\'accueil', 'value' => '<a href="' . $url . '">' . $recordAccueil->caption_hei . '</a>');
		} else if ($record->public_id_hei >0){
			$url = _url('heading|element|prepareEdit', array('type' =>$record->type_hei, 'id' => $record->id_helt, 'heading' => $record->parent_heading_public_id_hei));
			$infos['accueil'] = array ('caption' => 'Page d\'accueil', 'value' => '<a href="' . $url . '">[Définir la page d\'accueil]</a>');
		}
		
		return CopixZone::process ('heading|headingelement/headingelementpreview', array ('record' => $record,	'infos' => $infos));
	}

	/**
	 * Permet de changer les actions (couper, copier, etc) possibles sur un élément
	 * /!\ A ne pas appeler directement, passer par HeadingElementInformationServices::getActions ()
	 *
	 * @param stdClass $pElement Enregistrement de l'élément
	 * @param stdClass $pActions Actions déja prédéfinies par HeadingElementInformationServices::getActions
	 */
	public function getActions ($pElement, $pActions) {
		$element = $this->getById ($pElement->id_helt);
		$pActions->copy = false;
		$pActions->publish = false;
		$pActions->show = ($element->home_heading > 0);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see project/modules/devel/cms3/heading/classes/HeadingElementServices::findGhosts()
	 */
	public function findGhosts (){
		$toReturn['specific'] = _doQuery ('select * from cms_headings where id_heading not in(select id_helt from cms_headingelementinformations where type_hei = :type and status_hei <> :status)', array (':type'=>'heading', ':status'=>HeadingElementStatus::DELETED));
		$toReturn['general']  = _doQuery ('select * from cms_headingelementinformations where type_hei = :type and id_helt not in (select id_heading from cms_headings)', array (':type'=>'heading'));
		return $toReturn;
		
	}
}
