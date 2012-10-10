<?php
/**
 * @package standard
 * @subpackage admin 
 * @author		Landry Benguigui
 * @copyright	CopixTeam
 * @link			http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Gestion des profils de cache
 * @package standard
 * @subpackage admin 
 */
class ActionGroupcache extends CopixActionGroup {
	/**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction (){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Page par défaut
	 */
   	public function processDefault(){
   		return $this->processShow ();
   	}
		
	/**
	 * Ecran d'accueil pour l'administration des caches
	 */
	public function processAdmin (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('cache.admin');
		$ppo->arRegistered = CopixConfig::instance ()->copixcache_getRegistered ();	
		//var_dump(CopixConfig::instance ()->copixcache_getRegistered ()); exit;
		return _arPpo ($ppo, 'cache.admin.tpl');
	}
	
	/**
	 * Modification d'un profil de cache
	 */
	public function processEdit (){
		$type = _request ('type', null);
		if ($type !== null){
			if (!in_array ($type, CopixConfig::instance ()->copixcache_getRegistered ())){
				return _arRedirect (_url ('admin||'));
			}
			CopixSession::set ('admin|cache|edit', CopixConfig::instance ()->copixcache_getType ($type));
		}
		
		$type = CopixSession::get ('admin|cache|edit');
		
		$ppo = new CopixPpo ();
		$ppo->TITLE_PAGE = _i18n ('cache.update');
		$ppo->cache = CopixSession::get ('admin|cache|edit');
		//var_dump(CopixSession::get ('admin|cache|edit')); exit;
		$ppo->arStrategies = Copixcache::getStrategies ();
		
		//Liens déjà établi
		if($ppo->cache['link'] != ""){
			$ppo->asLinked = explode("|", $ppo->cache['link']);
		}
		$link = CopixConfig::instance ()->copixcache_getRegistered ();
		$finalLink = array();
		foreach ($link as $lien){
			if( !($lien == $ppo->cache['name'] || ( isset($ppo->asLinked) && in_array($lien, $ppo->asLinked))) ){
				$finalLink[$lien] = $lien;
			}
		}
		$ppo->arLink = $finalLink;
		
		return _arPpo ($ppo, 'cache.update.tpl');
	}
	
	/**
	 * Création d'un profil de cache
	 */
	public function processCreate (){
		$type = _request ('type', null, true);
		if ($type === null ) {
			return CopixActionGroup::process ('genericTools|Messages::getError',array ('message'=>CopixI18N::get ('cache.error.noname'), 'back'=>_url('admin|cache|admin')));
		}
		//On utilise les fonctions de CopixConfig pour être sur d'avoir un profil complètement initialisé
		CopixConfig::instance ()->copixcache_registerType (array ('name'=>$type, 
				'enabled'=>false));
		$type = CopixConfig::instance ()->copixcache_getType ($type);
		CopixSession::set ('admin|cache|edit', $type);
		
		return _arRedirect (_url ('cache|edit'));
	}
	
	public function processRemoveLink(){
		if ($linkToRemove = _request ('linkToRemove')){
			if ($type = CopixSession::get ('admin|cache|edit')){
				$tabLink = explode("|", $type['link']);				
				unset($tabLink[array_search($linkToRemove, $tabLink)]);
				$type['link'] = implode("|", $tabLink);
				
				CopixSession::set ('admin|cache|edit', $type);
			}
		}
		return _arRedirect (_url ('cache|edit'));
	}
	
	/**
	 * Validation des modification sur le profil de cache
	 */
	public function processValid (){
		$type = CopixSession::get ('admin|cache|edit');
		if (_request ('enabled')){
			$type['enabled'] = true;
		}
		
		$type['strategy'] = _request ('strategy');
		if (_request ('strategy_class', null, true)){
			$type['strategy'] = _request ('strategy_class'); 			
		}
		if ($dir = _request ('dir')){	
			$type['dir'] = $dir;
		}
		$type['duration'] = _request ('duration');
		if ($link = _request ('link')){
			$type['link'] .= ($type['link'] == "") ? $link:"|".$link ;		
		}
		
		CopixSession::set ('admin|cache|edit', $type);
		
		if (_request ('save')){
			$types = CopixConfig::instance ()->copixcache_getRegisteredProfiles ();
			$types[$type['name']] = $type;
			_class ('cacheConfigurationFile')->write ($types);
			CopixSession::set ('admin|cache|edit', null);
			return _arRedirect (_url ('cache|admin'));
		}else{
			return _arRedirect (_url ('cache|edit'));
		}
	}
	
	/**
	 * Supression d'un cache
	 */
	public function processDeleteType (){
		$type = _request ('type');
		if (CopixRequest::getInt ('confirm') == 1){
			if(Copixcache::exists($type)){
				Copixcache::clear ($type);
			}
			$types = CopixConfig::instance ()->copixcache_getRegisteredProfiles ();
			unset ($types[$type]);
			_class ('cacheConfigurationFile')->write ($types);
			return _arRedirect (_url ('cache|admin'));
		}else{
			if (!in_array ($type, CopixConfig::instance ()->copixcache_getRegistered ())){
				return _arRedirect (_url ('cache|admin'));
			}
			return CopixActionGroup::process ('generictools|Messages::getConfirm', 
				array ('message'=>_i18n ('cache.delete', $type),
						'confirm'=>_url ('admin|cache|deleteType', array ('type'=>$type, 'confirm'=>1)),
						'cancel'=>_url ('admin|cache|admin')));
		}
	}
}
?>