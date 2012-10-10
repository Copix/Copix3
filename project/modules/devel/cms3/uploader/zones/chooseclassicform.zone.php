<?php
/**
 * Affiche un choix vers le formulaire classique
 */
class ZoneChooseClassicForm extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $toReturn HTML à retourner
	 * @return boolean
	 */
	public function _createContent (&$toReturn) {
		$message = 'Vous utilisez actuellement le formulaire "Flash", qui permet d\'envoyer plusieurs fichiers à la fois, mais ne gère pas le proxy.';
		$message .= '<br />Si vous utilisez un proxy vous devez utiliser le formulaire "classique".';
		$message .= '<br /><br /><center>' . _tag ('button', array ('img' => 'uploader|img/form_classic.png', 'caption' => 'Utiliser le formulaire classique', 'url' => $this->getParam ('url'))) . '</center>';
		$toReturn = _tag ('notification', array ('title' => 'Formulaire Flash', 'message' => $message));
		return true;
	}
}