var OverlayFixDiv = new Class({
	initialize: function(el) {
		this.element = $(el);
		if (Browser.ie6){
			this.element.addEvent('trash', this.destroy.bind(this));
			this.fix = new Element('iframe', {
				properties: {
					frameborder: '0',
					scrolling: 'no'
					/*src: 'https://0'*/
				},
				styles: {
					position: 'absolute',
					border: 'none',
					display: 'none',
					filter: 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)'
				}
			});
                        this.fix.src = (window.location.protocol=='https:') ? 'https://0' : '';
			this.fix.injectAfter(this.element);
			this.element.addEvent ('trash', this.destroy);
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
                this.fix.setStyles($extend(
			this.element.getCoordinates(), {
				display: '',
				position:'absolute',
				zIndex: (this.element.getStyle('zIndex') || 1) - 1
			}));
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

Element.implement({
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
