<?php
/**
 * Enter description here...
 *
 */
class ZoneUploader extends CopixZone {
	
	//repertoire de stockage par dÃ©faut des fichiers uploadÃ©s
	const DEFAULT_PATH = "uploader/";
	
	public function _createContent (&$toReturn){	
		//nettoyage des vieilles sessions inutilisÃ©es
		$this->_cleanSessions (); 
		//rÃ©cuperation de l'identifiant de formulaire
		$id = $this->getParam ('id_session', uniqid());
	
		//rÃ©cupÃ©ration des paramÃ¨tres
		$authorisedExtensions = $this->getParam ('extensions');
		$authorisedExtensions = is_array($authorisedExtensions) ? implode(";", $authorisedExtensions) : $authorisedExtensions;
		$extensionsDescription = $this->getParam ('extensionsDescription', $authorisedExtensions ? "Extension (".$authorisedExtensions.")" : "Tous les fichiers");
		$zone = $this->getParam('zone');
		$action = $this->getParam('action');
		$cancel = $this->getParam('cancel');
		$parent_heading_public_id_hei = $this->getParam('parent_heading_public_id_hei');

		//mise en session en base
		$this->_saveSession($id);
		
		//preparation du template
		$tpl = new CopixTpl ();
		$tpl->assign ('authorisedExtensions', $authorisedExtensions);
		$tpl->assign ('extensionsDescription', $extensionsDescription);
		$tpl->assign ('zone', $zone);
		$tpl->assign ('id', $id);
		$tpl->assign ('action', $action);
		$tpl->assign ('cancel', $cancel);
		$tpl->assign ('parent_heading_public_id_hei', $parent_heading_public_id_hei);
				
		$toReturn = $tpl->fetch ('upload.form.php');		
		return true;
	}
	
	private function _saveSession ($pSessionId){
		$session = DAORecordcms_uploader_sessions::create ();
		$session->id_session = $pSessionId;
		$session->create_session = date ('YmdHis');
		$session->path_session = $this->getParam ('path', COPIX_TEMP_PATH.self::DEFAULT_PATH.$pSessionId."/");
		$session->state_session = 'rightsToRead';
		DAOcms_uploader_sessions::instance ()->insert ($session);
	}
	
	private function _cleanSessions (){
		$date = CopixDateTime::timeStampToyyyymmddhhiiss(mktime(0,0,0,date('m'), date('d') - 1, date('Y')));
		$criteres = _daoSP ()->addCondition ('create_session', '<', $date);
		$results = DAOcms_uploader_sessions::instance ()->findBy ($criteres);
		foreach ($results as $file){
			if($file->path_session != ''){
				CopixFile::removeDir($file->path_session);
			}
		}
				
		DAOcms_uploader_files::instance ()->deleteBy (_daoSP ()->addCondition ('create_file', '<', $date));
		DAOcms_uploader_sessions::instance ()->deleteBy ($criteres);
	}
}