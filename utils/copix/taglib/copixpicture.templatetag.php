<?php
/**
 * @package		copix
 * @subpackage	taglib
 * @author		Salleyron Julien
 * @copyright	2000-2006 CopixTeam
 * @link			http://www.copix.org
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @package		copix
 * @subpackage	taglib
 */
class TemplateTagCopixPicture extends CopixTemplatetag {
	public function process ($pContent = null) {
		$pParams = $this->getParams ();
		$size='';
		if (isset ($pParams['width'])) {
			$size .= ' width="'.$pParams['width'].'"';
		}

		if (isset ($pParams['height'])) {
			$size .= ' height="'.$pParams['height'].'"';
		}

		$src = '';
		if (isset($pParams['resource'])) {
			$src = _resource ($pParams['resource']);
		}

		if (isset($pParams['id']) && $src=='') {
			if (!CopixModule::isEnabled('pictures')) {
				throw new CopixException ('Vous devez activer le module pictures');
			}

			$arParams = array('picture_id'=>$pParams['id']);
			if (isset ($pParams['width'])) {
				$arParams['width'] = $pParams['width'];
			}
			if (isset ($pParams['height'])) {
				$arParams['height'] = $pParams['height'];
			}

			$src = CopixUrl::get('pictures|default|getImage',$arParams);
		}

		$title = '';
		if (isset($pParams['title'])) {
			$title = $pParams['title'];
		}
		$alt = $title;
		if (isset($pParams['alt'])) {
			$alt = $pParams['alt'];
		}

		return '<img src="'.$src.'" alt="'.$alt.'" title="'.$title.'" '.$size.' />';
	}
}