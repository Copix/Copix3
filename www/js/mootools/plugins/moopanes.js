/*
Moopanes.js
Author : 

window.addEvent('load', function(){
	var vpane = new MooPane('pane1','pane2', true, {disposition: 'vertical', resizable: true, pane1percentage: 30});
	var hpane = new MooPane('pane3','pane4', false,{disposition: 'horizontal', resizable: true, pane1percentage: 30});
	hpane.setParent(vpane);	
});


*/

var MooPanes = {
    panes: []
};

var MooPane = new Class({
                                        
    getOptions: function(){          
		return {			
			pane1percentage: 30,
            pane1size:   50,
			pane2size:   50,
			disposition: 'vertical',
			resizable:   false,
			dividersize: 4
		};
	},
			
	initialize: function( div1id , div2id , isParent , options ){
                
                this.setOptions(this.getOptions(), options);    
                this.pane1 = $(div1id).addClass('pane');
                this.pane2 = $(div2id).addClass('pane');
                this.container = this.pane1.getParent();
                this.children = [] ;
                this.parentPane = Class.empty;
                this.isParent = isParent;
                
				this.container.setStyles({
					width: this.container.getParent().getSize().x,
					height: this.container.getParent().getSize().y
				});
				
                if( this.options.disposition == 'vertical' ){
                     this.pane1size = ( this.container.getSize().x / 100 ) * this.options.pane1percentage;
                     this.pane2size = this.container.getSize().x - this.options.dividersize - this.pane1size ;
                }else{
                     this.pane1size = ( this.container.getSize().y / 100 ) * this.options.pane1percentage;
                     this.pane2size = this.container.getSize().y - this.options.dividersize - this.pane1size ;
                }
                
				
				
                if( this.options.disposition == 'vertical' ){
                   this.pane1.setStyles({
                     width: this.pane1size+'px',
                     height: this.container.getSize().y+'px' ,                     
                     overflow: 'auto'
                   });
                   this.pane2.setStyles({
                     width: this.pane2size+'px',
                     height: this.container.getSize().y+'px',
					 left: this.pane1size + this.options.dividersize ,                     
                     overflow: 'auto'
                   });	
                }else{
                   this.pane1.setStyles({
                     height: this.pane1size+'px',
					 left: 0+'px',
                     width: this.container.getSize().x+'px',
                     overflow: 'auto'
                   });
                   this.pane2.setStyles({
                     height: this.pane2size+'px',
                     width: this.container.getSize().x+'px',
					 top: this.pane1size + this.options.dividersize , 
                     overflow: 'auto'
                   });	
                }
                
                
		        this.splitbar = new Element('div').addClass(this.options.disposition+'-divider').setStyles({
					
		            zIndex: 8                        
				}).injectInside(this.container);
				
				if( this.options.disposition == 'vertical' ){          
		          this.splitbar.setStyle('left',this.pane1.getSize().x+'px' )
		                       .setStyle('height',this.pane1.getSize().y+'px' )
		                       .setStyle('cursor','e-resize');
		        }else{
		          this.splitbar.setStyle('top',this.pane1.getSize().y+'px'  )
		                       .setStyle('width',this.pane1.getSize().x+'px' )
		                       .setStyle('cursor','s-resize');
		        }
		
				if( this.options.disposition == 'vertical' ){
                    this.splitbar.setStyles({
                         width: this.options.dividersize+'px',
                         height: this.container.getSize().y+'px'                     
                    });
                }else{
                    this.splitbar.setStyles({
                         height: this.options.dividersize+'px',
                         width: this.container.getSize().x+'px'                     
                    });
                }
                
                if(this.options.resizable){
                  this.splitbar.makeDraggable({container: this.container});
                  if( this.options.disposition == 'vertical' ){
	                  new Drag.Move(this.pane1, {
	    		          handle: this.splitbar,                          
	    		          modifiers:{x:'width', y:false},						  
	    		          onDrag: this.resize.bind(this),
						  limit: {x:[0 , this.container.getSize().x - this.options.dividersize ]}
    	              });
                  }else{
                      new Drag.Move(this.pane1, {
	    		          handle: this.splitbar,                          
	    		          modifiers:{y:'height', x:false},						  
	    		          onDrag: this.resize.bind(this),
						  limit: {y:[0 , this.container.getSize().y - this.options.dividersize ]}
    	              });    	                	              
                  }
                }
                
                if( this.isParent ){                	
					MooPanes.panes.include(this);
                }                
        },
        
        setParent: function(moopane){
        	this.parentPane = moopane;
        	moopane.addChild(this);        	        	
        },
        
        getChildren: function(){
        	return this.children;
        },
        
        getChild: function( index ){
        	return this.children[index];
        },
        
        addChild: function(moopane){
        	this.children.include(moopane);        	
        },
        
        resize: function(){
				this.container.setStyles({
					width: this.container.getParent().getSize().x,
					height: this.container.getParent().getSize().y
				});
				if( this.options.disposition == 'vertical' ){
                  this.pane1.setStyle ('height', this.container.getSize().y +'px');
				  this.pane2.setStyle ('height', this.container.getSize().y +'px');
				  this.pane2.setStyle( 'left',(this.pane1.getSize().x + this.options.dividersize)+'px'   );
                  this.pane2.setStyle( 'width',(this.container.getSize().x - this.options.dividersize - this.pane1.getSize().x ) +'px' );
				  this.splitbar.setStyle('height',this.container.getSize().y +'px');
				  
                }else{
				  this.pane1.setStyle ('width', this.container.getSize().x + 'px');
				  this.pane2.setStyle ('width', this.container.getSize().x + 'px');
				  this.pane2.setStyle( 'top', (this.pane1.getSize().y + this.options.dividersize ) +'px'  );
				  this.pane2.setStyle( 'height',(this.container.getSize().y - this.options.dividersize - this.pane1.getSize().y )+'px'  );
				  this.splitbar.setStyle('width',this.container.getSize().x +'px' );
                }
                this.children.each(function(pane){ pane.resizeOnParent() }); 
        },
        
        resizeOnParent: function(){			
        	this.container.setStyle( 'width',this.parentPane.pane2.getSize().x+'px' );			
			this.pane1.setStyle( 'width',this.container.getSize().x+'px' );
        	this.pane2.setStyle( 'width',this.container.getSize().x+'px' );
        	this.splitbar.setStyle( 'width',this.container.getSize().x+'px' );
        	this.splitbar.setStyle( 'left',0 );        	        	         	
            this.resize();
        }
        
        
});

MooPane.implement(new Events);
MooPane.implement(new Options);  

window.addEvent('resize', function(){	
   MooPanes.panes.each(function(pane){pane.resize();});
});