<?php
/**
 * @package cms
 * @subpackage heading
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 */

/**
 * Informations sur un élément
 *
 * @package cms
 * @subpackage heading
 */
class ZoneRenderElement extends CopixZone {
	/**
	 * Création du contenu
	 *
	 * @param string $pToReturn Code HTML à retourner
	 * @return boolean
	 */
	protected function _createContent (&$pToReturn) {
		$public_id = $this->getParam ('public_id');

		try {
			$element = _ioClass ('headingelementinformationservices')->get ($public_id);
			$infos = _ioClass ('heading|headingelementtype')->getInformations ($element->type_hei);
			$editedElement = _ioClass ($infos['classid'])->getByPublicId ($public_id);
			if (!method_exists ($editedElement, 'render')) {
				throw new CopixException ('L\'élément "' . $public_id . '" de type "' . $element->type_hei . '" n\'a pas la méthode render.');
			}

		} catch (Exception $e) {
			// uniquement un log d'erreur si l'élément n'est pas trouvé, ou qu'il y a une exception
			if (!$this->getParam ('exception', true)) {
				$extras = array (
					'exception_public_id' => $public_id,
					'exception_type' => get_class ($e),
					'exception_message' => $e->getMessage ()
				);
				_log ($e->getMessage (), 'errors', CopixLog::ERROR, $extras);
				return true;

			// on laisse les exceptions passer
			} else {
				throw $e;
			}
		}

		$mode = $this->getParam ('mode', RendererMode::HTML);
		$context = $this->getParam ('context', RendererContext::DISPLAYED);
		$pToReturn = $editedElement->render ($mode, $context);

		return true;
	}
}