<?php
/**
 * @package tools
 * @subpackage breadcrumb
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Ecoute l'événement breadcrumb
 * 
 * @package tools
 * @subpackage breadcrumb
 */
class ListenerBreadCrumb extends CopixListener {
	/**
	 * Identifiant du fil d'ariane
	 *
	 * @var string
	 */
	private $_id = null;
	
	/**
	 * Ecoute l'événement breadcrumb
	 *
	 * @param CopixEvent $pEvent Evénement
	 * @param CopixEventResponse $pEventResponse Réponse à l'événement
	 */
	public function processBreadCrumb ($pEvent, $pEventResponse) {
		// paramètre identifiant
		$this->_id = $pEvent->getParam ('id', 'default');
				
		// paramètre reset
		if ($pEvent->getParam ('reset')) {
			_ioClass ('breadcrumb|breadcrumb')->reset ($this->_id);
		}
		
		// paramètre showlastlink
		if ($show = $pEvent->getParam ('showlastlink')) {
			_ioClass ('breadcrumb|breadcrumb')->setShowLastLink ($show, $this->_id);
		}
		
		// paramètre callback
		if ($callback = $pEvent->getParam ('callback')) {
			$params = $pEvent->getParam ('callbackparams', array ());
			$this->_complexPath (call_user_func_array ($callback, $params));
		}
		
		// paramètre path
		if (($path = $pEvent->getParam ('path')) !== null) {
			$this->_path ($path);
		}
		
		// paramètre complexpath
		if (($path = $pEvent->getParam ('complexpath')) !== null) {
			$this->_complexPath ($path);
		}
	}
	
	/**
	 * Ajoute les chemins dans le fil d'ariane
	 *
	 * @param array $pLinks Tableau de liens, forme CopixURL => Label (mettre un # devant le CopixURL pour ne pas faire un lien mais juste afficher le label)
	 * @throws ModuleBreadcrumbException Le paramètre $pLinks n'est pas un tableau, code ModuleBreadcrumbException::INVALID_PATH
	 */
	private function _path ($pLinks) {
		if (!is_array ($pLinks)) {
			throw new ModuleBreadcrumbException (_i18n ('breadcrumb|module.error.invalidPath'), ModuleBreadcrumbException::INVALID_PATH);
		}
		
		foreach ($pLinks as $url => $caption) {
			$showLink = true;
			if (substr ($url, 0, 1) == '#') {
				$url = substr ($url, 1);
				$showLink = false;
			}
			$link = new BreadCrumbLink ($url, $caption, $showLink);
			_ioClass ('breadcrumb|breadcrumb')->add ($link, $this->_id);
		}
	}
	
	/**
	 * Ajoute les chemins dans le fil d'ariane
	 *
	 * @param array $pLinks Tableau complexe de liens, forme 0 => array ('url' => CopixURL, 'showlink' => boolean, 'extras' => string, 'caption' => string)
	 * @throws ModuleBreadcrumbException Le paramètre $pLinks n'est pas un tableau, code ModuleBreadcrumbException::INVALID_COMPLEXPATH
	 */
	private function _complexPath ($pLinks) {
		if (!is_array ($pLinks)) {
			throw new ModuleBreadcrumbException (_i18n ('breadcrumb|module.error.invalidComplexPath'), ModuleBreadcrumbException::INVALID_COMPLEXPATH);
		}
		
		foreach ($pLinks as $link) {
			$url = (array_key_exists ('url', $link)) ? $link['url'] : '#';
			$showlink = (array_key_exists ('showlink', $link)) ? $link['showlink'] : true;
			$extras = (array_key_exists ('extras', $link)) ? (array)$link['extras'] : array ();
			$caption = (array_key_exists ('caption', $link)) ? $link['caption'] : $url;
			$link = new BreadCrumbLink ($url, $caption, $showlink, $extras);
			_ioClass ('breadcrumb|breadcrumb')->add ($link, $this->_id);
		}
	}
}