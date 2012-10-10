// Select Overflow: unobtrusive accessible script to replace long selects with inputs that fit into your layout
// Usage: new SelectOverflow($('aVeryLongSelect')); or new SelectOverflow($('anotherVeryLongSelect'),{'class':'classes for input'});
// Only necessary for IE, adapt the following CSS for other browsers
// <style type="text/css">select { width: 115px;} span.selectoverflow select {width: auto;}</style>
// Yet to be made compatible with multiple selects

var SelectOverflow = new Class({
	Implements: Options,
	obj : null,
	options: {},
	initialize: function(obj, options){
		// basic error checking
		if (!obj || !obj.getProperty('name') || obj.getProperty('multiple')) { return false; }
		// Only necessary for IE, use CSS for other browsers
                options.onlyIE = (options.onlyIE!=undefined) ? options.onlyIE : true;
		if (!window.Browser.Engine.trident && options.onlyIE) { return false; }
		this.obj = obj;
		
		this.inputText = new Element('input', {'type': 'text','id':obj.getProperty('id') });
		this.inputHidden = new Element('input', {'type': 'hidden','name':obj.getProperty('name') });
		obj.setStyles({'display':'none', 'position': 'absolute', 'top':'80%','left':0});
		obj.removeProperty('id');
		
		if (this.options) {
			this.setOptions(options);
			this.inputText.setProperties(this.options);
		}
		
		this.wrapper = new Element('span', {'class': 'selectoverflow', styles:{'z-index':'1', 'position': 'relative','display': 'inline-block'}});
                
		this.wrapper.inject(obj, 'after');
                
		this.inputText.inject(this.wrapper, 'bottom');
		this.inputHidden.inject(this.wrapper, 'bottom');

                this.selectWrapper = new Element('span', {'class': 'selectoverflow', styles:{'z-index':'2','position': 'relative','display': 'inline-block'}});
                this.selectWrapper.inject(this.wrapper, 'before');
                this.selectWrapper.wraps(obj);
                
		// Maximum number of elements to display when auto-opening the select
		if (!obj.getProperty('size')) {
			var max = obj.options.length.limit(obj.options.length, 10);
			obj.setProperty('size', max);
		}
		this.attachEvents();
		this.update();
		return true;
	},
	attachEvents: function(){
		var obj = this.obj;
		obj.addEvent('change', this.update.bind(this));
		obj.addEvent('click', this.hide.bind(this));
		obj.addEvent('blur', this.hide.bind(this));
		obj.addEvent('keydown', this.handleKey.bind(this));
		this.inputText.addEvent('focus', this.show.bind(this));
	},
	update: function(){
		var obj = this.obj;
		if(obj.options){
			this.inputHidden.value = obj.options[obj.selectedIndex].value;
	                if (this.options.useValue) {
	                    this.inputText.value = obj.options[obj.selectedIndex].value;
	                } else {
	                    this.inputText.value = obj.options[obj.selectedIndex].text;
	                }
			if (!this.options.title) {
				this.inputText.title = obj.options[obj.selectedIndex].text;
			}
		}
	},
	hide: function(){
		var obj = this.obj;
		this.update();
		obj.setStyles({'display':'none', 'z-index':0});
		obj.setProperty('disabled', true);
	},
	show: function(){
		var obj = this.obj;
		obj.removeProperty('disabled');
		obj.setStyles({'display':'block', 'z-index':9999});
		this.inputText.focus(); // BUGFIX for IE6 which blurs obj for no reason the very first time
		obj.focus();
	},
	handleKey: function(e){
		if ( e.type == 'keydown' && (e.key == 'esc' || e.key == 'enter' ||  e.key == 'tab' ) ) {
			this.hide();
			e.preventDefault();
		}
	}
});