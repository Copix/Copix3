<?php
/**
 * Exception pour la gestion des groupes de commentaire
 */
class CommentsGroupsException extends CopixException {
	/**
	 * Erreur lors de la validation de l'élément
	 */
	const VALIDATOR_ERRORS = 10;

	/**
	 * Identifiant existant lors d'une tentative d'ajout
	 */
	const ID_EXISTS = 20;

	/**
	 * Constructeur
	 * 
	 * @param string $pMessage Texte de l'exception
	 * @param int $pCode Code d'erreur
	 * @param array $pExtras Informations supplémentaires
	 * @param array $pErrors Erreur(s) au format array pour les récupérer plus facilement
	 */
	public function __construct ($pMessage, $pCode = 0, $pExtras = array (), $pErrors = array ()) {
		// recherche des erreurs, soit dans $pErrors, soit dans $pMessage
		if (is_array ($pErrors)) {
			if (count ($pErrors) > 0) {
				$pExtras['errors'] = $pErrors;
			} else {
				$pExtras['errors'] = array ($pMessage);
			}
		} else if (strlen ($pErrors) > 0) {
			$pExtras['errors'] = array ($pErrors);
		} else {
			$pExtras['errors'] = array ($pMessage);
		}

		parent::__construct ($pMessage, $pCode, $pExtras);
	}

	/**
	 * Retourne les erreurs
	 * 
	 * @return array
	 */
	public function getErrors () {
		return $this->getExtra ('errors');
	}
}