String.prototype.find=function(what){
return(this.indexOf(what)>=0 ? true : false);
}


var Nifty = new Class({
	
	initialize : function() {
	},
	//Initialisation des options	
	info: function(opt) {
		
        this.opt = {
			fixedheight: false,
			corner: 'top bottom',
			radius: 8,
			overcolor: null,
			transition: Fx.Transitions.Circ.easeIn
    	};

        Object.extend(this.opt, opt || {});

    },

	 Nifty: function (selector,options){
	 	this.info(options);
		
		currentcolor = selector.getStyle('background-color');
		
		var i,h=0;
		
		if(this.opt.fixedheight)
		    h=selector.offsetHeight;
		
		    this.Rounded(selector);
		
		if(this.opt.fixedheight) this.SameHeight(selector,h);
		
		//Gestion des événements pour les rollovers
		/*window.addEvent('DOMAttrModified',function(e){
			  var test = new Event(e);
			  if (selector!=test.target || test.event.attrName!='style' || selector.getStyle ('background-color')=='transparent') return;
			  this.color = selector.getStyle ('background-color');
			  color = selector.getStyle ('background-color');
			  selector.setStyle('background-color','transparent');
			  selector.getChildren().each(function(lem) {
			  	if (lem.getProperty('class')!='niftycorners') {
					lem.setStyles({'background-color': color
			   	    });
			    } else {
					lem.getChildren().each(function(lemb) {
						lemb.setStyles({'background-color': color
			   	    });
					});	
				}
			 });
	});*/
	
		if (this.opt.overcolor!=null) {
			overcolor = this.opt.overcolor;
			selector.addEvent('mouseover', function(){
				color = overcolor;
				selector.getChildren().each(function(lem) {
			  	if (lem.getProperty('class')!='niftycorners') {
					lem.setStyles({'background-color': color
			   	    });
			    } else {
					lem.getChildren().each(function(lemb) {
						lemb.setStyles({'background-color': color
			   	    });
					});	
				}
			 });
			});
			selector.addEvent('mouseout',function() {
				color = currentcolor;
				selector.getChildren().each(function(lem) {
			  	if (lem.getProperty('class')!='niftycorners') {
					lem.setStyles({'background-color': color
			   	    });
			    } else {
					lem.getChildren().each(function(lemb) {
						lemb.setStyles({'background-color': color
			   	    });
					});	
				}
			 });
				
			});
		}
	},
	
	Rounded: function (selector){
		var i,top="",bottom="";
	    this.opt.corner=this.opt.corner.replace("left","tl bl");
	    this.opt.corner=this.opt.corner.replace("right","tr br");
	    this.opt.corner=this.opt.corner.replace("top","tr tl");
	    this.opt.corner=this.opt.corner.replace("bottom","br bl");
	    
		if(this.opt.corner.find("tl")){
	        top="both";
	        if(!this.opt.corner.find("tr")) top="left";
	        }
	    else if(this.opt.corner.find("tr")) top="right";
		
	    if(this.opt.corner.find("bl")){
	        bottom="both";
	        if(!this.opt.corner.find("br")) bottom="left";
	        }
	    else if(this.opt.corner.find("br")) bottom="right";
		
		this.FixIE(selector);
		
		var clone=selector.clone();
		selector.empty;
		selector.set('html', '');
		
		clone.removeProperty('id');
		clone.removeProperty('class');
		
		if (selector.getStyle('height').find('px')) {
				hei = selector.getStyle('height').toInt() - this.opt.radius*2;
				if (window.ie)
					if (hei<0) hei=0;
				clone.setStyle('height',hei);
		}
		
		var pb = selector.getStyle('padding-bottom').toInt()-this.opt.radius;
		var pt = selector.getStyle('padding-top').toInt()-this.opt.radius;
		if (window.ie) {
			if (pb<0) pb = 0;
			if (pt<0) pt = 0; 
		}
		
		clone.setStyles(selector.getStyles('background-color','font-size','padding'));
		clone.setStyles({
			'margin-bottom':0,
			'margin-top':0,
			'padding-bottom':pb,
			'padding-top':pt,
			'width':'auto'
		});
		
		
		
		if(top!="") {
			d = this.AddTop(selector,top);
			d.injectTop(selector);	
		}
		
		clone.injectInside(selector);
		
		if(bottom!="") {
			d = this.AddBottom(selector,bottom);
			d.injectInside(selector);
		}
		selector.setStyles({'padding':"0",'background-color':'transparent'});
		
		$$('div.niftycorners').each(function(el) {
			el.setStyles({
			'width':el.getStyle('width'),
			'background-color':'transparent',
			'display':'block'
			});
			
			el.getChildren().each(function(elem){
			elem.setStyles({
					'display':'block',
					'height': '1px',
					'line-height':'1px',
					'font-size': '1px',
	    			'overflow':'hidden',
					'border-style':'solid',
					'border-width': '0px'
				});
			
			});
		});
		
	},
	
	AddTop: function (el,side){
		var d=new Element("div"),lim=4,border="",p,i,btype="r",bk,color;
		
		d.addClass("niftycorners");		
	    
		for(i=1;i<=this.opt.radius;i++)
	    	d.appendChild(this.CreateStrip(i,side,this.getBk(el),border));
		return d;
	},
	
	AddBottom: function (el,side){
		var d=new Element("div"),lim=4,border="",p,i,btype="r",bk,color;
	
		d.addClass("niftycorners");	
		
		for(i=this.opt.radius;i>0;i--) {
	    		d.appendChild(this.CreateStrip(i,side,this.getBk(el),border));
		}
		return d;
		
	},
		
	CreateStrip: function (index,side,color,border){
		var x = new Element ('div');
		size = Math.round(this.opt.transition((this.opt.radius - index) / this.opt.radius, 0, 1, 1) * this.opt.radius);
		//size = Math.round(fx.transitions.linear((this.opt.radius - index) / this.opt.radius, 0, 1, 1) * this.opt.radius);
		x.style.backgroundColor=color;
		x.setStyle('border','none');
		x.addClass('niftyover')
		x.setStyles({
				'margin-left':size,
				'margin-right':size
		});
		
		if(side=="left"){
		    x.style.marginRight="0";
		    }
		else if(side=="right"){
		    x.style.marginLeft="0";
		}
		return(x);
	},
	
	FixIE: function (el){
		/*if(el.currentStyle!=null && el.currentStyle.hasLayout!=null && el.currentStyle.hasLayout==false)
		    el.style.display="inline-block";*/
	},
	
	SameHeight: function (selector,maxh){
		var i,t,j,gap;
		    if(selector.offsetHeight>maxh) maxh=selector.offsetHeight;
		    selector.style.height="auto";
		    gap=maxh-selector.offsetHeight;
		    if(gap>0){
		        t=this.CreateEl("b");t.className="niftyfill";t.style.height=gap+"px";
		        nc=selector.lastChild;
		        if(nc.className=="niftycorners")
		            selector.insertBefore(t,nc);
		        else selector.appendChild(t);
		    }
	},
	
	getBk: function (x){
		var c=x.getStyle("background-color");
		if(c==null || c=="transparent" || c.find("rgba(0, 0, 0, 0)"))
		    return("transparent");
		if(c.find("rgb")) c=this.rgb2hex(c);
		return(c);
	},
	
	rgb2hex: function (value){
		var hex="",v,h,i;
		var regexp=/([0-9]+)[, ]+([0-9]+)[, ]+([0-9]+)/;
		var h=regexp.exec(value);
		for(i=1;i<4;i++){
		    v=parseInt(h[i]).toString(16);
		    if(v.length==1) hex+="0"+v;
		    else hex+=v;
		    }
		return("#"+hex);
	},
	
	changecolor: function(selector) {
			
			  
	}

	
});

Element.implement({
	roundedNifty: function(round,options){
		var test= new Nifty();
		if ($type(this)=='element') {
			var opt = {corner :round};
			 Object.extend(options, opt || {});	 
			 test.Nifty(this,options);
		} else {
			this.each(function(element){
				element.roundedNifty(round,options);
  			});
		}
	}
});