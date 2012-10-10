<?php
/**
 * Default action wich will process a thread.
 *
 */
class ActionGroupDefault extends CopixActionGroup{

	/**
	 * Action called to execute a Thread
	 * If errors, exceptions are serialized to be sended to clients
	 *
	 */
	function processDefault(){
		$allowed = (array)explode(',',CopixConfig::get('thread|clients'));
		if(!in_array($_SERVER['REMOTE_ADDR'],$allowed)){
			echo serialize(new CopixException('You are not the allowed to ask me to process thread'));
			return _arNone();
		}
		try{
			CopixClassesFactory::fileInclude('thread|threadmanager');
			$obj = _ioClass(_request('thread'));
			echo serialize($obj->process(unserialize(_request('params'))));
		}catch (CopixException $e){
			echo serialize($e);
		}catch(Exception $unknown){
			echo serialize($unknown);
		}
		return _arNone();
	}

}
?>