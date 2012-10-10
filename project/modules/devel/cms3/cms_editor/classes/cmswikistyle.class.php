<?php

class CmsWikiStyle {
	/**
     * Renvoi la liste de tous les styles qui ont été définis pour l'éditeur
     * @return mixte
     */
	public function getList () {
        if (!$sXmlPath = CopixTpl::getFilePath('cms_editor|cmswikistyle.xml')){
            return null;
        }
        if (!($aList = simplexml_load_file ($sXmlPath))) {
			throw new CopixException ('Impossible de charger le fichier xml');
		}
		return $aList;
	}	
}
?>