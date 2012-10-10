<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author			Croes Gérald, Jouanneau Laurent
 * @copyright		CopixTeam
 * @link				http://copix.org
 * @license			http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


/**
 * E-mail avec un contenu au format texte
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixTextEMail extends CopixEMail {
	/**
	 * Constructeur
	 *
	 * @param string $to recipient
	 * @param string $cc Carbon Copy
	 * @param string $cci Hidden Carbon Copy
	 * @param string $message the message (HTML Format)
	 */
	public function __construct ($to, $cc, $cci, $subject, $message){
		parent::__construct ($to, $cc, $cci, $subject, $message);
	}
	
	/**
	 * Ajoute les informations stockées dans cette classe à $pMail
	 *
	 * @param htmlMimeMail $pMail Object htmlMimeMail auquel on veut ajouter les informations
	 * @param string $pFromAdress Adresse de l'expéditeur, sera prise dans la configuration '|mailFrom' si == null
	 * @param string $pFromName Nom de l'expéditeur, sera prit dans la configuration '|mailFromName' si == null
	 * @return array Adresses de destination
	 */
	public function prepareEmail (htmlMimeMail $pMail, $pFromAdress, $pFromName) {
		$to = parent::prepareEmail($pMail, $pFromAdress, $pFromName);
		$pMail->setTextEncoding(new EightBitEncoding ());
		$pMail->setText ($this->_message);
		return $to;
	}
}