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
	 * Indique à setPost d'écraser les valeurs déjà passées
	 */
	const REPLACE = 0;
	
	/**
	 * Indique à setPost de rajouter les nouvelles valeurs
	 */
	const ADD = 1;
	
	/**
	 * Indique à setPost de rajouter les nouvelles valeurs et d'ecraser les anciennes si elles ont le même nom
	 */
	const ADD_REPLACE = 2;
	
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
	 * Paramètre pour savoir si on veut choisir l'utilisation du proxy
	 */
	private $_interface = '';
	
	private $_useAuthCertificate = false;
	
	private $_authCertificatePath = '';
	
	private $_authCertificatePass = '';
	
	private $_certificatePath = '';
	
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
	 * Récupération de l'url demandée
	 * 
	 * @return string	l'adresse demandé
	 */
	function getUrl (){
		return $this->_url;
	}

	/**
	 * Permet de définir les paramètres à POSTer
	 * 
	 * @param 	$pArray	array	Différents champs du POST
	 * @param int REPLACE, ADD_REPLACE, ADD (ancien comportement par défaut)
	 */
	function setPost ($pArray, $pReplace = self::ADD) {
		switch ($pReplace) {
			case self::REPLACE:
				$this->_post = $pArray;
				break;
			case self::ADD:
				$this->_post = array_merge ($pArray, $this->_post);
				break;
			case self::ADD_REPLACE:
				$this->_post = array_merge ($this->_post, $pArray);
				break;
		}
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
	 * Permet de définir un fichier à poster
	 * 
	 * @param 	$pFile	string	Nom du fichier à poster (il doit exister) 
	 */
	function setFile ($pFile){
		$this->_file = $pFile;
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
	 * Fixe la durée avant expiration de la requete
	 *
	 * @param int $pTimeout durée avant expiration de la requete 
	 */
	function setTimeout ($pTimeout){
		$this->_timeout = $pTimeout;
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
	 * Permet de spécifier si on souhaite ignorer la vérification du certificat SSL ou non
	 *
	 * @param boolean $pIgnoreSslVerification
	 */
	function setIgnoreCertificate ($pIgnoreSslVerification){
		$this->_doIgnoreSslVerification = $pIgnoreSslVerification;
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
	 * Permet de spécifier une entête de fichier
	 * 
	 * @param string $pNameHeader Nom de l'entête
	 * @param string $pContentHeader Contenu de l'entête
	 */
	function setHeader ($pNameHeader, $pContentHeader){
		$this->_header[] = $pNameHeader.': '.$pContentHeader;
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
	 * Permet d'instancier un cookie
	 *
	 * @param string valeur du Cookie
	 */
	public function setCookie ($pCookie){
		$this->_cookie = $pCookie;
	}

	/**
	 * Retour la valeur du cookie définie par l'utilisateur
	 * 
	 * @return string
	 */
	public function getCookie (){
		return $this->_cookie;
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
	 * On indique si l'on veut utiliser une interface différente de celle configurée 
	 *
	 * @param string $pInterface
	 */
	function setInterface ($pInterface){
		$this->_interface = $pInterface;
	}

	/**
	 * Retourne l'interface du client HTTP
	 *
	 * @return string
	 */
	function getInterface (){
		return $this->_interface;
	}
	
	public function setUseAuthCertificate($useCertificate){
		$this->_useAuthCertificate = $useCertificate;
	}
	
	public function useAuthCertificate(){
		return $this->_useAuthCertificate; 
	}
	
	public function setAuthCertificate($certificatePath){
		$this->_authCertificatePath = $certificatePath;
	}
	
	public function getAuthCertificate(){
		return $this->_authCertificatePath;
	}
	
	public function setAuthCertificatePass($certificatePass){
		$this->_authCertificatePass = $certificatePass;
	}
	
	public function getAuthCertificatePass(){
		return $this->_authCertificatePass;
	}
	
	public function setCertificate($certificate){
		$this->_certificatePath = $certificate;
	}
	
	public function getCertificate(){
		return $this->_certificatePath;
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
			if (strpos ($lineHeader, ':') === false) {
				continue;
			}
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
	public function launch ($pRequest = null){
		// Réinitialisation du tableau des résultats
		$this->_requestResults = array ();

		if ($this->addRequest ($pRequest) === 0){
			throw new CopixException ("Aucune demande de requête, rien à faire");
		}

		// Initialisation du navigateur si besoin
		if ($this->_curl === null){
			$this->_curl = curl_init ();
		}

		// Lancement du script de connexion
		foreach ($this->_requests as $request){
			$this->_requestResults[] = $this->_launchRequest ($request);
		}

		curl_close ($this->_curl);
		return $this->_requestResults;
	}
	
	/**
	 * Fonction permettant de Pinguer une adresse
	 * 
	 */
	public function ping ($pRequest){
		// Initialisation du navigateur si besoin
		if ($this->_curl === null){
			$this->_curl = curl_init ();
		}

		// Lancement du script de connexion
		$result = $this->_launchRequest ($request);
		
		// Si on a pas de HTTP code, alors on renvoie null
		if ($result->getHttpCode () === null) {
			return false;
		}
		return true;
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
		curl_setopt ( $this->_curl, CURLOPT_TIMEOUT, $pRequest->getTimeout());

		// Choix de l'interface à utiliser
		
		$interfaceUsed = $pRequest->getInterface();
		
		// Si pas d'interface on récupère celle en configuration
		if (empty($interfaceUsed)) {
			$interfaceUsed = CopixConfig::get('default|webservicesInterface');
		}

		// Mise en place de l'interface
		if (!empty ($interfaceUsed)) {
			curl_setopt ( $this->_curl, CURLOPT_INTERFACE,$interfaceUsed);
		}
		if ($pRequest->getIgnoreCertificate()) {
	        curl_setopt ( $this->_curl, CURLOPT_SSL_VERIFYPEER, false);
	        curl_setopt ( $this->_curl, CURLOPT_SSL_VERIFYHOST, false);
		}else if($pRequest->getCertificate()){
			curl_setopt ( $this->_curl, CURLOPT_SSL_VERIFYPEER, true);
	        curl_setopt ( $this->_curl, CURLOPT_SSL_VERIFYHOST, true);
	        curl_setopt ($this->_curl, CURLOPT_CAINFO, $pRequest->getCertificate());
		}
		
		
		if($pRequest->useAuthCertificate()){
			curl_setopt ($this->_curl, CURLOPT_SSLCERT, $pRequest->getAuthCertificate()); 
			curl_setopt ($this->_curl, CURLOPT_SSLCERTPASSWD, $pRequest->getAuthCertificatePass()); 
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