<?php
/**
 * @package copix
 * @subpackage log
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Log affiché directement via un echo
 * 
 * @package copix
 * @subpackage log
 */
class CopixLogPageStrategy extends CopixLogAbstractStrategy {
	/**
	 * Indique si on peut lire le contenu du profil de log indiqué
	 *
	 * @param string $pProfile Nom du profil
	 * @return false
	 */
	public function isReadable ($pProfile) {
		return false;
	}
	
	/**
	 * Effectue un log
	 *
	 * @param string $pProfile Nom du profil
	 * @param string $pType Type de log
	 * @param int $pLevel Niveau de log, utiliser les constantes de CopixLog
	 * @param string $pDate Date et heure du log, format YmdHis
	 * @param string $pMessage Message à loger
	 * @param array $pExtras Informations supplémentaires
	 */
	public function log ($pProfile, $pType, $pLevel, $pDate, $pMessage, $pExtras) {
		echo '<div style="background-color: white; border: solid red 1px; padding: 5px; margin: 5px;';
		echo '-moz-box-shadow: 0px 1px 5px #000;';
		echo '-webkit-box-shadow: 0px 1px 5px #000;';
		echo 'box-shadow: 0px 1px 5px #000;';
		echo 'filter: progid:DXImageTransform.Microsoft.dropshadow(OffX=0px, OffY=1px, Color=\'#000000\');';
		echo 'border-radius: 3px;';
		echo '-moz-border-radius: 3px;';
		echo '-webkit-border-radius: 3px;';
		echo '">';
		echo '<font color="#969696">' . _i18n (
			'copix:log.page.title',
			array (
				'CopixLogPageStrategy',
				CopixLog::getLevel ($pLevel),
				$pType
			)
		) . '<br />';
		if (isset ($pExtras['file'])) {
			echo _i18n ('copix:log.page.file', $pExtras['file']);
			if (isset ($pExtras['line'])) {
				echo ' | ' . _i18n ('copix:log.page.line', $pExtras['line']);
			}
			echo '<br />';
		}
		echo '</font>';
		echo '<font color="black"><b>' . $pMessage . '</b></font>';
		echo '</div>';
	}
}