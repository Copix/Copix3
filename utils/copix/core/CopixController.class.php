<?php
/**
* @package		copix
* @author		Croes Gérald, Jouanneau Laurent
* @copyright	2001-2006 CopixTeam
* @link			http://copix.org
* @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
* Sert au stockage des paramètres d'execution
*
* @package   copix
* @subpackage core
* @see CopixController
*/
class CopixExecParam {
	/**
	 * nom du module demandé
    * @var string    
    */
	public $module = 'default';

	/**
	 * nom de l'action demandée
    * @var string    
    */
	public $action = 'default';

	/**
	 * nom du fichier desc demandé
    * @var string    
    */
	public $desc   = 'default';
}

/**
* Coordinateur de l'application.
* C'est l'objet principal de Copix, qui coordonne toute la cinematique de l'application,
* et met en oeuvre toutes les fonctionnalités de Copix.
*
* @package  copix
* @subpackage core
*/
class CopixController {
	/**
	 * Instance du coordinateur de l'application
	 * @var CopixController
	 */
	protected static $_instance = false;
	 
	/**
	 * Récupère l'instance du coordinateur de l'application (Singleton)
	 * <code>
	 *    $userPlugin = CopixController::instance ()->getPlugin ('auth|auth');
	 * </code>
	 * @return CopixController
	 */
	public static function instance (){
		if (self::$_instance === false){
			throw new Exception ('Instance du contrôller non disponible');			
		}
		return self::$_instance;
	} 

	/**
    * Construction du controller
    * @param   string  $configFile     chemin du fichier de configuration du projet
    */
	public function __construct ($configFile){
		self::$_instance = $this;

		// creating CopixConfig Object and includes the asked configuration file.
		$config = CopixConfig::instance ();
		require ($configFile);

		CopixRequest::setRequest (CopixUrl::parse (CopixUrl::getRequestedPathInfo (), false, true));

		// do what we need for each plugin before starting the session
		$this->_beforeSessionStart ();
		if ($config->sessionName != null){
			session_name ($config->sessionName);
		}
		session_start ();
		$config->afterSessionStart ();
	}

	/**
	 * Appel des méthodes des plugins "beforeSessionStart" pour donner l'opportunité
	 * à ces derniers d'exécuter des actions avant le demarrage de la session 
	 * (inclusion de classes par exemple)
	 */
	protected function _beforeSessionStart (){
		foreach (CopixPluginRegistry::getRegistered () as $name=>$plugin){
			$plugin->beforeSessionStart ();
		}
	}

	/**
    * Fonction principale du coordinateur à appeler dans le index.php pour démarrer le framework.
    * gère la cinématique globale du fonctionnement du site.
    */
	public function process (){
		$action   = null;
		$fromDesc = true;

		//Choix des couples actions pour la tache a réaliser.
		$execParams = $this->_extractExecParam ();//trio desc, action, module
		
		CopixContext::push ($execParams->module);

		//détermine le chemin d'exécution.
		$path       = CopixModule::getPath ($execParams->module);

		// On regarde s'il existe un fichier desc.
		if (file_exists ($fileName = $path . COPIX_DESC_DIR . strtolower ($execParams->desc) . '.desc.php')){
		    include ($fileName);
			//LAISSER INCLUDE (PAS ONCE). Php n'arrive pas a rafraichir sans cela.
			//Dans l'idée: inclusion une première fois, les variables sont connues de la fonction.
			//La deuxième fois, ne réinclus pas le fichier, et du coups les variables sont connues mais comme globales.
			// on verifie si la variable correspondante à l'action existe
			if (isset (${$execParams->action})){
				$action = ${$execParams->action};
			}
		}

		if ($action === null){
			$fileToRead = $path . COPIX_ACTIONGROUP_DIR . strtolower ($execParams->desc) . '.actiongroup.php';
			// verification de l'existence du fichier
			$fromDesc = false;
			if (file_exists($fileToRead)){
				$action = new CopixAction ($execParams->module.'|'.$execParams->desc, $execParams->action);
			}
		}
		//Traitement de l'objet reçu.
		if ($action === null){
			$this->_doNotExistsAction (_i18n ('copix:copix.error.load.desc', $execParams->desc));
		}else{
			CopixContext::clear ();
			foreach (CopixPluginRegistry::getRegistered () as $name=>$plugin){
				$plugin->beforeProcess ($action);
			}
			CopixContext::push ($action->file->module);
			$this->_doAction ($action, $fromDesc);
		}
	}

	/**
    * Instancie l'objet ActionGroup correspondant au CopixAction, et éxecute la methode adéquate.
    *
    * @param CopixAction $pAction décrivant la classe ActionGroup et la méthode à utiliser
    * @todo  contrôler le fontionnement avec un type File, puis prévoir les fichiers inexistants.
    * @access protected
    * 
    * @todo supprimer le paramètre $pFromDesc dans les futures versions de copix
    */
	protected function _doAction ($pAction, $pFromDesc) {
		$class = strtolower (get_class ($pAction));
		switch ($class){
			case 'copixaction':
			   $this->_processResult (CopixActionGroup::process ($pAction->file->module.'|'.$pAction->file->fileName.'::'.$pAction->useMeth, array (), $pFromDesc));
			   break;
			case 'copixactionredirect':
   				return new CopixActionReturn (CopixActionReturn::REDIRECT, $pAction->url);
   			case 'copixactionstatic':
				//page statiques.
				return new CopixActionReturn (CopixActionReturn::STATIC_FILE, $pAction->useFile, $action->more);
			case 'copixactionzone':
				//implémenter l'action zone.
				$tpl = new CopixTpl ();
				$tpl->assign ('TITLE_PAGE', $pAction->titlePage);
				$tpl->assign ('TITLE_BAR',  $pAction->titleBar);
				$tpl->assign ('MAIN', CopixZone::process ($pAction->zoneId, $pAction->zoneParams));
				return new CopixActionReturn (CopixActionReturn::DISPLAY, $tpl);
			default :
			   throw new Exception ('Demande inconnue '.$pAction); 
		}
	}

	/**
    * Appel des méthodes beforeDisplay des plugins enregistrés et remplace le HEAD pour les 
    * en tête HTML.
    * 
    * Cette méthode donne aux plugins l'opportunité de modifier le contenu final de la page
    * web avant qu'il soit affiché définitivement.
    * @param 	string	$pContent	ce qui va être affiché.
    */
	protected function _beforeDisplay (& $pContent){
		foreach (CopixPluginRegistry::getRegistered () as $name=>$plugin){
			$plugin->beforeDisplay ($pContent);
		}
		$pContent = str_replace ('<$HTML_HEAD />', CopixHTMLHeader::get (), $pContent);
	}

	/**
	 * Réalise les derniers traitements (généralement d'affichage ou de redirection).
	 * 
	 * En fonction du code retour de l'action CopixActionReturn::CONST, Copix réalise 
	 * un certain nombre de traitements pour terminer la requête en cours.
	 * 
     * @param	CopixActionReturn      $pToProcess   Ce qui a été retourné par CopixActionGroup::process ()
     * @see CopixActionGroup
     */
	protected function _processResult ($toProcess){
		$config = CopixConfig::instance ();

		//appel les plugins de post-processing.
		foreach (CopixPluginRegistry::getRegistered () as $name=>$plugin){
			$plugin->afterProcess ($toProcess);
		}
		
		//Analyse du résultat du process
		switch ($toProcess->code){
			case CopixActionReturn::ERROR:
			//erreur
			header ("Content-Type: text/html");
			$tpl = new CopixTpl();
			if (($assignedTitlePage = $tpl->getAssigned ('TITLE_PAGE')) !== null){
				$tpl->assign ('TITLE_PAGE', '['._i18n ('copix:common.messages.error').']'.$assignedTitlePage);
			}else{
				$tpl->assign ('TITLE_PAGE', '['._i18n ('copix:common.messages.error').']');
			}
			$this->_processStandard ($tpl);
			$tpl->assign ('HTML_HEAD', '<$HTML_HEAD />'); 
			$tpl->assign ('MAIN', $toProcess->data);
			$content = $tpl->fetch ('|error.tpl');
			CopixContext::clear ();
			$this->_beforeDisplay ($content);
			echo $content;
			break;

			case CopixActionReturn::DISPLAY:
			header("Content-Type: text/html");
			$this->_processStandard ($toProcess->data);//appel de la méthode de préparation de la page standard.
			$toProcess->data->assign ('HTML_HEAD', '<$HTML_HEAD />'); 
			//Par ex, bandeaux de pub, menus dynamiques, ... (propres aux projets.)
			CopixContext::clear ();
			$content = $toProcess->data->fetch ($config->mainTemplate);
			$this->_beforeDisplay($content);
			echo $content;
			break;

			case CopixActionReturn::DISPLAY_IN:
			$this->_processStandard ($toProcess->data);//appel de la méthode de préparation de la page standard.
			$toProcess->data->assign ('HTML_HEAD', '<$HTML_HEAD />'); 
			//Par ex, bandeaux de pub, menus dynamiques, ... (propres aux projets.)
			$content = $toProcess->data->fetch ($toProcess->more);
			$this->_beforeDisplay($content);
			echo $content;
			break;

			case CopixActionReturn::STATIC_FILE :
			$tpl = new CopixTpl ();
			$tpl->assign ('MAIN', self::includeStatic ($toProcess->data));
			$waitForMore = array ('TITLE_PAGE', 'TITLE_BAR');
			foreach ($waitForMore as $key){
				if (isset ($toProcess->more[$key])){
					$tpl->assign ($key, $toProcess->more[$key]);
				}
			}
			$this->_processStandard ($tpl);
			$tpl->assign ('HTML_HEAD', '<$HTML_HEAD />'); 
			//Affichage dans le template principal.
			CopixContext::clear ();
			$content = $tpl->fetch ($config->mainTemplate);
			$this->_beforeDisplay($content);
			echo $content;
			break;
							
			case CopixActionReturn::FILE:
			case CopixActionReturn::CONTENT:
			$contentDisposition = 'inline';
			$contentTransfertEnconding = 'binary';
			$contentType = null;
			$fileNameOnly = null;

			if ($toProcess->code == CopixActionReturn::FILE){
				$fileName = $toProcess->data;
				$fileNameOnly = explode ('/', str_replace ('\\', '/', $fileName));
				$fileNameOnly = $fileNameOnly[count ($fileNameOnly)-1];
			}

			if (is_array ($toProcess->more)){
				if (isset ($toProcess->more['content-disposition'])){
					$contentDisposition = $toProcess->more['content-disposition'];
				}
				if (isset ($toProcess->more['filename'])){
					$fileNameOnly = $toProcess->more['filename'];
				}
				if (isset ($toProcess->more['content-type'])){
					$contentType = $toProcess->more['content-type'];
				}
				if (isset ($toProcess->more['content-transfer-encoding'])){
					$contentTransfertEnconding = $toProcess->more['content-transfer-encoding'];
				}
			}elseif (strlen (trim ($toProcess->more))){
				$contentType = $toProcess->more;
			}
			
			if ($contentType === null){
				$contentType = CopixMIMETypes::getFromFileName ($fileNameOnly);
			}

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: protected", false);
			header("Content-Type: ".$contentType);
			header("Content-Disposition: ".$contentDisposition."; filename=\"".$fileNameOnly."\";");
			header("Content-Transfer-Encoding: ".$contentTransfertEnconding);
			header("Content-Length: ".($toProcess->code == CopixActionReturn::FILE ? filesize ($fileName) : strlen ($toProcess->data)));
			if ($toProcess->code == CopixActionReturn::FILE){
				readfile ($fileName);
			}else{
				echo $toProcess->data;
			}
			flush();
			break;

			case CopixActionReturn::PPO:
			$contentType = 'text/html';
			$cacheControl = false;
			$mainTemplate = $config->mainTemplate;
			$charset = CopixI18N::getCharset ();

			if (is_array ($toProcess->more)){
				$template = $toProcess->more['template'];
				if (isset ($toProcess->more['content-type'])){
					$contentType = $toProcess->more['content-type'];
				}
				if (array_key_exists ('mainTemplate', $toProcess->more)){
					$mainTemplate = $toProcess->more['mainTemplate'];
				}
				if (isset ($toProcess->more['charset'])){
					$charset = $toProcess->more['charset'];
				}
				if (isset ($toProcess->more['cache-control'])){
					$cacheControl = $toProcess->more['cache-control'];
				}
			}else{
				$template = $toProcess->more;
			}

			$tplContent = new CopixTpl ();
			$tplContent->assign ('ppo', $toProcess->data);
			header("Content-Type: ".$contentType.";charset=".$charset);
			if ($cacheControl !== false){
				header ('Cache-Control: '.$cacheControl);
			}
			if ($mainTemplate !== null){
				$tplMain = new CopixTpl ();
				$tplMain->assign ('TITLE_PAGE', isset ($toProcess->data->TITLE_PAGE) ? $toProcess->data->TITLE_PAGE : null);
				$tplMain->assign ('TITLE_BAR', isset ($toProcess->data->TITLE_BAR) ? $toProcess->data->TITLE_BAR : null);
				$tplMain->assign ('MAIN', $tplContent->fetch  ($template));
				$this->_processStandard ($tplMain);				
				$tplMain->assign ('HTML_HEAD', '<$HTML_HEAD />');
				$content = $tplMain->fetch ($mainTemplate);
			}else{
				$tplContent->assign ('TITLE_PAGE', isset ($toProcess->data->TITLE_PAGE) ? $toProcess->data->TITLE_PAGE : null);
				$tplContent->assign ('TITLE_BAR', isset ($toProcess->data->TITLE_BAR) ? $toProcess->data->TITLE_BAR : null);
				$this->_processStandard ($tplContent);				
				$tplContent->assign ('HTML_HEAD', '<$HTML_HEAD />');
				$content = $tplContent->fetch  ($template);
			}
			CopixContext::clear ();
			$this->_beforeDisplay($content);
			echo $content;
			break;

			case CopixActionReturn::REDIRECT:
			//redirection standard, message http.
			header ('location: '.$toProcess->data);
			break;

			case CopixActionReturn::HTTPCODE:
			foreach ($toProcess->data as $code){
				header ($code);
			}
			echo $toProcess->more;
			break;			

			case CopixActionReturn::NONE:
			break;
		}
	}
	
	/**
	 * Traitements effectués par défaut lors de la demande d'une action d'affichage
	 */
	protected function _processStandard ($tpl){}

	/**
	 * Analyse les données passées au controller et en extrait le trio module/desc/action
	 * 
     * @return CopixExecParam    les parametres d'execution à utiliser.
     */
	protected function _extractExecParam (){
		$execParam = new CopixExecParam ();
		$execParam->module = $this->_safeFilePath (CopixRequest::get ('module', 'default'));
		$execParam->desc   = $this->_safeFilePath (CopixRequest::get ('desc', CopixRequest::get ('group', 'default')));
		$execParam->action = CopixRequest::get ('action', 'default');

		$config = CopixConfig::instance ();
		if($config->checkTrustedModules){
			$a = isset($config->trustedModules[$execParam->module]);
			if (!$a ||( $a && !$config->trustedModules[$execParam->module]))
			trigger_error(_i18n('copix:copix.error.module.untrusted',$execParam->module), E_USER_ERROR);
		}
		return $execParam;
	}

	/**
    * Deletes special characters that could be considered as tricky whith
    *    action descriptions
    * @param    string  $path   chemin à traité
    * @return   string  chemin nettoyé des caractères interdits
    * @access protected
    */
	protected function _safeFilePath ($path){
		return str_replace (array ('.', '/', '\\'), '', $path);
	}

	/**
    * L'action demandée n'existe pas (moule/ag/action)
    */
	protected function _doNotExistsAction (){
		if (CopixConfig::instance ()->invalidActionTriggersError){
			trigger_error(_i18n('copix:copix.error.load.module',$param->module), E_USER_ERROR);
		}
		header("HTTP/1.0 404 Not Found");
		header("Status: 404 Not found");
		echo "Page introuvable";
		exit;
	}
	
	/**
	 * Affichage d'une exception
	 *
	 * @param Exception $e	l'exception à afficher
	 */
	public function showException ($e){
	   $this->_processResult (CopixActionGroup::process ('generictools|messages::Exception', array ('exception'=>$e)));
	}
}
?>