<?php

class LinkInfos {
	public $caption = null;
	public $captionI18n = null;
	public $url = null;
	public $credentials = null;
	
	/**
	 * Constructeur
	 */	
	public function __construct ($pUrl, $pCaption, $pCaptionI18n = null, $pCredentials = null) {
		$this->url = $pUrl;
		$this->caption = $pCaption;
		$this->captionI18n = $pCaptionI18n;
		$this->credentials = $pCredentials;
	}
}
?>
