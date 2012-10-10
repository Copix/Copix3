<?php
/**
 * @package    standard
 * @subpackage test
 * @author     Guillaume Perréal
 * @copyright  2001-2008 CopixTeam
 * @link       http://copix.org
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

class Test_CopixJSWidgetTest extends CopixTest {

	public function testRaw_() {
		$js = new CopixJSWidget();
		$js->raw_('goublizajklm"èéç(_èç"');
		$this->assertEquals('goublizajklm"èéç(_èç";', _toString($js));
	}
	
	public function test__get() {
		$js = new CopixJSWidget();
		$x = $js->x;
		$this->assertEquals('', _toString($js));
		$this->assertEquals('x', _toString($x));
	}

	public function test__set1() {
		$js = new CopixJSWidget();
		$js->x = 5;
		$this->assertEquals('var x = 5;', _toString($js));
	}

	public function test__set2() {
		$js = new CopixJSWidget();
		$y = $js->y;
		$js->y = 8;
		$this->assertEquals('y = 8;', _toString($js));
	}	
	
	public function test__isset() {
		$js = new CopixJSWidget();
		$y = $js->y;
		$this->assertTrue(isset($js->y));
		$this->assertFalse(isset($js->x));
	}

	public function test__call() {
		$js = new CopixJSWidget();
		$js->func("5");
		$this->assertEquals('func("5");', _toString($js));
	}

	public function testfunction_1() {
		$js = new CopixJSWidget();
		$js->function_('f',null,'return 5;');
		$this->assertEquals('function f(){return 5;};', _toString($js));
	}


	public function testfunction_2() {
		$js = new CopixJSWidget();
		$f = $js->function_(null,null,'return 5;');
		$this->assertEquals('', _toString($js));
		$this->assertEquals('function(){return 5;}', _toString($f));		
	}

	public function testfunction_3() {
		$js = new CopixJSWidget();
		$f = $js->function_(null,'a,b','return 5;');
		$this->assertEquals('', _toString($js));
		$this->assertEquals('function(a,b){return 5;}', _toString($f));				
	}

	public function testfunction_4() {
		$js = new CopixJSWidget();
		$f = $js->function_(null,array('a','b','c'),'return 5;');
		$this->assertEquals('', _toString($js));
		$this->assertEquals('function(a,b,c){return 5;}', _toString($f));				
	}
	
	public function testvar_() {
		$js = new CopixJSWidget();
		$js->var_('x');
		$this->assertEquals('var x;', _toString($js));
	}

	public function testreturn_() {
		$js = new CopixJSWidget();
		$js->return_('x');
		$this->assertEquals('return "x";', _toString($js));
	}

	
	public function test_() {
		$js = new CopixJSWidget();
		$js->_('5');;
		$this->assertEquals('$("5");', _toString($js));
	}
	
	public function test__() {
		$js = new CopixJSWidget();
		$js->__('5');
		$this->assertEquals('$$("5");', _toString($js));
	}

	public function test_A() {
		$js = new CopixJSWidget();
		$js->_A('5');
		$this->assertEquals('$A("5");', _toString($js));
	}	
	
	public function testObj__call() {
		$js = new CopixJSWidget();
		$js->obj->method(1, 'arg', true, false);
		$this->assertEquals('obj.method(1,"arg",true,false);', _toString($js));
	}

	public function testObj__get() {
		$js = new CopixJSWidget();
		$prop = $js->obj->prop;
		$this->assertEquals('', _toString($js));
		$this->assertEquals('obj.prop', _toString($prop));
	}
			
	public function testObj__set() {
		$js = new CopixJSWidget();
		$js->obj->prop = "x";
		$this->assertEquals('obj.prop = "x";', _toString($js));
	}
		
	public function testObj__unset() {
		$js = new CopixJSWidget();
		unset($js->obj->prop);
		$this->assertEquals('delete obj.prop;', _toString($js));
	}
		
	public function testObjoffsetGet() {
		$js = new CopixJSWidget();
		$val = $js->arr[5];
		$this->assertEquals('', _toString($js));
		$this->assertEquals('arr[5]', _toString($val));
	}
		
	public function testObjoffsetSet() {
		$js = new CopixJSWidget();
		$js->arr[5] = null;
		$this->assertEquals('arr[5] = null;', _toString($js));
	}
		
	public function testObjoffsetUnset() {
		$js = new CopixJSWidget();
		unset($js->arr[5]);
		$this->assertEquals('delete arr[5];', _toString($js));
	}	

	public function testObjnew_() {
		$js = new CopixJSWidget();
		$obj = $js->obj->new_(5);
		$this->assertEquals('', _toString($js));
		$this->assertEquals('new obj(5)', _toString($obj));
	}
	
}
?>