<?php
/**
 * @package     standard
 * @subpackage  authextend
 * @author      Duboeuf Damien
 * @copyright   CopixTeam
 * @link        http://copix.org
 * @license     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions réalisées par le framework
 * @package standard
 * @subpackage authextend
 */
class ActionGroupDefault extends CopixActionGroup {
	
	public function processGetPictureValue () {
		
		$id_user    = _request ('id_user');
		$id_handler = _request ('id_handler');
		$id_extend  = _request ('id_extend');
		
		$value = _ioDAO ('dbuserextendvalue')->get ($id_extend, $id_user, $id_handler);
		$param = _ioDAO ('dbuserextend')->get ($id_extend);
		if ($param && $param->type == AuthExtend::TYPE_PICTURE && $value) {
			
			$name = unserialize ($value->value);
			$w = _request ('width', 0);
			$h = _request ('height', 0);
			$pathCache = COPIX_TEMP_PATH.'cache'.DIRECTORY_SEPARATOR.'authextend'.DIRECTORY_SEPARATOR.$name.'/'.$w.'.'.$h.'.'.$name;
			$content = '';
			
			
			if (file_exists ($pathCache)) {
				$content = CopixFile::read ($pathCache);
			} else {
				$image = CopixImage::load (COPIX_VAR_PATH.'authextend/'.$name);
				if ($image) {
					if ($w || $h) {
						$image->resize ($w , $h, true);
					}
					
					// Crée un fichier de cache
					//var_dump ($image->getContent ());
					$content = $image->getContent ();
					CopixFile::write ($pathCache, $content);
					
				}
			}
			$mimetypes = array_flip (_ioClass ('authextend|authextend')->arPictTypemimeList);
			header('Content-type: '.$mimetypes[CopixFile::extractFileExt ($name)]);
			echo $content;
		
		}
		return _arNone ();
	}
	

}
?>