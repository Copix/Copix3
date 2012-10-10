<?php
/**
 * Affiche un choix vers le formulaire classique
 */
class ZoneChooseflashForm extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $toReturn HTML à retourner
	 * @return boolean
	 */
	public function _createContent (&$toReturn) {
		$message = 'Vous utilisez actuellement le formulaire "classique", qui ne permet l\'envoi que d\'un seul fichier à la fois, mais gère le proxy.';
		$message .= '<br />Si vous n\'utilisez pas de proxy et que vous voulez envoyer plusieurs fichiers en même temps, vous pouvez utiliser le formulaire Flash.';
		$message .= '<br /><br /><center>' . _tag ('button', array ('img' => 'uploader|img/form_flash.gif', 'caption' => 'Utiliser le formulaire Flash', 'url' => $this->getParam ('url'))) . '</center>';
		$toReturn = _tag ('notification', array ('title' => 'Formulaire classique', 'message' => $message));
		return true;
	}
}