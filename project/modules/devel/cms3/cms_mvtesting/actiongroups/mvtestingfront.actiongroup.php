<?php
/**
 * Affichage d'un MV Testing
 */
class ActionGroupMVTestingFront extends CopixActionGroup {
	/**
	 * On vérifie que Heading|| a lancé l'ordre d'affichage des éléments demandés.
	 *
	 * @param string $pActionName
	 */
	protected function _beforeAction ($pActionName) {
		//HeadingFront utilise Copixregistry pour indiquer les public_id dont il a demandé l'affichage
		$front = CopixRegistry::instance ()->get ('headingfront');

		if ($front !== _request ('public_id')) {
			throw new CopixCredentialException ('basic:admin');
		}
	}

	/**
	 * Affichage du MV Testing
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$public_id = _request ('public_id');
		$element = _ioClass ('MVTestingServices')->getNext ($public_id);
		if ($element->type_element == MVTestingServices::TYPE_CMS) {
			return CopixActionGroup::process ('heading|default::default', array ('public_id' => $element->value_element, 'origin_public_id' => $public_id));
		} else {
			$mvtesting = 'mvtesting=' . $public_id . '|' . $element->id_element;
			$url = CopixURL::appendToUrl (_url ($element->value_element), array ('mvtesting' => $public_id . '|' . $element->id_element));
			return _arRedirect ($url);
		}
	}
}