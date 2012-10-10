<?php
/**
 * @package		copix
 * @subpackage	validator
 * @author		Favre Brice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Validation d'un numéro de téléphone
 * @package		copix
 * @subpackage	validator
 */
class CopixValidatorPhone extends CopixAbstractValidator {
	
	/**
	 * Methode qui fait les tests sur la $pValue
	 *
	 * @param mixed $pValue La valeur
	 */
	public function _validate ($pValue) {
		// Récupération
		$telKind = $this->getParam ('kind', null);	
		try {
			CopixFormatter::getTelephone($pValue);
		} catch (CopixException $e) {
			return $e->getMessage ();
		}
		switch ($telKind) {
			case 'mobile':
				if (! preg_match ('/^06/', $pValue)) {
					return _i18n ('copix:copixformatter.error.badmobile');
				}
				break;
			case 'international':
				if (! preg_match ('/^00/', $pValue)) {
					return _i18n ('copix:copixformatter.error.badtel');
				}
				break;
		}
	}
}
?>