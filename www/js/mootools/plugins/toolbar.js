/**
* SlidePanel version 0.2
* Author: Patrice Ferlet (metal3d@copix.org)
* Licence: MIT (to be included into mootools)
* Usage: 
*   p = new SidePanel(object)
* object may contain:
    position (top,left, right or bottom is where the toolbar appears)
    duration
    opacity
    left or right (if position is bottom or top)
    bottom or top (if position is right or left)
    color is the background color
    
* you can add element with:
    p.add(element)
* element is a mootools Element.js object
* Some options will come soon
* have fun !
*/
var SlidePanel = new Class({

	initialize: function(options){
		this.options = options;
		this.bord = "5px";
		this.margin=0;
		
		if(!options.opacity){
			this.options.opacity=0.5
		}
		if(!options.color){
			this.options.color="white"
		}

		if(!options.position){
			this.options.position="top"
		}
		
		if(!options.duration){
			this.options.duration = 500
		}
		
		if(this.options.position=="top"){
			this.styletochange="margin-top"
		}else if(this.options.position=="bottom"){
			this.styletochange="margin-bottom"
		}else if(this.options.position=="left"){
			this.styletochange="margin-left"
		}else if(this.options.position=="right"){
			this.styletochange="margin-right"
		}

		this.pan = this.createPanel();

	},


	createPanel: function (){		
		var pan = new Element("div").setStyles({
		    'position': 'absolute',
			'display' : 'block',
			'background-color' : this.options.color,
			'opacity' : this.options.opacity
		}).setProperties({
			'id': "z_toolbar"+(Math.random()*10000).toInt()
		});
		/*if(this.options.position == "left" || this.options.position == "right")
		    this.margin = -this.options.width.toInt() + this.bord.toInt();
		else
			this.margin = -this.options.height.toInt() + this.bord.toInt();
		*/
		if(this.options.height){
			pan.setStyle('height',this.options.height.toInt()+"px");
		}

		if(this.options.width){
			pan.setStyle('width',this.options.width.toInt()+"px");
		}

		if(this.options.left){
			pan.setStyles({
		    'left' : this.options.left.toInt()+"px",
		    'margin-left' : this.margin
		    });		    
		}
		if(this.options.top){
			pan.setStyles({
		    'top': this.options.top.toInt()+"px",
   		    'margin-top' : this.margin
		    });
		}
		if(this.options.bottom){
			pan.setStyles({
		    'bottom': this.options.bottom.toInt()+"px",
   		    'margin-bottom' : this.margin
		    });
		}
		if(this.options.right){
			pan.setStyles({
		    'right': this.options.right.toInt()+"px",
   		    'margin-right' : this.margin
		    });
		}
		if(this.options.position=="top"){
			pan.setStyles({
				'top': '0px'
			});
		}else if(this.options.position=="bottom"){
			pan.setStyles({
				'bottom': '0px'
			});
		}else if(this.options.position=="left"){
			pan.setStyles({
				'left': '0px'
			});
		}else if(this.options.position=="right"){
			pan.setStyles({
				'right': '0px'
			});
		}		
		pan.injectInside(document.body);
		//roudify
		//Nifty('#'+pan.id,'bottom normal');
		//keep panel		
		//and check if we have to show !
		document.addEvent('mousemove', function(event){
				this.checkMouse(event);
			}.bindWithEvent(this)
		);	
		return pan		
	},
	
	check: function(){
		console.debug(this.pan);
	},
	
	
	checkMouse : function (event){
		//check if mouse is on toolbar zone
		//console.debug(this.pan)
		pan = this.pan;
		
		var left = pan.getStyle('left').toInt();
		var right = left+pan.getStyle('width').toInt();
		var top = pan.getStyle('top').toInt();
		var bottom = top + pan.getStyle('height').toInt();
		
		var slidefx = new Fx.Style(pan,this.styletochange,{
			'duration': this.options.duration,
			'wait': false,
			'transition' : Fx.Transitions.bounceOut
		});				
		if(event.client.x>left 
			&& event.client.x<right 
			&& event.client.y>top
			&& event.client.y<bottom){
			if(pan.getStyle(this.styletochange).toInt() == this.margin)
				slidefx.start(0);
		}else{
			if(pan.getStyle(this.styletochange).toInt() == 0)
				slidefx.start(this.margin);
		}
	},
	
	add: function(element){
		element.injectInside(this.pan);
		if(this.options.position == "left" || this.options.position == "right")
		    this.margin = -this.pan.getStyle('width').toInt() + this.bord.toInt();
		else
			this.margin = -this.pan.getStyle('height').toInt() + this.bord.toInt();
			
		this.pan.setStyle(this.styletochange,this.margin+"px")
	},
	
	getContainer: function(){
		return this.pan;
	}
});