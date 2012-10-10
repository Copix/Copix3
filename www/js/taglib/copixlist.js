var copixlist_nonajax = {}
var copixlist_nonajaxurl = null;

function copixlist_event_pager (id ,pUrl ,previous, next, last) {
	$$('.next_'+id).each(function (el) {
		el.setStyle('cursor','pointer');  
		el.addEvent('click', function () {
			javascripts.goToPage(id,pUrl,next);
		});
	});
	    
	$$('.previous_'+id).each(function (el) {
		el.setStyle('cursor','pointer');  
		el.addEvent('click', function () {
			javascripts.goToPage(id,pUrl,previous);
		});
	});
	    
	$$('.first_'+id).each(function (el) { 
		el.setStyle('cursor','pointer'); 
		el.addEvent('click', function () {
			javascripts.goToPage(id,pUrl,1);
		});
	});
	    
	$$('.last_'+id).each(function (el) {
		el.setStyle('cursor','pointer');  
		el.addEvent('click', function () {
			javascripts.goToPage(id,pUrl,last);
		});
	});
}

function copixlist_setnonajax (id, url) {
	copixlist_nonajax[id] = true;
	copixlist_nonajaxurl = url;
}

function copixlist_launch (id) {
			// Lance toutes les inscriptions d'événements
			$$('#submit_'+id).each(function (el) {
			  el.setStyle('cursor','pointer'); 
			  el.addEvent('click', function () {
				javascripts.gettable(id,Copix.getActionURL('generictools|copixlist|getTable',{'table_id':id}));
			});});
			
			$$('#reset_'+id).each (function (el) {
				el.setStyle('cursor','pointer');
				el.addEvent('click', function () {
					$('searchform'+id).fireEvent('reset');
				});
			});

			var el = $('searchform'+id);
				if (el != null) {
        			el.addEvent('reset', function (e) {
						try {
        				var e = new Event(e);
        				e.stop();
        				e.stopPropagation();
						} catch (e) {}
        				$$('#searchform'+id+' input').each( function (el) {
        					if (el.getProperty('type')!='button' && el.getProperty('type')!='submit' && el.getProperty('type')!='reset' && el.getProperty('type')!='checkbox') {
        						el.value = '';
        					} else if (el.getProperty('type') == 'checkbox') {
        						el.checked = false;
        					}
        					el.fireEvent('reset');
    					});
    				});
				}
			todo.doEvent (id);
}



var javascripts = {
					currentAjax: {},
        			gettable : function (id, pUrl) {
        				if (copixlist_nonajax[id]) {
        					try {
								$('searchform'+id).submit ();
							} catch (e) {}
        					return;
        				}
						try {
        					$$('#searchform'+id+', #searchform'+id+' select ').each(function (el) { el.fireEvent('formsubmit'); });
						} catch (e) {}
						try {
        					$('submit_'+id).setOpacity(0);
						} catch (e) {}
						loader.load(id);
        				this.currentAjax[id] = new Ajax(pUrl, {
        					method: 'post',
        					postBody: $('searchform'+id),
        					update: $('divlist_'+id),
        					evalScripts : true,
        					onComplete: function () {
								javascripts.currentAjax[id] = null;
								loader.unload(id);
        						$('submit_'+id).setOpacity(1);
    							todo.doEvent (id);
        					}
        				});
						this.currentAjax[id].request();
        			},
					goToPage : function (id, pUrl, page) {
						if (copixlist_nonajax[id]) {
							document.location.href = url+'&page_'+id+'='+page;
						}
						loader.load();
						var temp = {};
						temp['page_'+id] = page;
						new Ajax(pUrl, {
        					method: 'post',
        					update: $('divlist_'+id),
        					evalScripts : true,
        					
							data : temp,
        					onComplete: function () {
								loader.unload();
								todo.doEvent (id);
        					}
        				}).request();
					},
					 orderby : function (id,pUrl,champ,el) {
					 	if (copixlist_nonajax[id]) {
					 		document.location.href = url+'&order_'+id+'='+champ;
					 	}
						loader.load();
						var temp = {} 
						temp['order_'+id] = champ;
					    new Ajax(pUrl, {
        					method: 'post',
        					update: $('divlist_'+id),
        					evalScripts : true,
							data : temp,
        					onComplete: function () {
								loader.unload();
								todo.doEvent (id);
        					}
        				}).request();
					}
        		};






var loader = {
				divloader: null,
				divfond: null,
				load: function (id) {
				    if (loader.divloader == null) {
    					loader.divloader = new Element('div');
    					loader.divloader.setStyles({'vertical-align':'bottom','background-color':'white','border':'1px solid black','width':'100px','height':'100px','top': window.getScrollTop().toInt()+window.getHeight ().toInt()/2-50+'px','left':window.getScrollLeft().toInt()+window.getWidth ().toInt()/2-50+'px','position':'absolute','text-align':'center','background-image':'url('+Copix.getResourceURL('img/tools/load.gif')+')")','background-repeat':'no-repeat','background-position':'center','zIndex':999});
    					loader.divloader.injectInside(document.body);
						cancel = new Element('input');
						cancel.setProperty('type','button');
						cancel.setProperty('value','Annuler');
						cancel.setStyle('margin-top','75px');
						cancel.addClass('copixlist_cancel');
						cancel.injectInside(loader.divloader);
						cancel.addEvent('click', function () {
    						if (javascripts.currentAjax[id] != null) {
    							javascripts.currentAjax[id].cancel();
								javascripts.currentAjax[id] = null;
    							loader.unload();
            					$('submit_'+id).setOpacity(1);
    						}
						});
						loader.divfond = new Element('div');
						loader.divfond.setStyles({'width':window.getWidth(),'height':window.getHeight(),'top': window.getScrollTop(),'left':window.getScrollLeft(),'position':'absolute','text-align':'center','background-color':'black','zIndex':998});
						loader.divfond.setOpacity(0.5);
						loader.divfond.injectInside(document.body);
    				} else {
						loader.divloader.setStyles({'background-color':'white','border':'1px solid black','width':'100px','height':'100px','top': window.getScrollTop().toInt()+window.getHeight ().toInt()/2-50+'px','left':window.getScrollLeft().toInt()+window.getWidth ().toInt()/2-50+'px','position':'absolute','text-align':'center','background-image':'url('+Copix.getResourceURL('img/tools/load.gif')+')','background-repeat':'no-repeat','background-position':'center','zIndex':999});
						loader.divfond.setStyles({'width':window.getWidth(),'height':window.getHeight(),'top': window.getScrollTop(),'left':window.getScrollLeft(),'position':'absolute','text-align':'center','background-color':'black','zIndex':998});
    					loader.divloader.setStyle('visibility','');
						loader.divfond.setStyle('visibility','');
    				}
					loader.divfond.fixdivShow();
				},
				unload: function (id) {
					if (loader.divloader != null) {
				    	loader.divloader.setStyle('visibility','hidden');
						loader.divfond.setStyle('visibility','hidden');
				   	}
					loader.divfond.fixdivHide();
				}
				
				};
				
				var todo = {
					doEvent: function (id, pUrl) {
						$$('.copixlistorder'+id).each(function (el) {
							var rel = el.getProperty('rel'); 
								el.setStyle('cursor','pointer');
								el.addEvent('click', function () {
								javascripts.orderby(id,Copix.getActionURL('generictools|copixlist|getTable',{'table_id':id,'submit':'false'}),rel,el);
							});});
					   }
					};