<?php
/**
 * @package		tutorials
 * @subpackage 	edituser
 * @author		Steevan BARBOYON
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Edition des informations de l'utilisateur connecté, accessible uniquement par lui-même
 * /!\ Ce tutorial n'utilise pas le système de userhandler, il modifie directement la table dbuser
 * Le but est de montrer l'utilisation des DAO, et quelques fonctionnements de Copix
 * 
 * @package		tutorials
 * @subpackage 	edituser
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Proriété privée qui contient les informations sur l'utilisateur connecté
	 * La norme de développement Copix indique que les propriétés et méthodes privées doivent commencer par _
	 * 
	 * @var CompiledDAORecordDBUser
	 */
	private $_user = null;
	
	/**
	 * beforeAction est une méthode facultative qui est executée avant toute action
	 * si elle return un CopixActionReturn, alors l'action ne sera pas appelée, et c'est le retour de beforeAction qui sera traité
	 * ça permet d'effecuter un traitement que l'on veut faire sur chaque action, mais sans copier / coller le code à chaque action
	 * 
	 * @param string $pActionName Nom de l'action que l'on veut executer
	 * @return CopixActionReturn
	 */
	public function beforeAction ($pActionName) {
		// on vérifie que l'utilisateur est connecté
		if (!_currentUser ()->isConnected ()) {
			// si il n'est pas connecté, on le redirige sur le module auth, qui effectue la demande login / mot de passe
			// _arRedirect retourne un CopixActionReturn, qui permet de rediriger vers l'url passée en paramètre
			// _url génère une URL selon le trigramme utilisé par Copix : module|actiongroup|action (les parties non spécifiées sont remplacées par default)
			// le module auth a comme paramètre facultatif auth_url_return, qui indique où on doit aller après la connexion 
			return _arRedirect (_url ('auth||', array ('auth_url_return' => _url ('edituser||'))));
		}
		
		// recherche des informations sur l'utilisateur connecté
		// un DAO (Data Access Object) permet d'accéder à la base de données sans taper de requête directement
		// ici, on utilise un DAO automatique, c'est à dire que la recherche des informations sur la table se fait par Copix
		// _io permet de renvoyer un singleton, c'est à dire la même instance du DAO à chaque appel (évite de créer un objet à chaque fois)
		// on passe comme paramètre à ioDAO le nom de la table dont on veut des informations
		// la méthode get permet de récupérer un seul enregistrement de la table, celui dont la clef primaire vaut le paramètre qu'on lui passe
		 $this->_user = _ioDAO ('dbuser')->get (_currentUser ()->getId ());
		// si on n'a pas trouvé l'utilisateur, _ioDAO renvoie false au lieu d'un CompiledDAORecordDBUser
		if ( $this->_user === false) {
			throw new CopixException ('L\'utilisateur "' . _currentUser ()->getId () . '" n\'a pu être trouvé.');
		}
		// on vérifie que l'utilisateur est actif
		if (! $this->_user->enabled_dbuser) {
			throw new CopixException ('L\'utilisateur "' . _currentUser ()->getId () . '" n\'est pas actif.');
		}
	}
	
	/**
	 * Action par défaut, affiche le formulaire d'édition des informations de l'utilisateur connecté
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		// _ppo est un raccourci vers new CopixPPO, qui permet de passer des variables à un template smarty ou PHP
		$ppo = _ppo ();
		// le template main.php attend une variable TITLE_PAGE, qu'il associera à TITLE_BAR pour pouvoir générer le nom de la page
		$ppo->TITLE_PAGE = 'Informations sur l\'utilisateur';
		
		// ensuite, on assigne au CopixPPO les valeurs utiles de $this->_user
		// on pourrait très bien passer directement $this->_user au template, mais à ce compte là,
		// le template aurait accès aux méthodes de $this->_user, et un template se doit de ne faire que de l'affichage
		$ppo->user_id = $this->_user->id_dbuser;
		$ppo->user_login = $this->_user->login_dbuser;
		$ppo->user_email = $this->_user->email_dbuser;
		
		// si on a eu des erreurs lors de la soumission du formulaire, on les affiche
		$ppo->errors = _sessionGet ('edituser_errors');
		
		// on retourne un CopixActionReturn, _arPPO est un raccourci quand on veut retourner un template et y mettre les infors du CopixPPO 
		return _arPPO ($ppo, 'edit.form.tpl');
	}

	/**
	 * Action appelée lors de la soumission du formulaire d'édition
	 * 
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		// CopixRequest et une classe qui permet de faciliter la récupération des paramètres, passés en POST ou GET
		// CopixRequest::assert certifie que les champs passés en paramètres existent bien dans CopixRequest, si un champ n'existe pas une exception est levée
		CopixRequest::assert ('user_login', 'user_password_1', 'user_password_2', 'user_email');
		$errors = array ();
		
		// si le login est vide, on ajoute un message d'erreur à afficher
		if (strlen (trim (_request ('user_login'))) == 0) {
			$errors[] = 'Vous devez indiquer un login.';
		// si le login est OK, on modifier les informations contenues dans $this->_user en vue de la modification en base de données
		} else {
			$this->_user->login_dbuser = trim (_request ('user_login'));
		}
		
		// si on veut changer le mot de passe, et que le mot de passe et sa confirmation sont différents, on ajoute un message d'erreur à afficher
		if (strlen (_request ('user_password_1')) > 0 && _request ('user_password_1') <> _request ('user_password_2')) {			 
			$errors[] = 'Le mot de passe et sa confirmation ne correspondent pas.';
		// si on veut changer le mot de passe, et que le mot de passe et sa confirmation sont les mêmes
		} else if (strlen (_request ('user_password_1')) > 0) {
			$this->_user->password_dbuser = md5 (_request ('user_password_1'));
		}
		
		// on vérifie que l'adresse email est une adresse valide
		try {
			// getMail renvoi l'adresse email si elle est valide, sinon, lève une exception
			CopixFormatter::getMail (_request ('user_email'));
			$this->_user->email_dbuser = _request ('user_email');
		} catch (Exception $e) {
			$errors[] = 'L\'adresse e-mail indiquée n\'est pas valide.';
		}
		
		// si on a des erreurs, on les stockes en session, et on redirige sur la page du formulaire, qui les affichera
		if (count ($errors) > 0) {
			// CopixSession est une classe qui permet de gérer plus facilement le stockage d'informations en session,
			// notamment les objets qui demandent quelques opérations pour être mis en session
			// _sessionSet est un raccourci pour CopixSession::set, qui permet d'ajouter une information en session
			_sessionSet ('edituser_errors', $errors);
			return _arRedirect (_url ('edituser||'));
		}
		
		// si on arrive ici, c'est qu'on n'a aucune erreur dans le formulaire, on peut donc supprimer les erreurs stockées en session
		_sessionSet ('edituser_errors', null);
		
		// la méthode update d'un DAO accepte comme paramètre un record compatibles avec ce DAO
		// il va mettre à jour en base de données les informations contenues dans ce record
		// pour trouver la ligne à modifier, il va servir de la valeur de la clef primaire indiquée dans le record
		// donc dans notre cas, la valeur de $this->_user->id_dbuser
		// la méthode update lève une exception si elle n'arrive pas à mettre à jour la base de données
		_ioDAO ('dbuser')->update ($this->_user);
		
		// CopixActionGroup::process permet d'executer une action d'un actiongroup
		// ici, on veut executer l'action getInformation de generictools|Messages, qui permet d'afficher un message d'information
		return CopixActionGroup::process (
			'generictools|Messages::getInformation',
			array (
				'message' => 'Vos informations ont été modifiées.',
				'continue' => _url ('edituser||')
			)
		);
	}
}
?>