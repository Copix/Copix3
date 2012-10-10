<?php
/**
 * Classe à étendre pour tout type de syndication
 */
abstract class SyndicationType {
	/**
	 * Retourne le conten
	 * 
	 * @param syndication $pSyndication Objet de type syndication qui contient les informations
	 * @return string
	 */
	abstract public function getContent ($pSyndication);

	/**
	 * Retourne une url avec le / de fin
	 *
	 * @param string $pUrl Url
	 * @return string
	 */
	protected function _getUrlWithSlash ($pUrl) {
		return (substr ($pUrl, strlen ($pUrl) - 1) == '/') ? $pUrl : $pUrl . '/';
	}
}