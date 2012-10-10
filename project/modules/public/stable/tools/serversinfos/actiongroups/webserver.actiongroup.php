<?php
/**
 * @package tools
 * @subpackage serverinfos
 * @copyright CopixTeam
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 * @author Steevan BARBOYON
 * @link http://www.copix.org
 */

/**
 * Informations sur le server web
 * 
 * @package tools
 * @subpackage serverinfos
 */
class ActionGroupWebServer extends CopixActionGroup {
	/**
	 * Indique si on doit ajouter la class alternate à un tr lors d'un remplacement
	 *
	 * @var boolean
	 */
	private $_alternate = false;
	
	/**
	 * Executé avant toute action
	 * 
	 * @param string $pActionName nom de l'action
	 * @throws Test le droit basic:admin
	 */
	public function beforeAction ($pActionName) {
		_notify ('breadcrumb', array (
			'path' => array (_url ('admin||') => _i18n ('admin|breadcrumb.admin'))
		));
		CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
	}
	
	/**
	 * Affichage du PHPInfo dans la charte courante
	 * 
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		_notify ('breadcrumb', array (
			'path' => array (
				_url ('serversinfos|webserver|') => _i18n ('module.breadcrumb.phpInfos')
			)
		));
		$ppo = new CopixPPO ();
		$ppo->TITLE_PAGE = _i18n ('webserver.titlepage');

		ob_start ();
		phpinfo ();
		$info = ob_get_contents ();
		ob_end_clean ();
		$body = preg_replace ('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
		$body = str_replace ('<table ', '<table class="CopixTable" ', $body);
		$body = str_replace ('<td class="e"', '<td', $body);
		$body = preg_replace_callback ('(<tr>)', array ($this, '_replace_callback'), $body);
		$ppo->phpinfo = $body;

		return _arPpo ($ppo, 'webserver.tpl');
	}
	
	/**
	 * Callback pour preg_replace_callback sur les <tr>
	 *
	 * @return string
	 */
	private function _replace_callback () {
		$toReturn = ($this->_alternate) ? '<tr class="alternate">' : '<tr>';
		$this->_alternate = !$this->_alternate;
		return $toReturn;
	}
}