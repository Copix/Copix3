<?php
/**
 * Informations sur une ligne de log
 */
class LogReaderLine {
	private $_index = 0;
	private $_date = null;
	private $_type = null;
	private $_text = null;
	private $_shortText = null;

	public function __construct ($pIndex, $pText, $pShortText = null, $pDate = null, $pType = null) {
		$this->_index = $pIndex;
		$this->_text = $pText;
		$this->_shortText = ($pShortText !== null) ? $pShortText : $pText;
		$this->_date = $pDate;
		$this->_type = $pType;
	}

	public function getIndex () {
		return $this->_index;
	}

	public function getText () {
		return $this->_text;
	}

	public function getShortText () {
		return $this->_shortText;
	}

	public function getDate ($pFormat = 'd/m H:i:s') {
		return ($this->_date == null) ? null : date ($pFormat, $this->_date);
	}

	public function getType () {
		return $this->_type;
	}
}
