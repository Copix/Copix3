var zone = new Class ({
		isExec       : false,
		currentZone  : { x     : null,
					     y     : null,
					 	 sizex : null,
						 sizey : null,
						 elem  : null
					  },
		flag         :false,
		initialize : function () {
    		document.addEvent('mousemove', function(event){
    			if (this.isExec) {
    				var event = new Event(event);
    				
    				
					mouseposx = event.client.y+window.getScrollTop(); 
					mouseposy = event.client.x+window.getScrollLeft();
					
					if ( mouseposx < this.currentZone.x || mouseposy < this.currentZone.y || mouseposx > ( this.currentZone.x + this.currentZone.sizex ) || mouseposy > ( this.currentZone.y + this.currentZone.sizey )) {
						if (!this.flag){
							this.flag   = true;
							this.testDelay.delay(500,this);
						}
					} else {
						this.flag   = false;
					}
    			}
    		}.bindWithEvent(this));
		},
		testDelay : function () {
			if (this.flag) {
				this.isExec = false;
				this.currentZone.elem.fireEvent('mouseleavezone');
			}
		},
		testZone : function (elem, x , y, sizex, sizey) {
			if (!this.isExec) {
				this.isExec            = true;
				this.currentZone.x     = x;
				this.currentZone.y     = y;
				this.currentZone.sizex = sizex;
				this.currentZone.sizey = sizey;
				this.currentZone.elem  = elem;
			}
		}
	}
);

currentZone = new zone();
	
Element.extend({
	testZone: function(x,y,sizex,sizey){
		currentZone.testZone (this, x, y, sizex, sizey );
	},
	deleteZone: function (){
		currentZone.isExec = false;
	}
});

Element.extend({
	isZone: function(){
		return currentZone.isExec;
	}
});