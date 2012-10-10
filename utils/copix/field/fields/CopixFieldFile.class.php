<?php
/**
 * @package		copix
 * @subpackage	forms
 * @author		Salleyron Julien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @experimental
 */

/**
 * Représente un champ de saisi pour un fichier
 * @package copix
 * @subpackage forms 
 */
class CopixFieldFile extends CopixAbstractField implements ICopixField {
	/**
	 * Retour du HTML pour le champ de formulaire de type File
	 *
	 * @param string $pName  le nom du champ dans le formulaire
	 * @param string $pValue le chemin actuel du fichier 
	 * @param string $pMode edit pour la modification, view pour l'affichage du contenu (view par défaut)
	 * 
	 * @return string
	 */
	public function getHTML ($pName, $pValue, $pMode = 'edit') {
		if ($pMode == 'edit') {
			return $this->getHTMLFieldEdit ($pName, $pValue);
		}
		return $this->getHTMLFieldView ($pName, $pValue);
	}

	/**
	 * Retourne le HTML de l'éditeur de fichier dans un contexte de modification
	 * 
	 * @param string $pName  le nom du champ dans le formulaire
	 * @param string $pValue le chemin actuel du fichier 
	 * @return string
	 */
	public function getHTMLFieldEdit ($pName, $pValue) {
		return '<input type="file" name="'.$pName.'" id="'.$pName.'" />';
	}

	/**
	 * Retourne le HTML de l'éditeur dans un contexte de visualisation
	 *
	 * @param string $pName  le nom du champ dans le formulaire
	 * @param string $pValue le chemin actuel du fichier 
	 * @return string
	 */
	public function getHTMLFieldView ($pName, $pValue) {
		return $pValue;
	}

	/**
	 * Mise à jour de la valeur du champ depuis la requête
	 *
	 * @param string $pName    nom du champ
	 * @param string $pDefault la valeur par défaut si jamais le champ n'est pas trouvé
	 * 
	 * @return CopixUploadedFile
	 */
	public function fillFromRequest ($pName, $pDefault = null, $pValue = null) {
		return CopixRequest::getFile ($pName);
	}
}
?>