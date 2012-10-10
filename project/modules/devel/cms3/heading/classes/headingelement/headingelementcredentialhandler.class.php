<?php
/**
 * @package standard
 * @subpackage auth
 * @author		Vuidart Sylvain
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Gestion de droits du cms
 * @package cms3
 * @subpackage cms_auth
 */
class HeadingElementCredentialHandler implements ICopixCredentialHandler {
	/**
	 * Les services utilisés pour faire des recherches dans les droits
	 *
	 * @var HeadingElementInformationServices
	 */
	private $_headingServices = false;
	
	/**
	 * Contructeur
	 */
	public function __construct (){
		$this->_headingServices = new HeadingElementInformationServices ();
	}
	
	/**
	 * S'assure que l'utilisateur peut réaliser la chaine de droit donnée
	 *
	 * @param	string		$pString	La chaine de droit à tester
	 * @param 	CopixUser	$pUser		L'utilisateur dont on teste les droits
	 * @return	boolean
	 */
	public function assert ($pStringType, $pString, $pUser){	
		if ($pStringType == "cms"){
			//on vérifie que la chaine soit de type cms:read@125 ou bien cms:10@125
			if (strpos($pString, '@') !== false){
				list ($right, $public_id_hei) = explode ('@', $pString);
			} else {
				return false;
			}
			
			//on vérifie que la chaine de droit soit du type "read" ou 10
			$right = is_numeric($right) ? $right : constant("HeadingElementCredentials::".strtoupper($right));
			if (!is_numeric($right)){
				return false;
			}

			//on verifie d'abord l'heritage de l'element, il se peut que l'element herite de ses parents pour tous les groupes
			//on recupere le public id de ce parent
			$public_id_hei = _ioClass('heading|HeadingElementInformationServices')->getParentPublicIdForCredential ($public_id_hei);
		        $heis = _ioClass('heading|headingElementInformationServices');	
			foreach ($pUser->getGroups () as $handler=>$arGroupForHandler){
				$groupHandler = CopixGroupHandlerFactory::create ($handler);
				foreach ($arGroupForHandler as $id=>$groupCaption){
					$credentials = $heis->getHeadingElementCredential ($id,$handler, $public_id_hei);
					if ($credentials != null && $credentials->value_credential >= $right){
						return true;
					}
				}
			}		
		}
		
		return false;
	}
}
