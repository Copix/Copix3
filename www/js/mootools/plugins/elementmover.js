/**
ElementMover v0.2
Author: Patice Ferlet (metal3d@copix.org)
Licence: MIT

exemple: new ElementMover({
	'place' : 'after',
	'order' : $('id'),
	'styles' : {
		'color' : '#fff',
		'width': '50%'
	},
	'target' : $('maincontent')
});

or

$('id').changeLocation({
	'target' : $('maincontent'),
	'order' : 'after',
	'styles' : {
		'color' : '#fff',
		'width': '50%'
	}
})

*/
ElementMover = new Class({
	initialize: function(options){	
		this.options = options
		if(options.source && $type(options.source)!='array' && $type(options.source)!='boolean'){
			this.move(options.source)
		}
		else if(options.source && $type(options.source)=="array"){
			options.source.each(function(el){
				this.move(el);
			},this);
		}
	},
	
	move: function(el){
		if(this.options.styles)	el.setStyles(this.options.styles);
		if(this.options.order=="before"){
			el.injectBefore(this.options.target);
		}
		else if(this.options.order=="after"){
			el.injectAfter(this.options.target);
		}
		else{
			el.injectInside(this.options.target);
		}
	}
});


Element.extend({
	changeLocation: function(options){ 
		options.source = this;
		return new ElementMover(options);	
	}
});