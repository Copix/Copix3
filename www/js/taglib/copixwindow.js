var CopixWindow = new Class ({
	options: {},
	div: null,
	sync:false,
	isOpen:false,
	ajax: null,
	namespace: 'default',
	initialize: function (id, options) {
		this.div = $(id);
		if (!this.div) {
			throw ("CopixWindow : The element ["+id+"] must exists");
		}
		
		this.div.injectInside(document.body);
		this.div.setStyle('position','absolute');
		
		this.options = options;
		this.namespace = this.options.namespace;
		
		this.div.addEvent('display', this.display.bind(this));
		this.div.addEvent('close', this.close.bind(this));
		
		this.div.addEvent('mousedown', this.focus.bind(this));
		
		if (this.options.clicker) {
			this.clicker = $(this.options.clicker);
			if (this.clicker) {
				this.clicker.addEvent ('click', function () {
					this.display ();
					this.placeRelativeButVisible (this.clicker);
					return false;
				}.bind(this));
				this.div.addEvent('sync', function () {
					this.placeRelativeButVisible (this.clicker);
				}.bind(this));
			}
		}
		
		this.dragOptions = {'onDrag':this.div.fixdivUpdate.bind(this.div)};
		if (this.options.dragSelector) {
			this.dragOptions.handle = this.div.getElement (this.options.dragSelector);
			if (this.dragOptions.handle) {
				this.dragOptions.handle.addEvent('mousedown', this.focus.bind(this));
			}
		}
		
		
		this.div.makeDraggable(this.dragOptions);
		
	},
	place: function (x,y) {
		scrollTop  = 0;
		scrollLeft = 0;
		if (this.options.position != 'absolute') {
	    	scrollTop = (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
			scrollLeft = (document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
	    }
		
	    x = (x || this.options.x+scrollTop  || '0');
	    y = (y || this.options.y+scrollLeft || '0');
	    
	     
	    
	    this.div.setStyles({
	        'top' : y,
	        'left': x
	    });
	    this.div.fixdivUpdate ();
	},
	placeRelativeButVisible: function (element) {

		scrollTop = (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
		scrollLeft = (document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
	
		x = element.getPosition().x;
		y = element.getPosition().y+element.getSize().size.y;
		
		
		
		tempx = (element.getPosition().x+element.getSize().size.x)-this.div.getSize().size.x;
		tempy = element.getPosition().y-this.div.getSize().size.y;
		
		
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
		this.place (x,y);
	},
	display: function () {
		if ($(this.options.zone) && !this.options.sync) {
			$(this.options.zone).fireEvent('display');
			$(this.options.zone).addEvent('sync', function () {
				this.options.sync = true;
				this.display ();
				this.div.fireEvent('sync');
			}.bind(this));
			return this;
		}
		this.div.setStyle('display','');
		this.place ();
		this.div.fixdivShow();
		this.focus ();
		this.isOpen=true;
		return this;
	},
	close: function () {
		this.div.setStyle('display','none');
		this.div.fixdivHide ();
		this.unfocus ();
		this.isOpen=false;
	},
	focus: function (e) {
//		this.div.injectInside(document.body);
		if (Copix.focus[this.namespace] != this) {
			Copix.set_focuswindow (this);
			this.div.removeClass('unfocus');
			this.div.setStyle('zIndex','1000');
			if (this.options.onFocus) {
				this.options.onFocus ();
			}
			this.div.fireEvent('focus');
			this.div.fixdivUpdate ();
		}
	},
	unfocus: function () {
		Copix.set_unfocuswindow (this);
		this.div.setStyle('zIndex','999');
		this.div.addClass('unfocus');
		if (this.options.onUnfocus) {
			this.options.onUnfocus ();
		}
		this.div.fireEvent('unfocus');
		this.div.fixdivUpdate ();
	},
	destroy: function () {
        this.div.remove();
	}

});

CopixClass.implement({
    windows: new Array(),
    focus: new Array (),
    duringFocus:false,
	register_copixwindow: function (id, params) {
		if (this.windows[id]) {
		     this.windows[id].destroy();
		     delete(this.windows[id]);
		}
	    this.windows[id] = new CopixWindow(id, params);
	    return this.windows[id];
	},
	get_copixwindow: function (id) {
		if (!this.windows[id]) {
			throw ("this window does not exist: "+id);
		}
		return this.windows[id]; 
	},
	set_unfocuswindow: function (unfocus) {
		//On change de focus si on est pas en train de changer de focus
		if (this.focus[unfocus.namespace] == unfocus && !this.duringFocus) {
			this.focus[unfocus.namespace] = null;
			for (el in this.windows) {
				if ((this.windows[el].constructor == CopixWindow) && (this.windows[el].namespace == unfocus.namespace)) {
					if (this.windows[el].isOpen && this.windows[el] != unfocus) {
						this.windows[el].focus();
					}
				}
			}			
		}
	},
	set_focuswindow: function (focus) {
		if (this.focus[focus.namespace]) {
			this.duringFocus = true;
			this.focus[focus.namespace].unfocus();
			this.duringFocus = false;
		}
		this.focus[focus.namespace] = focus;
	}
});
