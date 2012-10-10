<?php
/**
 * @package copix
 * @subpackage utils
 * @author Croës Gérald, Judith Florian, Favre Brice
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Client HTTP
 * 
 * @package copix
 * @subpackage net
 */
class CopixHttpClient  {
	/**
	 * La session cURL
	 */
	private $_curl = null;

	/**
	 * La liste des requêtes à exécuter
	 *
	 * @var array of CopixHTTPClientRequest
	 */
	private $_requests = array ();

	/**
	 * Résultats des requêtes
	 *
	 * @var array of CopixHTTPRequestResult
	 */
	private $_requestResults = array ();

	/**
	 * Fichier contenant la session
	 *
	 * @var unknown
	 */
	private $_sessionFile = null;

	/**
	 * Handler du fichier
	 *
	 * @var bool
	 */
	private $_sessionFileHandler = false;

	/**
	 * Création des fichiers temporaires qui vont intercepter les flux de CURL
	 * @todo générer des exceptions en cas d'échec d'ouverture des fichiers
	 */
	protected function _makeResponseHandlers () {
		$this->_sessionFile = COPIX_TEMP_PATH . uniqid ('session_');
		$this->_sessionFileHandler = fopen ($this->_sessionFile, 'w');
	}

	/**
	 * Lecture des flux en provenance de CURL et libération
	 */
	protected function _readResponseHandlers () {
		fclose ($this->_sessionFileHandler);
		$this->_sessionFile = CopixFile::read ($this->_sessionFile);
		CopixFile::delete ($this->_headerFile);
	}

	/**
	 * Ajoute une requête HTTP à exécuter
	 *
	 * @param CopixHTTPClientRequest $pRequest Requête à lancer
	 * @return int
	 */
	public function addRequest (CopixHTTPClientRequest $pRequest = null) {
		if ($pRequest !== null) {
			$this->_requests[] = $pRequest;
		}
		return count ($this->_requests);
	}

	/**
	 * Fonction de sauvegarde de la session
	 */
	public function saveSession () {
		curl_setopt ($this->_curl, CURLOPT_COOKIEJAR, $this->_sessionFile);
	}

	/**
	 * Lancement du test
	 *
	 * @param CopixHTTPClientRequest $pRequest La requête à exécuter (se rajoute à la liste si déja des requêtes présentent)
	 * @return array
	 */
	public function launch ($pRequest = null) {
		//réinitialisation du tableau des résultats
		$this->_requestResults = array ();

		if ($this->addRequest ($pRequest) === 0) {
			throw new CopixException ("Aucune demande de requête, rien à faire");
		}

		//initialisation du navigateur si besoin
		if ($this->_curl === null) {
			$this->_curl = curl_init ();
		}


		//lancement du script de connexion
		foreach ($this->_requests as $request) {
			$this->_requestResults[] = $this->_launchRequest ($request);
		}

		curl_close ($this->_curl);
		return $this->_requestResults;
	}

	/**
	 * Lancement d'une requête
	 *
	 * @param CopixHTTPClientRequest $pRequest
	 * @return unknown
	 */
	private function _launchRequest (CopixHTTPClientRequest $pRequest) {
		//gestion des proxy
		if ($pRequest->getProxy ()) {
			if ($pRequest->proxyIsHTTPTunnel ()) {
				curl_setopt ($this->_curl, CURLOPT_HTTPPROXYTUNNEL, true);
			}
			if ($pRequest->getProxyHost () != null) {
				curl_setopt ($this->_curl, CURLOPT_PROXY, str_replace ('http://', '', $pRequest->getProxyHost ()));
			}
			if ($pRequest->getProxyPort () != null) {
				curl_setopt ($this->_curl, CURLOPT_PROXYPORT, $pRequest->getProxyPort ());
			}
			if ($pRequest->getProxyUser () != null) {
				$proxyUserPass = $pRequest->getProxyUser ();
				if ($pRequest->getProxyPassword () != null) {
					$proxyUserPass .= ':' . $pRequest->getProxyPassword ();
				}
				curl_setopt ($this->_curl, CURLOPT_PROXYUSERPWD, $proxyUserPass);
			}
		}
		
		//Gestion de l'authentification HTTP
		if ($pRequest->isHTTPBasicAuthenticated ()) {
			$arLogins = $pRequest->getHTTPBasicAuthentication ();
			curl_setopt ($this->_curl, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
			curl_setopt ($this->_curl, CURLOPT_USERPWD, implode (':', $arLogins));
		}

		curl_setopt ($this->_curl, CURLOPT_TIMEOUT, $pRequest->getTimeout ());

		// Choix de l'interface à utiliser
		$interfaceUsed = $pRequest->getInterface ();

		// Si pas d'interface on récupère celle en configuration
		if (empty ($interfaceUsed)) {
			$interfaceUsed = CopixConfig::get ('default|webservicesInterface');
		}

		// Mise en place de l'interface
		if (!empty ($interfaceUsed)) {
			curl_setopt ($this->_curl, CURLOPT_INTERFACE, $interfaceUsed);
		}


		if ($pRequest->getIgnoreCertificate ()) {
			curl_setopt ($this->_curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt ($this->_curl, CURLOPT_SSL_VERIFYHOST, false);
		}

		curl_setopt ($this->_curl, CURLOPT_URL, $pRequest->getUrl ());
		curl_setopt ($this->_curl, CURLOPT_VERBOSE , 1 );
		if ($pRequest->getHeader ()) {
			curl_setopt ($this->_curl, CURLOPT_HTTPHEADER, $pRequest->getHeader());
		}
		if ($pRequest->getCookie ()) {
			curl_setopt ($this->_curl, CURLOPT_COOKIE, $pRequest->getCookie());
		}

		if (count ($pRequest->getPost ())) {
			if ($pRequest->getFile ()) {

				$boundary = uniqid ('------------------');
				$MPboundary = '--' . $boundary;
				$endMPboundary = $MPboundary . '--';
				$postBody = 'Content-type: multipart/form-data, boundary=' . $boundary . "\r\n\r\n";
				foreach ($pRequest->getPost () as $name => $content) {
					$postBody .= $MPboundary . "\r\n";
					$postBody .= 'content-disposition: form-data; name="' . $name . '"'."\r\n\r\n";
					$postBody .= $content . "\r\n";
				}
				$file = $pRequest->getFile ();
				$fileContent = file_get_contents ($file);
				$postBody .= $MPboundary . "\r\n";
				$postBody .= 'Content-Disposition: form-data; name="file"; filename="' . basename ($file) . '"'. "\r\n";
				$postBody .= 'Content-Type: ' . CopixMIMETypes::getFromFileName ($file). "\r\n";
				$postBody .= 'Content-Transfer-Encoding: binary' . "\r\n\r\n";
				$postBody .= $fileContent;
				$postBody .= "\r\n" . $endMPboundary;

				curl_setopt ($this->_curl, CURLOPT_POST, true);
				curl_setopt ($this->_curl, CURLOPT_POSTFIELDS, $postBody);
				curl_setopt ($this->_curl, CURLOPT_HTTPHEADER, array ("Content-Type: multipart/form-data; boundary=$boundary"));
				curl_setopt ($this->_curl, CURLOPT_RETURNTRANSFER, true);
			} else {
				curl_setopt ($this->_curl, CURLOPT_POST, true);
				curl_setopt ($this->_curl, CURLOPT_POSTFIELDS, CopixUrl::valueToUrl (null, $pRequest->getPost ()));
			}
				
		}

		if ($pRequest->getFollowRedirect ()) {
			curl_setopt ($this->_curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt ($this->_curl, CURLOPT_RETURNTRANSFER, true);
			$this->saveSession ();
		} else {
			curl_setopt ($this->_curl, CURLOPT_FOLLOWLOCATION, 0);
		}

		return new CopixHTTPRequestResult ($pRequest, $this->_curl);
	}
}