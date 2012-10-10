<?php
/**
 * @package     cms
 * @subpackage  heading
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author      VUIDART Sylvain
 */

/**
 * Webservices pour les menus
 * 
 */
class WebserviceHeadingElementMenu {
	
	/**
	 * Récupération d'un menu
	 * On envoie en paramètre l'identifiant du menu, et si necessaire un public_id
	 *
	 * @param string $pXML
	 * @return string $rXML
	 */
	public function getMenu ($pXML) {
		//Pas de DTD, le xml à envoyer est simple
		$xml = new SimpleXMLElement($pXML);
		$type_hei = (String)$xml->ID_MENU;
		$public_id_hei = is_numeric((String)$xml->PUBLIC_ID) ? (String)$xml->PUBLIC_ID : 0;
		
		if(isset($xml->LOGIN) && isset($xml->PASSWORD)){
			$login = (String)$xml->LOGIN;
			$password = (String)$xml->PASSWORD;					
			_currentUser()->login(array("login"=>$login, "password"=>$password));
		}
		
		$menu = _class ('heading|headingelementmenuservices')->getHeadingElementMenu ($public_id_hei, $type_hei);
		if ($menu->level_hem != 0) {
			$heading = _ioClass('HeadingElementInformationServices')->getParentAtLevel($public_id_hei, $menu->level_hem);
			$public_id_hei = $heading->public_id_hei;					
		}			
		$tree = _ioClass('HeadingElementInformationServices')->getTree ($menu->public_id_hem, $menu->depth_hem); 
		
		$xmlToReturn = $this->_parseTreeToXml ($tree);
		
		return $xmlToReturn;
	}
	
	/**
	 * Retourne le XML du menu
	 *
	 * @param Array $pTree
	 * @return String
	 */
	private function _parseTreeToXml ($pTree){
		$dom = new DomImplementation ();
		$doc = $dom->createDocument ();
 		$doc->encoding = 'UTF-8';

		$mainElement = $doc->createElement ('menu');
		$node = $doc->appendChild ($mainElement);
		$this->_createXmlTree($pTree, $doc, $node);

		return $doc->saveXML ();
	}
		
	/**
	 * Transforme l'arbre tableau en arbre XML
	 *
	 * @param Array $pTree
	 * @param DOMDocument $pDomDocument
	 * @param DOMElement $pParentNode
	 */
	private function _createXmlTree ($pTree, $pDomDocument, &$pParentNode) {
		foreach ($pTree as $leaf){
			//_dump($leaf);
			$element = $pDomDocument->createElement ('element');
			$node = $pParentNode->appendChild ($element);
			$node->setAttribute ('caption_hei', htmlspecialchars ($leaf->caption_hei));
			$node->setAttribute ('path', $leaf->path);
			$node->setAttribute ('credentials', "cms:".HeadingElementCredentials::READ."@".$leaf->public_id_hei);
			if ($leaf->menu_html_class_name_hei != null){
				$node->setAttribute ('menu_html_class_name_hei', $leaf->menu_html_class_name_hei);
			}
			if (isset($leaf->children)){
				$this->_createXmlTree($leaf->children, $pDomDocument, $node);
			}		
		}
	}
	
}