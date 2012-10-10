<?php
class CnUserHandler implements ICopixUserHandler {
    // Fonction de connexion
    public function login ($pParams){		
		$login = $pParams['login'];
		$password = $pParams['password'];
		
		$sp = _daoSp ()->addCondition ('login', '=', $login)->addCondition ('password', '=', $password);
		$results = _ioDao('cn_user')->findBy ($sp);
		
		if (count ($results) >= 1) {
			return new CopixUserLogResponse (true, 'communet_final|cnuserhandler', $results[0]->id, $results[0]->login);
		}
		return new CopixUserLogResponse (false, null, null, null);
    }

    // Fonction de déconnexion
    public function logout ($pParams){
		return new CopixUserLogResponse (false, null, null, null);
    }

    // Chercher un utilisateur
    public function find ($pParams = array()){
		$login = $pParams['login'];
		$sp = _daoSp ()->addCondition ('login', '=', $login);
		$results = _ioDao('cn_user')->findBy ($sp);
		return $results;
    }

    // Récupérer les informations d'un utilisateur
    public function getInformations ($pUserId){
		_ioDao('cn_user')->get ($pUserId);
    }
}
?>