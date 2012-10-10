<?php
/**
 * @package selectcomplex
 * @author  Damien Duboeuf
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions par défaut réalisées par le framework
 * @package selectcomplex
 */
class ActionGroupLanguage extends CopixActionController {
	
	protected function processChange () {
		
		$redirect = _request ('redirect', _url ());
		
		// Force la langue si autorisé et demandé
		if (CopixConfig::instance ()->i18n_use_cookie_locale && 
		    ($local = _request ('force_language')) &&
		    CopixI18N::localIsAvailable ($local)) {
			CopixI18N::getLangCountryByLocale ($local, $lang, $country);
			CopixI18N::setLang($lang);
			CopixI18N::setCountry($country, true);
		}
		
		return _arRedirect (_url ($redirect));
	}
	
}
?>