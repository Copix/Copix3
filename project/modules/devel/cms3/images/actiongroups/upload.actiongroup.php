<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Gestion de l'upload des images  
 * 
 * @package cms
 * @subpackage images
 */
class ActionGroupUpload extends CopixActionGroup {

	public function processSaveFiles (){
		$id_session = _request('id_session');

		$imagesValues = _request('image');
		$path = '';

		$query = "SELECT * FROM cms_uploader_files as uf
					LEFT JOIN cms_uploader_sessions as us ON uf.id_session = us.id_session
					WHERE uf.id_session = :id_session";
		$results = _doQuery ($query, array(':id_session'=>$id_session));
		
		$selected = array();
		foreach ($results as $file){
			//on vérifie que le fichier en base n'a pas été supprimé de la page et n'a pas été envoyé.
			if (array_key_exists($file->id_file, $imagesValues)){
				$image = _ppo (DAORecordcms_images::create ());
				$image->parent_heading_public_id_hei = _request("parent_heading_public_id_hei", CopixSession::get ('heading', $id_session));
				$image->caption_hei = $imagesValues[$file->id_file]['caption_hei'];
				$image->description_hei = $imagesValues[$file->id_file]['description_hei'];
				//nom du fichier
				$image->file_image = $file->name_file;
				$image->size_image = filesize($file->path_session.$id_session.$file->name_file);
				_class ('ImageServices')->insert ($image);
				
				$filename = strtr(utf8_decode($image->caption_hei),
				utf8_decode('ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'), 
				'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
				$filename = preg_replace('/([^.a-z0-9]+)/i', '_', $filename);
				
				$image->file_image = $image->public_id_hei."_".$image->version_hei."_".str_replace(" ", "_", $filename).".".pathinfo($image->file_image, PATHINFO_EXTENSION);
				
				_class ('ImageServices')->update ($image);
				if (_request('topublish', false)){
					_ioClass('heading|headingelementinformationservices')->publishById ($image->id_image, $image->type_hei);
				}
				
				//on deplace le fichier en verifiant que le dossier existe bien	
				CopixFile::createDir(COPIX_VAR_PATH.ImageServices::IMAGE_PATH);
				if(!rename($file->path_session.$id_session.$file->name_file, COPIX_VAR_PATH.ImageServices::IMAGE_PATH.$image->file_image)){
					_log("Impossible de renommer le fichier pour l'image ".$image->caption_hei.' ('.$image->public_id_hei."), vous n'avez pas les droits.", "errors", CopixLog::ERROR);
				}
				$path = $file->path_session;
				$selected[] = $image->id_image . '|' . $image->type_hei;
			}
			//on supprime l'enregistrement en base du fichier
			DAOcms_uploader_files::instance ()->delete ($file->id_file);
		}
		//on supprime l'enregistrement en base de la session
		$criteres = _daoSP()->addCondition ('id_session', '=', $id_session);
		DAOcms_uploader_sessions::instance ()->deleteBy ($criteres);
		//on supprime le repertoire temporaire
		if($path != ''){
			CopixFile::removeDir($path);
		}
		
		return _arRedirect(_url('heading|element|finalizeEdit', array ('editId'=>$id_session, 'result'=>'saved', 'selected'=>$selected)));
	}
	
	public function processShowImage (){
		$id_file = _request ('id_file');
		$file = DAOcms_uploader_files::instance ()->get ($id_file);
		return _arFile (COPIX_TEMP_PATH.'uploader/'.$file->id_session.'/'.$file->id_session.$file->name_file);
	}
	
	/**
	 * 
	 * Retour de page aprés ajout dynamique d'image en édition de page
	 */
	public function processConfirmImageChooser(){
		$ppo = new CopixPPO();
		CopixConfig::instance()->mainTemplate = "default|popup.php";
		return _arPPO($ppo, "confirmimagechooser.php");
	}
}