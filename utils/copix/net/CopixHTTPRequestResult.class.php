<?php
/**
 * @package		copix
 * @subpackage	utils
 * @author		Croës Gérald, Favre Brice
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Classe permettant de construire le résultat d'une requête
 * @package    copix
 * @subpackage net
 */
class CopixHTTPRequestResult {

	/**
	 * Requete
	 */
	private $_request = null;

	/**
	 * En-tête de la requête
	 */
	private $_header = null;

	/**
	 * Corps de la requête
	 */
	private $_body = null;

	/**
	 * Code de retour de la requête
	 */
	private $_httpCode = null;

	/**
	 * Dernière URL appellée
	 */
	private $_lastURL = null;

	/**
	 * Est ce que la requête renvoie une erreur
	 */
	private $_error = false;

	/**
	 * Durée d'établissement de la connexion en secondes
	 */
	private $_connectTime = null;

	/**
	 * Durée en secondes, entre le début de la transaction
	 *  et de début du transfert de fichiers
	 */
	private $_startTransferTime = null;

	/**
	 * Durée totale de la transaction
	 */
	private $_totalTime = null;

	/**
	 * Le fichier dans lequel sera sauvegardé les éléments en retours de l'url (partie HEAD) 
	 */
	private $_headerFile = null;

	/**
	 * Le fichier dans lequel sera sauvegardé les éléments en retours de l'url (partie BODY) 
	 */
	private $_bodyFile = null;

	/**
	 * Handler pour le fichier qui va recevoir les en têtes
	 *
	 * @var handler
	 * @see fopen
	 */
	private $_headerFileHandler = false;

	/**
	 * Handler pour le fichier qui va recevoir le contenu de la requête
	 *
	 * @var handler
	 * @see fopen
	 */
	private $_bodyFileHandler = false;

	/**
	 * Création des fichiers temporaires qui vont intercepter les flux de CURL
	 * @todo générer des exceptions en cas d'échec d'ouverture des fichiers
	 */
	protected function _makeResponseHandlers  (){
		$this->_headerFile = COPIX_TEMP_PATH . uniqid ('header_');
		$this->_bodyFile = COPIX_TEMP_PATH . uniqid ('body_');
		$this->_headerFileHandler = fopen ($this->_headerFile, 'w');
		$this->_bodyFileHandler = fopen ($this->_bodyFile, 'w');
	}

	/**
	 * Lecture des flux en provenance de CURL et libération
	 */
	protected function _readResponseHandlers (){
		fclose ($this->_headerFileHandler);
		// il m'est arrivé de devoir faire plusieurs fclose sur des fichiers téléchargés
		// après une grosse recherche je n'ai pas compris pourquoi, c'est la seule "solution" que j'ai trouvé
		while (is_resource ($this->_bodyFileHandler)) {
			fclose ($this->_bodyFileHandler);
		}

		$this->_header = CopixFile::read ($this->_headerFile);
		$this->_body   = CopixFile::read ($this->_bodyFile);

		CopixFile::delete ($this->_headerFile);
		CopixFile::delete ($this->_bodyFile);
	}

	/**
	 * Le résultat d'une requête
	 *
	 * @param CopixHTTPClientRequest $pRequest
	 * @param unknown_type $pResource
	 */
	function __construct (CopixHTTPClientRequest $pRequest, $pCURLResource){
		$this->_request = $pRequest;
		$this->_body = '';
		$this->_header = '';

		$this->_makeResponseHandlers ();
		curl_setopt( $pCURLResource, CURLOPT_WRITEHEADER, $this->_headerFileHandler);
		curl_setopt( $pCURLResource, CURLOPT_FILE, $this->_bodyFileHandler);
		curl_exec ($pCURLResource);
		$this->_readResponseHandlers ();
			
		$error = curl_error ($pCURLResource);
		if ( !empty($error) ) {
			$this->_error = $error;
		} else{
			$this->_httpCode = curl_getinfo ($pCURLResource, CURLINFO_HTTP_CODE);
			$this->_lastURL = curl_getinfo ($pCURLResource, CURLINFO_EFFECTIVE_URL);
			$this->_connectTime = curl_getinfo ($pCURLResource, CURLINFO_CONNECT_TIME);
			$this->_startTransferTime = curl_getinfo ($pCURLResource, CURLINFO_STARTTRANSFER_TIME);
			$this->_totalTime = curl_getinfo ($pCURLResource, CURLINFO_TOTAL_TIME);
		}
	}

	/**
	 * Fonction d'écriture de l'entête de la requête
	 */
	protected function _writeHeader($pCURLResource, $pData) {
		$this->_header .= $pData;
		return strlen($pData);
	}

	/**
	 * Fonction d'écriture du corps de la requête
	 */
	protected function _writeBody($pCURLResource, $pData) {
		$this->_body .= $pData;
		return strlen($pData);
	}

	/**
	 * Récupération de l'entête
	 */
	public function getHeader (){
		return $this->_header;
	}

	/**
	 * Récupère la valeur d'une entête particulière
	 *
	 * @param string $pNameHeader Nom du header
	 * @param int $pIndex Index du header dont on veut la valeur (exemmple : si on a plusieurs fois le même header, on récupère l'index 0, ensuite le 1, etc)
	 * @return string
	 */
	public function getHeaderValue ($pNameHeader, $pIndex = 0){
		$arrayHeader = explode ("\n", $this->_header);
		$index = 0;
		foreach ($arrayHeader as $lineHeader) {
			if(strchr($lineHeader, ':')){
				list ($pName, $pValue) = explode (': ', $lineHeader);
				if ($pName == $pNameHeader) {
					if ($index == $pIndex) {
						return $pValue;
					}
					$index++;
				}
			}
		}
		return null;
	}

	/**
	 * Renvoie le corps de la requête
	 *
	 */
	public function getBody () {
		return $this->_body;
	}

	/**
	 * Récupération du code HTTP Renvoyé
	 *
	 */
	public function getHttpCode (){
		return $this->_httpCode;
	}

	/**
	 * Renvoie la dernière URL appellée
	 */
	public function getLastUrl (){
		return $this->_lastURL;
	}

	/**
	 * Renvoie la requête 
	 */
	public function getRequest (){
		return $this->_request;
	}

	/**
	 * Renvoie l'erreur
	 */
	public function getError (){
		return $this->_error;
	}

	/**
	 * Renvoie le temps de connexion
	 */
	public function getConnectTime () {
		return $this->_connectTime;
	}

	/**
	 * Renvoie le temps entre le début de la transaction et le téléchargement 
	 */
	public function getStartTransferTime () {
		return $this->_startTransferTime;
	}

	/**
	 * Renvoie le temps total de la transaction
	 */
	public function getTotalTime () {
		return $this->_totalTime;
	}
}