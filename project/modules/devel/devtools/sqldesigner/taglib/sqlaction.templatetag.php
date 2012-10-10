<?php
/**
 * @package sqldesigner
 * @subpackage taglib
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Affiche un icone pour effectuer une action sur WWW Sql Designer
 * 
 * @package sqldesigner
 * @subpackage taglib
 */
class TemplateTagSQLAction extends CopixTemplateTag {
	/**
	 * Affiche un icone pour effectuer une action sur WWW Sql Designer
	 * 
	 * @param mixed $pParams Paramètres
	 * @param string $pContent Contenu (???)
	 * @return string
	 */
	public function process ($pParams) {
		// récupération des paramètres
		$icon = $this->requireParam ('icon');
		if (!file_exists (_resourcePath ($icon))) {
			$this->_reportErrors (array ('invalid' => array ('icon' => true)));
		}
		$iconURL = _resource ($icon);
		$idElement = $this->requireParam ('idElement');
		if ($this->getParam ('caption') !== null) {
			$caption = $this->getParam ('caption');
		} else {
			$captioni18n = $this->requireParam ('captioni18n');
			$this->validateParams ();
			$caption = (_i18n ($captioni18n));
		}
		$this->validateParams ();
		
		// génération de l'HTML
		$toReturn = '<img src="' . $iconURL . '" ';
		$toReturn .= 'style="cursor:pointer" ';
		$toReturn .= 'onclick="javascript: window.frames[\'sqldesigner\'].document.getElementById (\'' . $idElement . '\').click ();" ';
		$toReturn .= 'alt="' . $caption . '" ';
		$toReturn .= 'title="' . $caption . '" ';
		$toReturn .= '/>';
		
		return $toReturn;
	}
}