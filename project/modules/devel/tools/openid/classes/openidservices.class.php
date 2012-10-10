<?php

define ('OPEND_ID_TEMP_PATH', COPIX_TEMP_PATH . 'openid/');

//

/**
 * 
 * Classes de services pour la Bibliothèque OpenId
 * @author sylvain
 *
 */
class OpenIdServices{	
	
	const GOOGLE_IDENTIFIER = 1;
	
	const YAHOO_IDENTIFIER = 2;
	
	const FACEBOOK_IDENTIFIER = 3;
	
	/**
	 * 
	 * Retourne une url de connexion avec les paramètres de retour, sur un serveur openID
	 * @param String $pIdentifier identifiant du serveur
	 * @param String $pRealm domaine qui sera affiché sur la page de connexion du serveur openId
	 * @param String $pReturnTo page de retour 
	 */
	public function getUrl ($pIdentifier, $pRealm, $pReturnTo){
		$openid = new LightOpenID($pRealm);
		$openid->identity = $pIdentifier;
		$openid->returnUrl = $pReturnTo;
		$openid->required = array('contact/email');
		$openid->optional = array('namePerson', 'namePerson/friendly');
		return $openid->authUrl();
	}
	
	/**
	 * 
	 * Retourne une url de connexion avec les paramètres de retour, sur un serveur openID par son nom
	 * @param String $pIdentifier identifiant du serveur
	 * @param String $pRealm domaine qui sera affiché sur la page de connexion du serveur openId
	 * @param String $pReturnTo page de retour 
	 */
	public function getUrlByIdentifierName ($pIdentifierName, $pRealm, $pReturnTo){
		$results = _dao('openid_identifier')->findBy(_daoSP()->addCondition('caption_identifier', '=', $pIdentifierName))->fetchAll();
		if(!empty($results)){
			$identifierInfos = $results[0];
			return $this->getUrl($identifierInfos->url_identifier, $pRealm, $pReturnTo);
		}
		return null;
	}
	
	public function getOpenIdReturnInfos($pRealm){
		$openid = new LightOpenID($pRealm);
		$validate = $openid->validate();
		return $validate ? $openid : null;
	}
	
	/**
	 * 
	 * Retourne l'identifiant unique du client OpenId 
	 * @param String $pReturnTo
	 */
	public function getConsumerId ($pRealm){
		$infos = $this->getOpenIdReturnInfos($pRealm);
		return $infos ? $infos->identity : null;
	}
	

	
	/**
	 * 
	 * Renvoi vrai si un openId existe déjà pour un utilisateur
	 * @param String $pOpenId
	 */
	public function openIdExists ($pOpenId){
		$results = _dao("openid")->findBy(_daoSP()->addCondition('open_id', '=', $pOpenId))->fetchAll();
		return !empty($results);
	}
	
	public function getUserId ($pOpenId){
		$results = _dao("openid")->findBy(_daoSP()->addCondition('open_id', '=', $pOpenId))->fetchAll();
		return empty($results) ? null : $results[0]->user_id;
	}
	
	/**
	 * 
	 * Enregistre un couple openId/userId avec l'handler correspondant et l'id de l'identifier (google, yahoo, etc...)
	 * @param String $pOpenId
	 * @param String $pUserId
	 * @param String $pUserHandler
	 * @param String $pIdentifierId
	 */
	public function storeOpenId ($pOpenId, $pUserId, $pUserHandler, $pIdentifierId){
		$record = _record("openid");
		$record->open_id = $pOpenId;
		$record->user_id = $pUserId;
		$record->user_handler = $pUserHandler;
		$record->identifier_id = $pIdentifierId;
		_dao("openid")->insert($record);
	}
	
	/**
	 * 
	 * Vérifier qu'un utilisateur existe pour un couple openId/userId et un handler donné
	 * @param String $pOpenId
	 * @param String $pUserId
	 * @param String $pUserHandler
	 */
	public function userExists ($pOpenId, $pUserId, $pUserHandler){
		$results = _dao("openid")->findBy(_daoSP()->addCondition('open_id', '=', $pOpenId)
						->addCondition('user_id', '=', $pUserId)
						->addCondition('user_handler', '=', $pUserHandler))->fetchAll();
		return !empty($results);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $pUrl
	 */
	public function getIdentifierIdByUrl ($pUrl){
		$results = _dao('openid_identifier')->findBy(_daoSP()->addCondition('url_identifier', '=', $pUrl))->fetchAll();
		return !empty($results) ? $results[0] : null;
	}
	
	/**
	 * 
	 * Retourne un couple openid/userid par identifiant de couple
	 * @param int $pId
	 */
	public function getOpenIdById ($pId, $pUserHandler = false){
		$sp = _daoSP()->addCondition('id', '=', $pId);
		if ($pUserHandler){
			$sp->addCondition('user_handler', '=', $pUserHandler);
		}
		$results = _dao("openid")->findBy($sp)->fetchAll();
		return empty($results) ? null : $results[0];
	}
	
	/**
	 * 
	 * Suppression couple openId/login
	 * @param int $pId
	 */
	public function deleteOpenIdById($pId){
		return _dao("openid")->delete($pId);
	}
	
	/**
	 * 
	 * Retourne la liste des couples openid/login pour un login donné
	 * @param $pLogin
	 */
	public function getUserOpenIdList($pUserId, $pUserHandler = false){
		$sp = _daoSP()->addCondition('user_id', '=', $pUserId);
		if ($pUserHandler){
			$sp->addCondition('user_handler', '=', $pUserHandler);
		}
		return _dao("openid")->findBy($sp);
	}
	
} 