<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Sylvain Vuidart
 */

/**
 * Export des différents éléments du CMS
 *
 * @package cms
 * @subpackage heading
 */
class ActionGroupExport extends CopixActionGroup{
	
	const EXPORT_PATH = "export/";
	
    protected function _beforeAction ($pAction) {
    	if (! (CopixAuth::getCurrentUser ()->testCredential ('basic:admin') || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'._request ('heading', 0)) || CopixAuth::getCurrentUser ()->testCredential ('cms:write@'._request ('inheading', 0)))) {
	   		throw new CopixCredentialException ('basic:admin');
    	}
		CopixPage::add ()->setIsAdmin (true);
		if (CopixConfig::get('heading|usecmstheme') && CopixTheme::getInformations(CopixConfig::get('heading|cmstheme')) !==false){
        	CopixTPL::setTheme (CopixConfig::get('heading|cmstheme'));
        } 
		_ioClass ('HeadingElementInformationServices')->breadcrumbAdmin ();
    }
	
	public function processExport(){
		if (!($elements = CopixSession::get('heading|export'))){
			// Valeurs par défaut
			$options = _Ppo(array (
				'status_hei' => '',
				'caption_hei' => '',
				'type_hei' => '',
				'nbrParPage' => 20,
				'page' => CopixRequest::getInt ('page', 1),
				'inheading' => CopixRequest::getInt ('heading', 0),
				'resolve_public_id' => '',
				'content' => '',
				'sort' => 'caption_hei',
				'sortOrder' => 'DESC',
				'url_id_hei' => ''
			));
			$sessionOptionName = 'heading|advancedSearch|options';
			// Récupération des options depuis la session ou $_POST
			if (CopixSession::exists ($sessionOptionName)) {
				$options = _sessionGet ($sessionOptionName);
				$options->page = CopixRequest::getInt('page', $options->page);
			}
			$isSubmitted = !(array_keys (CopixRequest::asArray ()) === array('module', 'group', 'action') || array_keys (CopixRequest::asArray ()) === array('module', 'group', 'action', 'inheading', 'page'));
			if ($isSubmitted) {
				foreach ($options as $key => $value) {
					if (is_bool ($value)) {
						$options->$key = CopixRequest::exists ($key);
					} elseif (is_int($value)) {
						$options->$key = CopixRequest::getInt ($key, $value);
					} else {
						$options->$key = _request ($key, $value, false);
					}
				}
				if(!CopixRequest::exists('page')){
					$options->page = 1;
				}
			}
			if ($options->nbrParPage == 0) {
				$options->nbrParPage = 20;
			}
			_sessionSet ($sessionOptionName, $options);

			$elements = _ioClass ('heading|advancedsearchservices')->search ($options, 0, false);
			$elements = $elements['results'];
			_sessionSet ('heading|export', $elements);
		}
		if (!($zipName = CopixSession::get('heading|export|zip'))){
			CopixFile::createDir(COPIX_CACHE_PATH.self::EXPORT_PATH);
			$zipName = COPIX_CACHE_PATH.self::EXPORT_PATH.'CMSexport'.uniqid().'.zip';
			_sessionSet ('heading|export|zip', $zipName);
		}
		
		$element = $elements[_request('key')];
		if ($element->status_hei != HeadingElementStatus::DELETED){
			HeadingElementServices::call ($element->type_hei, 'export', array($zipName, $element));
		}
		if (isset($elements[_request('key') +1])){
			return _arString($elements[_request('key') +1]->caption_hei ? $elements[_request('key') +1]->caption_hei : '(sans titre)');
		} else {
			return _arString($element->caption_hei ? $element->caption_hei : '(sans titre)');
		}
	}
	
	public function processGetZip (){
		if (!($zipName = CopixSession::get('heading|export|zip'))){
			return _arNone();
		}
		_sessionSet ('heading|export|zip', null);
		_sessionSet ('heading|export', null);
		return _arFile($zipName, array ('content-disposition' => 'attachment', 'filename' => 'exportCopixCMS3'.date('Ymd').'.zip'));
	}
	
	public function processCancelExport (){
		_sessionSet ('heading|export|zip', null);
		_sessionSet ('heading|export', null);
		return _arNone();
	}
	
}