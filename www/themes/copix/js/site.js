/* Copix Pro */
//Get  root url
var curl = document.location.href.replace(document.location.protocol+"//","").split("/");

var zurl="";
for (i=0;i<curl.length;i++){
	if(!curl[i].match(/index.php/)){
		zurl+="/"+curl[i];
	}
	if(curl[i].match(/index.php/)){
		break;
	}
}

zurl = document.location.protocol+"/"+zurl

//the site effects
var Site = {
	start : function(){
		Site.processBlogger();
		Site.processShadows();
		Site.createLinkEffect();
		Site.roundedCorners();
		Site.animWikiNavBar();
		Site.createResizeElements();
		Site.createToolBar();	
	},
	
	processShadows: function (){
		$$('.addshadow').each(function (el){
			el.onload=function(){el.addShadow(5)};
		});
	},	
	roundedCorners: function(){
		//let's get rounded !!!
		var softgrey = '#868e74';
		var hardgrey = $('banner').getStyle('background-color');
		var maincolor = $('maincontent').getStyle('background-color');
		
		document.body.style.backgroundColor = softgrey;
		$$('#menu li').each(function (el){
			el.makeRounded('top',{radius: 5});
		});
		document.body.style.backgroundColor = '#FFF';
		
		$$('div.wiki_code').each(function(el){
			el.makeRounded('top',{radius: 15})
			el.makeRounded('bottom',{radius: 15})
		});
	},
	
	createLinkEffect: function(){
		$$('#menu li').each(function (el,i){
				var mli = el.getStyle('margin-top').toInt();
				el.setStyle('z-index',2);
				el.addEvent('mouseenter',function(){
					var ofx = new Fx.Style(el,'margin-top',{duration: 100, wait: true});
					ofx.start(-4);
				});
				el.addEvent('mouseleave',function(){
					var ofx = new Fx.Style(el,'margin-top',{duration: 100,wait: true});
					ofx.start(mli);
				});
		});
		
		if(document.location.href.match(/Accueil/)){
			//l'arrivée du menu		
			var menu_ml = $('menu').getStyle('margin-left').toInt();
			//$('menu').setStyle('margin-left','-800px');
			
			$$('#menu li').each(function (el){
				//el.setStyle('margin-right',120);
				el.setStyle('margin-left',-900);
			});
				
			var timer=4200;
			var mlfx = new Array();
			$$('#menu li').each(function (el,i){
				mlfx[i] = new Fx.Styles(el,{ duration: 1000, 
											 wait: true, 
											 transition: Fx.Transitions.Back.easeOut}
				);
				mlfx[i].start.delay(timer,mlfx[i],{
					'margin-left': 2				
				});
				timer-=1000
			});
		}
		else{
			var timer=200;
			var mlfx = new Array();
			var mli = 0;
			$$('#menu li').each(function (el,i){
				mli = el.getStyle('margin-top').toInt();
				el.setStyle('margin-top',12);
			});
			
			$$('#menu li').each(function (el,i){
				mlfx[i] = new Fx.Styles(el,{ duration: 800, 
											 wait: true, 
											 transition: Fx.Transitions.Bounce.easeOut}
				);
				mlfx[i].start.delay(timer,mlfx[i],{
					'margin-top': mli				
				});
				timer-=50
			});			
		}
		
		
	
		var links = $$('#maincontent a');
		links.each(function(el){
		
			if(el.parentNode.id=="footer") return;
			
			var fx = new Fx.Styles(el,{'wait': false, 'duration': 200});			
			var zcolor = el.getStyle('color')			
			el.addEvent('mouseover',function(){
				fx.start({
					'color': "872a2a"
				});
			});
			el.addEvent('mouseout',function(){
				fx.start({
					'color': zcolor
				});
			});			
		},this);
	},
	
	createResizeElements: function(){
		//wiki editor
		var resizable = $$('.resizable');
		resizable.each(function (el,i){		
			var container = new Element('div').setProperties({
				'class' : 'resizable'
			});
			
			container.injectBefore(el).id=el.id+"_resizer";
			el.setProperties({'class' : 'noresize'});
			el.injectInside(container)
			el.setStyles({
				'height': '100%',
				'width': '100%'
			});
			
			var handle = new Element('div').injectInside(container).setStyles({
				'position': 'relative',
				'margin-right': -15,
				'margin-top': -15,
				'float': 'right',
				'width' : '30px',
				'height' : '30px'
			}).setText('');
			
			container.makeResizable({
				'handle' : handle,
				'onComplete' : Site.setFocus
			});
		});
	},
	
	setFocus: function (element) {
		//console.log('focus check');
		$$('.noresize').each(function(el){el.focus()});
	},
	
	animWikiNavBar: function(){
		
	},
	
	createToolBar: function(){	
		p2 = new SlidePanel({
			'opacity': 0.5,
			'left': "750px",
			'color' : "#BB1111",
			'position': 'top'
		});
		
		var zoomerp = new Element('a').setStyles({
			'font-size': '25pt',
			'margin-left' : '20px'			
		}).setProperties({
			'href' : 'javascript: void(null);',
			'title': 'zoom in'
		});
			
			
		var zoomerm = new Element('a').setStyles({
			'font-size': '25pt',
			'margin-left' : '20px'
				
		}).setProperties({
			'href' : 'javascript: void(null);',
			'title': 'zoom out'
		});
			
	
		zoomerp.addEvent("click",function(){
			//zoom +
			var fz = $('maincontent').getStyle('font-size').toInt() + 1;
			$('maincontent').setStyles({'font-size' : fz+'px'})
		});
		zoomerm.addEvent("click",function(){
			//zoom -
			var fz = $('maincontent').getStyle('font-size').toInt() - 1;
			$('maincontent').setStyles({'font-size' : fz+'px'})
		});
	
		//zoomerp.innerHTML = "+";
		var imgaddr = Copix.getResourceURL('img/tools/zoomplus.png');		
		zoomerp.setHTML('<img alt="zoom in" src="'+imgaddr+'" />');
		p2.add(zoomerp);
		
		//zoomerm.innerHTML = "-";
		var imgaddr = Copix.getResourceURL('img/tools/zoomminus.png');
		zoomerm.setHTML('<img alt="zoom out" src="'+imgaddr+'" />');
		p2.add(zoomerm);	
	
		var printerbutton = new Element('a').setProperties({
			'href': 'javascript: void(null);',
			'title': 'print'
		}).setStyles({
			'margin-left': '20px'
		});
		var imgaddr = Copix.getResourceURL('img/tools/print.png');
		printerbutton.setHTML('<img alt="print content" src="'+imgaddr+'" />');
		
		printerbutton.addEvent('click',function(){
			var toprint = $('maincontent').innerHTML;
			var p = window.open("","Print","height=640,width=480,toobar=no,scrollbar=yes");
			p.title="Print";
			p.document.body.innerHTML=toprint;
			p.print();
			p.close();
		})
		p2.add(printerbutton);
		
		var pan = p2.getContainer();	
	},
	
	processBlogger: function (){
		//return;
		if(!$('blog_mainview')) return;
		$('blog_panel').injectAfter('searchengine');
		//new Element('br').injectAfter('blogger_panel');
		$$('#blog_panel h3').each(function (el){
			el.makeRounded('top', {radius: 8});
		});		
	},
	
	correctSizeAndFooter: function (){
	
		//for anims
		checkSizes();
		
		//main size
		if($('tiers1') && $('tiers1').getSize().size.y>$('maincontent').getSize().size.y){
			$('maincontent').setStyle('height',$('tiers1').getSize().size.y+"px");	
		}
		if($('tiers2') && $('tiers2').getSize().size.y>$('maincontent').getSize().size.y){
			$('maincontent').setStyle('height',$('tiers2').getSize().size.y+"px");	
		}
		if($('oncenter') && $('oncenter').getSize().size.y>$('maincontent').getSize().size.y){
			$('maincontent').setStyle('height',$('oncenter').getSize().size.y+"px");	
		}
	    
	    //footer
	    var windowHeight=window.getSize().size.y;
        if (windowHeight>0) {
            var contentHeight=$('maincontent').getSize().size.y;
            var footerHeight=$('footer').getSize().size.y;
	        if (windowHeight-(contentHeight+footerHeight)>=0) {
	            $('footer').setStyles({
	            	'position':'relative',
	            	'margin-top': (windowHeight-(contentHeight+(4.1*footerHeight))).toInt()+'px'
	           	});	            
	        }
	        else{
	            $('footer').setStyle('position','static');
	       	}
       }
	      
	},
	
	animPanels: function (){
		var timer = 500;
		var fxs = new Array();
		$$('#onright h3, #onright p, #onright ul').each(function (el,i){
			el.setStyle('opacity',0);
			fxs[i] = new Fx.Style(el,'opacity',{duration: 1200});
			fxs[i].start.delay(timer,fxs[i],1);
		});
	}
	
};


window.onload = function() {
	Site.correctSizeAndFooter.delay(50);
}
window.onresize = function() {
	Site.correctSizeAndFooter.delay(50);
}

window.addEvent('domready',function(){
	if($('oncenter')){
	    $('oncenter').makeRounded('top',{radius: 8});
	    $('oncenter').makeRounded('bottom',{radius: 8});    
	    $$('.tiers2 p').each(function (el){
			el.makeRounded('top', {radius: 8});
			el.makeRounded('bottom', {radius: 8});
	    });
	    $$('.tiers1 h3').each(function (el){
			el.makeRounded('top', {radius: 8});
	    });
    }
    
	checkSizes();

    $E('h1.main').setStyle('padding',7);
    
	$E('h1.main').makeRounded('top', {radius: 10})
	$E('h1.main').makeRounded('bottom', {radius: 10})

});

function checkSizes(){
	Site.maxh = $('banner').getSize().size.y.toInt() - 30;
	Site.maxw = $('banner').getSize().size.x.toInt();
	Site.maxw = Site.maxw - 150;
	Site.maxw = Site.maxw.toInt();
}

function moveWords(d){
	var fxs = new Array();
  	var tox = ((Math.random()*Site.maxw)).toInt();
  	if (tox<135) tox=135; 
   	var toy = ((Math.random()*80)).toInt();
	var opacity=Math.random()+0.2;
	if(opacity>0.6) opacity = 0.6;
	
  	fxs[i] = new Fx.Styles(d,{duration: 6000, 
  								transition: Fx.Transitions.Bounce.easeOut
  	});
   	fxs[i].start({
   		'left' : tox,
   		'top': toy,
   		'opacity' : opacity
   	});
}


//when elements are ready... we can run site effects.
window.addEvent('domready', Site.start);