<?php
/**
 * Gestion des templates
 */
class TemplateServices {
	/**
	 * Charge un fichier XML de définition de templates
	 *
	 * @param string $pXmlPath Chemin du fichier XML
	 * @return SimpleXML
	 */
	private function _loadXML ($pXmlPath) {
		if (!is_readable ($pXmlPath)) {
			throw new CopixException ('Fichier xml des templates du cms "' . $pXmlPath . '" introuvable');
		}
		if (!($xml = simplexml_load_file ($pXmlPath))) {
			throw new CopixException ('Impossible de charger le fichier xml "' . $pXmlPath . '".');
		}
		return $xml;
	}

	/**
	 * Retourne le nombre de templates dans le fichier xml
	 *
	 * @param string $pXmlPath Chemin du fichier XML
	 * @return int
	 */
    public function getTemplateNb ($pXmlPath) {
        return sizeof ($this->_loadXML ($pXmlPath));
    }

	/**
	 * Retourne les templates définits dans un fichier XML
	 *
	 * @param string $pXmlPath Chemin du fichier XML
	 * @return array
	 */
	public function getTemplates ($pXmlPath) {
		$xml = $this->_loadXML ($pXmlPath);
		$toReturn = array ();
		foreach ($xml->children () as $node) {
			$template = new stdClass ();
			$template->name = (string)$node->name;
			$template->tpl = (string)$node->tpl;
			$template->image = (string)$node->image;
			$template->description = (string)$node->description;
			$template->editionMode = (string)$node->editionMode;
			$template->options = $node->options;			
			$toReturn[] = $template;
		}
		return $toReturn;
	}

	/**
	 * Retourne les informations sur un template particulier
	 *
	 * @param string $pXmlPath Chemin du fichier XML
	 * @param string $pTPL Nom du template
	 * @return stdClass
	 */
	public function getInfos ($pXmlPath, $pTPL) {
		foreach ($this->getTemplates ($pXmlPath) as $template) {
			if ($template->tpl == $pTPL) {
				return $template;
			}
		}
		throw new CopixException ('Le template "' . $pTPL . '" du fichier XML "' . $pXmlPath . '" n\'existe pas.');
	}
}