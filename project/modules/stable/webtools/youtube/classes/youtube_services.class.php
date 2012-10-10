<?php
/**
 * Retourne des informations sur des vidéos Youtube
 */
class YoutubeServices {
	/**
	 * Retourne la liste des vidéos d'un utilisateur
	 *
	 * @param string $pUser Utilisateur dont on veut les vidéos
	 * @return YoutubeVideo[]
	 */
	public static function getVideos ($pUser, $pStart = 1, $pCount = 25) {
		$client = new CopixHTTPClient ();
		$request = new CopixHTTPClientRequest ('http://gdata.youtube.com/feeds/api/users/' . $pUser . '/uploads?start-index=' . $pStart . '&max-results=' . $pCount);
		$result = $client->launch ($request);
		if (count ($result) != 1 || $result[0]->getHttpCode () != 200) {
			throw new YoutubeException (_i18n ('Le compte Youtube "' . $pUser . '" n\'existe pas.'));
		}
		$xml = simplexml_load_string ($result[0]->getBody ());
		$toReturn = array ();
		foreach ($xml->entry as $entry) {
			$toReturn[] = new YoutubeVideo ($entry);
		}
		return $toReturn;
	}
}