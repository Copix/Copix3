<?php
/**
 * Zone d'affichage d'ajout d'un article
 *
 */
class ZoneFormAddField extends CopixZone {

	public function _createContent (&$toReturn){
		$tpl = new CopixTpl ();
		$arKind = array ('text'=>'Champ texte', 'textformat:telephone'=> 'champ telephone', 'textformat:email' => 'champ email', 'rib'=>'RIB');


		$formulaire = $this->getParam ('formulaire');
		
		if ($formulaire != '') {
			$formulaire = unserialize ($formulaire);

			$objForm = $formulaire->getRemoteObject ();
	
			
			foreach ($objForm->getInputElement() as $idElement => $inputElement){
					$arField [] = $inputElement;
			}
			$tpl->assign ('arField', $arField);
		}
		$tpl->assign('arKind', $arKind);

		$toReturn = $tpl->fetch ('formaddfield.php');
		return true;
	}
}
?>