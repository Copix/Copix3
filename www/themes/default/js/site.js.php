<?php       
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
        ob_start ("ob_gzhandler");
    }
    header("Content-type: text/javascript; charset: UTF-8");
    header("Cache-Control: must-revalidate");
    $offset = 60 * 60 ;
    $ExpStr = "Expires: " .
    gmdate("D, d M Y H:i:s",
    time() + $offset) . " GMT";
    header($ExpStr);
?>
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
		Site.roundedCorners();
		Site.animWikiNavBar();
		Site.createLinkEffect();
		Site.createMenuEffect();
		Site.createResizeElements();
		Site.createToolBar();
		Site.processBlogger();		
	},
		
	createMenuEffect: function(){
		//menu
		var timer=500;
		var slidefxs=new Array();
		var items = $$('#menu li');
		items.each(function(el,i){
			//let'get appear
			var ml = el.getStyle('margin-left').toInt();
			if(window.khtml){
				if(!ml) ml='-40';
			}
			el.setStyles({
				'margin-left': '-200px',
				'display':    'block'
			});	
			slidefxs[i] = new Fx.Style(el ,'margin-left', {
				'duration' : 800, 
				'wait': true,
				'transition': Fx.Transitions.bounceOut
			});
			timer += 200;
			slidefxs[i].start.delay(timer, slidefxs[i], ml);
			//console.log(el);			
		},this);
		
		var menulinks = $$("#menu li");
		Site.fontSize = menulinks[0].getStyle('font-size').toInt();
		menulinks.each(function(el,i){
			//work on li element, so parentNode
		    var fxparams = {'duration':250, 'wait': false};
		    var fx1 = new Fx.Style(el,'padding-left', fxparams);
		    var fx2 = new Fx.Style(el,'padding-right', fxparams);
		    var fx3 = new Fx.Style(el,'background-color', fxparams);
		    var fx4 = new Fx.Style(el,'font-size', fxparams)
			el.addEvent('mouseenter', function(e){
				new Event(e).stop();
				fx1.start(10);
				fx2.start(20);
				fx3.start('a40909');
				fx4.start(Site.fontSize*1.5);
			});
			el.addEvent('mouseleave', function(e){
				new Event(e).stop();
				fx1.start(5);
				fx2.start(5);
				fx3.start('4b4d46');
				fx4.start(Site.fontSize);
			});		
		},this);
		
	},
	
	roundedCorners: function(){
		//let's get rounded !!!
		var softgrey = '#868e74';
		var hardgrey = $('banner').getStyle('background-color');
		var maincolor = $('maincontent').getStyle('background-color');
		

		document.body.style.backgroundColor = hardgrey;
		$('maincontent').makeRounded('top', {radius: 8});
		document.body.style.backgroundColor = softgrey;

		$('maincontent').setStyle('background-color',softgrey);
		$('footer').makeRounded('bottom', {radius: 8});
		$('maincontent').setStyle('background-color','#FFF');
		
		$$('.info, h1.main, .wiki_nav_bar, div#wiki_footnotes, #wiki_footnotes h2, .info h2').each(function(el){
			el.makeRounded('top',{radius: 8})
			el.makeRounded('bottom',{radius: 8})
		});
		
		$$('div.wiki_code').each(function(el){
			el.makeRounded('top',{radius: 15})
			el.makeRounded('bottom',{radius: 15})
		});
	},
	
	createLinkEffect: function(){
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
		var nb = $('wiki_nav_bar');
		if(nb){			
			var d = new Element('div').injectBefore(nb);			
			var infoLink = new Element('a').setProperties({
				'href': 'javascript: void(null);'
			}).injectInside(d);
			
			infoLink.setHTML('informations');
			
			var slider = new Fx.Slide(nb);
			slider.hide();			
			infoLink.addEvent('click',function(){
				slider.toggle();
			});
		}
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
		if(!$('blogger_mainview')) return;
		if($('blogger_mainview').getSize().size.y.toInt()<500){
			if(window.ie6)
				$('blogger_mainview').setStyle('height','500px');
			else
				$('blogger_mainview').setStyle('min-height','500px');
		}
		$('blogger_mainview').setStyle('width','70%');
		$('blogger_panel').injectBefore('blogger_mainview');
		$('blogger_panel').setStyle('float','right');
		$('blogger_panel').setStyle('padding','8px');
		$('blogger_panel').setStyle('margin','8px');
		$('blogger_panel').setStyle('margin-top','-90px');
		$('blogger_panel').setStyle('margin-right','22px');
		$('blogger_panel').setStyle('background-color',"#EFEFEF");
		$('blogger_panel').makeRounded('bottom',{radius: 10});
		$('blogger_panel').makeRounded('top',{radius: 10});
		$$('#blogger_panel h3').each(function (el){
			el.makeRounded('top', {radius: 8});
		});
		$$('#blogger_panel p').each(function (el){
			el.makeRounded('bottom', {radius: 8});
		});
		$$('#blogger_panel ul').each(function (el){
			el.makeRounded('bottom', {radius: 8});
		});
	}
};

admin = {
	doPanes: function (){
		//create panels
		var main = new Element('div').setProperties({
			'id': 'mainpane'
		}).injectInside($E('body'));
		
		var left = new Element('div').setProperties({
			'id': 'leftpane'
		}).injectInside($('mainpane'));
		var right = new Element('div').setProperties({
			'id': 'rightpane'
		}).injectInside($('mainpane'));

		console.debug($('leftpane'))
		$('leftpane').id='leftpane';
		$('rightpane').id='rightpane';
		
		$('allcontent').injectInside(right);
		$$('#module_0 a').each(function (link){
			link.injectInside(left);
			new Element('br').injectAfter(link);
			var url = link.href
			link.addEvent('click',function(ev){
				console.log('ouet');
				var e = new Event(ev);
				e.stopPropagation();
				var a = new Ajax(url,{
					data: 'byajax=1',
					method: 'get',
					update: $('rightpane'),
					onComplete: function (){
						$$('#rightpane a').each(function (l){
							var u = l.href;
							l.href="#"
							l.addEvent('click',function(e){
								var e = new Event(e);
								e.stopPropagation();
								var a = new Ajax(u,{
									data: 'byajax=1',
									method: 'get',
									update: $('rightpane')
								}).request();
							})
						})
					}
				}).request();
			});
			link.href="#";
		});
		
		var vpane = new MooPane('leftpane','rightpane', false, {
			disposition: 'vertical', 
			resizable: true, 
			pane1percentage:15
			});
				
		
	}
}

//when elements are ready... we can run site effects.
window.addEvent('domready', Site.start);
//window.addEvent('domready', admin.doPanes);