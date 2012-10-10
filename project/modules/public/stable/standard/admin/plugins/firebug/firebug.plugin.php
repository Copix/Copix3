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
    function afterProcess (& $pAction){
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
                $date = CopixDateTime::yyyymmddhhiissToDateTime ($log->date);
                $log->message = str_replace ("'","\'",$log->message);
                $log->message = explode ("\n",$log->message);
                $tmp = array ();
                foreach ($log->message as $mess){
                    $lines = explode("\r",$mess);
                    foreach ($lines as $line){
                        $tmp[] = $line;
                    }
                }
                $message = "var mess = '';\n";
                foreach ($tmp as $line){
                    $message .= 'mess+=\''.$line.'\'+"\n";'."\n";
                }

                switch ($log->level){
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
                	
                $jscode[]= "\n
try {
	$message
	_title = '[COPIX] - in file:".$log->file." line:".$log->line." in ".$log->classname.'::'.$log->functionname."';
	var _copixobj = new Object();
	_copixobj.date = '$date';	
	_copixobj.type = '$log->type';
	_copixobj.user = '$log->user';
	console.group(_title);
	console.$type(mess);
	console.dir(_copixobj)
	console.groupEnd();
}catch(e){
}
\n";
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
            $jscode = array_reverse ($jscode);
            foreach ($jscode as $js){
                CopixHTMLHeader::addJSCode ($js);
            }
        }
    }
}
?>