<?php
/**
* @package		copix
* @subpackage	utils
* @author		Croës gérald, Judith Florian, Favre Brice
* @copyright	CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Permet d'envoyer des paquets POST
* @package copix
* @subpackage utils
*/
class CopixHTTPClientRequest {
	
	/**
	 * Le tableau des paramètre à passer en post
	 */
	private $_post = array ();

	/**
	 * Envoi d'un fichier  
	 */
	private $_file = null;
		
	/**
	 * L'url ou aller
	 */
	private $_url= '';

	/**
	 * Indique si l'on souhaite suivre les redirections
	 */
	private $_followRedirect = false;

	/**
	 * Durée d'attente avant expiration de la requete
	 */
	private $_timeout = 0;
	
	/**
	 * Indique si on ignore ou non la vérification du certificat
	 */
	private $_doIgnoreSslVerification = false;
	
	/**
	 * Tableaux d'entête 
	 */
	private $_header = array ();

	/**
	 * Paramètre cookie
 	 */
	private $_cookie = '';

	
	/**
	 * Construction d'une requête
	 * @param	$pUrl	string	adresse sur laquelle envoyer le paquet.	
	 */
	function __construct ($pUrl){
		if (!function_exists ('curl_init')){
			throw new Exception ('L\'extension CURL est nécessaire pour pouvoir mener à bien les tests fonctionnels');
		}
		$this->setUrl ($pUrl);
	}

	/**
	 * Assignation d'une URL
	 * 
	 * @param	$pUrl	string	remplace l'url définie dans le constructeur 
	 */
	function setUrl ($pUrl){
		$this->_url = $pUrl;
	}

	/**
	 * Permet de définir les paramètres à POSTer
	 * 
	 * @param 	$pArray	array	Différents champs du POST
	 */
	function setPost ($pArray){
		$this->_post += $pArray;
	}
	
	/**
	 * Permet de définir un fichier à poster
	 * 
	 * @param 	$pFile	string	Nom du fichier à poster (il doit exister) 
	 */
	function setFile ($pFile){
		$this->_file = $pFile;
	}

	
	/**
	 * Fixe la durée avant expiration de la requete
	 *
	 * @param int $pTimeout durée avant expiration de la requete 
	 */
	function setTimeout ($pTimeout){
		$this->_timeout = $pTimeout;
	}
	
	/**
	 * Permet de spécifier si on souhaite ignorer la vérification du certificat SSL ou non
	 *
	 * @param boolean $pIgnoreSslVerification
	 */
	function setIgnoreCertificate ($pIgnoreSslVerification){
		$this->_doIgnoreSslVerification = $pIgnoreSslVerification;
	}
	
	/**
	 * Permet de spécifier une entête de fichier
	 * 
	 * @param string $pNameHeader Nom de l'entête
	 * @param string $pContentHeader Contenu de l'entête
	 */
	function setHeader ($pNameHeader, $pContentHeader){
		$this->_header[] = $pNameHeader.': '.$pContentHeader;
	}

	/**
	 * Permet d'instancier un cookie
	 *
	 * @param string valeur du Cookie
	 */
	public function setCookie ($pCookie){
		$this->_cookie = $pCookie;
	}

	
	/**
	 * Indique si la vérification du certificat SSL sera faite ou non
	 *
	 * @return boolean vrai si le certificat est vérifié
	 */
	function getIgnoreCertificate () {
		return $this->_doIgnoreSslVerification;
	}
	
	/**
	 * Renvoie la durée avant expiration de la requete
	 *
	 * @return int durée avant expiration de la requete
	 */
	function getTimeout (){
		return $this->_timeout;
	}
	
	/**
	 * Récupération des paramètre demandés en POST
	 * 
	 * @return array	les éléments du formulaire
	 */
	function getPost (){
		return $this->_post;
	}

	/**
	 * Récupération de l'url demandée
	 * 
	 * @return string	l'adresse demandé
	 */
	function getUrl (){
		return $this->_url;
	}

	/**
	 * On indique si l'on souhaite ou non suivre les demandes de redirection HTTP
	 *
	 * @param boolean $pBoolean
	 */
	function setFollowRedirect ($pBoolean){
		$this->_followRedirect = (bool) $pBoolean;
	}

	/**
	 * Indique s'il faut suivre ou non les demandes de redirections HTTP
	 *
	 * @return bool
	 */
	function getFollowRedirect (){
		return $this->_followRedirect;
	}
	
	/**
	 * Retourne le nom du fichier à envoyer
	 * 
	 * @return string
	 */
	function getFile (){
		return $this->_file;
	}
	
	/**
	 * Retourne l'entête définie par l'utilisateur
	 * 
	 * @return array
	 */
	public function getHeader (){
		return $this->_header;
	}

	/**
	 * Retour la valeur du cookie définie par l'utilisateur
	 * 
	 * @return string
	 */
	public function getCookie (){
		return $this->_cookie;
	}

	
}

/**
 * Classe permettant de construire le résultat d'une requête
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
	 * Le résultat d'une requête
	 *
	 * @param CopixHTTPClientRequest $pRequest
	 * @param unknown_type $pResource
	 */
	function __construct (CopixHTTPClientRequest $pRequest, $pCURLResource){
		$this->_request = $pRequest;
		$this->_body = '';
		$this->_header = '';
		
		curl_setopt( $pCURLResource, CURLOPT_HEADERFUNCTION, array($this, '_writeHeader'));
		curl_setopt( $pCURLResource, CURLOPT_WRITEFUNCTION, array($this, '_writeBody'));
		
		curl_exec ($pCURLResource);
			
		$error = curl_error ($pCURLResource);
		if ( !empty($error) ) {
			$this->_error = $error;
		}else{
			$this->_httpCode = curl_getinfo ($pCURLResource, CURLINFO_HTTP_CODE);
			$this->_lastURL = curl_getinfo ($pCURLResource, CURLINFO_EFFECTIVE_URL);
		}
	}
	
	/**
	 * Fonction d'écriture de l'entête de la requête
	 * 
	 */
	protected function _writeHeader($pCURLResource, $pData) {
		$this->_header .= $pData;
		return strlen($pData);
	}

	/**
	 * Fonction d'écriture du corps de la requête
	 * 
	 */
	protected function _writeBody($pCURLResource, $pData) {
		$this->_body .= $pData;
		return strlen($pData);
	}
	
	/**
	 * Récupération de l'entête
	 * 
	 */
	public function getHeader (){
		return $this->_header;
	}
	
	/**
	 * Récupère la valeur d'une entête particulière
	 * 
	 * @param $pNameHeader 
	 */
	public function getHeaderValue ($pNameHeader){
		$arrayHeader = explode ("\n", $this->_header);
		foreach ($arrayHeader as $lineHeader) {
			list ($pName, $pValue) = explode (': ', $lineHeader);
			if ($pName == $pNameHeader) {
				return $pValue;
			}
		}
		return '';
	}
	
	/**
	 * Renvoie le corps de la requête
	 * 
	 */
	public function getBody (){
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
}

/**
 * Classe HTTPClient
 */
class CopixHttpClient  {

	/**
	 * La session cURL
	 */
	private $_curl = null;

	/**
	 * La liste des requêtes à exécuter
	 * @var array of CopixHTTPClientRequest
	 */
	private $_requests = array ();

	/**
	 * résultats des requêtes
	 * @var array of CopixHTTPRequestResult
	 */
	private $_requestResults = array ();

	/**
	 * Ajoute une requête HTTP à exécuter
	 * 
	 * @param CopixHTTPClientRequest $pRequest Requête à lancer
	 */
	function addRequest (CopixHTTPClientRequest $pRequest){
		if ($pRequest !== null){
			$this->_requests[] = $pRequest;
		}
		return count ($this->_requests);
	}

	/**
	 * Lancement du test
	 * @param	CopixHTTPClientRequest	$pRequest	La requête à exécuter (se rajoute à la liste si déja des requêtes présentent)
	 * @param	int	$pTimeout	Durée au bout de laquelle la requete est abandonnée.
	 */
	function launch ($pRequest = null){
		//réinitialisation du tableau des résultats
		$this->_requestResults = array ();

		if ($this->addRequest ($pRequest) === 0){
			throw new CopixException ("Aucune demande de requête, rien à faire");
		}

		//initialisation du navigateur si besoin
		if ($this->_curl === null){
			$this->_curl = curl_init ();
		}

		//lancement du script de connexion
		foreach ($this->_requests as $request){
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
	private function _launchRequest (CopixHTTPClientRequest $pRequest){
		if (CopixConfig::get('default|proxyEnabled')) {
            //proxy_host, proxy_port, proxy_login  et proxy_password
            curl_setopt($this->_curl,CURLOPT_HTTPPROXYTUNNEL,true);
            if (CopixConfig::get('default|proxyHost') != null) {
                curl_setopt($this->_curl,CURLOPT_PROXY,str_replace('http://','',CopixConfig::get('default|proxyHost')));
            }
            if (CopixConfig::get('default|proxyPort') != null) {
                curl_setopt($this->_curl,CURLOPT_PROXYPORT,intval(CopixConfig::get('default|proxyPort')));
            }
            if (CopixConfig::get('default|proxyUser') != null) {
                $proxyUserPass = CopixConfig::get('default|proxyUser');
	            if (CopixConfig::get('default|proxyPass') != null) {
	                $proxyUserPass.= ':'.CopixConfig::get('default|proxyPass');
	            }
	            curl_setopt ( $this->_curl,CURLOPT_PROXYUSERPWD,$proxyUserPass);
            }
        }
		curl_setopt ( $this->_curl, CURLOPT_TIMEOUT,$pRequest->getTimeout());
		$interfaceUsed = CopixConfig::get('default|webservicesInterface');
		if (!empty($interfaceUsed)) {
			curl_setopt ( $this->_curl, CURLOPT_INTERFACE,$interfaceUsed);
		}
		if ($pRequest->getIgnoreCertificate()) {
	        curl_setopt ( $this->_curl, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt ( $this->_curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		
        curl_setopt ( $this->_curl, CURLOPT_URL, $pRequest->getUrl ());
        curl_setopt ( $this->_curl, CURLOPT_VERBOSE , 1 );
		if ($pRequest->getHeader ()) {
			curl_setopt ($this->_curl, CURLOPT_HTTPHEADER, $pRequest->getHeader());
		}
		if ($pRequest->getCookie ()) {
			curl_setopt ($this->_curl, CURLOPT_COOKIE, $pRequest->getCookie());
		}
 
        if (count ($pRequest->getPost ())){
			if ($pRequest->getFile ()) {
				
				$boundary = uniqid ('------------------');
				$MPboundary = '--'.$boundary;
				$endMPboundary = $MPboundary. '--';
				$postBody = 'Content-type: multipart/form-data, boundary='.$boundary."\r\n\r\n";
				foreach ($pRequest->getPost () as $name=>$content) {
					$postBody .= $MPboundary."\r\n";
					$postBody .= 'content-disposition: form-data; name="'.$name.'"'."\r\n\r\n";
					$postBody .= $content."\r\n";
				}
				$file = $pRequest->getFile ();
				$fileContent = file_get_contents ($file);
				$postBody .= $MPboundary."\r\n";
				$postBody .= 'Content-Disposition: form-data; name="file"; filename="'.basename($file).'"'. "\r\n";
				$postBody .= 'Content-Type: '.CopixMIMETypes::getFromFileName ($file). "\r\n";
				$postBody .= 'Content-Transfer-Encoding: binary'."\r\n\r\n";
				$postBody .= $fileContent;
				$postBody .= "\r\n". $endMPboundary;
				
				curl_setopt ($this->_curl, CURLOPT_POST, true);
				curl_setopt ($this->_curl, CURLOPT_POSTFIELDS, $postBody);
				curl_setopt ($this->_curl, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data; boundary=$boundary"));
				curl_setopt ($this->_curl, CURLOPT_RETURNTRANSFER, true);
			}
			else {
				curl_setopt ( $this->_curl, CURLOPT_POST, true);
				curl_setopt ( $this->_curl, CURLOPT_POSTFIELDS, CopixUrl::valueToUrl (null, $pRequest->getPost ()));
			}
					
		}

		if ($pRequest->getFollowRedirect ()){
			curl_setopt ( $this->_curl, CURLOPT_FOLLOWLOCATION, 1);
		}else{
			curl_setopt ( $this->_curl, CURLOPT_FOLLOWLOCATION, 0);
		}
		
		return new CopixHTTPRequestResult($pRequest, $this->_curl);		
	}
}
?>