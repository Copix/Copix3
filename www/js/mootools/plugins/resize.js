/*  
 *	Based on : resize.js	
 *	Script available at
 *	http://www.tricolormix.de/  
 *
 *	CopixTeam - VUIDART Sylvain 
 */ 
Resizing = new Class(  
{
	initialize: function(id, options)  
	{ 
		this.options = options || {};
		this.options.min = this.options.min || 100;
		this.options.max = this.options.max || 500;
		this.element = $(id);
		this.gripid = id+'_grip';
		this.build();
	},  
	build: function() {
		var grip = new Element('div',{'class':'grip','id':this.gripid});
		grip.injectAfter(this.element);
		this.element.makeResizable({
	    	handle: grip.id,
	    	modifiers:{x: false, y:'height'}, 
	    	limit: {y: [this.options.min, this.options.max]},
			userpreference: this.options.userpreference,
	    	onStart : function (){this.element.setStyle('opacity','0.3');},
	    	onComplete : function () {
				this.element.setStyle('opacity','1');
				if (this.options.userpreference) {
					Copix.savePreference (this.options.userpreference, this.element.getCoordinates ().height);
				}
			}
		});
		var coordinates = this.element.getCoordinates();
		// 2 = border-width
		grip.setStyle('width', coordinates.width - 2);
		grip.setStyle('margin-left', this.element.getStyle('margin-left'));
		grip.setStyle('max-width', this.element.getStyle('max-width'));
	}
}   
); 


/*********Exemple d'appel***************/
/*
window.addEvent('domready', function() {    
	new ResizingTextArea('resize',{'min':200,'max':400});
});
*/