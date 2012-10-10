<?php
/**
 * @package cms3
 * @subpackage cms_editor
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */
interface ICmsWysiwygParser {
	/**
	 * Transforme le texte en parsant et modifiant ce que le parseur veut changer
	 * 
	 * @param string $pText Texte de base, parsé par les parseurs précédents
	 * @return string
	 */
	public function transform ($pText);
}