<?php
class RepositoriesCommit {
	private $_id = null;
	private $_author = null;
	private $_date = null;
	private $_message = null;
	private $_files = null;

	public function __construct () {

	}

	public function setId ($pId) {
		$this->_id = $pId;
	}

	public function getId () {
		return $this->_id;
	}

	public function setAuthor ($pAuthor) {
		$this->_author = $pAuthor;
	}

	public function getAuthor () {
		return $this->_author;
	}

	public function setDate ($pTimestamp) {
		$this->_date = $pTimestamp;
	}

	public function getDate ($pFormat = 'd/m/Y h:i:s') {

	}

	public function setMessage ($pMessage) {
		$this->_message = $pMessage;
	}

	public function getMessage () {
		return $this->_message;
	}

	public function addFile () {
		return $this->_files[] = new RepositoriesFile ();
	}

	public function getFiles () {
		return $this->_files;
	}
}