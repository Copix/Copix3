var OverlayFixDiv = new Class({
	initialize: function(el) {
		this.element = $(el);
		if (window.ie){
			this.element.addEvent('trash', this.destroy.bind(this));
			this.fix = new Element('iframe', {
				properties: {
					frameborder: '0',
					scrolling: 'no',
					src: 'javascript:false;'
				},
				styles: {
					position: 'absolute',
					border: 'none',
					display: 'none',
					filter: 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)'
				}
			}).injectAfter(this.element);
		}
	},

	show: function() {
		if (this.fix) {
		  this.fix.setStyles($extend(
			this.element.getCoordinates(), {
				display: '',
				position:'absolute',
				zIndex: (this.element.getStyle('zIndex') || 1) - 1
			}));
		  this.fix.removeEvents();
		  this.fix.cloneEvents(this.element);
		}
		return this;
	},
	
	update: function() {
	   if (this.fix) {
		  this.fix.setStyles(this.element.getCoordinates());
		}
		return this;
	},
	
	hide: function() {
		if (this.fix) this.fix.setStyle('display', 'none');
		return this;
	},

	destroy: function() {
		try {
			this.fix.remove();
		} catch (e) {
		}
	}
});

Element.extend({
	fixdivShow: function(){
		if (this.fix == null) {
			this.fix = new OverlayFixDiv(this);
		}
		this.fix.show();
	},
	fixdivHide: function(){
		if (this.fix == null) {
			this.fix = new OverlayFixDiv(this);
		}
		this.fix.hide();
	},
	fixdivUpdate: function(){
		if (this.fix == null) {
			this.fix = new OverlayFixDiv(this);
		}
		this.fix.update();
	}
	
});
