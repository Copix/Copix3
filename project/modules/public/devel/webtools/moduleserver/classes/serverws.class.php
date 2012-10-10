<?php


class serverWS {
	
	/**
	 * Récupère les données d'un module
	 *
	 * @param string $pModuleName
	 * @return string Contenu du module
	 */
	public function getModuleData ($pModuleName) {
		return base64_encode(CopixFile::read(COPIX_TEMP_PATH.'/test.tar.gz'));
	}
	
	
}

?>