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
 * E-mail avec un contenu au format HTML, et éventuellement un contenu alternatif en texte
 *
 * @package		copix
 * @subpackage	utils
 */
class CopixHTMLEMail extends CopixEMail {
	/**
	 * Contenu text du mail
	 *
	 * @var string
	 */
	protected $_textEquivalent;

	/**
	 * Constructeur
	 *
	 * @param string $pTo Adresse du destinataire, séparer par des , pour plusieurs adresses
	 * @param string $pCC Copie carbone, séparer par des , pour plusieurs adresses
	 * @param string $pCCI Copix carbone cachée, séparer par , pour plusieurs adresses
	 * @param string $pSubject Sujet
	 * @param string $pMessage Contenu du mail au format HTML
	 * @param string $pTextEquivalent Contenu du mail au format texte
	 */
	public function __construct ($pTo, $pCC, $pCCI, $pSubject, $pMessage, $pTextEquivalent = null) {
		parent::__construct ($pTo, $pCC, $pCCI, $pSubject, $pMessage);
		$this->_textEquivalent = $pTextEquivalent;
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
		$to = parent::prepareEmail ($pMail, $pFromAdress, $pFromName);
		$pMail->setTextEncoding (new EightBitEncoding ());
		$pMail->setHtmlEncoding (new EightBitEncoding ());
		$pMail->setHtml ($this->_message);
		$pMail->setText ($this->_textEquivalent);
		return $to;
	}
}