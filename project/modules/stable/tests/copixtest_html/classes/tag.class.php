<?php
/**
 * @package standard
 * @subpackage copixtest_html
 * @author		Croës Gérald, Julien Alexandre
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */


class Tag {
	
	/* Attributs */
	 private $_xml = array (); /* Balises du corps HTML dans un object SimpleXMLElement */ 
	 private $_header = array (); /* Balises du Header */
	 private $_body; /* String Balises du body */
	 
	 /**
	  * Effectue une requête HTTP sur une URL
	  * @param HttpClientRequest $request : contient les données pour faire la rêquete HTTP
	  * @return HttpClientResult $requestResult[0] : contient les résultat de la rêquete HTTP
	  */
	public function requestHTTP ($request) {
		$copixHttpClient = new CopixHttpClient;
		$request->setFollowRedirect (true);
		$requestResult = $copixHttpClient->launch($request);
		$this->_body = $requestResult[0]->getBody();
		$this->_header = preg_split("/\n/", htmlentities($requestResult[0]->getHeader()));
		return $requestResult[0];
	}
	
	public function requestHTTPSession ($copixHttpClient) {
		$requestResult = $copixHttpClient->launch ();
		$this->_body = $requestResult[1]->getBody ();
		$this->_header = preg_split("/\n/", htmlentities($requestResult[1]->getHeader()));
		return $requestResult[1];
	}
	
	/**
	 * Accesseur retourne l'en-tête du document
	 * @param void
	 * @return array : contient les balises du Header
	 */
	public function getHeader () {
		return $this->_header;
	}
	
	/**
	 * Accesseur : retourne le corps du document
	 */
	public function getBody () {
		return $this->_body;
	}
	
	/**
	 * Méthode permettant de "parser" le HTML en XML et d'en générer un code à partir
	 * du XML.
	 * @param void
	 * @return void
	 */
	public function configureBody () {
		CopixClassesFactory::fileInclude('copixtest_html|encoding');
		$tidy = tidy_parse_string ($this->_body, array ('output-xhtml'=>true));
		$tidy->cleanRepair ();
		$bodyTidy = $tidy->body ();
		$result = _toString ($bodyTidy);
		if (str_replace ('&#233', '', $result) == $result) {
			$result = html_entity_decode ($result); 
		} else {
			//$result = htmlspecialchars_decode ($result);
		}
		// $result = html_entity_decode ($result);
		$result = str_replace ('&', '&amp;', $result);
		//$result = str_replace ('<script', '<!--', $result);
		//$result = str_replace ('</script>', '-->', $result);
		//$result = str_replace ('&eacute;', 'é', $result);
		/*$parts = preg_split (
			'/((?:[\x20-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})+)/',
			$result,
			-1,
			PREG_SPLIT_DELIM_CAPTURE
		);*/
		
		/*for($i=0; $i <= $lenght; $i++) {
			if(preg_match('/^(?:['."\r\t\n".'x20-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})*$/', $result[$i])) {
				$result[$i] = utf8_encode($result[$i]);
				//echo htmlspecialchars($result[$i]);
			}
		}*/

		if ($xml = simplexml_load_string (Encoding::checkEncoding($result, $this->_header))) {}
		 else {
			$xml = simplexml_load_string (utf8_encode(Encoding::checkEncoding($result, $this->_header)));
		}
		$simpleXmlElement = $xml->xpath('/*');
		
	
	foreach ($simpleXmlElement as $value) {
			$path = '/'.$value->getName();
			$tag = new stdClass();
			$tag->path_tag = $path;
			$tag->name_tag = $value->getName();
			$tag->attributes_tag = $value->attributes();
			$this->_xml[] = $tag;
			self::getChildren ($value, $path);
		}
	}
	
	/**
	 * Permet de récupérer les balises filles et ajoute ces dernières dans le tableau
	 * _xml[]
	 * @param simpleXmlElement $simpleXmlElement
	 * @param String $ath : chemin xpath
	 * @return void
	 */
	public function getChildren ($simpleXmlElement, $path) {
		foreach ($simpleXmlElement as $child) { 
			$newPath = $path.'/'.$child->getName();
			$tag = new stdClass();
			$tag->name_tag = $child->getName();
			$tag->path_tag = $newPath;
			$tag->attributes_tag = self::getAttributes($child->attributes());
			$tag->contains = (string) $child;
			$this->_xml[] = $tag;
			self::getChildren($child, $newPath);
		}
	}
	
	
	/**
	 * Permet d'avoir les attributs de la forme attribut1=valeur1, attribut2=valeur2
	 * @param SimpleXmlElement $attributes : contient les attributs dans un XML
	 * @return String $stringAttributes : attributs formatés 
	 */
	public function getAttributes ($attributes) {
		$stringAttributes = '';
		foreach ($attributes as $key => $value) {
			if($stringAttributes == null) {
				$stringAttributes = $key.'='.$value;
			} else {
				$stringAttributes = $stringAttributes.", ".$key.'='.$value;
			}
		}
		return $stringAttributes;
	}
	
	/**
	 * Accesseur
	 * @param void
	 * @return array $this->_xml : contient les balises du body à exploiter
	 */
	public function getXML () {
		return $this->_xml;
	}
}
?>