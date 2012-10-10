<?php
/**
 * Notification de mises à jour du flux
 * http://cyber.law.harvard.edu/rss/soapMeetsRss.html#rsscloudInterface
 */
class SyndicationCloud {
	/**
	 * Nom de domaine (ex : test.com)
	 *
	 * @var string
	 */
	public $domain = null;
	
	/**
	 * Port (80 par défaut)
	 *
	 * @var int
	 */
	public $port = 80;
	
	/**
	 * Répertoire de la procedure (ex : /myDir)
	 *
	 * @var string
	 */
	public $path = null;
	
	/**
	 * Nom de la procedure (ex : rssNotify)
	 *
	 * @var string
	 */
	public $registerProcedure = null;
	
	/**
	 * Protocole à utiliser (ex : xml-rpc)
	 *
	 * @var string
	 */
	public $protocol = 'xml-rpc';
}