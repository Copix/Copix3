var ie6 = (Browser.Engine.trident && Browser.Engine.version <= 4) ? true : false;
var CopixWindow = new Class ({
	options: {},
	div: null,
	modaldiv : null,
	sync:false,
	isOpen:false,
	ajax: null,
	namespace: 'default',
	initialize: function (id, options) {
		this.div = $(id);
		if (!this.div) {
			throw ("CopixWindow : The element ["+id+"] must exists");
		}
		this.options = options;
		
		this.div.injectInside(document.body);
        // position:fixed; n'existe pas sous IE6
		if(this.options.fixed == true && !ie6){
            this.div.setStyle ('position','fixed');
        }else {
            this.div.setStyle ('position','absolute');
        }
		this.namespace = this.options.namespace;
		
		this.div.addEvent('display', this.display.bind(this));
		this.div.addEvent('close', this.close.bind(this));
		
		this.div.addEvent('mousedown', this.focus.bind(this));
		if (this.options.clicker) {
			this.clicker = $(this.options.clicker);
			if (this.clicker) {
				this.clicker.addEvent ('click', function () {
					this.display ();
					if (this.options.center) {
						this.placeCenter ();
					} else {
						this.placeRelativeButVisible (this.clicker);
					}
					return false;
				}.bind(this));
				this.div.addEvent('sync', function () {
					if (this.options.center) {
						this.placeCenter ();
					} else {
						this.placeRelativeButVisible (this.clicker);
					}
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
		
		if (this.options.canDrag) {
			this.div.makeDraggable(this.dragOptions);
		}
	},
	place: function (x,y) {
		scrollTop  = 0;
		scrollLeft = 0;
	    
		if(this.options.fixed == true){
            var browserWindowSize = window.getSize();
            var elementSize = this.div.getSize();
            x = (browserWindowSize.x / 2) - (elementSize.x / 2);
            y = (browserWindowSize.y / 2) - (elementSize.y / 2);
        }else{
            if (this.options.position != 'absolute') {
                scrollTop = (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
                scrollLeft = (document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
            }

            x = (x || this.options.x+scrollTop  || '0');
            y = (y || this.options.y+scrollLeft || '0');
        }
        this.div.setStyles({
            'top' : y,
            'left': x
        });
        this.div.fixdivUpdate ();
	},
	placeCenter: function () {
		scrollTop = (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
		scrollLeft = (document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
		this.place ((window.getSize().x-this.div.getSize().x)/2+scrollTop, ((window.getSize().y-this.div.getSize().y)/2+scrollLeft));
		
	},
	placeRelativeButVisible: function (element) {

		scrollTop = (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
		scrollLeft = (document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
	
		x = element.getPosition().x;
		y = element.getPosition().y+element.getSize().y;
		
		
		
		tempx = (element.getPosition().x+element.getSize().x)-this.div.getSize().x;
		tempy = element.getPosition().y-this.div.getSize().y;
		
		
		if (x+this.div.getSize().x-scrollLeft > window.getSize().x && (tempx-scrollLeft) > 0) {
			x=tempx;
		} else if (x+this.div.getSize().x-scrollLeft > window.getSize().x && (tempx-scrollLeft) < 0) {
			x=scrollLeft;
		}

		if (y+this.div.getSize().y-scrollTop > window.getSize().y && (tempy-scrollTop) > 0) {
			y=tempy;
		} else if (y+this.div.getSize().y-scrollTop > window.getSize().y && (tempy-scrollTop) < 0) {
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
		if(this.options.modal){
			this.showmodal();
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
		if(this.options.modal){
			this.hidemodal();
		}
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
		if (this.div != undefined) {
			this.div.destroy();
		}
	},
	showmodal:function(){
		if (this.modaldiv == null){
			this.modaldiv = new Element('div', {'id':this.div.id + '_copixwindow_modaldiv', 'class':'copixwindow_modaldiv'});
			this.modaldiv.injectInside(document.body);
			if (this.options.modalclose){
				this.modaldiv.addEvent('click', function(){
					this.close();
				}.bind(this));
			}
		}
		this.modaldiv.setStyles({'display':''
					,'width':'100%'
                    ,'height':'100%'
                    ,'top': 0
                    ,'left':0
                    ,'position':'fixed'
                    ,'text-align':'center'
                    ,'zIndex':998
							});
	},
	hidemodal:function(){
		this.modaldiv.setStyle ('display', 'none');
	}

});

CopixClass.implement({
    windows: new Array(),
    focus: new Array (),
    duringFocus:false,
	register_copixwindow: function (id, params, domready) {
		if (domready){		
			window.addEvent('domready', function(){
				return Copix.register_copixwindow (id, params, false);
			});
		} else {
			if (this.windows[id]) {
			     this.windows[id].destroy();
			     delete(this.windows[id]);
			}
		    this.windows[id] = new CopixWindow(id, params);
		    return this.windows[id];
		}
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
