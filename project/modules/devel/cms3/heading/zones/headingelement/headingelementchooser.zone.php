<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link	http://copix.org
 * @license	http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * ElementChooser : génère une fenètre de choix d'element avec arborescence.
 * Plusieurs modes disponibles : gère l'affichage de type imageChooser, documentChooser et elementChooser.
 *
 */
class ZoneHeadingElementChooser extends CopixZone {
    /**
     * Constantes des modes de l'element chooser
     * le template change en fonction du mode
     */
    const ELEMENT_CHOOSER_MOD = 0;

    const IMAGE_CHOOSER_MOD = 1;

    const DOCUMENT_CHOOSER_MOD = 2;
    
    const ARTICLE_CHOOSER_MOD = 3;
    
    const MEDIA_CHOOSER_MOD = 4;

    const CLICK_CHOOSER_MOD = 0;

    const CLICK_EXPLORER_MOD = 1;

    const DOC_ICONS_PATH = 'img/docicons/';

    private $_headingElementInformationServices;
    private $_headingElementType;
    private $_linkservices;

    protected function _createContent (& $toReturn) {
        $this->_headingElementInformationServices = _ioClass('heading|headingElementInformationServices');
        $this->_headingElementType = new HeadingElementType ();
        $this->_linkservices = new linkservices ();

        $ppo = new CopixPPO ();
        $ppo->arHeadingElements = $this->_headingElementInformationServices->getElementChooserTree (0, 1);
        $ppo->arHeadingElementTypes = $this->_headingElementType->getList ();       
        $ppo->clickMod = $this->getParam ('clickMod', self::CLICK_CHOOSER_MOD);
        $ppo->selectedIndex = $this->getParam ('selectedIndex', _request('heading', null));
        // Si aucun index n'est sélectionné, on ouvre l'arborescence jusqu'à l'élément en cours de manipulation
        if ( $ppo->selectedIndex === null && CopixSession::get ('heading', _request ('editId'))) {
            $ppo->startIndex = CopixSession::get ('heading', _request ('editId'));
        }
        // pour vérifier que l'élément demandé existe bien
        try {
            if (is_numeric($ppo->selectedIndex)) {
                $this->_headingElementInformationServices->get ($ppo->selectedIndex);
            }
            $ppo->selectedIndexExists = true;
            $ppo->askedSelectedIndex = $ppo->selectedIndex;
        } catch (Exception $e) {
            $ppo->selectedIndexExists = false;
            $ppo->askedSelectedIndex = $ppo->selectedIndex;
            $ppo->selectedIndex = null;
        }
        $type = '';
   		if ($this->getParam ('mode', self::ELEMENT_CHOOSER_MOD) != self::ELEMENT_CHOOSER_MOD) {
        	switch ($this->getParam ('mode', self::ELEMENT_CHOOSER_MOD)){
        		case self::IMAGE_CHOOSER_MOD :
        			$type = 'image';
        			break;
        		case self::DOCUMENT_CHOOSER_MOD :
        			$type = 'document';
        			break;
        		case self::ARTICLE_CHOOSER_MOD :
        			break;
        	}
            $children = $this->_headingElementInformationServices->getChildrenByType ($ppo->selectedIndex ? $ppo->selectedIndex : 0, $type);
            $ppo->arElementsPreview = _ioClass('heading|headingelementchooserservices')->orderChildren ($children);
        }

        $ppo->filters = $this->getParam ('arTypes', $type ? array ($type) : array());
        $ppo->inputElement = $this->getParam ('inputElement');
        $ppo->name = $this->getParam ('name', $ppo->inputElement);       
        $ppo->id = $this->getParam ('id', $ppo->inputElement);
        $ppo->identifiantFormulaire = $this->getParam ('identifiantFormulaire', uniqid());
        $ppo->multipleSelect = $this->getParam ('multipleSelect', true);
        $ppo->linkOnHeading = $this->getParam ('linkOnHeading', false);
        $ppo->showSelection = $this->getParam ('showSelection', true);
        $ppo->showJustHeading = $this->getParam ('showJustHeading', false);
        $ppo->showAnchor = $this->getParam ('showAnchor', false);
        $ppo->newElementOption = sizeof($ppo->filters) == 1 && $this->getParam ('newElementOption', true) && _request('editId');
        $ppo->img = $this->getParam ('img', null);
		$ppo->clickerCaption = $this->getParam ('clickerCaption', null);
        //parametre pour ouvrir directement l'elementChooser sur un element sans le selectionner
        $ppo->openElement = $this->getParam ('open', false);
        $ppo->jsonOptions = "{'filter':'" . implode(':', $ppo->filters) ."', 'anchor':".($ppo->showAnchor ? 'true' : 'false')." , 'searchIndex':'$ppo->searchIndex', 'openElement':'".$ppo->openElement."'}";
        $ppo->treeGenerator = $this->_getRootTree ($ppo);
        $ppo->arDocIcons = self::getArDocIcons();
        $ppo->selectHeading = $this->getParam ('selectHeading', true);
        $ppo->copixwindow = $this->getParam ('copixwindow', true);
        
        //tableau des elements de filtre pour le select
        $filter = '';
        if (empty($ppo->filters)) {
            $filter = $ppo->arHeadingElementTypes;
        } else if (!empty($ppo->filters) && sizeof($ppo->filters)>1) {
            $filter = $ppo->filters;
        } else {
            $filter = array();
        }
        $ppo->selectFilter = $filter;
		$ppo->fixed = $this->getParam ('fixed', true);
		$ppo->canDrag = $this->getParam ('canDrag', true);

        $toReturn = $this->_usePPO ($ppo, $this->_getTemplate($this->getParam ('mode', self::ELEMENT_CHOOSER_MOD)));
        return true;
    }

    /**
     * Retourne le code javascript de generation de l'element racine de l'arbre
     *
     * @param Object $options
     * @return String
     */
    private function _getRootTree ($options) {
        //$children = _class('heading|headingelementinformationservices')->getElementChooserTree ();
        $children = $options->arHeadingElements;
        $arHeadingElementTypes = _class ('heading|HeadingElementType')->getList ();

        $selectedPath = array();
        try {
            $selectedElement = $this->_headingElementInformationServices->get ($options->selectedIndex ? $options->selectedIndex : 0);
            $selectedPath = explode('-', $selectedElement->hierarchy_hei);
        } catch (HeadingElementInformationNotFoundException $e) {
            _log ($e->getMessage(), "errors", CopixLog::WARNING);
        }
        if ($selectedPath == array(0 => '0') && $options->startIndex) {
            try {
                $selectedElement = $this->_headingElementInformationServices->get ($options->startIndex);
                $selectedPath = explode('-', $selectedElement->hierarchy_hei);
            } catch (HeadingElementInformationNotFoundException $e) {

            }
        }
        
        $openPath = array();
        if ($options->openElement){        
	        try {
	            $openElement = $this->_headingElementInformationServices->get ($options->openElement);
	            $openPath = explode('-', $openElement->hierarchy_hei);
	        } catch (HeadingElementInformationNotFoundException $e) {
	            _log ("Parametre 'open' passé en paramètre d'elementChooser invalide. ".$e->getMessage(), "errors", CopixLog::WARNING);
	        }
        }

        $toReturn = '';
        if (_ioClass('heading|headingelementchooserservices')->checkFilter($children, $options->filters)) {
            $toReturn = "rootNodeArray = new Array();\n";

            //on ordonne les enfants selon leur display_order_hei
            $order = array();
            foreach ($children as $child) {
                $order[$child->display_order_hei] = $child;
            }
            ksort($order);
            foreach ($order as $child) {
              	if (_ioClass('heading|headingelementchooserservices')->checkFilter (array($child), $options->filters)){	
              		$hasChildren = false;
            		if ($options->showAnchor && $child->type_hei == 'page') {
	                    $arAnchors = _class ('portal|pageservices')->getPageAnchors ($child->id_helt);
	                    $hasChildren = !empty($arAnchors);
	                } else if ($child->type_hei == "heading"){
	                	//Pour économiser du temps de traitement, on considère que toutes les rubriques disposent d'enfants
	                	$hasChildren = true;
	                }          
	                	$toReturn .= "rootNodeArray.push({'public_id_hei':'" . $child->public_id_hei . "','open':" . (in_array($child->public_id_hei, $openPath) || in_array($child->public_id_hei, $selectedPath) ? 'true' : 'false') . ",'caption_hei':'" . addslashes($child->caption_hei) . "'" . ($hasChildren ? ",'children':true" : '') . ($child->type_hei != "heading" ? ",'icon':'"._resource ($arHeadingElementTypes[$child->type_hei]['icon'])."'" : '') . ", 'type_hei':'" . $child->type_hei . "', 'selectedIndex':'$options->selectedIndex'});\n";
              	}
            }
            $toReturn .= "createTree (tree" . $options->identifiantFormulaire . ", tree" . $options->identifiantFormulaire . ".root, rootNodeArray, '" . $options->identifiantFormulaire ."', ".$options->jsonOptions.");";
        }
        return $toReturn;
    }

    private function _getTemplate($pMode) {
        switch ($pMode) {
            case self::DOCUMENT_CHOOSER_MOD :
                return 'document|headingelementdocumentchooser.php';
            case self::IMAGE_CHOOSER_MOD :
                return 'images|headingelementimagechooser.php';
            case self::ARTICLE_CHOOSER_MOD : 
            	return 'articles|headingelementarticlechooser.php';
            case self::MEDIA_CHOOSER_MOD : 
            	return 'medias|headingelementmediachooser.php';
            default:
                return 'heading|headingelement/headingelementchooser.php';
        }
    }

    public static function getArDocIcons() {
        return array(
                'doc'=>self::DOC_ICONS_PATH.'doc.png',
                'docx'=>self::DOC_ICONS_PATH.'doc.png',
                'gif'=>self::DOC_ICONS_PATH.'gif.png',
                'jpeg'=>self::DOC_ICONS_PATH.'jpeg.png',
                'png'=>self::DOC_ICONS_PATH.'png.png',
                'bmp'=>self::DOC_ICONS_PATH.'bmp.png',
                'ppt'=>self::DOC_ICONS_PATH.'odp.png',
                'xls'=>self::DOC_ICONS_PATH.'xls.png',
                'pdf'=>self::DOC_ICONS_PATH.'pdf.png',
        		'swf'=>self::DOC_ICONS_PATH.'swf.gif',
        		'zip'=>self::DOC_ICONS_PATH.'zip.gif',
        		'exe'=>self::DOC_ICONS_PATH.'exe.gif'
        );
    }
}