<?php
/**
 * @package copix
 * @subpackage i18n
 * @author Duboeuf Damien
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Permet de gérer des contenus différents en fonction des langues / pays
 * Depuis la base de donnée
 * @package copix
 * @subpackage i18ndb
 */
class I18NDBHandler implements ICopixI18N {
	
	const DEFAULT_LANG = 'en';
	
	/** Retourne le message conrespondant à la clef, pour la langue $pLocale, ou la langue courante, ou la langue par défaut
	 * 
	 * @param string $pKey Clef
	 * @param mixed String ou array, paramètre(s) %s à remplacer dans le message
	 * @param string $pLocale Force à retourne ce couple langue_PAYS
	 * @return string
	 */
	public function get ($pKey, $pArgs = null, $pLocale = null) {
		
		CopixI18N::getLangCountryByLocale ($pLocale, $lang, $country);
		
		// recupère le context
		if (strpos ($pKey, '|')) {
			$context = explode ($pKey, '|');
			$context = $context[0];
		} else {
			$context = CopixContext::get();
		}
		
		$dao = array ();
		if ($country) {
			$sp = _daoSP()->addCondition ('key', '=', $pKey)
			              ->addCondition ('context', '=', $context)
			              ->addCondition ('lang', '=', $lang)
			              ->addCondition ('country', '=', $country);
			$dao = _dao ('i18ndb')->findBy ($sp);
		}
		
		if (count ($dao) == 0) {
			$sp = _daoSP()->addCondition ('key', '=', $pKey)
			              ->addCondition ('context', '=', $context)
			              ->addCondition ('lang', '=', $lang)
			              ->addCondition ('country', '=', NULL);
			
			if (count ($dao = _dao ('i18ndb')->findBy ($sp)) == 0 ) {
				$sp = _daoSP()->addCondition ('key', '=', $pKey)
				              ->addCondition ('context', '=', $context)
				              ->addCondition ('lang', '=', self::DEFAULT_LANG)
				              ->addCondition ('country', '=', NULL);
				$dao = _dao ('i18ndb')->findBy ($sp);
			}
		}
		
		if (count ($dao) == 0) {
			return NULL;
		}
		return $dao[0]->value;
	}
	
	/**
	 * Indique si la clef $pKey existe
	 * 
	 * @param string $pKey Clef
	 * @param string $pLocale Couple langue_PAYS dont on veut vérifier l'existance, null pour le couple courant
	 * @return bool
	 */
	public function exists ($pKey, $pLocale = null) {
		
		return $this->get ($pKey, array(), $pLocale = null) !== NULL;
	}
	
	
	
}