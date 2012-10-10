<?php
/**
 * @package		repository
 * @author		Favre Brice
 * @copyright 	CopixTeam
 * @link 		http://copix.org
 * @license  	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package		tools
 * @subpackage	repository
 */
class ActionGroupFile extends CopixActionGroup {

	
	/**
	 * On vérifie que l'utilisateur est bien connecté
	 *
	 * @param string $pActionName
	 */
	public function beforeAction ($pActionName){
		_currentUser ()->assertCredential ('basic:registered');
	}


	/**
	 * Affichage de la liste des fichiers
	 *
	 * @return CopixActionReturn
	 */
	public function processList (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('repository.title.list');
		
		// Récupération des catégories
		$ppo->list_categories = _ioClass ('repository|categories')->get ();
		$ppo->list_subcategories = _ioClass ('repository|categories')->get (true);

		// Récupération des paramètres
		$ppo->id_category = _request ('id_category', null);
		$ppo->id_subcategory  = _request ('id_subcategory', null);
		
		// Récupération de la zone d'affichage des fichiers
		$ppo->zonelist = CopixZone::process ('repository|filelist', array ('id_category'=>$ppo->id_category, 'id_subcategory'=>$ppo->id_subcategory));

		return _arPPO ($ppo, 'file.page.php');
	}
	/**
	 * Affichage du formulaire
	 */
	public function processUpload (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('repository.title.upload');
		$ppo->zoneform = CopixZone::process ('repository|uploadform');
		$ppo->uploadedFile = _request ('uploaded', false);
		return _arPPO ($ppo, 'upload.page.php');
	}
	/**
	 * Validation de l'upload
	 *
	 * @return CopixActiongroup::process () Message Générique
	 */
	public function processValidForm (){
		$result = false;
		// Si on passe par la version dégradé alors on enregistre le fichier
		if (isset($_FILES["resume_degraded"]) && $_FILES["resume_degraded"]["error"] == 0) {
			$result = _ioClass ('repository|storedfile')->store ('resume_degraded');
		}
		// Fichier bien enregistré ou formulaire validé par Ajax
		if ($result !== false || (_request ('hidFileID') != '')){
			$result = _ioClass ('repository|storedfile')->addInformation (_request ('hidFileID'));
			return _arRedirect (_url ('repository|file|upload', array ('uploaded' => _request ('file_title'))));
		} else {
			return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>CopixI18N::get ('repository.error.upload'), 'back'=>_url ('repository|file|addfile')));
		}
	}

	/**
	 * Fonction de téléchargement, permet de télécharger un fichier uploadé
	 *
	 * @return CopixActionReturn::Content le fichier déposé.
	 */
	public function processDownload (){
		// Recuperation de l'id
		if (CopixRequest::get ('id', null) !== null) {
			// On cherche et renvoie le fichier

			$dao = & CopixDAOFactory::create ('storedfile');
			$sp = & CopixDAOFactory::createSearchParams();
			$sp->addCondition ("storedfile_id", "=", CopixRequest::get ('id'));
			$arRecord = $dao->findBy ($sp);
			if ( count($arRecord) >0 ) {
				$record = $arRecord[0];
				$data = file_get_contents($record->storedfile_path.$record->storedfile_name);
				$record-> storedfile_nbdownload +=1;
				$dao->update ($record);
				return new CopixActionReturn (CopixActionReturn::CONTENT, $data, array('filename'=>$record->storedfile_name, 'content-type'=>CopixMIMETypes::getFromFileName($record->storedfile_name)));
			} else {
				// @todo : changer le message d'erreur
				return CopixActionGroup::process ('genericTools|Messages::getError', array ('message'=>CopixI18N::get ('repository.error.filenotfound'), 'back'=>_url ('repository|file|')));
			}
		} else {
			return new CopixActionReturn (CopixActionReturn::REDIRECT, CopixUrl::get ('repository|file|'));
		}
	}

}
