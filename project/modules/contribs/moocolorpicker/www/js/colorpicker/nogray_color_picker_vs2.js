/* 
Script: nogray_color_picker_vs2.js 
	The Color Picker Class (see below)
 
License: 
	http://www.nogray.com/license.php 
		
provided by the NoGray.com
by Wesam Saif
support: support@nogray.com
*/ 

/*
Class:
	ColorPicker
	The ColorPicker class will attach the nogray color picker to an input field
	
Options:
	Color: The color picker start value defaults to red #FF0000
	openOnClick: opens the color picker when the user click on the swatch or the input field
				defaults to true
	colorPickerInterface: the path for the color picker interface
				defaults to 'color_picker_files/color_picker_interface.html'
				

Events:
	onPreview: this event will run everytime the color picker value change
			defaults to changing the field value and the swatch background
			
	onChange: this event will run once the user click on the ok button
			defaults to call the onPreview event
			
	onCancel: this event will run once the user click on the cancel button
			defaults to call the onPreview with the original value;
			

Function:
	showPicker: will open the color picker in a new window
*/
var ColorPicker = new Class({		
		options: {
			color: '#FF0000',
			openOnClick:true,
			
			onPreview: function(color){
				if ($defined(this.swatch)) {
					this.swatch.setStyle("backgroundColor", color);
					if (new Color(color).hsb[2] < 65) var fg_color = "#ffffff";
					else var fg_color = "#000000";
					this.swatch.setStyle("color", fg_color);
				}
				this.element.value=color;
			},
			
			onChange: function(color){
				this.options.color = this.color = color;
				this.fireEvent("onPreview", color);
				this.fireEvent("onComplete");
			},
			
			onCancel: function(color){
				this.fireEvent("onPreview", this.options.color);	
			},
			
			onComplete: function(){
				return this.color;
			}
		},
		setOptions: function (opts) {
			for (var i in opts) {
				this.options[i] = opts[i];
			}
			this.addEvent ('onPreview', this.options.onPreview.bind(this));
			this.addEvent ('onChange', this.options.onChange.bind(this));
			this.addEvent ('onCancel', this.options.onCancel.bind(this));
			this.addEvent ('onComplete', this.options.onComplete.bind(this));
		},
		initialize: function (el, swatch, options){
			this.element = $(el);
			this.swatch = $(swatch);
			this.randomLetters = $randomLetters(8);
			this.setOptions(options);
			
			this.color = this.options.color;
			
			this.fireEvent("onPreview", this.color);
			
			$color_picker_object[this.randomLetters] = this;
			this.element.setAttribute('picker', this.randomLetters);
			if ($defined(this.swatch)) this.swatch.setAttribute('picker', this.randomLetters);
			
			if(this.options.openOnClick){
				this.element.addEvent("click", function(){
					var obj = $color_picker_object[this.getAttribute('picker')];
					obj.showPicker();
				});
				if ($defined(this.swatch)) this.swatch.addEvent("click", function(){
					var obj = $color_picker_object[this.getAttribute('picker')];
					obj.showPicker();
				});
			}
			this.element.addEvent("change", function(){
				this.Change(this.element.value);
			});
		},
		
		showPicker: function(){
			var lnk = Copix.getActionURL('moocolorpicker|moocolorpicker|default')+"?color="+this.options.color+"&pre_color="+this.element.value+"&pickerObject="+this.randomLetters;
			window.open(lnk, this.randomLetters, "width=465, height=350");	
		}
});

// implementing the events and options to the color picker class
ColorPicker.implement(new Events, new Options);

// var color_picker_object will save which object is currently open
// so the picker will fire the correct events
var $color_picker_object = new Array();



CopixClass.implement({
	
	register_colorpicker: function (id, value) {
	    return new ColorPicker(id, id, {'color': value});
	}
});
