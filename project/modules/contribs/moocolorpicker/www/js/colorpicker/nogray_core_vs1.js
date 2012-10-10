/* 
Script: nogray_mootools_core.js 
	these functions will extend the mootool framework
	and will add extra tools
 
License: 
	http://www.nogray.com/license.php
		
provided by the NoGray.com
by Wesam Saif
support: support@nogray.com
*/ 

/*
Variable:
	$_GET

Returns:
	Same as the PHP $_GET
	
Example:
	if the URL is http://www.domain.com/page.html?ID=2134
	
	alert($_GET['ID']);		// will alert 2134
*/
var $_GET = new Array;

// internal script to parse the $_GET variable
var _uri = location.href;
var _temp_get_arr = _uri.substring(_uri.indexOf('?')+1, _uri.length).split("&");
var _temp_get_arr_1 = new Array();
var _temp_get_val_holder = "";
for(_get_arr_i=0; _get_arr_i<_temp_get_arr.length; _get_arr_i++){
	_temp_get_val_holder = "";
	_temp_get_arr_1 = _temp_get_arr[_get_arr_i].split("=");
	for (_get_arr_j=1; _get_arr_j<_temp_get_arr_1.length; _get_arr_j++){
		if (_get_arr_j > 1) _temp_get_val_holder += "=";
		_temp_get_val_holder += decodeURI(_temp_get_arr_1[_get_arr_j]);
	}
	$_GET[decodeURI(_temp_get_arr_1[0])] = _temp_get_val_holder;
}
delete _uri; delete _temp_get_arr; delete _temp_get_arr_1; delete _temp_get_val_holder;


/*
Function:
	$randomLetters
	
Returns:
	return a random string
	
Arguments:
	numLetters is the number of letters in the returned string
	
Example:
	alert($randomLetters(7));	// will alert "dl4D3sD" for example 
*/
var $randomLetters = function(numLetters){
	var st = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789", re = "", i = 0;
	for(i=0; i<= numLetters; i++) re += st.substr($random(0, st.length), 1);
	return re;	
};


/*
Function:
	$hCenterAbsolute
	Center an object with an absolut positioning (horizontal)
	
Returns:
	if reValue is true will return the left position to center the object
	otherwise center the object horizontally
	
Arguments:
	obj is the object to be centerd
	reValue boolean value to either return the left position if true
	or move the object if false
	
Example:
	HTML: <div id="div1" style="position:absolute;">Test object</div>
	... later
	Script:
	$hCenterAbsolut("div1");
*/
var $hCenterAbsolute = function(obj, reValue){
	var obj = $(obj);
	if ($defined(obj)){
		var coord = obj.getCoordinates();
		
		var l = (window.getWidth() - coord['width'])/2;
		if (reValue) return l;
		else obj.setStyle('left', l);
	}
};

/*
Function:
	$vCenterAbsolute
	Center an object with an absolut positioning (verticlly)
	
Returns:
	if reValue is true will return the top position to center the object
	otherwise center the object verticlly
	
Arguments:
	obj is the object to be centerd
	reValue boolean value to either return the top position if true
	or move the object if false
	
Example:
	HTML: <div id="div1" style="position:absolute;">Test object</div>
	... later
	Script:
	$vCenterAbsolute("div1");
*/
var $vCenterAbsolute = function(obj, reValue){
	var obj = $(obj);
	if ($defined(obj)){
		var coord = obj.getCoordinates();
		
		var t = window.getHeight()/2 - coord['height'];
		
		if (reValue) return t;
		else obj.setStyle('top', t);
	}
};

/*
Function:
	$centerAbsolute
	Center an object with an absolut positioning on the screen
	
Returns:
	if reValue is true will return the object's top and left (as an object)
	position to center on the screen
	otherwise center the object on the screen
	
Arguments:
	obj is the object to be centerd
	reValue boolean value to either return the object's top and left (as an object)
	position to center on the screen
	or move the object if false
	
Example:
	HTML: <div id="div1" style="position:absolute;">Test object</div>
	... later
	Script:
	$centerAbsolute("div1");
*/
var $centerAbsolute = function (obj, reValue){
	if (reValue){
		return {'left':$hCenterAbsolute(obj, true),
				'top':$vCenterAbsolute(obj, true)};	
	}
	else {
		$hCenterAbsolute(obj, false);
		$vCenterAbsolute(obj, false);
	}
};

/*
Function:
	$alwaysCenter
	will try to keep the object centered on the screen at all the time
	
Return:
	void

Arguments:
	a list of object to be centered on a screen
	e.g $alwaysCenter("obj1", "obj2", "obj3", ...);
	
Example:
	HTML: <div id="div1" style="position:absolute;">Test object</div>
	... later
	Script:
	$alwaysCenter("div1");
*/
var $alwaysCenter = function (){
	var i=0;
	if(typeof arguments[0] != "string") arguments = arguments[0];
	for (i=0; i<arguments.length; i++){
		$centerAbsolute(arguments[i]);
	}
	if ($alwaysCenterArray.length == 0){
		for (i=0; i<arguments.length; i++){
			$alwaysCenterArray[i] = arguments[i];
		}
	}
	window.addEvent("scroll", function(){
			$alwaysCenter($alwaysCenterArray);
		});
	window.addEvent("resize", function(){
			$alwaysCenter($alwaysCenterArray);
		});
};
var $alwaysCenterArray = new Array();


/*
Function:
	$fixIEPng
	will fix PNG images on IE 6 and below
	
	Don't use with the DockingMenu, the images will disappear.
	use $switchIEPng2Gif instead
	
Return:
	void

Arguments:
	spacer is the spacer image source, if empty will be
	images/spacer.gif
	
Example:
	$fixIEPng();
*/
var $fixIEPng = function(spacer){
	if (window.ie && !window.ie7){
		if (!$defined(spacer)) var spacer = "../img/colorpicker/spacer.gif";
		$$("img").each(function(el){
			if (el.src.substr(el.src.length - 3).toLowerCase() == "png"){
				var coord = el.getCoordinates();
				el.width = coord['width'];
				el.height = coord['height'];
				try {
					var foo = el.filters.item("DXImageTransform.Microsoft.AlphaImageLoader");
				}
				catch(e){
					el.style.filter += "\nprogid:DXImageTransform.Microsoft.AlphaImageLoader()";
				}
				el.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").Enabled = true;
				el.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = el.src;
				el.src = spacer;
			}
		});
	}
};

/*
Function:
	$switchIEPng2Gif
	switch the png image with a gif version
	
	the extension must be in lower case on both images
	both images should share the same name except for the extension
	
Return:
	void

Arguments:
	imgs a list of images to be changed
	
Example:
	$switchIEPng2Gif($$('img'));
*/

var $switchIEPng2Gif = function (imgs){
	if (window.ie && !window.ie7){
		var i=0;
		for(i=0; i<imgs.length; i++){
			imgs[i].src = imgs[i].src.replace(".png", ".gif");
		}
	}
};


/*
Extending the mooTools String function
*/
String.extend({
/*
Function:
	isUpper
	
Returns:
	true if all the characters in the string are upper case
	false if otherwise
	
Example:
	alert("Hello World".isUpper());	// will alert false
	alert("HELLO WORLD".isUpper()); // will alert true
	
*/
	isUpper: function (){
		return (this.match(/^([^a-z]\.*[^a-z]*)$/) != null);
	},
	
/*
Function:
	isLower
	
Returns:
	true if all the characters in the string are lower case
	false if otherwise
	
Example:
	alert("Hello World".isLower());	// will alert false
	alert("hello world".isLower()); // will alert true
	
*/
	isLower: function(){
		return (this.match(/^([^A-Z]\.*[^A-Z]*)$/) != null);	
	},
	
/*
Function:
	isNumeric
	
Returns:
	true if the string is a number
	false if otherwise
	
Example:
	alert("45 men".isNumeric());	// will alert false
	alert("45.25".isNumeric()); // will alert true
	
*/
	isNumeric: function(){
		return (this.match(/^(-?\d*\.?\d*)$/) != null);		
	}
});


/*
extends the Color plugin for mootools
*/
if ($defined(Color)){
	Color.implement({

/*
Function:
	desaturate
	
Returns:
	the desaturated value of the color
	
Example:
	alert(new Color("#ff0000").desaturate().hex);     // alert #808080
	
*/
		desaturate: function(){
			return new Color([this.hsb[0], 0, Math.round(this.hsb[2] - ((this.hsb[1]/200) * this.hsb[2]))], 'hsb');
		},
		

/*
Function:
	webSafe
	
Returns:
	the websafe equivalent of the color
	
Example:
	alert(new Color("#32fd88").webSafe().hex);     // alert 33ff99
	
*/
		webSafe: function(){
			var i=0;
			for(i=0; i<3; i++){
				if (this[i] > 230) this[i] = 255;
				else if(this[i] > 179) this[i] = 204;
				else if(this[i] > 128) this[i] = 153;
				else if(this[i] > 77) this[i] = 102;
				else if(this[i] > 25) this[i] = 51;
			}
			
			return new Color(this);
		}
	});
};