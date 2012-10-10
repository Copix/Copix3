CopixLoader = new Class ({
	div:null,
	cancel:null,
	fond:null,
	onCancel: function () {},
	initialize: function (params) {
		if (params && params['onCancel']) {
			this.onCancel = params['onCancel']; 
		}
		
		this.div = new Element('div');
		this.div.setStyles({
							'vertical-align':'bottom'
						   ,'background-color':'white'
						   ,'border':'1px solid black'
						   ,'width':'100px'
						   ,'height':'100px'
						   ,'top': window.getScrollTop().toInt()+window.getHeight ().toInt()/2-50+'px'
						   ,'left':window.getScrollLeft().toInt()+window.getWidth ().toInt()/2-50+'px'
						   ,'position':'absolute'
						   ,'text-align':'center'
						   ,'background-image':'url("themes/default/img/tools/load.gif")'
						   ,'background-repeat':'no-repeat'
						   ,'background-position':'center'
						   ,'zIndex':999
						   ,'visibility':'hidden'
						   });

		this.div.injectInside(document.body);
		
		this.fond = new Element('div');
		this.fond.setOpacity(0.5);
		this.fond.setStyles({'width':window.getWidth()
							,'height':window.getHeight()
							,'top': window.getScrollTop()
							,'left':window.getScrollLeft()
							,'position':'absolute'
							,'text-align':'center'
							,'background-color':'black'
							,'zIndex':998
							,'visibility':'hidden'
							});
		this.fond.injectInside(document.body);
		
		this.cancel = new Element('input');
		this.cancel.setProperty('type','button');
		this.cancel.setProperty('value','Annuler');
		this.cancel.setStyle('margin-top','75px');
		this.cancel.addClass('loader_cancel');
		this.cancel.injectInside(this.div);
		this.cancel.addEvent('click', function () {
			this.unload();
			this.onCancel();
		}.bind(this))
	},
	load: function () {
		this.div.setStyles({
						   'top': window.getScrollTop().toInt()+window.getHeight ().toInt()/2-50+'px'
						   ,'left':window.getScrollLeft().toInt()+window.getWidth ().toInt()/2-50+'px'
						   ,'visibility':''
						  });
		this.fond.setStyles({'width':window.getWidth()
		                    ,'height':window.getHeight()
		                    ,'top': window.getScrollTop()
		                    ,'left':window.getScrollLeft()
		                    ,'visibility':''
		                    });
		this.fond.fixdivShow();
	},
	unload: function () {
		this.div.setStyle ('visibility', 'hidden');
		this.fond.setStyle ('visibility', 'hidden');
		this.fond.fixdivHide();
	}
});