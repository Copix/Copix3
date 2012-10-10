<?php
/**
 * Affiche le champ de sélection d'un template
 */
class ZoneTemplateChooser extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $toReturn HTML à retourner
	 * @return boolean
	 */
	public function _createContent (&$toReturn) {
		$public_id = CopixSession::get ('heading', _request ('editId'));
		$theme = '';
		try {
			$theme = _ioClass('heading|headingElementInformationServices')->getTheme ($public_id, $foo);
		} catch (CopixException $e) {}
		$listeTemplates = _ioClass ('portal|TemplateServices')->getTemplates ($this->getParam ('xmlPath'));

		// check s'il y a ades options, on est obligé de faire le test pour tous les noeuds car c'est chargé en ajax
		$hasOptions = false;
		foreach ($listeTemplates as $template) {
			if ($template->options) {
				$hasOptions = true;
			}
		}
		
		$tpl = new CopixTpl ();
		$tpl->assign ('listeTemplates', $listeTemplates);
		$tpl->assign ('identifiant', $this->getParam ('identifiant'));
		$tpl->assign ('selected', $this->getParam ('selected'));
		$tpl->assign ('module', $this->getParam ('module'));
		$tpl->assign ('text', $this->getParam ('textBouton', 'visuel'));
		$tpl->assign ('showText', $this->getParam ('showText', true));
		$tpl->assign ('img', $this->getParam ('img', 'heading|img/general_view.png'));
		$tpl->assign ('inputId', $this->getParam ('inputId'));
		$tpl->assign ('portletId', $this->getParam ('portletId'));
		$tpl->assign ('hasOptions', $hasOptions && $this->getParam('showOptions', true));
		// TODO : trouver pourquoi ce paramètre a été supprimé (par erreur ?)
		$tpl->assign ('showSelection', $this->getParam ('showSelection', false));
		$tpl->assign ('theme', $theme);
		
		$toReturn = $tpl->fetch ('portal|templatechooser.php');
		return true;
	}
}