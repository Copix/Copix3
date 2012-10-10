CopixPopup = new Class({
	options:{},
	div:null,
	declencheur:null,
	zone:null,
	divsize:{},
	state: 'hide',
	flag:false,
	initialize: function (id, options) {
		this.options = options;
		this.declencheur = $('div'+id);
		if (this.declencheur) {
			this.div = $(this.declencheur.getProperty('rel'));
			this.zone = $('zone_'+this.declencheur.getProperty('rel'));
		}
		this.div.injectInside(document.body);
		
		this.div.setStyles({'display':'','visibility':'hidden', 'position':'absolute'});
		this.divsize = this.div.getSize();
		this.div.setStyles({'display':'none','visibility':'visible'});
		this.declencheur.addEvent('trash', function () {
			$ (this.div).remove ();
		}.bind(this));
		
		this.declencheur.addEvent('sync', this.sync.bind(this));
		
		if (this.options.handler == 'onclick') {
			this.declencheur.addEvent('click', function () {
				if (this.state == 'display') {
					this.hide ();
				} else {
					this.display ();
				}
				return false;
			}.bind(this));
		} else if (this.options.handler == 'clickdelay') {
			this.declencheur.addEvent('click', function () {
				if (this.state == 'display') {
					this.hide ();
				} else {
					this.display ();
					this.flag = false;
				}
				return false;
			}.bind(this));
			
			this.declencheur.addEvent('mouseleave', function () {
				this.flag = true;
				if (this.state == 'display') {
					this.hideWithFlag.delay (this.options.duration || 1000, this);
				}
			}.bind(this));
			this.declencheur.addEvent('mouseenter', function () {
				this.flag = false;
			}.bind(this));
			
			this.div.addEvent('mouseleave', function () {
				this.flag = true;
				if (this.state == 'display') {
					this.hideWithFlag.delay (this.options.duration || 1000, this);
				}
			}.bind(this));
			this.div.addEvent('mouseenter', function () {
				this.flag = false;
			}.bind(this));
		} else {
			this.declencheur.addEvents({
				'mouseenter':this.display.bind(this),
				'mouseleave':this.hide.bind(this)
			});
		}
	},
	replace:function () {
		scrollTop = (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
		scrollLeft = (document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
		
		x = this.declencheur.getPosition().x;
		y = this.declencheur.getPosition().y+this.declencheur.getSize().size.y;
		
		tempx = (this.declencheur.getPosition().x+this.declencheur.getSize().size.x)-this.div.getSize().size.x;
		tempy = this.declencheur.getPosition().y-this.div.getSize().size.y;
		
		if (x+this.div.getSize().size.x-scrollLeft > window.getSize().size.x && (tempx-scrollLeft) > 0) {
			x=tempx;
		} else if (x+this.div.getSize().size.x-scrollLeft > window.getSize().size.x && (tempx-scrollLeft) < 0) {
			x=scrollLeft;
		}
		
		if (y+this.div.getSize().size.y-scrollTop > window.getSize().size.y && (tempy-scrollTop) > 0) {
			y=tempy;
		} else if (y+this.div.getSize().size.y-scrollTop > window.getSize().size.y && (tempy-scrollTop) < 0) {
			y=scrollTop;
		}

		this.div.setStyles({
			'position':'absolute',
			'top' : y+'px',
			'left' : x+'px',
			'zIndex':'1001'
		});
	},
	sync: function () {
		this.div.fixdivHide();
		this.replace();
		this.div.fixdivShow();
	},
	display: function (e) {
		this.state = 'display';
		if (this.zone) {
			this.zone.fireEvent('display');
		}
		this.div.setStyle('display','');
		this.replace();
		this.div.fixdivShow();
	},
	hide: function () {
		this.state = 'hide';
		this.div.fixdivHide();
		this.div.setStyle('display','none');
	},
	hideWithFlag: function () {
		if (this.flag) {
			this.hide();
		}
	}
});
CopixClass.implement({
	popups:[],
	register_popup: function (id, options) {
		this.popups[id] = new CopixPopup (id, options);
	}
});