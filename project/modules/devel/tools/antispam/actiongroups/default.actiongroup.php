<?php
/**
 * @package 	tools
 * @subpackage 	antispam
 * @author      Duboeuf Damien
 * @copyright	2001-20010, CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * Actions par défaut réalisées par le framework
 * @package tools
 * @subpackage antispam
 */
class ActionGroupDefault extends CopixActionGroup {
	
	/**
	 * Par défaut, on redirige vers l'url de demonstration
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault (){
		return $this->processTest();
	}

	/**
	 * Génère et renvoie une image de sécurité
	 *
	 * @return CopixActionReturn
	 */
	public function processGetImageCaptcha (){
		CopixRequest::assert ('id');
		$id       = _request ('id');
		$path     = COPIX_TEMP_PATH.'captcha/';
		$pathFile = $path . $id;
		
		CopixFile::createDir($path);
		
		$oldimages = glob($path . '*');
		foreach ($oldimages as $oldimage) {
			$datefile = @filemtime($oldimage);
			$date = date('U');
			if (($date - (($datefile !== false)?$datefile:$date)) > 60) {
				CopixFile::delete ($oldimage);
			}
		}
		
		_class('antispam|copixCaptcha')->createImage ($id, $pathFile);
		
		return _arFile($pathFile, array ('filename'=>$id.'.jpg', 'content-type'=>CopixMIMETypes::getFromExtension ('.jpg')));
	}

	/**
	 * Action de test pour afficher le formulaire
	 *
	 * @return CopixActionReturn
	 */
	public function processTest (){
		$ppo = _rPPO ();
		$ppo->antispam = CopixZone::process ('antispam|antispam');
		return _arPPO ($ppo, 'antispam|antispam.test.php');
	}
	
	/**
	 * Action permettant de valider le captcha
	 *
	 */
	public function processValid (){
		$ppo = _rPPO ();
		$ppo->isValid = _class('antispam|captcha')->create()->check ();
		return _arPPO ($ppo, 'antispam|antispam.result.php');
	}
}