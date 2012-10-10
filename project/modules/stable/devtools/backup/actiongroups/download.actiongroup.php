<?php
/**
 * Effectue une sauvegarde
 */
class ActionGroupDownload extends CopixActionGroup {
	/**
	 * Appelée avant toute action
	 *
	 * @param string $pAction Nom de l'action
	 */
	protected function _beforeAction ($pAction) {
		_currentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Effectue un backup
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		CopixRequest::assert ('id');
		$id = _request ('id');
		if (!file_exists (COPIX_TEMP_PATH . 'backup/downloads/' . $id . '.zip')) {
			throw new BackupException ('Le fichier de sauvegarde "' . $id . '" n\'existe pas.');
		}
		
		return _arFile (COPIX_TEMP_PATH . 'backup/downloads/' . $id . '.zip');
	}
}