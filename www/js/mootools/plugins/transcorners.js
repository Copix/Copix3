//	nifty.js: nifty corners on mootools
//	by Yaroslaff Fedin ([url=http://inviz.ru]http://inviz.ru[/url]) MIT-style license.
var Transcorner = new Class({
	setOptions: function(options){
		this.options = Object.extend({
			radius: 10,
			borderColor: null,
			backgroundColor: this.el.getStyle('background-color'),
			transition: this.fx,
			onComplete: Class.empty
		}, options || {});
	}	
	,initialize: function(el, sides, options) {
		this.el = $(el);
		if (!sides || $type(sides)=='object') {
			options = sides || false;
			sides = 'top, bottom';
		};
		this.setOptions(options);
		sides.split(',').each(function(side) {
			side = side.clean().test(' ') ? side.clean().split(' ') : [side.trim()];
			this.assemble(side[0], side[1]);
		}, this);
	}
	,fx: function(pos){
	    return -(Math.sqrt(1 - Math.pow(pos, 2)) - 1);
	}
	,assemble: function(vertical, horizontal) {
		var corner;
		var el = this.el;
		while ((el = el.getParent()) && el.getTag()!='html' && [false, 'transparent'].test(corner = el.getStyle('background-color'))) {};
		var s = function(property, dontParse) {	return !dontParse ? (parseInt(this.el.getStyle(property)) || 0) : this.el.getStyle(property); }.bind(this);
		var sides = {
			left:'right',
			right:'left'
		};
		var styles = {
			display: 'block',
			backgroundColor: corner,
			//zIndex: 1,
			position: 'relative',
			zoom: 1
		};
		for (side in sides) {styles['margin-' + side] = "-" + (s('padding-' + side) + s('border-' + side + '-width')) + "px";}
		for (side in {top:1, bottom:1}) {styles['margin-' + side] = vertical == side ? "0" : (s('padding-' + vertical) - this.options.radius) + "px";}
		var handler = new Element("b").setStyles(styles).addClass('corner-container');
		this.options.borderColor = this.options.borderColor || (s('border-'+vertical+'-width') > 0 ? s('border-'+vertical+'-color', 1) : this.options.backgroundColor);
		this.el.setStyle('border-'+vertical, '0').setStyle('padding-'+vertical, '0');
		var stripes = [];
		var borders = {};
		var exMargin = 0;
		for (side in sides) {borders[side] = s('border-' + side + '-width',1) + " " + s('border-' + side + '-style',1) + " " + s('border-' + side + '-color',1);}
		for (var i = 1; i <= this.options.radius; i++) {
			margin = Math.round(this.options.transition((this.options.radius - i) / this.options.radius, 0, 1, 1) * this.options.radius);
			var styles = {
				background: i==1 ? this.options.borderColor : this.options.backgroundColor,
				display: 'block',
				height: '1px',
			    overflow: 'hidden',
				zoom: 1
			};
			for (side in sides) {
				var check = horizontal == sides[side];
				styles['border-' + side] = check ? borders[side] : (((exMargin || margin)-margin) || 1) + 'px solid ' + this.options.borderColor ;
				styles['margin-' + side] = check ? 0 : margin + 'px';
			};
			exMargin = margin;
			stripes.push(new Element("b").setStyles(styles).addClass('corner'));
		};
		if (vertical=='top') {this.el.insertBefore(handler, this.el.firstChild);}
		else {
			handler.injectInside(this.el);
			stripes = stripes.reverse();
		};
		stripes.each(function(stripe) {stripe.injectInside(handler);});
		this.options.onComplete();
	}
});
Element.extend({
	makeRounded: function(side, options){ return new Transcorner(this, side, options);	}
});