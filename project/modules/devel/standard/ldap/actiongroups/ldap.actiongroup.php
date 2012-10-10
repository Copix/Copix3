<?php
/**
 * @package 	standard
 * @subpackage 	ldap
 * @author		Brice Favre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Actions de configuration des profils LDAP.
 * @package standard
 * @subpackage ldap
 */
class ActionGroupLdap extends CopixActionGroup {
	/**
	 * Vérifie que l'on est bien administrateur
	 */
	public function beforeAction ($actionName){
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}

	/**
	 * Fonction par défaut : Redirige vers l'affichage du formulaire d'édition
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault (){
		return $this->processConfigurationForm ();
	}

	/**
	 * Validation du formulaire
	 */
	public function processValidForm() {
		$arConnections = array ();
		$arKeyConnections = array_keys($this->_getConnections());
		$arKeyConnections[] = "";

		foreach ($arKeyConnections as $key => $i) {
			try {
				CopixRequest::assert ('profilName'.$i,'dnName'.$i, 'hostName'.$i);
				$profilName = str_replace(' ', '_', CopixRequest::getAlphaNum('profilName'.$i));
				if ($profilName != "" and 'dnName' != '' and 'hostName' !=''){
					$arConnections[$profilName] = array (
					'profilName'	=> $profilName,
					'dnName'    	=> _request ('dnName'.$i),
					'hostName'  	=> _request ('hostName'.$i),
					'portNumber'  	=> _request ('portNumber'.$i),
					'userName'  	=> _request ('userName'.$i),
					'password'  	=> _request ('password'.$i),
					'shared'    	=> _request ('shared'.$i),
					'default'   	=> (_request ('defaultRadio') == 'default'.$i));

				}
			}
			catch (Exception $e){
				// var_dump($e);
				break;
			}
		}
			
		CopixSession::set ('admin|ldap|configure', $arConnections);
		$default_ldap =  (CopixRequest::get('defaultRadio')=='nodefault') ? 'nodefault' : substr(CopixRequest::get('defaultRadio'),7);
		CopixSession::set ('admin|ldap|default', $default_ldap);
		$result = $this->_testConnections ();

		if ((CopixRequest::get ('btn') == _i18n ('ldap.configure.save')) && ($result && count (CopixSession::get('admin|ldap|configure')) >= 1))
		{
			if (_ioClass ('LdapConfigurationFile')->write (CopixSession::get('admin|ldap|configure'), CopixRequest::get('defaultRadio')))
			{
				CopixSession::set ('admin|ldap|configure',null);
				CopixSession::set ('admin|ldap|default', null);
				return _arRedirect (_url ('ldap|ldap|'));
			}
		}
		if (count (CopixSession::get('admin|ldap|configure'))==1) {
			return _arRedirect (_url ('ldap|ldap|ConfigurationForm', array('valid'=>($result && count (CopixSession::get('admin|ldap|configure')) >= 1), 'forcedefault'=>true)));
		}else{
			return _arRedirect (_url ('ldap|ldap|ConfigurationForm',array('valid'=>($result && count (CopixSession::get('admin|ldap|configure')) >= 1))));
		}
	}

	private function _getConnections (){
		if ((CopixSession::get ('admin|ldap|configure')) === null){
			CopixSession::set ('admin|ldap|configure', _ioClass ('LdapConfigurationFile')->getConnections ());
		}
		return CopixSession::get ('admin|ldap|configure');
	}

	/**
	 * Affichage du formulaire de configuration des connections LDAP
	 *
	 * @return CopixActionReturn
	 */
	public function processConfigurationForm (){

		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('ldap.configure.title');

		$ppo->connections = $this->_getConnections ();

		$currentDefault = CopixLdapFactory::getDefaultConnectionName();

		if (CopixSession::get('admin|ldap|default') !== null) {
			$currentDefault = CopixSession::get('admin|ldap|default');
		}


		if (CopixRequest::get ('forcedefault') != null) {
			//$ppo->connections[0]['default']=true;
			list ($currentDefault) = array_keys($ppo->connections);
			$ppo->connections[$currentDefault]['default']=true;
		}

		//Les connexions en dur
		$ppo->nodefault = ($currentDefault == 'nodefault');
		$ppo->configurationFileIsWritable = _ioClass ('ldapconfigurationfile')->isWritable ();
		$ppo->configurationFilePath = _ioClass ('ldapconfigurationfile')->getPath ();
		$ppo->valid = false;

		if (CopixRequest::get ('valid')) {
			$ppo->valid = true;
		}
		return _arPPO ($ppo, 'ldap.form.php');
	}

	/**
	 * Récupération des connexions
	 *
	 * @return array liste des connections
	 */
	private function _getConnections (){
		if ((CopixSession::get ('admin|ldap|configure')) === null){
			CopixSession::set ('admin|ldap|configure', _ioClass ('LdapConfigurationFile')->getConnections ());
		}
		return CopixSession::get ('admin|ldap|configure');
	}

	/**
	 * Marque les tests de connection pour les connections configurées en session
	 *
	 * @return boolean Vrai si les connections sont toutes accessibles
	 */
	private function _testConnections (){
		$toReturn = true;
		$arConnections = $this->_getConnections();

		foreach ($arConnections as $position=>$connection){
			try {
				$profile = new CopixLdapProfil ($connection['dnName'],
				$connection['hostName'],
				$connection['userName'],
				$connection['password'],
				$connection['shared']
				);
				$ldapConnexion = new CopixLdapConnection();

				if ($ldapConnexion->connect ($profile) === false)
				{
					$arConnections[$position]['available'] = false;
					//$arConnections[$position]['errorNotAvailable'] = _etag ('i18n', 'ldap.error.host');
					$toReturn = false;
				}
				elseif ($ldapConnexion->get ($connection['dnName']) === null)
				{
					$arConnections[$position]['available'] = false;
					//$arConnections[$position]['errorNotAvailable'] = _etag ('i18n', 'ldap.error.dn');
					$toReturn = false;
				}
				else
				{
					$arConnections[$position]['available'] = true;
					$arConnections[$position]['errorNotAvailable'] = ($result === true ? '' : $result);
				}
			}catch (CopixException $e){
				$toReturn = false;
				$arConnections[$position]['available'] = false;
				$arConnections[$position]['errorNotAvailable'] = $e->getMessage ();
			}
		}
		// var_dump($arConnections);
		CopixSession::set ('admin|ldap|configure', $arConnections);
		return $toReturn;
	}
}