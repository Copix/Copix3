<?php
/**
* @package		standard
* @subpackage	plugin_i18n
* @author 		Croes Gérald, Salleyron Julien
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 * Plugin qui détecte la langue de l'utilisateur et qui permet 
 * également d'en changer
 * 
 * @package		standard
 * @subpackage	plugin_i18n
 */
class PluginI18n extends CopixPlugin {
	
	/**
	 * La langue de l'utilisateur
	 * @var string
	 */
	private $_lang = null;
	
	/**
	 * Le pays de l'utilisateur
	 * @var string
	 */
	private $_country = null;
	
	/**
    * Traitements à faire avant execution du controller
    */
	public function beforeProcess (& $pExecParams){
		//Si la langue n'a pas encore été détectée, on tente l'autodétection
		if (!$this->_alreadyDefinied ()){
		    //Demande de l'autodétection de la langue
		    $this->_autoDetect ();
		}
		
		//si l'on autorise la sélection utilisateur
		if ($this->config->enableUserLanguageChoosen){
			$this->_detectUserSelection ();
		}

		CopixI18N::setLang ($this->_lang);
		CopixI18N::setCountry ($this->_country);
	}

	/**
	 * Détecte les langues et country par défaut du navigateur
	 *
	 */
	private function _autoDetect() {
	    if ($this->config->useDefaultLanguageBrowser) {
		    $language = $this->_getBrowserLanguage();
		    $this->_accept ($language['lang'], $language['country']);
	    }
	}
	
	/**
	 * Destruction de l'objet, on met à jour la session avec les langues retenues
	 */
	public function __destruct (){
		CopixSession::set ('plugin|i18n|lang', $this->_lang);
		CopixSession::set ('plugin|i18n|country', $this->_country);
	}

	/**
	 * Initialisation de la langue avec les éléments par défaut
	 */
	public function __construct ($config){
		parent::__construct ($config);
		$this->_lang = CopixI18N::getLang ();
		$this->_country = CopixI18N::getCountry ();		
	}
	
	/**
	 * On regarde s'il existe des informations de sélection en session
	 * 
	 * Si c'est le cas, on met à jour les variables d'état du plugin
	 * @return bool
	 */
	private function _alreadyDefinied (){
		$lang = null;
		$country = null;
		
		if (CopixSession::get ('plugin|i18n|lang') !== null){
			$this->_lang = CopixSession::get ('plugin|i18n|lang');
		}
		if (CopixSession::get ('plugin|i18n|country') !== null){
			$this->_country = CopixSession::get ('plugin|i18n|country');
		}

		return $this->_lang || $this->_country;
	} 

	/**
	 * Recherche le language du navigateur
	 *
	 * @return stdClass contenant les données de language
	 */
	private function _getBrowserLanguage() {
		$browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach ($browser_languages as $bl){
			if(preg_match("/^([a-zA-Z]{2})([-_][a-zA-Z]{2})?(;q=[0-9]\\.[0-9])?$/",$bl,$match)){
				// pour les user-agents qui livrent un code internationnal
				if($data=$this->_getLanguageData($match[0],$match[1])){
					return $data;
				}
			}elseif(preg_match("/^([a-zA-Z ]+)(;q=[0-9]\\.[0-9])?$/",$bl,$match)){
				// pour les user agent qui indique le nom en entier
				if($data=$this->_getLanguageData($match[1],'',false)){
					return $data;
				}
			}
		}
		return false;
	}


	/**
    * Recupere les données de la langue à partir du code international
    */
	private function _getLanguageData($code, $code2='', $direct=true){
		$code  = strtolower(str_replace('-','_',$code));
		$code2 = strtolower($code2);

		//include(realpath(dirname(__FILE__).'/../config/i18n.plugin.datas.php'));
		include('i18n.plugin.datas.php');
		if(!$direct){
			if(isset($i18n_alternate_languages_code[$code])){
				$code=$i18n_alternate_languages_code[$code];
			}else{
				return false;
			}
		}

		$l=null;
		if(isset ($i18n_languages[$code])){
			$l= $i18n_languages[$code];
		}elseif ($code2 !='' && isset($i18n_languages[$code2])){
			$l= $i18n_languages[$code];
		}

		if($l !== null){
		    return array('code'=>$code,
			'lang'=>$l[0],
			'country'=>$l[1],
			'name'=>$l[2],
			'default_currency' => $l[3]
			);
		}else{
		    return false;
		}
	}

	/**
	 * Détecte si l'utilisateur a défini une lang et un country
	 */
	private function _detectUserSelection() {
	    $lang=null;
	    $country=null;
	    if (CopixRequest::get('lang') != null){
	    	 $lang    = CopixRequest::get('lang');
	    }
	    if (CopixRequest::get('country') != null){
	    	 $country = CopixRequest::get('country');
	    }
	    if (($lang || $country) && $this->_accept ($lang, $country)){
	    	$this->_lang = $lang;
	    	$this->_country = $country;
	    }
	}
	
	/**
	 * Regarde si la lang et le country correspondent a qq chose existant
	 */
	private function _accept ($lang, $country=null) {
		return true;
	}
}
?>