<?php
/**
 * @package     cms3
 * @subpackage  portal
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      Selvi ARIK
 */

/**
 * Portlet RSS Reader
 * @package     cms3
 * @subpackage  cms_rss_reader
 */
class PortletRssReader extends PortletRenderHTML {

	const DEFAULT_HTML_DISPLAY_TEMPLATE = 'cms_rss_reader|portlettemplates/list.portlet.php';

	/**
	 * Initialisation des paramètres de la portlet
	 */
	public function __construct (){
		//seuls les texts sont autorisés dans cette portlet
		parent::__construct ();
		$this->type_portlet = "PortletRssReader";
	}

    /**
     * Retorune le fichier contenant les visuels
     * @return string
     */
	protected function getXmlPath(){
		return CopixTpl::getFilePath("cms_rss_reader|portlettemplates/portlettemplates.xml");
	}

    /**
	 * Rendu pour le mode HTML
	 *
	 * @param string $pRendererContext le contexte de rendu
	 * @return string
	 */
	private function _renderHTML ($pRendererContext){
        if (in_array ($pRendererContext, array (RendererContext::DISPLAYED, RendererContext::DISPLAYED_ADMIN)) 
        || $this->getEtat () == self::DISPLAYED) {
			return $this->_renderHTMLDisplay ();
		}
        else {
			return $this->_renderHTMLUpdate ();
		}
	}

	/**
	 * Retourne le contenu du template de la portlet en rendu update
	 *
	 * @return String
	 */
	protected function _renderHTMLUpdate () {      
		if (!$this->getOption ('template', false)) {
			$this->setOption ('template', self::DEFAULT_HTML_DISPLAY_TEMPLATE);
		}

        // ajout de l'option "Visuel du bloc"	
		$theme = isset($this->public_id_hei) ? _ioClass('heading|headingelementinformationservices')->getTheme($this->public_id_hei, $foo) : null;
		$xmlPath = CopixTpl::getFilePath ('cms_rss_reader|portlettemplates/portlettemplates.xml', $theme);
		$toReturn = CopixZone::process ('portal|PortletMenu', array ('portlet' => $this, 'module' => 'cms_rss_reader', 'xmlPath' => $xmlPath));

        // ajout des option de la portler (titre)
		$toReturn .= CopixZone::process ('cms_rss_reader|RssReaderOptionMenu', array ('portlet' => $this));

        $tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);
        $tpl->assign ('feeds',  $this->getOption ('feeds', array ()));
        $toReturn .= $tpl->fetch ($this->getTemplateElementEditor ());
		return $toReturn;
	}

	/**
	 * Retourne le contenu du template de la portlet en rendu display
	 *
	 * @return String
	 */
	protected function _renderHTMLDisplay ($pIsAdmin) {
		$tpl = new CopixTpl ();
		$tpl->assign ('portlet', $this);
		$params = new CopixParameterHandler ();

        // Nombre d'éléments à afficher
        $iNbItem = $this->getOption ('nb_item', 4);
        // récupération des flux rss
        $aFeeds = $this->getOption ('feeds', array ());

        $oIterator = new FeedItemIterator ();
        // on passe en revue les flux RSS
        foreach ($aFeeds as $feed) {
            $oReader = new FeedReader ($feed);
            while($oReader->valid ()) {
                $oIterator->addItem ($oReader->current ());
                $oReader->next();
            }
        }
        $oIterator->sort ();
        //$oIterator->rewind ();
        $params->setParams (array ('feeds_iterator' => $oIterator));
        $tpl->assign('feeds_iterator', $oIterator);

		return $tpl->fetch ($this->getOption ('template', self::DEFAULT_HTML_DISPLAY_TEMPLATE));
	}

    protected function getTemplateElementEditor () {
		return 'cms_rss_reader|portletrssreader.form.php';
	}
}