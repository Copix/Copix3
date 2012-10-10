function checkBlankTarget (arBlankTarget){
	for(var i = 0 ; i < arBlankTarget.length ; i++){
		$$('a[href=' + arBlankTarget[i] + ']').each (function (el){
			el.set('target', 'blank');
		});
	}
}

function checkPopupTarget (arPopupTarget){
	for(var i = 0 ; i < arPopupTarget.length ; i++){	
		var infos = arPopupTarget[i].split('|');
		var params = infos[1].split(';');
		var width = 0;
		var height = 0;
		for (var j = 0 ; j < params.length ; j++){
			eval (params[j]);
		}
		$$('a[href=' + infos[0] + ']').each (function (el){
			el.addEvent('click', function(){		
				var left = (window.getScrollLeft() + (window.getWidth() - width) / 2);
            	var top = (window.getScrollTop() + (window.getHeight() - height) / 2);
				window.open(el.get('href'),'_blank','top='+top+', left='+left+', width='+width+', height='+height);
				return false;
			});
		});
	}
}

function checkSmoothBoxTarget (arSmoothBoxTarget){
	for(var i = 0 ; i < arSmoothBoxTarget.length ; i++){	
		var infos = arSmoothBoxTarget[i].split('|');
		var params = infos[1].split(';');
		var width = 0;
		var height = 0;
		for (var j = 0 ; j < params.length ; j++){
			eval (params[j]);
		}
		$$('a[href=' + infos[0] + ']').each (function (el){
			el.set('href', el.get('href') + '?TB_iframe=true&height='+height+'&width='+width); 
			el.addClass('smoothbox');
		});
	}
	if (arSmoothBoxTarget.length > 0){
		TB_init ();
	}
}

function addBookmark(public_id){
	var ajax = new Request.HTML({
		url : Copix.getActionURL ('heading|ajax|addbookmark'),
		evalScripts: false,
		onComplete : function (){	
			if (this.response.html != ""){			
				$$('.AddBookmarkTable').each(function(el){
					var tr = el.insertRow (-1);
					tr.className = 'bookmark' + public_id;
					tr.innerHTML = this.response.html;
				}.bind(this));
			}
		}
	});
	ajax.post ({'newBookmark':public_id});
}

function deleteBookmark(public_id){
	var ajax = new Request.HTML({
		url : Copix.getActionURL ('heading|ajax|deletebookmark'),
		onComplete : function (){
			$$('.bookmark'+public_id).each(function(el){				
					el.dispose ();
				});
		}
	});
	ajax.post ({'bookmarkToDelete':public_id});
}

function bookMarkSelectTree(public_id, treeId, filters){
	$$('.mooTree_search').each(function(el){
	 	el.removeClass ('mooTree_search');
	});
	startLoading('elementChooserTree', treeId);
	new Request.HTML ({
	    url: Copix.getActionURL ('heading|ajax|selectNode', {'public_id':public_id, 'formId':treeId, 'filter':filters}),
		evalScripts: true,
		update:'divTreeConstruct'+treeId
	}).send ();
}

/** Creation de la widget deplaçable */
var createDraggableWidget = function (widget, handleId){
	$(handleId).addEvent('mousedown', function(e) {
		widget.toggleClass('cmsdroppablewidget');
		widget.toggleClass('cmsdraggablewidget');
		e = new Event(e).stop();
 
 		var indicator = new Element( 'div', {'class':'widgetindicator'});
		indicator.setStyles({'width': widget.getCoordinates().width, 'height': '38px'});
		widget.setStyles({'width': widget.getCoordinates().width});
		
		var dropElements = $$('.cmsdroppablewidget');
			 		
 		var hoveredWidget = null;
 		var lastHoveredWidget = null;
		var position = null;
		var rapport = 1 / 3;
		var drag = widget.makeDraggable({
			droppables: dropElements,		
			onStart : function(event){
				if (event.rightClick) return;
			},
			onDrag: function (el){
				if(hoveredWidget != null){					
					if (hoveredWidget.hasClass('cmshiddenwidget')){
						indicator.inject( hoveredWidget, 'before');
					} else {
						mouseTop = el.getCoordinates().top - hoveredWidget.getCoordinates().top;
						if (lastHoveredWidget != hoveredWidget){
							rapport = 1 / 2;
						}
						if (mouseTop < hoveredWidget.getCoordinates().height * rapport){
							position = 'before';
							rapport = 2 / 3;
							indicator.inject( hoveredWidget, 'before');
						} else {
							position = 'after';
							rapport = 1 / 3;
							indicator.inject( hoveredWidget, 'after');
						}
					}
				}
			},
			onEnter: function(el,droppable) { 
				if (droppable != widget){
					lastHoveredWidget = hoveredWidget;
					hoveredWidget = droppable;
				}
			}.bind(this),
			onComplete : function (el){				
				var fromColumn = widget.getParent().id;
				widget.inject(indicator, 'before');
				indicator.dispose();
				this.detach(); 
				widget.setStyles({'width':'', 'position':'', 'left':'','top':''});
				if (hoveredWidget && hoveredWidget.hasClass('cmshiddenwidget')){
				     new Request ({
				     url: Copix.getActionURL ('heading|ajax|movewidget', {id : widget.id, column : widget.getParent().id, from : fromColumn})
						}).send ({method : 'get'});
				}else{
				     if (hoveredWidget && hoveredWidget.id){
				    	 new Request ({
				     	url: Copix.getActionURL ('heading|ajax|movewidget', {id : widget.id, column : widget.getParent().id, from : fromColumn, position : position, positionid :hoveredWidget.id})
							}).send ({method : 'get'});
				     }
				}
				widget.toggleClass('cmsdroppablewidget');
				widget.toggleClass('cmsdraggablewidget');
			}
		}).start(e); 
	});	
	$(handleId).addEvent('mouseup', function(e) {
		widget.setStyles({'width':'', 'position':'', 'left':'','top':''});
	});
}

var Mutex = function (exec){
	var type = typeof exec;
	this.exec = exec;
	this.verrou = 0;
	this.demande = false;
		
	this.push = function (){
		--this.verrou;
	}.bind(this);
	
	this.pop = function (){
		++this.verrou;
		this.checkEnvoie ();
	}.bind(this);
	
	this.checkEnvoie = function (){
		if (this.verrou == 0 && this.demande){
			if (type == 'function'){
				this.exec ();
			}else{
				eval (this.exec);
			}
			this.demande = false;
		}
	}.bind(this);
	
	this.execute = function (){
		this.demande = true;
		this.checkEnvoie ();
	}.bind(this);
	
	this.executeFunction = function (func){
		this.demande = true;
		type = typeof func;
		this.exec = func;
		this.checkEnvoie ();
	}.bind(this);
};

var checkSheduleDate = function(id){
	var today = new Date();

	var dateParts = $('scheduler_published_date' + id).value.split('/');	
	var published_date = new Date(dateParts[2], dateParts[1] - 1, dateParts[0], $('scheduler_published_hour'+id).value, $('scheduler_published_minute'+id).value);
	var endDateParts = $('scheduler_end_published_date' + id).value.split('/');	
	var end_published_date = new Date(endDateParts[2], endDateParts[1] - 1, endDateParts[0], $('scheduler_end_published_hour'+id).value, $('scheduler_end_published_minute'+id).value);
	
	var error = false;
	if(end_published_date<published_date){
		error = "La date de fin de publication doit être supérieure à la date de début publication.";
	}
	else if(today>published_date){
		error = "La date de début de publication est inférieure ou égale à la date du jour.";
	} 
	else if (today>end_published_date){
		error = "La date de fin de publication est inférieure ou égale à la date du jour.";
	}
	
	
	if(error){
		$('actionPlanned' + id).setProperty('disabled', 'disabled');
	} else {
		$('actionPlanned' + id).removeProperty('disabled');
	}

	$('schedulerError' + id).innerHTML = error ? "<div class='errors'>" + error + "</div>" : '<br />';

}