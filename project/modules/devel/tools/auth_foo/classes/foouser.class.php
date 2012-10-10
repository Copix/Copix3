<?php
/**
 * @package devel
 * @subpackage auth_foo
 * @author		Gérald Croës
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Un utilisateur qui provient de FooUserHandler
 */
class FooUser implements ICopixUser {
	/**
	 * Libellé.
	 *
	 * @var string
	 */
	public $caption;

	/**
	 * Login.
	 *
	 * @var string
	 */
	public $login;
	
	/**
	 * Identifiant.
	 *
	 * @var integer
	 */
	public $id;
	
	/**
	 * Adresse e-mail.
	 *
	 * @var string
	 */
	public $email;
	
	/**
	 * Construit un DBUser à partir d'un enregistrement en base.
	 *
	 * @param ICopixDAORecord $record
	 */
	public function __construct ($record) {
		$this->caption = $record->login;
		$this->login   = $record->login;
		$this->id      = $record->ID;
	}
	
	/**
	 * Retourne le libellé de l'utilisateur.
	 *
	 * @return string
	 */
	public function getCaption() {
		return $this->caption;
	}
	
	/**
	 * Retourne le login de l'utilisateur.
	 * 
	 * @return string
	 */
	public function getLogin() {
		return $this->login;
	}

	/**
	 * Retourne l'identifiant technique de l'utilisateur. 
	 *
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Retourne le nom du handler responsable de cet utilisateur.
	 *
	 * @return string
	 */
	public function getHandler() {
		return 'auth_foo|foouserhandler';
	}
}