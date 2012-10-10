<?php
/**
 * @package		webtools
 * @subpackage	index_search
 * @author		Duboeuf Damien
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions de recherche
 * @package		webtools
 * @subpackage	index_search
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Fonction exécutée par défaut / formulaire / résultats
	 */
	public function processDefault (){
		$theme = _request ('theme');
		if ($theme != null && in_array ($theme, explode (',', CopixConfig::get ('index_search|allowedThemes')))) {
			CopixTPL::setTheme ($theme);
		}
		if (CopixRequest::get ('criteria') !== null){
			return $this->processResults ();
		}
		return $this->processForm ();
	}
	
	/**
	 * Ecran de saisie pour le moteur de recherche
	 */
	public function processForm (){
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('index_search.search');
		$ppo->theme = _request ('theme');
		$ppo->path = _request ('path');
		$ppo->form_action = _request ('form_action', _url ('index_search||'));
		return new CopixActionReturn (CopixActionReturn::PPO, $ppo, 'search.form.tpl');
	}

	/**
	* Lance la recherche et affiche les résultats.
	*/
	public function processResults (){
		CopixRequest::assert ('criteria');
		$page = CopixRequest::getInt ('page', 1) - 1;
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE  = _i18n ('index_search.title.show');
		$ppo->currentPage = $page + 1;
		$ppo->path = _request ('path');
		$ppo->theme = _request ('theme');
		$ppo->criteria = _request ('criteria');
		$ppo->url = _request ('url', _url('index_search||'));
		return _arPPO ($ppo, 'search.show.tpl');
	}
	
	/**
	 * renvoie le fichier stoqué en version texte
	 *
	 */
	public function processGetFileText () {
		
		CopixRequest::assert ('id');
		$idObject = _request ('id');
		if (($objectIndex = _dao ('search_objectindex')-> get ($idObject)) == NULL) {
			throw new CopixException (_i18n ('index_search.exception.file_notexist'));
		}
		
		_classInclude ('Researcher');
		Researcher::testCredential ($idObject, true);
		
		_classInclude('IndexingServices');
		
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE  = _i18n ('index_search.title.show');
		$ppo->contentFile = file_get_contents (indexingServices::getTextPathFile ($idObject));
		if (!IndexingServices::isUTF8 ($ppo->contentFile)) {
		    $ppo->contentFile = utf8_encode ($ppo->contentFile);
		}
		$ppo->contentFile = str_replace("\n", '<br />', $ppo->contentFile);
		
		return _arPPO($ppo, 'search.textversion.tpl');
		
	}
	
	/**
	 * renvoie le fichier stoqué en cache
	 *
	 */
	public function processGetFileCache () {
		
		CopixRequest::assert ('id');
		$idObject = _request ('id');
		if (($objectIndex = _dao ('search_objectindex')-> get ($idObject)) == NULL) {
			throw new CopixException (_i18n ('index_search.exception.file_notexist'));
		}
		
		_classInclude ('Researcher');
		Researcher::testCredential ($idObject, true);
		
		_classInclude ('indexingServices');
		
		// Choisi l'extention du fichier
		switch ($objectIndex->objectindex_type) {
			
			case indexingServices::TYPE_PDF  :
				$extention = 'pdf';
				break;
			case indexingServices::TYPE_DOC  :
				$extention = 'doc';
				break;
			case indexingServices::TYPE_HTML :
			case indexingServices::TYPE_HTML_BRUTE :
				$extention = 'html';
				break;
			case indexingServices::TYPE_TXT  :
			case indexingServices::TYPE_TXT_BRUTE :
			default : 
				$extention = 'txt';
		}

		return _arFile (indexingServices::getCachePathFile($idObject), array ('filename'=>'archive.'.$extention));
	}
}