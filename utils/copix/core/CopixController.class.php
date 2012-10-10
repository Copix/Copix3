<?php
/**
 * @package copix
 * @author Croes Gérald, Jouanneau Laurent, Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Coordinateur de l'application.
 * C'est l'objet principal de Copix, qui coordonne toute la cinématique de l'application,
 * et met en oeuvre toutes les fonctionnalités de Copix.
 *
 * @package copix
 * @subpackage core
 */
class CopixController {
	/**
	 * Instance du coordinateur de l'application
	 *
	 * @var CopixController
	 */
	protected static $_instance = false;
	
	/**
     * L'objet de configuration
     * 
     * @var CopixConfig
	 */
	protected $_copixconfig;
	 
	/**
	 * Récupère l'instance du coordinateur de l'application (Singleton)
	 * <code>
	 *	$userPlugin = CopixController::instance ()->getPlugin ('auth|auth');
	 * </code>
	 *
	 * @return CopixController
	 * @throws Exception
	 */
	public static function instance () {
		if (self::$_instance === false) {
			throw new Exception (_i18n ('copix:copixcontroller.error.unavailable'));
		}
		return self::$_instance;
	}

	/**
	 * Construction du controller
	 *
	 * @param string $pConfigFile Chemin du fichier de configuration du projet
	 */
	public function __construct ($pConfigFile) {
		self::$_instance = $this;
		//Configuration
		$this->_copixconfig = CopixConfig::instance ()->load ($pConfigFile)->initialize ();

		//Request
		CopixRequest::setRequest (array_merge (array ('module'=>'default', 'group'=>'default', 'action'=>'default'), CopixUrl::parse (CopixUrl::getRequestedPathInfo (), false, true)));
		// do what we need for each plugin before starting the session
		$this->_beforeSessionStart ();
		if ($this->_copixconfig->session_autostart){
		    CopixSession::start ();
		}
	}

	/**
	 * Appel des méthodes des plugins "beforeSessionStart" pour donner l'opportunité
	 * à ces derniers d'exécuter des actions avant le démarrage de la session
	 */
	protected function _beforeSessionStart () {
		foreach (CopixPluginRegistry::getRegistered () as $name => $plugin) {
			if ($plugin instanceof ICopixBeforeSessionStartPlugin){
				$plugin->beforeSessionStart ();
			}
		}
	}

	/**
	* Fonction principale du coordinateur à appeler dans le index.php pour démarrer le framework.
	* Gère la cinématique globale du fonctionnement du site.
	*/
	public function process () {
		try {
			// S'assure que la session est sécurisée avant d'aller plus loin
			CopixSession::assertSecure();
			
			// Choix des couples actions pour la tache a réaliser.
			$execParams = $this->_extractExecParam ();//trio group, action, module
			try {
				$processAction = true;
				foreach (CopixPluginRegistry::getRegistered () as $name => $plugin) {
					if ($plugin instanceof ICopixBeforeProcessPlugin){
						if (($result = $plugin->beforeProcess ($execParams)) instanceof CopixActionReturn) {
							$this->_processResult ($result);
							$processAction = false;
							break;
						}
					}
				}
				if ($processAction) {
					CopixContext::push ($execParams->module);
					$this->_processResult (CopixActionGroup::process ($execParams));
				}
			}catch (CopixActionGroupNotFoundException $e){
				$this->_doNotExistsAction ();
			}
		} catch (Exception $e) {
			try {
				$response = false;
				foreach (CopixPluginRegistry::getRegistered () as $name => $plugin) {
					if ($plugin instanceof ICopixCatchExceptionsPlugin){
						if ($plugin->catchExceptions ($e) === true) {
							$response = true;
						}
					}
				}
				if (!$response) {
					throw $e;
				}
			// on gère les exceptions de type CopixCredentialException différement, elles redirigent au lieu de s'afficher, et ne génèrent pas de log
			} catch (CopixCredentialException $e) {
				$authUrl = CopixConfig::get('|authUrl');
				$returnUrl = _url ('#', $_SERVER['REQUEST_METHOD'] == "POST" ? $_POST : array());
				header ('location: ' . CopixUrl::get ($authUrl , array ('noCredential' => 1, 'auth_url_return' => $returnUrl)));
				exit ();
				
			// toutes les exceptions de Copix passeront ici, sauf CopixCredentialException
			} catch (CopixException $e) {
				$extras = array_merge ($e->getExtras (), array (
					'file' => $e->getFile (),
					'line' => $e->getLine (),
					'exception' => get_class ($e)
				));
				
				// il peut arriver que _log génère aussi une exception ...
				try {
					_log ($e->getMessage (), 'errors', CopixLog::EXCEPTION, $extras);
				} catch (Exception $e) {}
				
				// on vérifie que le coordinateur arrive bien à afficher l'exception, il se peut qu'il n'y arrive pas
				try {
					$this->showException ($e);
					
				} catch (Exception $e2) {
					try {
						_log ($e2->getMessage (), 'errors', CopixLog::EXCEPTION);
					} catch (Exception $e3) {}
					echo $e->getMessage () . '<br/>';
					echo $e2->getMessage ();
					exit ();
				}
			// les exceptions qui ne dépendent pas de Copix
			} catch (Exception $e) {
				echo $e->getMessage ();
				exit ();
			}
		}
	}

	/**
	* Appel des méthodes beforeDisplay des plugins enregistrés et remplace le HEAD pour les en tête HTML.
	* Cette méthode donne aux plugins l'opportunité de modifier le contenu final de la page web avant qu'il soit affiché définitivement.
	*
	* @param string	$pContent Passé par référence. Ce qui va être affiché.
	*/
	protected function _beforeDisplay (&$pContent) {
		foreach (CopixPluginRegistry::getRegistered () as $name => $plugin) {
			if ($plugin instanceof ICopixBeforeDisplayPlugin){
				$plugin->beforeDisplay ($pContent);
			}
		}
		// On teste la présence de HTML_HEADER pour éviter de passer inutilement dans CopixHTMLHeader (qui appelle CopixConcat à chaque fois)
		if (strpos ($pContent, '<!--<$HTML_HEADER />-->') !== false) {
			$pContent = str_replace ('<!--<$HTML_HEADER />-->', CopixHTMLHeader::getHeader (), $pContent);
			$pContent = str_replace ('<!--<$HTML_FOOTER />-->', CopixHTMLHeader::getFooter (), $pContent);
		} else {
			$pContent = str_replace ('<!--<$HTML_HEAD />-->', CopixHTMLHeader::get (), $pContent);
		}
		CopixCookie::setCookies ();
	}

	/**
	 * Réalise les derniers traitements (généralement d'affichage ou de redirection).
	 * En fonction du code retour de l'action CopixActionReturn::CONST, Copix réalise
	 * un certain nombre de traitements pour terminer la requête en cours.
	 *
	 * @param CopixActionReturn $pToProcess Ce qui a été retourné par CopixActionGroup::process ()
	 * @see CopixActionGroup
	 * @throws CopixException
	 */
	protected function _processResult ($pToProcess) {
		//appel les plugins de post-processing.
		foreach (CopixPluginRegistry::getRegistered () as $name => $plugin) {
			if ($plugin instanceof ICopixAfterProcessPlugin){
				if (($return = $plugin->afterProcess ($pToProcess)) != null && $return instanceof CopixActionReturn) {
					$pToProcess = $return;
				}
			}
		}
		
		$defaultMainTemplate = CopixAJAX::isAJAXRequest () ? 'generictools|blank.tpl' : $this->_copixconfig->mainTemplate;

		//Analyse du résultat du process
		switch ($pToProcess->code) {
			// affichage
			case CopixActionReturn::DISPLAY:
				$charset = CopixI18N::getCharset ();
				header("Content-Type: text/html;charset=".$charset);
				CopixHTMLHeader::addOthers ('<meta http-equiv="Content-Type" content="text/html;charset='.$charset.'" />');

				//appel de la méthode de préparation de la page standard.
				$this->_processStandard ($pToProcess->data);
				$pToProcess->data->assign ('HTML_HEAD', '<!--<$HTML_HEAD />-->');
				$pToProcess->data->assign ('HTML_HEADER', '<!--<$HTML_HEADER />-->');
				$pToProcess->data->assign ('HTML_FOOTER', '<!--<$HTML_FOOTER />-->');
				//Par ex, bandeaux de pub, menus dynamiques, ... (propres aux projets.)
				CopixContext::clear ();
				$content = $pToProcess->data->fetch ($defaultMainTemplate);
				$this->_beforeDisplay ($content);
				echo $content;
				break;
			
			// affichage en restant dans le contexte utilisé avant l'appel
			case CopixActionReturn::DISPLAY_IN:
				$charset = CopixI18N::getCharset ();
				header("Content-Type: text/html;charset=".$charset);
				CopixHTMLHeader::addOthers ('<meta http-equiv="Content-Type" content="text/html;charset='.$charset.'" />');

				// appel de la méthode de préparation de la page standard.
				$this->_processStandard ($pToProcess->data);
				$pToProcess->data->assign ('HTML_HEAD', '<!--<$HTML_HEAD />-->');
				$pToProcess->data->assign ('HTML_HEADER', '<!--<$HTML_HEADER />-->');
				$pToProcess->data->assign ('HTML_FOOTER', '<!--<$HTML_FOOTER />-->');
				//Par ex, bandeaux de pub, menus dynamiques, ... (propres aux projets.)
				$content = $pToProcess->data->fetch ($pToProcess->more);
				$this->_beforeDisplay ($content);
				echo $content;
				break;
			
			// contenu d'un fichier
			case CopixActionReturn::FILE:
			case CopixActionReturn::CONTENT:
				$contentDisposition = 'inline';
				$contentTransfertEnconding = 'binary';
				$contentType = null;
				$fileNameOnly = null;
				
				if ($pToProcess->code == CopixActionReturn::FILE) {
					$fileName = $pToProcess->data;
					$fileNameOnly = explode ('/', str_replace ('\\', '/', $fileName));
					$fileNameOnly = $fileNameOnly[count ($fileNameOnly) - 1];
					if( !file_exists( $fileName ) ){
						_log ('File not found : ' . $fileName, 'errors', CopixLog::ERROR);
						header ("HTTP/1.0 404 Not Found");
						header ("Status: 404 Not found");
						echo $fileNameOnly. ' not found';
						exit ();
					}
				}
				
				if (is_array ($pToProcess->more)) {
					if (isset ($pToProcess->more['content-disposition'])){
						$contentDisposition = $pToProcess->more['content-disposition'];
					}
					if (isset ($pToProcess->more['filename'])) {
						$fileNameOnly = $pToProcess->more['filename'];
					}
					if (isset ($pToProcess->more['content-type'])) {
						$contentType = $pToProcess->more['content-type'];
					}
					if (isset ($pToProcess->more['content-transfer-encoding'])) {
						$contentTransfertEnconding = $pToProcess->more['content-transfer-encoding'];
					}
				} else if (strlen (trim ($pToProcess->more))) {
					$contentType = $pToProcess->more;
				}
				
				if ($contentType === null) {
					$contentType = CopixMIMETypes::getFromFileName ($fileNameOnly);
				}

				// spécial IE6 et les ZIP, IE6 ne gère pas comme il faut le type mime des zip
				// si on a content-type=multipart/x-zip + content-disposition=inline, le nom du document est récupéré, mais l'archive fait 2 octets de moins et est illisible
				// si on a content-type=application/zip + content-disposition=inline, le nom du document n'est pas récupéré, mais l'archive est valide
				// si on a content-type=multipart/x-zip + content-disposition=attachment, le nom du document est récupéré, et l'archive est valide
				if ($contentType == CopixMIMETypes::getFromExtension ('zip')) {
					$contentDisposition = 'attachment';
				}

				header ("Pragma: public");
				if (isset ($pToProcess->more['last-modified'])){
					header ("Last-Modified: ".$pToProcess->more['last-modified']);
				}else{
					if ($pToProcess->code == CopixActionReturn::FILE){
						header ("Last-Modified: ".gmdate('r', filemtime ($fileName)));
					}
				}
				if (isset ($pToProcess->more['expires'])){
					header ("Expires: ".$pToProcess->more['expires']);
				}else{
					if ($pToProcess->code == CopixActionReturn::FILE){
						header ("Expires: ".gmdate('r', time () + 86400));
					}else{
						header ("Expires: 0");
					}
				}
				
				//No cache uniquement sur envois de données temporaires
				if ($pToProcess->code == CopixActionReturn::CONTENT){
					header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header ("Cache-Control: protected", false);
				}else{
					header ("Cache-Control: public");
				}
				
				header ("Date: ".gmdate("r"));
				header ("Content-Type: " . $contentType);
				header ("Content-Disposition: " . $contentDisposition."; filename=\"" . $fileNameOnly . "\";");
				header ("Content-Transfer-Encoding: " . $contentTransfertEnconding);
				header ("Content-Length: " . ($pToProcess->code == CopixActionReturn::FILE ? filesize ($fileName) : strlen ($pToProcess->data)));

				if ($pToProcess->code == CopixActionReturn::FILE) {
					// Vérification de la date de modification
					if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
						$time = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
						if(($time !== false) && (filemtime($fileName) <= $time)) {
							header("Status 304 Not Modified", true, 304);
							break;
						}
					}
					switch ($this->_copixconfig->etag){
						case CopixConfig::ETAG_MD5_FILECONTENT:
							$etagValue = md5_file($fileName);
							break;

						case CopixConfig::ETAG_FILEDATETIME:
							$etagValue = filemtime ($fileName);
							break;

						case CopixConfig::ETAG_MD5_FILEDATETIME_AND_SIZE:
							$etagValue = md5($fileName.filesize($fileName));
							break;

						default:
							$etagValue = false;
							break;
					}
					if ($etagValue !== false){
						header('ETag: '.$etagValue);
					}
					CopixCookie::setCookies ();
					if($_SERVER['REQUEST_METHOD'] != 'HEAD') {
			        	readfile ($fileName);
					}
				} else {
					CopixCookie::setCookies ();
					echo $pToProcess->data;
				}
				flush ();
				break;
			
			// PPO, utilisant Smarty
			case CopixActionReturn::PPO:
				$contentType = 'text/html';
				$cacheControl = false;
				$mainTemplate = $defaultMainTemplate;
				$charset = CopixI18N::getCharset ();
	
				if (is_array ($pToProcess->more)) {
					$template = $pToProcess->more['template'];
					if (isset ($pToProcess->more['content-type'])) {
						$contentType = $pToProcess->more['content-type'];
					}
					if (array_key_exists ('mainTemplate', $pToProcess->more)) {
						$mainTemplate = $pToProcess->more['mainTemplate'];
					}
					if (isset ($pToProcess->more['charset'])) {
						$charset = $pToProcess->more['charset'];
					}
					if (isset ($pToProcess->more['cache-control'])) {
						$cacheControl = $pToProcess->more['cache-control'];
					}
				} else {
					$template = $pToProcess->more;
				}
	
				$tplContent = new CopixTpl ();
				$tplContent->assign ('ppo', $pToProcess->data);
				header ("Content-Type: " . $contentType . ";charset=" . $charset);
				CopixHTMLHeader::addOthers ('<meta http-equiv="Content-Type" content="'.$contentType.';charset='.$charset.'" />');

				if ($cacheControl !== false){
					header ('Cache-Control: ' . $cacheControl);
				}
				if ($mainTemplate !== null) {
					$tplMain = new CopixTpl ();
					$tplMain->assign ('TITLE_PAGE', isset ($pToProcess->data->TITLE_PAGE) ? $pToProcess->data->TITLE_PAGE : null);
					$tplMain->assign ('TITLE_BAR', isset ($pToProcess->data->TITLE_BAR) ? $pToProcess->data->TITLE_BAR : null);
					$tplMain->assign ('MAIN', $tplContent->fetch ($template));
					$tplContent = null;//on libère la mémoire
					$tplMain->assign ('ppo', $pToProcess->data);
					$this->_processStandard ($tplMain);
					$tplMain->assign ('HTML_HEAD', '<!--<$HTML_HEAD />-->');
					$tplMain->assign ('HTML_HEADER', '<!--<$HTML_HEADER />-->');
					$tplMain->assign ('HTML_FOOTER', '<!--<$HTML_FOOTER />-->');
					$content = $tplMain->fetch ($mainTemplate);
				} else {
					$tplContent->assign ('TITLE_PAGE', isset ($pToProcess->data->TITLE_PAGE) ? $pToProcess->data->TITLE_PAGE : null);
					$tplContent->assign ('TITLE_BAR', isset ($pToProcess->data->TITLE_BAR) ? $pToProcess->data->TITLE_BAR : null);
					$this->_processStandard ($tplContent);
					$tplContent->assign ('HTML_HEAD', '<!--<$HTML_HEAD />-->');
					$tplContent->assign ('HTML_HEADER', '<!--<$HTML_HEADER />-->');
					$tplContent->assign ('HTML_FOOTER', '<!--<$HTML_FOOTER />-->');
					$content = $tplContent->fetch ($template);
				}
				CopixContext::clear ();
				$this->_beforeDisplay ($content);
				echo $content;
				break;
			
			// redirection
			case CopixActionReturn::REDIRECT:
				//redirection standard, message http.
				if (isset ($pToProcess->more['301']) && $pToProcess->more['301']){
					header ("HTTP/1.1 301 Moved Permanently");
					header ("Status: 301 Moved Permanently", false, 301);
				}
				CopixCookie::setCookies ();
				header ('location: ' . _url ($pToProcess->data));
				break;
			
			// retour d'un code HTTP
			case CopixActionReturn::HTTPCODE:
				foreach ($pToProcess->data as $code) {
					header ($code);
				}
				CopixCookie::setCookies ();
				echo $pToProcess->more;
				break;
			
			// aucune action
			case CopixActionReturn::NONE:
				if(!headers_sent()) {
					CopixCookie::setCookies ();
				}
				break;
				
			// type d'action inconnue
			default :
				throw new CopixException (_i18n ('copix:copixcontroller.error.invalidActionReturn', array ($pToProcess->code)));
				break;
		}

		//Appel des méthodes afterDisplay des plugins
		foreach (CopixPluginRegistry::getRegistered () as $name => $plugin) {
			if ($plugin instanceof ICopixAfterDisplayPlugin){
				$plugin->afterDisplay ();
			}
		}
		
		//Pour la compatibilité avec APC, on va terminer la session avant la fin du script 
		// (sans quoi les variables statiques peuvent être "nettoyées" par APC avant l'écriture)
		session_write_close();
	}
	
	/**
	 * Traitements effectués par défaut lors de la demande d'une action d'affichage
	 *
	 * @param string $pTplObject
	 */
	protected function _processStandard ($pTplObject) {
		$tplVars = $pTplObject->getTemplateVars ();
				
		if (! isset ($tplVars['TITLE_PAGE'])) {
			$tplVars['TITLE_PAGE'] = CopixConfig::get ('|titlePage');
			$pTplObject->assign ('TITLE_PAGE', $tplVars['TITLE_PAGE']);
		}

		if (! isset ($tplVars['TITLE_BAR'])) {
			$tplVars['TITLE_BAR'] = str_replace ('{$TITLE_PAGE}', $tplVars['TITLE_PAGE'], CopixConfig::get ('|titleBar'));
			$pTplObject->assign ('TITLE_BAR', $tplVars['TITLE_BAR']);
		}
	}

	/**
	 * Analyse les données passées au controller et en extrait le trio module/group/action
	 *
	 * @return CopixExecParam Parametres d'execution à utiliser.
	 * @throws CopixException
	 */
	protected function _extractExecParam () {
		$execParam = _ppo ();
		$execParam->module = strtolower ($this->_safeFilePath (CopixRequest::get ('module', 'default')));
		$execParam->group = strtolower ($this->_safeFilePath (CopixRequest::get ('group', 'default')));
		$execParam->action = CopixRequest::get ('action', 'default');

		if ($this->_copixconfig->checkTrustedModules) {
			$a = isset ($this->_copixconfig->trustedModules[$execParam->module]);
			if (!$a || ($a && !$this->_copixconfig->trustedModules[$execParam->module])) {
				throw new CopixException (_i18n ('copix:copixmodule.error.untrusted', $execParam->module));
			}
		}
		return $execParam;
	}

	/**
	 * Suppression des caractères qui posent soucis dans un chemin
	 *
	 * @param string $path Chemin à traiter
	 * @return string Chemin nettoyé des caractères interdits
	 */
	protected function _safeFilePath ($pPath) {
		return str_replace (array ('.', '/', '\\'), '', $pPath);
	}

	/**
	 * L'action demandée n'existe pas (moule/ag/action)
	 *
	 * @throws CopixException
	 */
	protected function _doNotExistsAction () {
		if ($this->_copixconfig->invalidActionTriggersError) {
			throw new CopixException (_i18n ('copix:copixcontroller.error.notExistsAction', _request ('#')));
		}
		header ("HTTP/1.0 404 Not Found");
		header ("Status: 404 Not found");
		echo _i18n ('copix:copixcontroller.error.notExistsAction', _url ('#'));
		exit ();
	}
	
	/**
	 * Affichage d'une exception
	 *
	 * @param Exception $pE Exception à afficher
	 */
	public function showException ($pE) {
		$this->_processResult (CopixActionGroup::process ('generictools|messages::Exception', array ('exception' => $pE)));
	}
}