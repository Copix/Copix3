<?php
/**
 * @package cms
 * @subpackage cms_mvtesting
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Actions d'administration des mv testing
 * 
 * @package cms
 * @subpackage cms_mvtesting
 */
class ActionGroupAdmin extends ActionGroupAbstractAdminHeadingElement {
	/**
	 * Formulaire de modification de l'élément
	 *
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		$ppo = _ppo ();
		$ppo->editedElement = $this->_getEditedElement ();
		$ppo->editId = _request ('editId');

		if ($ppo->editedElement->public_id_hei) {
			$ppo->TITLE_PAGE = 'Modification d\'un MV Testing';
		} else {
			$ppo->TITLE_PAGE = 'Création d\'un MV Testing';
		}

		if (count ($ppo->editedElement->elements) == 0) {
			$ppo->editedElement->elements[0] = _ioClass ('MVTestingServices')->getNewElement ();
		}
		$ppo->random = 100 / count ($ppo->editedElement->elements);
		if (is_float ($ppo->random)) {
			$ppo->random = number_format ($ppo->random, 2, ',', ' ');
		}

		if (_request ('errors') == 'true') {
			$errors = _validator ('MVTestingValidator')->check ($ppo->editedElement);
			if ($errors instanceof CopixErrorObject) {
				$ppo->errors = $errors->asArray ();
			}
		}
		return _arPpo ($ppo, 'mvtesting.form.php');
	}
	
	/**
	 * Sauvegarde de la page
	 *
	 * @return CopixActionReturn
	 */
	public function processValid () {
		$element = $this->_getEditedElementFromRequest ();
		$editId = _request ('editId');

		// test de la validité du mv testing
		if (_validator ('MVTestingValidator')->check ($element) instanceof CopixErrorObject) {
			return _arRedirect (_url ('cms_mvtesting|admin|edit', array ('editId' => $editId, 'errors' => 'true')));
		}

		// sauvegarde du mv testing
		if ($element->id_mvt === null) {
			_ioClass ('MVTestingServices')->insert ($element);
		} else if ($element->status_hei == HeadingElementStatus::DRAFT) {
			_ioClass ('MVTestingServices')->update ($element);
		} else {
			_ioClass ('MVTestingServices')->version ($element);
		}

		$params = array ('editId' => $editId, 'result' => 'saved', 'selected' => array ($element->id_helt . '|' . $element->type_hei));

		// notification de publication
		if (CopixRequest::getBoolean ('publish')) {
			_ioClass ('HeadingElementInformationServices')->publishById ($element->id_helt, $element->type_hei);
			if (CopixUserPreferences::get ('cms_mvtesting|mvtestingNotification') == '1') {
                $params['prevaction'] = 'publish';
            }
		}

	  	return _arRedirect (_url ('heading|element|finalizeEdit', $params));
	}

	/**
	 * Ajoute un élément à visualiser au mv testing
	 *
	 * @return CopixActionReturn
	 */
	public function processAddElement () {
		$element = $this->_getEditedElementFromRequest ();
		$element->elements[] = _ioClass ('MVTestingServices')->getNewElement ($element->id_mvt);
		return _arRedirect (_url ('cms_mvtesting|admin|edit', array ('editId' => _request ('editId'))));
	}

	/**
	 * Supprime un élément du mv testing
	 *
	 * @return CopixActionReturn
	 */
	public function processDeleteElement () {
		$element = $this->_getEditedElementFromRequest ();
		$index = _request ('index');
		if (!isset ($element->elements[$index])) {
			throw new CopixException ('L\'élément à visualiser d\'index "' . $index . '" n\'existe pas.');
		}
		unset ($element->elements[$index]);
		$element->elements = array_values ($element->elements);
		return _arRedirect (_url ('cms_mvtesting|admin|edit', array ('editId' => _request ('editId'))));
	}

	/**
	 * Retourne le mv testing en cours d'édition, avec les infos prises depuis la validation du formulaire de modification
	 *
	 * @return CopixActionReturn
	 */
	private function _getEditedElementFromRequest () {
		$toReturn = $this->_getEditedElement ();
		_ppo (CopixRequest::asArray ('caption_hei', 'choice_mvt', 'conserve_mvt'))->saveIn ($toReturn);
		$toReturn->elements = array ();
		$index = 0;

		$service = _ioClass ('mvtestingservices');
		while (CopixRequest::exists ('element' . $index . '_type')) {
			$toReturn->elements[$index] = $service->getNewElement ($toReturn->id_mvt);
			$toReturn->elements[$index]->type_element = _request ('element' . $index . '_type');
			$toReturn->elements[$index]->value_element = ($toReturn->elements[$index]->type_element == MVTestingServices::TYPE_CMS) ? _request ('element' . $index . '_cms') : _request ('element' . $index . '_module');
			$toReturn->elements[$index]->percent_element = _request ('element' . $index . '_percent');
			$toReturn->elements[$index]->show_element = _request ('element' . $index . '_show');
			$index++;
		}
		return $toReturn;
	}
}