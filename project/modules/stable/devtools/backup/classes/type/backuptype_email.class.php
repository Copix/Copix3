<?php
/**
 * Envoi la sauvegarde par mail
 */
class BackupTypeEMail extends BackupType {
	/**
	 * Adresse du destinataire, séparateur : virgule
	 *
	 * @var string
	 */
	private $_to = null;

	/**
	 * Adresse du destinataire caché, séparateur : virgule
	 *
	 * @var string
	 */
	private $_bcc = null;

	/**
	 * Sujet
	 *
	 * @var string
	 */
	private $_subject = '[%BASEURL%] Sauvegarde du %TIME%';

	/**
	 * Effectue la sauvegarde
	 *
	 * @param string $pZipPath Archive de la sauvegarde
	 */
	public function backup ($pZipPath) {
		$message = 'Une sauvegarde a été effectuée sur "' . CopixURL::getRequestedBaseUrl () . '" à ' . date (CopixI18N::getDateTimeFormat ()) . '.';
		$subject = str_replace ('%BASEURL%', CopixURL::getRequestedBaseUrl (), $this->getSubject ());
		$subject = str_replace ('%TIME%', date (CopixI18N::getDateTimeFormat ()), $subject);
		$mail = new CopixHTMLEMail ($this->getTo (), $this->getBCC (), null, $subject, $message);
		$mail->addAttachment (file_get_contents ($pZipPath), CopixFile::extractFileName ($pZipPath));
		$mail->send ();
		
		$to = explode (',', $this->getTo ());
		if (count ($to) == 1) {
			return 'La sauvegarde a été envoyée à l\'adresse "' . $this->getTo () . '".';
		} else {
			return 'La sauvegarde a été envoyée aux adresses suivantes : ' . $this->getTo () . '.';
		}
	}

	/**
	 * Définit l'adresse du destinataire (séparateur : virgule)
	 *
	 * @param string $pTo Adresse du destinataire
	 */
	public function setTo ($pTo) {
		$this->_to = $pTo;
	}

	/**
	 * Retourne l'adresse du destinataire
	 *
	 * @return string
	 */
	public function getTo () {
		return $this->_to;
	}

	/**
	 * Définit la copie cadhée
	 *
	 * @param string $pBCC Copie cachée
	 */
	public function setBCC ($pBCC) {
		$this->_bcc = $pBCC;
	}

	/**
	 * Retourne la copie cachée
	 *
	 * @return string
	 */
	public function getBCC () {
		return $this->_bcc;
	}

	/**
	 * Définit le sujet
	 *
	 * @param string $pSubject Sujet
	 */
	public function setSubject ($pSubject) {
		$this->_subject = $pSubject;
	}

	/**
	 * Retourne le sujet
	 *
	 * @return string
	 */
	public function getSubject () {
		return $this->_subject;
	}

	/**
	 * Définit des propriétés depuis un tableau
	 *
	 * @param array $pArray Clef : nom, valeur : valeur
	 */
	public function setFromArray ($pArray) {
		if (isset ($pArray['to'])) {
			$this->setTo ($pArray['to']);
		}
		if (isset ($pArray['bcc'])) {
			$this->setBCC ($pArray['bcc']);
		}
		if (isset ($pArray['subject'])) {
			$this->setSubject ($pArray['subject']);
		}
	}
	
	/**
	 * Supprime la configuration spécifique au profil
	 */
	public function delete () {
		DAObackup_profiles_email::instance ()->deleteBy (_daoSP ()->addCondition ('id_profile', '=', $this->getIdProfile ()));
	}
	
	/**
	 * Sauvegarde la configuration spécifique au profil
	 */
	public function save () {
		$record = DAORecordBackup_profiles_email::create ();
		$record->id_profile = $this->getIdProfile ();
		$record->to_email = $this->getTo ();
		$record->bcc_email = $this->getBCC ();
		$record->subject_email = $this->getSubject ();
		DAObackup_profiles_email::instance ()->insert ($record);
	}
	
	/**
	 * Charge la configuration spécifique au profil
	 */
	public function load () {
		$record = DAObackup_profiles_email::instance ()->get ($this->getIdProfile ());
		if ($record !== false) {
			$this->setTo ($record->to_email);
			$this->setBcc ($record->bcc_email);
			$this->setSubject ($record->subject_email);
		} else {
			$this->setTo (null);
			$this->setBcc (null);
			$this->setSubject ('[%BASEURL%] Sauvegarde du %TIME%');
		}
	}
}