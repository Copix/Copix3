<?php
/**
 *
 */
class ActionGroupAjax extends CopixActionGroup {
	
	const MAX_FILE_SIZE = 10485760; //10*1024*1024
	/**
     *
     * @return <type>
     */
	public function processUpload (){
		$id_session = _request ('id_session');
 		$result = array();
 		$ppo = new CopixPPO();
 		
 		$criteres = _daoSP()->addCondition ("id_session", "=", $id_session);
 		$results = DAOcms_uploader_sessions::instance ()->findBy ($criteres)->fetchAll ();
 		$id_file = 0;
 		if (!empty ($results)) {
 			$resultat = $results[0];
 			if ($resultat->state_session == 'rightsToRead') {
				if (($cufFile = CopixUploadedFile::get ('fileupload')) !== false) {
                    $retour = false;
                    /*if ($cufFile->getSize() > self::MAX_FILE_SIZE) {
						$retour = 'Fichier envoyé trop volumineux !';
					}*/

                    // Upload ok
					if (!$retour) {
						CopixFile::createDir($resultat->path_session);
                        $aPathinfo = pathinfo($cufFile->getName());
                        $sFilename = CopixUrl::escapeSpecialChars($aPathinfo['filename'], true);
                        $sFilename = _filter ('LowerCase')->get ($sFilename);
                        if($aPathinfo['extension']) {
                            $sFilename .= '.'.$aPathinfo['extension'];
                        }
                        $cufFile->move($resultat->path_session, $id_session.$sFilename);
						$retour = 'Fichier envoyé ';
						$daoFile = DAORecordcms_uploader_files::create ();
						$daoFile->id_session = $id_session;
						$daoFile->name_file = $sFilename;
						$daoFile->create_file = date('YmdHis');
						
						DAOcms_uploader_files::instance ()->insert ($daoFile);
						$id_file = $daoFile->id_file;
					}
				}
				else {
					$retour = 'Missing file or internal error!';
				}
				
				if (($zone = _request('zone', null)) != null) {
                    $aParams = array(
                        'fileId' => $id_file,
                        'filename' => ($cufFile) ? $cufFile->getName() : ''
                    );
					$ppo->MAIN = CopixZone::process($zone, $aParams);
				}
				else {
					$result['result'] = $retour ? 'failed' : '';
					$result['error'] = $retour;
					$ppo->MAIN = json_encode($result); 
				}				
	 		}
 		}
		return _arDirectPPO($ppo, 'generictools|blank.tpl');
	}

	/**
     *
     * @return <type>
     */
	public function processRemoveFile (){
		$id_session = _request('id');
		$file = _request('file');
		
		$criteres = _daoSp ()->addCondition ('id_session', '=', $id_session)
							->addCondition ('file', 'LIKE', $id_session.$file['name']);
		DAOcms_uploader_files::instance ()->deleteBy ($criteres);

		return _arNone();
	}
}