<?php
class ActionGroupForm extends CopixFormActionGroup {
		
	public function processTest () {
		
		if (_request('form|test') == 'exception') {
			throw new CopixFormCheckException ('ne doit pas etre egal a exception', 'test');
		}
		
		if (_request('form|test2') == 'boum') {
			throw new CopixFormCheckException (array('test'=>'boum sur test1', 'test2'=>'boum sur test2'));
		} else if (_request('form|test2') == 'boumboum') {
		    throw new CopixFormCheckException (array('truc'=>'ahaha','test'=>array('boum sur test1','doubleboum sur test1'), 'test2'=>'boum sur test2'));
		}
		
		return _arNone ();
	}
	
}
?>