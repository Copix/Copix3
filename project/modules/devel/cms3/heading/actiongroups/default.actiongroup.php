<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain VUIDART
 */

/**
 * Front office sur les éléments de rubrique
 * 
 * @package cms
 * @subpackage heading
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Affichage d'un élément en front office, pour l'internaute.
	 */
	public function processDefault (){
		$heis = new HeadingELementInformationServices ();

		//Vérification de la présence des paramètres obligatoires
		CopixRequest::assert ('public_id');
		$public_id = _request ('public_id');

		// pour éviter les erreurs d'identifiant invalide, par exemple 123%20, on essaye de trouver l'identifiant voulu
		if (!is_numeric ($public_id)) {
			$int_public_id = intval ($public_id);
			// si c'est 0, c'est que l'id ne commence pas par des chiffres
			if ($int_public_id == 0 && substr ($public_id, 0, 1) != '0') {
				return new CopixActionReturn (CopixActionReturn::HTTPCODE, CopixHTTPHeader::get404 ());
			}
			$params = CopixRequest::asArray ();
			unset ($params['module']);
			unset ($params['group']);
			unset ($params['action']);
			$params['public_id'] = $int_public_id;
			return _arRedirect (_url ('heading||', $params));
		}
		
		//récupération des informations sur l'élément à afficher
		try {
			//verifie les dates de planification, met à jour les versions pour le public_id donné, et récupère la version publiée ou non
			$element = $heis->checkElementPlanning($public_id);
		}catch (CopixException $e){
			return _ar404 ();
		}
		
		//On vérifie le statut de l'élément (qui ne peut être QUE publié pour être affichable).
		if (intval ($element->status_hei) !== HeadingElementStatus::PUBLISHED){
			if (intval ($element->status_hei) === HeadingElementStatus::ARCHIVE) {
				_log ('Tentative d\'accès à un élément archivé : ' . $element->public_id_hei, 'errors');
				switch (CopixConfig::get('heading|seoArchivePolitic')){					
					case 301 :
						return _arRedirect(_url(CopixConfig::get('heading|seoArchivePoliticRedirect')), array('301'=>true));
					case 404 : 
						foreach (CopixHTTPHeader::get404() as $code) {
							header ($code);
						}
						break;
					case 410 :
					default:
						header('HTTP/1.0 410 Gone');
						break;
				}
				
				$ppo = new CopixPPO ();
				$typeInformations = _class('heading|headingelementtype')->getInformations($element->type_hei);
				$ppo->TITLE_PAGE = $typeInformations['caption']." non disponible";
				$ppo->captionType = $typeInformations['caption'];
				$ppo->element = $element;
				$config = CopixConfig::get ('heading|urlSearch');
				$ppo->canWrite = HeadingElementCredentials::canWrite ($element->public_id_hei);
				$ppo->search = ($config != null) ? _url ($config) . '?' . str_replace ('%CAPTION%', $element->url_id_hei ? $element->url_id_hei : $element->caption_hei, CopixConfig::get ('heading|paramsSearch')) : null;
				return _arPPO ($ppo, 'status.archive.php');
			} else {
				if (HeadingElementCredentials::canWrite ($public_id)) {
					$ppo = new CopixPPO ();
					$ppo->TITLE_PAGE = "Page non publiée";
					$ppo->element = $element;
					return _arPPO ($ppo, 'status.notpublished.php');
				}
				return _ar404 ();
			}
		}
		
		//On vérifie les droits sur l'élément
		if (! HeadingElementCredentials::canRead ($public_id)){
			throw new CopixCredentialException ("cms:read@".$public_id);
		}
		
		//Redirection 301 si l'url appelée n'est plus utilisée
		if ((!_request('origin_public_id', false)) && $element->url_id_hei && 
			//dans le cas ou on ne nettoir pas les url, on doit ajouter les - pour comparer
			((!CopixConfig::get ('heading|cleanupURLs') && !in_array(CopixUrl::getRequestedPathInfo(),
                                                                     array('/'.strtr ($element->url_id_hei, array ('-'=>'--', ' ' =>'-')),
                                                                           strtr ($element->url_id_hei, array ('-'=>'--', ' ' =>'-'))
                                                                          )
                                                                     ))
			//dans le cas ou on nettoie les adresses, les - ont été ajoutés à la création de l'url 
			|| (CopixConfig::get ('heading|cleanupURLs') && !in_array(CopixUrl::getRequestedPathInfo(),
                                                                      array('/'.$element->url_id_hei,
                                                                            $element->url_id_hei
                                                                            )
                                                                      )
               )
            )
		){
			if (! ($element->url_id_hei === '/' &&
                      (CopixUrl::getRequestedPathInfo () === '/' ||
                       CopixUrl::getRequestedPathInfo () === '' ||
                       CopixUrl::getRequestedPathInfo () === false
                       ))){
				return _arRedirect(_url('heading||', array('public_id'=>$public_id)), array('301'=>true));
			}
		}

		//Mise en place du thème graphique
		if ($theme = $heis->getTheme ($public_id, $foo)){
			CopixTpl::setTheme ($theme);
			CopixConfig::instance ()->mainTemplate = $heis->getTemplate ($public_id);
		}

		//Ajout de la balise meta robots
        if (CopixConfig::get ('heading|robotsActivated') && ($robots = $heis->getRobots ($public_id, $foo))){
    		if ($robots !== '') {
                CopixHTMLHeader::addOthers ('<meta name="robots" content="'.$robots.'" />');
            }
        }

		//On indique a CopixRegistry les éléments dont on a accepté la lecture
		CopixRegistry::instance ()->set ('headingfront', $public_id);

		//On récupère les informations sur le type d'élément a traiter pour déléguer son affichage
		$type = _ioClass ('heading|headingelementtype')->getInformations ($element->type_hei);
		return CopixActionGroup::process ($type['fronturl'], CopixRequest::asArray ());
	}
	
	/**
	 * Action d'indexation du contenu à l'attention des moteurs de recherche
	 */
	public function processIndexContent (){
		//Vérification de la présence des paramtres obligatoires
		CopixRequest::assert ('public_id');
		
		$element = _ioClass ('headingelementinformationservices')->get (_request ('public_id'));
		$typeInformations = _ioClass ('heading|headingelementtype')->getInformations ($element->type_hei);
		$content = _ioClass ($typeInformations['classid'])->getContent (_request ('public_id'));
		$inherited = false;
		$tags = implode (',', _ioClass ('heading|headingelementinformationservices')->getTags (_request ('public_id'), $inherited));
		_notify ('Content', array (
			'id'=>_request ('public_id'),
			'kind'=>'heading',
			'keywords'=>$tags,
			'title'=>$element->caption_hei,
			'summary'=>$content->summary,
			'content'=>$content->content,
			'url'=>_url ('heading|default|default', array ('public_id'=>$element->public_id_hei, 'caption_hei'=>$element->caption_hei)),
			'credentials' => array ('cms:' . HeadingElementCredentials::READ . '@' . _request ('public_id')),
			'path' => $element->hierarchy_hei
		));

		return _arNone ();
	}

	/**
	 * Action pour rediriger en 301 les URLs tombées en désuétude
	 */
	public function processRedirect(){
		if( _request('headingRedirectNewURL', false ) ){
			$url = _url('heading||',array('public_id'=> _request('headingRedirectNewURL')));
		} else {
			$url = _url( '404||');
		}
		return _arRedirect( $url, array('301'=>true) );
	}
	
	public function processRegisterCredentialHandler (){
		_currentUser()->assertCredential('basic:admin');
		$backUrl = _url('admin||');
		if(isset($_SERVER['HTTP_REFERER'])) {
		    $backUrl = $_SERVER['HTTP_REFERER'];
		}
		$handlers = CopixModule::getParsedModuleInformation ('copix|credentialhandlers','/moduledefinition/credentialhandlers/credentialhandler', array ('CopixAuthParserHandler', 'parsecredentialHandler'));
		if(isset($handlers['heading|headingelementcredentialhandler'])){
			$configurationFile = new useConfigurationFile ('credential');
			$credentialConfig = $configurationFile->get();
			$credentialConfig['heading|headingelementcredentialhandler'] = $handlers['heading|headingelementcredentialhandler'];
			$configurationFile->write($credentialConfig);
		}
		return _arRedirect($backUrl);
	}
}