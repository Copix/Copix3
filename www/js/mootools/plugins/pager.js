var Pager = new Class({
	initialize : function() {
	},
	currentline:0,
	currentpage:1,
	//Initialisation des options	
	info: function(opt) {
        this.opt = {
			nbline: 10,
			next: 'next',
			previous:'prev'
    	};

        Object.extend(this.opt, opt || {});

    },
    
	pager : function(pager,options) {
		this.info(options);
		var tempSize= new Array();
		var tablesize = pager.getStyle('width');
		$$('#'+pager.getProperty('id')+' thead tr th').each(function(el) {
			tempSize.push(el.getStyle('width'));
		});
		var divhead = new Element('div');
		divhead.setProperty('id','divhead');
		divhead.injectBefore(pager);
		var tablehead = new Element('table');
		tablehead.setProperty('id',pager.getProperty('id'));
		tablehead.setProperty('class',pager.getProperty('class'));
		tablehead.setStyles({'width':'100%','padding':'0px','margin':'0px','border':'none'});
		tablehead.injectInside(divhead);

		var temp = $$('#'+pager.getProperty('id')+' thead');
		myclone = temp[0];
		myclone.injectInside(tablehead);
		myclone.setStyles({
			'border':'none',
			'width':'100%',
			'margin':0
		});

		$$('#divhead thead tr th').each(function(el,i) {
			el.setStyle('width',tempSize[i]);
		});
		
		divhead.setStyles({
			'border':pager.getStyle('border'),
			'border-bottom':'1px solid '+$$('#divhead thead tr th')[0].getStyle('background-color'),
			'width':tablesize,
			'padding':0,
			'margin':pager.getStyle('margin'),
			'background-color':$$('#divhead thead tr th')[0].getStyle('background-color')
		});
		
		var divbody = new Element('div');
		divbody.setProperty('id','divbody');
		divbody.setStyles({
			'overflow':'hidden',
			'border':pager.getStyle('border'),
			'border-top':'none',
			'width':tablesize,
			'padding':0,
			'margin':pager.getStyle('margin')
		});
		divbody.injectAfter('divhead');

		var tablebody = new Element('table');
		tablebody.setProperty('id',pager.getProperty('id'));
		tablebody.setProperty('class',pager.getProperty('class'));
		tablebody.setStyles({'width':'100%','padding':'0px','margin':'0px','border':'none'});
		tablebody.injectInside(divbody);
		temp = $$('#'+pager.getProperty('id')+' tbody');		
		myclonebody = temp[0];
		
		myclonebody.injectInside(tablebody);
		i=0;
		$$('#divbody tbody tr td').each(function(el,i) {
			if (i < tempSize.length) {
				el.setStyle('width',tempSize[i]);
			}
		});
		

		myclonebody.setStyles({
			'border':'none',
			'width':'100%',
			'margin':0
		});
		
		pager.remove();
		var listtr = $$('#divbody tbody tr');
		if (listtr.length <= this.opt.nbline) {
			return null;
		}
		var hauteur = listtr[this.opt.nbline].getTop()-listtr[0].getTop(); 
		divbody.setStyle('height',hauteur+'px');
		
		var scrollExample = new Fx.Scroll(divbody,{'wait':true});
		

		var nbline=this.opt.nbline;
		this.nbpage = Math.ceil(listtr.length/nbline);
		$('nbpage').innerHTML = 'page '+this.currentpage+' sur '+this.nbpage;
		
		$('next').addEvent('click',function(){
			if ((this.currentline+nbline) < listtr.length) {
				this.currentpage++;
				var start = listtr[this.currentline].getTop() - listtr[0].getTop();
				this.currentline = this.currentline + nbline;
				var stop = listtr[this.currentline].getTop() - listtr[0].getTop();

				if (this.currentline + nbline < listtr.length) {
					var next = listtr[this.currentline+nbline].getTop() - listtr[0].getTop();
					
				} else {
					var next = listtr[listtr.length-1].getTop() - listtr[0].getTop() + listtr[listtr.length-1].getStyle('height').toInt();
				}

				var hauteur = next-stop;

				var currentline = this.currentline;
				var exampleFx = new Fx.Style('divbody', 'height', {
					onComplete:function(){
							if ((currentline+nbline) < listtr.length) {
								//scrollExample.scrollTo(start, stop);
								scrollExample.scrollTo(0, stop);
							} else { 
								scrollExample.toBottom({'wait':true});
							}
						}
				});
				exampleFx.start(divbody.getStyle('height'),hauteur);
				$('nbpage').innerHTML = 'page '+this.currentpage+' sur '+this.nbpage;
			}
		}.bindWithEvent(this));
		
		$('prev').addEvent('click',function(){
			var start = listtr[this.currentline].getTop() - listtr[0].getTop();
			if (this.currentline!=0) {
				this.currentpage--;
				this.currentline = this.currentline - nbline;
			}
			var stop = listtr[this.currentline].getTop() - listtr[0].getTop();
			var next = listtr[this.currentline+nbline].getTop() - listtr[0].getTop();
			//scrollExample.scrollTo(start, stop);
			scrollExample.scrollTo(0, stop);
			var hauteur = next-stop;
			var exampleFx = new Fx.Style('divbody', 'height', {
				duration: 1000
			});
			exampleFx.start(divbody.getStyle('height'),hauteur);
			$('nbpage').innerHTML = 'page '+this.currentpage+' sur '+this.nbpage;
		}.bindWithEvent(this))
	}
});


Element.extend({
	pager: function(options){
		var temp = new Pager();
		temp.pager(this,options);
		
	}
});