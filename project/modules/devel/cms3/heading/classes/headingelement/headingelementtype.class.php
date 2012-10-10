<?php
/**
 * @package    cms
 * @subpackage heading
 * @author     Gérald CROËS, Alexandre JULIEN
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Opérations sur les types d'éléments
 * @package cms
 * @subpackage heading
 */
class HeadingElementType {
	
	/**
	 * Récupération de la liste des types d'éléments qui sont déclarés et installés sous la forme d'un tableau de tableau
	 * 
	 * @return array
	 */ 
	public function getList() {
		static $xml = false;
		if ($xml === false){
			$xml = CopixModule::getParsedModuleInformation (
								"heading_ElementTypes",
								"/moduledefinition/registry/entry[@id='HeadingElement']/*",
								array ($this, 'getTypesFromXML'));
		}
		uasort ($xml, array ($this, '_sortList'));
		return $xml;
	}

	/**
	 * Callback pour uasort de getList
	 *
	 * @param array $pA Elément A
	 * @param array $pB Elément B
	 * @return boolean
	 */
	private function _sortList ($pA, $pB) {
		return $pA['caption'] > $pB['caption'];
	}
	
	/**
	 * Retourne le libellé du type de contenu d'identifiant $id
	 *
	 * @param string $id identifiant du type
	 * @return string libellé
	 */
	public function getCaption ($pId) {
		$data = self::getInformations ($pId);
		return $data['caption'];
	}
	
	/**
	 * Retourne les informations sur le type $pId donné
	 *
	 * @return array
	 */
	public function getInformations ($pId){
		$arData = self::getList ();
		if (!array_key_exists ($pId, $arData)){
			throw new CopixException ('Element de type '.$pId.' non trouvé');
		}
		return $arData[$pId];
	}
	
	/**
	 * Extraction des informations sur les types d'éléments depuis le registre du module.xml
	 * @param SimpleXmlElement $moduleNode le noeud qui contient la liste des types au format XML
	 * @return array 
	 */
	public function getTypesFromXml ($moduleNode){
		$arData = array ();
		foreach ($moduleNode as $moduleName=>$moduleNodes) {
			foreach ($moduleNodes as $node){
				if ($node->getName () === 'type') {
					//l'identifiant de l'élément
					$id = _toString ($node['id']);

					//Le libellé de l'élément
					if (_toString ($node['caption']) !== '') {
						$arData[$id]['caption'] = _toString ($node['caption']);
					} elseif (_toString ($node['captioni18n']) !== '') {
						$arData[$id]['caption'] = _i18n (_toString($node['captioni18n']));
					}
					
					//L'image du type d'élément
					if (_toString ($node['image']) !== ''){
						$arData[$id]['image'] = _toString ($node['image']);
					}else{
						$arData[$id]['image'] = null;
					}
				
					//L'icone du type d'élément
					if (_toString ($node['icon']) !== ''){
						$arData[$id]['icon'] = _toString ($node['icon']);
					}else{
						$arData[$id]['icon'] = $arData[$id]['image'];
					}
					
					//Lien vers l'url d'administration
					$arData[$id]['adminurl'] = _toString ($node['adminurl']);
					$arData[$id]['fronturl'] = _toString ($node['fronturl']);
					$arData[$id]['canrss'] = isset($node['canrss']) && _toString ($node['canrss'] == 'true');
					$aAdminURL = explode('|', $arData[$id]['adminurl']);
					$arData[$id]['module'] = _toString ($aAdminURL[0]);
					
					
					//Lien vers la classe de l'élement
					if (_toString ($node['classid']) !== '') {
						$arData[$id]['classid'] = _toString ($node['classid']);
					} else {
						$arData[$id]['classid'] = null;
					}
				}
			}
		}
		return $arData;
	} 
}