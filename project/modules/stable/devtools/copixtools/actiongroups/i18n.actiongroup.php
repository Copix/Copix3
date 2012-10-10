<?php
/**
 * @package standard
 * @subpackage admin
 * @author Gérald Croës, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Affichage des clefs i18n installées (permet de les modifier)
 * 
 * @package devtools
 * @subpackage copixtools
 */
class ActionGroupI18N extends CopixActionGroup {
	/**
	 * Vérification des droits d'administration
	 *
	 * @param string $pActionName
	 */
	public function beforeAction ($pActionName){
		CopixPage::add ()->setIsAdmin (true);
		_currentUser ()->assertCredential ('basic:admin');
	}

	public function processDefault (){
		$services = new I18NResourceServices ();

		$ppo = _ppo ();
		$ppo->locales = array ();
		$ppo->list = $services->find ($ppo->locales);

		return _arPpo ($ppo, 'lang.list.php');
	}

	public function processFile (){
		CopixRequest::assert ('file');
		
		$services = new I18NResourceServices ();
		$locales = $services->getLocales (_request ('file'));
		
		$ppo = _ppo ();
		$ppo->keys = $locales->getKeys ();
		$ppo->translations = $locales->getTranslations ();
		$ppo->locales = $locales->getLocales ();
		$ppo->file = _request ('file');
		
		return _arPpo ($ppo, 'locales.list.php');
	}
	
	public function processSave (){
		$new = _request ('new');
		$translations = _request ('translations');
		$module = _request ('file');

		if (is_array ($new) && isset ($new['name'])){
			foreach ($new as $locale=>$value){
				if ($locale === 'name'){
					continue;
				}else{
					$translations[$locale][$new['name']] = $value;
				}
			}
		}
		
		$locale = new Locales ();
		$locale->addKeys ($translations);
		
		$locale->save ($module);
		
		return _arRedirect (_url ('i18n|'));
	}
}