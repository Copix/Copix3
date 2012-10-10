<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Gérald Croës
 */

CopixHtmlHeader::addJSLink (_resource ('heading|js/tools.js'));

/**
 * Gestion des URL significatives pour le CMS
 * @package cms
 * @subpackage heading
 */
class UrlHandlerHeading extends CopixUrlHandler {
	/**
	 * La classe de service qu'on utilisera pour la gestion des urls
	 *
	 * @var HeadingElementInformationServices
	 */
	private $_headingElementInformationServices = false;

	/**
	 * Construction
	 */
	public function __construct (){
		$this->_headingElementInformationServices = new HeadingElementInformationServices ();
	}

	/**
	 * Décrypte l'url
	 */
	function parse ($path, $mode) {
		//uniquement en mode prepend
		if ($mode !== 'prepend'){
			return false;
		}
		
		$implodedPath  = implode ('/', $path);
		//on regarde si on ne cherche pas la page d'accueil.
		if ($implodedPath == '') {
			$implodedPath = '/';
		}
		
		if (CopixConfig::get ('heading|cleanupURLs')) {
			//en mode cleanup, pour éviter les -- on a retiré les tirets dans le get de l'urlhandler
			//quand on arrive ici pour les url de type "test-test" dans la barre d'adresse, on reçoit ici : "test test"
			//on ajoute donc un tiret pour redevenir conforme avec la base de données 
			$implodedPath = str_replace(" ", "-", $implodedPath);
		}
		
		if ($record = $this->_headingElementInformationServices->getByUrlId (CopixUrl::getRequestedUrl (), $implodedPath)){
			return array ('module'=>'heading', 'group'=>'default', 'action'=>'default', 'public_id'=>$record->public_id_hei);
		}
		
		// Uniquement si l'URL doit rediriger
		if ($return = $this->_headingElementInformationServices->getNewUrlByUrlId (CopixUrl::getRequestedUrl (), $implodedPath)){
			$element = $this->_headingElementInformationServices->get($return);
			if ($element->status_hei == HeadingElementStatus::ARCHIVE){
				// on va afficher une page 404 expliquant que la page a été archivée et n'existe plus
				return array ('module'=>'heading', 'group'=>'default', 'action'=>'default', 'public_id'=>$return);
			}
			return array ('module'=>'heading', 'group'=>'default', 'action'=>'redirect', 'headingRedirectNewURL' => $return);
		}
		
		//uniquement pour les noms de module heading ou cms
		if($path[0] !== "cms"){
			return false;
		}
		
		//Uniquement si le troisième élément du chemin est un numérique
		if (count ($path) !== 3 || ! _validator ('numeric')->check ($path[2])){
			return false;
		}
		
		// Si accedé par url générique et qu'une url propre existe
		$currentUrl = CopixUrl::getRequestedBaseUrl () . $implodedPath;
		$public_id = intval ($path[2]);
		/*$urlByPublicId = _url ('heading||', array ('public_id' => $public_id));
		if (strpos($currentUrl, 'index.php') === false) {
			$urlByPublicId = str_replace ('index.php/', '', $urlByPublicId);
		}
		if (strpos($urlByPublicId, 'xiti') === false && $currentUrl !== $urlByPublicId) {
			return array ('module' => 'heading', 'group' => 'default', 'action' => 'redirect', 'headingRedirectNewURL' => $public_id);
		}*/
		
		return array ('module'=>'heading', 'group'=>'default', 'action'=>'default', 'public_id'=>$path[2]);
	}

	/**
	 * Encode l'url
	 */
	function get ($dest, $parameters, $mode) {
		//Uniquement en mode prepend
		if ($mode !== 'prepend'){
			return false;
		}

		//Uniquement pour l'action de récupération d'un élément
		if ($dest['module'] === 'heading' && $dest['group'] === 'default' && $dest['action'] === 'default'){
			if (! isset ($parameters['public_id'])){
				return false;
			}
			$toReturn = new CopixUrlHandlerGetResponse ();
			$toReturn->vars = $parameters;
			foreach (array ('public_id', 'caption_hei', 'url_id_hei', 'target_hei') as $index){
			   if (array_key_exists($index, $toReturn->vars)){
			   	   unset ($toReturn->vars[$index]);
			   }
			}
			try {
				//on test s'il n'y a pas d'ancre
				$anchorInfo = explode('#', $parameters['public_id']);
				if (count($anchorInfo)>1){
					$parameters['public_id'] = $anchorInfo[0];
					$toReturn->anchor = $anchorInfo[1];
				}
				
				//on regarde le basepath de l'element sur lequel on pointe dans le cas d'une rubrique ou un lien
				$basepaths = explode(';', $this->_headingElementInformationServices->getBaseUrl ($parameters['public_id'], $foo));
				$basePath = '';
				
				foreach ($basepaths as $path) {
					if ($path === '') {
						// Si l'un des chemins est vide, prendre le premier
						$basePath = $basepaths[0];
						break;
					}
					// Correspondance parfaite : on prend et on arrête
					if (strpos(CopixUrl::getCurrentUrl(), $path) !== false){
						$basePath = $path;
						break;
					}
					// On prend le premier domaine ayant le même protocole. Ne sera utilisé que si on ne trouve pas de correspondance parfaite
					if ($basePath == '' && strpos ($path, CopixURL::getRequestedProtocol()) !== false) {
						$basePath = $path;
					}
				}
				
				if($basePath == ''){
					$basePath = $basepaths[0];
				}
							
				$url = _ppo (parse_url ($basePath));
				if ($url->scheme){
					$toReturn->protocol = $url->scheme.'://';
				}
				$toReturn->basePath = $url->host.$url->path;

				//on met ou non un nom de script
				if (($toReturn->basePath !== null) && (($scriptName = substr ($toReturn->basePath, strrpos ($toReturn->basePath, '/'))) != '/')){
					$toReturn->scriptName = $scriptName;
					$toReturn->basePath   = substr ($toReturn->basePath, 0, strrpos ($toReturn->basePath, '/'));
				}
								
				//on récupère l'element
				$element = _ioClass ('heading|headingelementinformationservices')->get ($parameters['public_id']);
				$url_id_hei = $element->url_id_hei;

				//si l'element est de type lien on recupere la fin d'url du lien
				if ($element->type_hei == 'link'){
					$link = _ioClass ('heading|linkservices')->getByPublicId($parameters['public_id']);
					if ($link->extra_link){
						$extras = _ioClass ('heading|linkservices')->getArExtra ($link->extra_link);
						if (array_key_exists('anchor', $extras)){
							$toReturn->anchor = $extras['anchor'];
						}
					}
					
					if ($link->linked_public_id_hei != null && $element->url_id_hei == null && !$link->not_rewritten_link){
						$elementLinked = _ioClass ('heading|headingelementinformationservices')->get ($link->linked_public_id_hei);
						$parameters['public_id'] = $link->linked_public_id_hei;
						if($elementLinked->url_id_hei){
							return $this->get($dest, $parameters, $mode);
						}
					}
					// url non réécrite
					elseif((!is_null($link->href_link)) && ($link->not_rewritten_link == 1)){
						$toReturn->externUrl = str_replace ('{$copixurl:domain}', CopixURL::getRequestedDomain (), $link->href_link);
						$sPattern = '#^([A-Za-z0-9]+://)(.*)#';
						// récupération du protocol du lien, on ne le met pas si c'est une url relative
						if(stripos($link->href_link, '.') !== 0 && !preg_match ($sPattern, $link->href_link, $aRegs)) {
							$toReturn->externUrl = 'http://'.$toReturn->externUrl;
						}
						if ($element->target_hei){
							HeadingElementTargetHandler::getHandler()->addUrl ($toReturn->externUrl, $element->target_params_hei, $element->target_hei);
						}
						return $toReturn;
					}

				}
				// element de type rubrique
				elseif ($element->type_hei == 'heading'){
					// si l'element est de type heading et qu'il possede une page d'accueil, on recupere la fin d'url de cette page d'accueil.
					$heading = _ioClass ('heading|headingservices')->getByPublicId($parameters['public_id']);
					if ($heading->home_heading != null && $element->url_id_hei == null){
						try {
							$accueil = _ioClass ('heading|headingelementinformationservices')->get ($heading->home_heading);
							$parameters['public_id'] = $heading->home_heading;
							if ($accueil->url_id_hei){
								return $this->get($dest, $parameters, $mode);
							}
							
						} catch (Exception $e){
							_log ('Echec de calcul URL : La page d\'accueil de la rubrique "'.$element->caption_hei.'" (public_id : '.$parameters['public_id']. ') n\'existe pas.', 'errors');						
						}
					}
				} 

				//si on a passé directement un parametre url_id_hei, on l'utilise
				if (isset($parameters['url_id_hei'])){				
					if (CopixConfig::get ('heading|cleanupURLs')) {
						$parameters['url_id_hei'] = str_replace('-', ' ', $parameters['url_id_hei']);
					}
					$toReturn->path = explode('/', $parameters['url_id_hei']);
				}
				//sinon si url_id_hei est defini
				elseif ($url_id_hei !== null){
					if (CopixConfig::get ('heading|cleanupURLs')) {
						$url_id_hei = str_replace('-', ' ', $url_id_hei);
					}
					
					if ($url_id_hei === '/'){
						$toReturn->path = array ();
                    }else{
                         $toReturn->path = explode('/', $url_id_hei);
                    }
				}
				//sinon on ecrit l'url de base de style : index.php/cms/caption_hei/public_id
				else {
					$caption = CopixUrl::escapeSpecialChars (isset ($parameters['caption_hei']) ? $parameters['caption_hei'] : $element->caption_hei);
					//utilisation des - plutot que des _ pour séparer les mots : les tirets permettent que les moteurs considèrent l'ensemble des mots séparément, et non pas l'expression complète
					//CopixUrl::prepend remplace les ' ' par des '-'
					$caption = str_replace('_', ' ', $caption);
					$toReturn->path = array ('cms', $caption, $parameters['public_id']);
				}

				//Gestion des targets des balises <a>
				if ($element->target_hei){
					HeadingElementTargetHandler::getHandler()->addUrl ($toReturn->protocol.$toReturn->basePath.$toReturn->scriptName.'/'.implode ('/', str_replace(' ', '-', $toReturn->path)), $element->target_params_hei, $element->target_hei);
				}

				//Si on a activé XITI, et que l'élément est un document, ou ajoute au lien le marqueur XITI en externUrl
				if ($element->type_hei == 'document' && CopixModule::isEnabled('xiti')
					&& ($marker = _ioClass('xiti|headingxitiservices')->getMarkerString($element->public_id_hei)) != null) {
					$path = implode("/", $toReturn->path);
					$urlXiti = _ioClass('xiti|headingxitiservices')->getRedirectURL ($element->public_id_hei, $marker, 'T');
					$url = $toReturn->protocol.$toReturn->basePath.$toReturn->scriptName."/".implode ('/', $toReturn->path);
					if (count ($toReturn->vars) > 0){
						$url .= '?'.CopixUrl::valueToUrl (null, $toReturn->vars, false, $pForXML);
					}
					$toReturn->externUrl = $urlXiti.$url;
				}
				return $toReturn;
			}catch (Exception $e){
				$extras = array (
					'exception_type' => get_class ($e),
					'exception_message' => $e->getMessage (),
					'exception_code' => $e->getcode ()
				);
				_log ('Echec de calcul URL pour un élément qui n\'existe pas '.$parameters['public_id'], 'errors', CopixLog::ERROR, $extras);
				return false;
			}
		}

		//dans les autres cas, on ne fait rien
		return false;
	}
}