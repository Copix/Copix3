<?php
/**
 * @package standard
 * @subpackage admin
 *
 * @author		Patrice Ferlet
 * @copyright	CopixTeam
 * @link		http://copix.org
 * @license		http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * @package standard
 * @subpackage admin
 *
 */
class PluginFireBug extends CopixPlugin {
	function afterProcess ($pAction){
		$logs = array();
		foreach (CopixConfig::instance()->copixlog_getRegistered () as $profil){
			$name= CopixConfig::instance ()->copixlog_getProfile ($profil);
			$name = $name['strategy'];
			if(strtoupper($name)=="FIREBUG"){
				$logs[]=CopixLog::getLog($profil);
			}
		}

		if ($pAction->code == CopixActionReturn::REDIRECT) {
			if (CopixSession::get ('plugin|firebug|log') === null){
				CopixSession::set ('plugin|firebug|log', $logs);
			}else{
				CopixSession::set ('plugin|firebug|log', array_merge (CopixSession::get ('plugin|firebug|log'), $logs));
			}
		}
	}

	public function beforeDisplay (&$display) {
		$jscode = array ();
		$logs   = array ();
		foreach (CopixConfig::instance()->copixlog_getRegistered () as $profil){
			$name= CopixConfig::instance ()->copixlog_getProfile ($profil);
			$name = $name['strategy'];
			if(strtoupper($name)=="FIREBUG"){
				$logs[]=CopixLog::getLog($profil);
			}
		}
		//merge last logs to new logs
		if (CopixSession::get ('plugin|firebug|log') !== null){
			$logs = array_merge (CopixSession::get ('plugin|firebug|log'), $logs);
			CopixSession::set ('plugin|firebug|log', null);
		}
		$logs = array_reverse ($logs);
		foreach ($logs as $arlog){
			foreach ($arlog as $log){
				foreach (array ('message', 'file', 'line', 'level', 'classname', 'functionname', 'type') as $var) {
					if(isset ($log->$var)) {
						$$var = $log->$var;
						unset ($log->$var);
					} else {
						$$var = null;
					}
				}
				$log->date = CopixDateTime::yyyymmddhhiissToDateTime ($log->date);
				$log->location = "$file:$line";
				$log->function = ($classname ? "$classname::" : "") . $functionname;

				switch ($level){
					case CopixLog::INFORMATION:
						$type="info";
						break;
					case CopixLog::WARNING:
					case CopixLog::NOTICE:
						$type="warn";
						break;
					case CopixLog::EXCEPTION:
					case CopixLog::ERROR:
					case CopixLog::FATAL_ERROR:
						$type="error";
						break;
					default:
						$type="log";
				}
				unset($log->level);

				$jscode[] = sprintf('_l(%s,%s,%s,%s);',
					CopixJSON::encode($type),
					CopixJSON::encode($message),
					CopixJSON::encode($log->location),
					CopixJSON::encode($log)
				);
			}
		}
		foreach (CopixConfig::instance()->copixlog_getRegistered () as $profil){
			$name= CopixConfig::instance ()->copixlog_getProfile ($profil);
			$name = $name['strategy'];
			if(strtoupper($name)=="FIREBUG"){
				CopixLog::deleteProfile ($profil);
			}
		}
		if (count($jscode)>0){
			$jscode[] = "if(window.console && console.firebug){var _l=function(t,m,l,e){console.group('[COPIX] - '+t+' - '+l);console[t](m);console.dir(e);console.groupEnd();}";
			$jscode = array_reverse ($jscode);
			$jscode[] = "}";
			CopixHTMLHeader::addJSCode (implode("\n", $jscode));
		}
	}
}
?>