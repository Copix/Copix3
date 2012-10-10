<?php
/**
 * @package copix
 * @subpackage taglib
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://www.copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Créé un bouton via un template
 *
 * @package copix
 * @subpackage taglib
 */
class TemplateTagButton extends CopixTemplateTag {
	/**
	 * Retourne le libellé pris dans caption, captioni18n ou la valeur par défaut
	 *
	 * @param string $pDefault Valeur par défaut
	 * @return string
	 */
	private function _getCaption ($pDefault = null) {
		$params = $this->getParams ();
		if (isset ($params['caption'])) {
			return $params['caption'];
		} else if (isset ($params['captioni18n'])) {
			return _i18n ($params['captioni18n']);
		}
		return $pDefault;
	}

	/**
	 * Retourne l'HTML
	 *
	 * @param string $pContent Contenu de base
	 */
	public function process ($pContent = null) {
		$action = $this->getParam ('action');
		if ($action != null) {
			$img = (CopixResource::exists ('img/tools/' . $action . '.png')) ? _resource ('img/tools/' . $action . '.png') : null;
			$caption = $alt = $title = $this->_getCaption (_i18n ('copix:common.buttons.' . $action));
		} else {
			$paramImg = $this->getParam ('img');
			$img = (CopixResource::exists ($paramImg)) ? _resource ($paramImg) : $paramImg;
			$caption = $this->_getCaption ();
			$alt = $this->getParam ('alt', $caption);
			$title = $this->getParam ('title', $alt);
		}

		$id = $this->getParam ('id', uniqid ('button'));
		$extra = $this->getParam ('extra', '');

		$url = $this->getParam ('url');
		$submit = $this->getParam ('submit');
		if ($url != null) {
			$type = 'button';
			if ($this->getParam ('confirm') != null) {
				CopixHTMLHeader::addJSDOMReadyCode ("$ ('" . $id . "').addEvent ('click', function () { if (confirm ('" . str_replace ("'", "\'", $this->getParam ('confirm')) . "')) { document.location = '" . _url ($url) . "' } })");
			} else {
				CopixHTMLHeader::addJSDOMReadyCode ("$ ('" . $id . "').addEvent ('click', function () { document.location = '" . _url ($url) . "' })");
			}
		} else if ($submit != null) {
			$type = 'button';
			CopixHTMLHeader::addJSDOMReadyCode ("$ ('" . $id . "').addEvent ('click', function () { $ ('" . $submit . "').fireEvent ('submit'); $ ('" . $submit . "').submit (); })");
		} else if ($this->getParam ('type') != null) {
			$type = $this->getParam ('type');
		} else {
			$type = $this->getParam ('type', 'submit');
		}

		$tpl = new CopixTPL ();
		$tpl->assign ('id', $id);
		$tpl->assign ('img', $img);
		$tpl->assign ('alt', $alt);
		$tpl->assign ('title', $title);
		$tpl->assign ('caption', $caption);
		$tpl->assign ('type', $type);
		$tpl->assign('extra', $extra);
		return $tpl->fetch ('default|taglib/button.php');
	}
}