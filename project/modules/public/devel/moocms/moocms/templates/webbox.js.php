
var counter = 0
var count = 0
var toolbars = [];
WebBoxes = {
			webBoxMarker: 0,
			webBoxCols: 0,
			draggables:0,
			boxes: [],
			init: function(){
				WebBoxes.initWebBoxes();
				WebBoxes.checkSize();
				window.addEvent('resize',WebBoxes.checkSize);
			},
						
			addBox: function(){
				var wb = new Element('div').setProperty('class','webBox');						
				//new Element('div').setProperty('class','handle').setHTML('').injectInside(wb);
				new Element('div').setProperty('class','content').setHTML('').injectInside(wb);
				
				wb.injectTop($E('.webBoxCol'));
				WebBoxes.createDraggable(wb);
				WebBoxes.checkSize();
				
				return wb;
			},
	
	
			checkSize: function(){
				var max = 0;
				$$('.webBoxCol').each(function(el){
					if(el.getSize().size.y>max){
						max = el.getSize().size.y;
					}
				});
				$('webBoxContainer').setStyle('height',max.toInt()+10);
			},
	
			initWebBoxes : function(){			
				// WebBoxes shared vars
				var draggables = $$('.webBox');
                WebBoxes.webBoxMarker = new Element('div').addClass('webBoxMarker').setStyles({'display': 'none'}).injectInside($E('body'));
                WebBoxes.webBoxCols = $$('.webBoxCol');
                // WebBoxes drag behavior for each
				draggables.each(function(el){
					// Make each webBox draggable using the handle
					WebBoxes.createDraggable(el);
				});
 				WebBoxes.checkSize();
			},
			
			createDraggable: function(el){
				var webBoxMarker = WebBoxes.webBoxMarker;
				var webBoxCols = WebBoxes.webBoxCols;
				
							
				//edit and remove buttons
				var toolbar = new Element('div').injectTop(el).addClass('wbtools');
				
				var handle = new Element('img').setProperties({
					'src' : '<?php echo _resource('img/tools/wizard.png')?>',
					'class' : 'handle' 
				}).injectTop(toolbar).setStyle('cursor','move');
				
				
				var deletebutton = new Element('img').setProperties({
					'src': '<?php echo _resource('img/tools/delete.png')?>'
				}).setStyles({
					'cursor' : 'pointer'
				})				
				deletebutton.injectInside(toolbar);
				deletebutton.addEvent('click',function(){
					processDelete(el.id);
				})
				
				s = new Fx.Slide(toolbar,{})
				s.hide();
				toolbars.push(s);
				toolbars.each(function (bar){
					bar.hide();
				})
				
				el.makeDraggable({
					'handle': handle,
					'onBeforeStart': function() {
						webBoxCols.each(function (col){
							if(col.getSize().size.y < el.getSize().size.y) col.setStyle('height', el.getSize().size.y+'px');
						});
						// Introduce the marking box, change the draging box to absolute
						// The order the next 4 lines seems to be important for it to work right in Firefox
						webBoxMarker.injectAfter(el).setStyles({'display': 'block', 'height': el.getStyle('height')});
						el.setStyles({'opacity': '0.55', 'z-index': '3', 'width': el.getStyle('width'), 'position': 'absolute'});
						el.injectInside($E('body'));
						el.setStyles({'top': webBoxMarker.getCoordinates().top + "px", 'left': webBoxMarker.getCoordinates().left + "px"});
						WebBoxes.checkSize();
					},
					'onComplete': function() {
						// Replace the draging webBox
						el.setStyle('width',webBoxMarker.getStyle('width').toInt());
						el.injectBefore(webBoxMarker).setStyles({'opacity': '1', 'z-index': '1', 'margin': '0 0 10px 0', 'position': 'relative', 'top': '0', 'left': '0'});
 
 						//record placement
 						for (i in WebBoxes.boxes){ 						
 							if(WebBoxes.boxes[i].id == el.id){

 								WebBoxes.boxes[i].zone = webBoxMarker.getParent().id;
 							}
 						}
 
 						//check order...
 						checkOrders();

						// Remove the marking box
						webBoxMarker.injectInside($E('body')).setStyles({'display': 'none'});
						
						webBoxCols.each(function (col){
							col.setStyle('height', null);
						});
						
						WebBoxes.checkSize();
					},
					'onDrag': function() {
					    var mouseX = this.mouse.now.x; var mouseY = this.mouse.now.y;
 
					     // Work from first column out and top down
					    webBoxTargetCol = webBoxCols[0];
					    webBoxTargetDiv = null;
 
						// X - Which column?
					    webBoxCols.each(function(el, i){
					        if (mouseX > el.getCoordinates().left && 
					        mouseY > el.getCoordinates().top && 
					        mouseY < el.getCoordinates().bottom ) 
					        webBoxTargetCol = el;
					    });
 
					    // Y - If we're half way or more past this webBox then insert it after this one
					    webBoxTargetCol.getChildren().each(function(el, i){
				            if (mouseY > (el.getCoordinates().top + Math.round(el.getCoordinates().height / 2))) webBoxTargetDiv = el;
					    });
 
						// Place the marker
						if (webBoxTargetDiv == null)
						{
							// On top
							if (webBoxTargetCol.getChildren()[0] != webBoxMarker) webBoxMarker.injectTop(webBoxTargetCol);
						}else{
							// Or after a webBox
							if ((webBoxTargetDiv != webBoxMarker) && (webBoxTargetDiv != webBoxMarker.getPrevious())) webBoxMarker.injectAfter(webBoxTargetDiv);
						}						
						WebBoxes.checkSize();
					}
				});
			}
		}
		
window.addEvent('domready',WebBoxes.init);

function addBoxFor(element){
	var element = new Element(element);
	var boxname = element.name;

	var aj = new Ajax('<?php echo _url('moocms|admin|getboxcontent');?>',{
		data: 'name='+boxname,
		onComplete: function(){checkIfEdit(this,boxname,null)},
		execScripts: true
	}).request();
	
	//WebBoxes.checkSize();
}

function checkIfEdit(aj,boxname,datas){
	window.fireEvent('ajax');
	response = aj.response.text;
	if(response.match(/MOOCMS_EDITMODE/)){
		response = response.replace('MOOCMS_EDITMODE','');
		var w = $('TB_ajaxContent')
		w.setHTML('');
		
		var d = new Element('div').setHTML(response).injectInside(w);
		new Element('br').injectInside(d);
		valid = new Element('a').setProperties({
			'title': "OK",
			'href': '#'
		}).setStyles({
			'font-size': '1em',
			height: '20px'
		});
		valid.setText('OK');
		valid.injectInside(d);
		valid.addEvent('click',function(){
			window.fireEvent('savebox');
			var datas = new Hash();			
			this.getParent().getElementsBySelector('input,textarea').each(function(input){
				datas.set(input.name,input.value)
			});
			var aj = new Ajax('<?php echo _url('moocms|admin|getboxcontent');?>',{
				data: 'name='+boxname+'&noedit=true&'+Object.toQueryString(datas.obj),
				onComplete: function(){checkIfEdit(this,boxname,datas)},
				evalScripts: true
			}).request();
		});
	}else{
		//add the box
		var wb = WebBoxes.addBox();
		wb.id="wb"+Math.round(Math.random()*10000)
		wb.getElementsBySelector('.content')[0].setHTML(response);
		var box = {
			boxtype : boxname,
			boxdatas : (datas) ? datas.obj : null,
			zone: 'zone1',
			id: wb.id,
			order: 0
		}
		WebBoxes.boxes.push(box);
		TB_remove(); //to close the edit box
	}
}

function onValidPage(){
	checkOrders();
	var aj = new Ajax('<?php echo _url('moocms|admin|createVersionPage'); ?>',{
			method: 'post',
			data: "pagename="+$('pagename').value+"&template="+$('template').value,
			onComplete: function(){reallySave(aj.response.text);}
		}).request();
	
}

function reallySave(pagedate){

	if(pagedate.match(/ERROR/)){
		$('messages').setStyle('opacity','0');
		$('messages').setStyles({
			color: "#995555",
			'font-size': '12pt'
		});
		$('messages').setHTML(pagedate);
		$('messages').setStyle('opacity','1');
		return false;
	}

	var aj = new Array();
	counter = 0;
	count = 0;
	
	//how many boxes ?
	for (i in WebBoxes.boxes){
		if(WebBoxes.boxes[i].boxtype){
			counter++;
		}
	}
	//let's go
	for (i in WebBoxes.boxes){
		if(WebBoxes.boxes[i].boxtype){
			var tmpdata ='pagename='+$('pagename').value;
			tmpdata+='&pagedate='+pagedate;
			tmpdata+="&zone="+WebBoxes.boxes[i].zone+"&id="+WebBoxes.boxes[i].id;
			tmpdata+="&order="+WebBoxes.boxes[i].order+"&boxtype="+WebBoxes.boxes[i].boxtype;
			if(WebBoxes.boxes[i].boxdatas)
				tmpdata+="&"+Object.toQueryString(WebBoxes.boxes[i].boxdatas); 

			aj[i] = new Ajax("<?php echo _url('moocms|admin|savepage'); ?>",{
				method: 'post',
				data : tmpdata,
				onComplete: function(){
					count++;
					checkOk()
				}
			}).request(); 	
		}
	}
}

function checkOk(){
	$('messages').setStyle('opacity','0');
	$('messages').setStyles({
		color: "#559955",
		'font-size': '12pt'
	});
	$('messages').setHTML('Page saved !');
	if(count>=counter){		
		$('messages').setStyle('opacity','1');
		var fx = new Fx.Styles($('messages'),{});
		fx.start.delay(5000,fx,{
			'opacity':0
		});
		counter = 0;
		count = 0;
	}
}


function checkOrders (){
	$$('.webBoxCol').each(function (wbc){
		var zone = wbc.id;
		var order = 0;
		wbc.getElementsBySelector('.webBox').each(function (wb){
			for (i in WebBoxes.boxes){
				if(WebBoxes.boxes[i].boxtype){
					if(WebBoxes.boxes[i].id == wb.id){
						WebBoxes.boxes[i].zone = zone;
						WebBoxes.boxes[i].order = order;
					}
				}
			}
			order++;			
		})
	})
}

function processDelete(id){
	//console.log(id);
	for (i in WebBoxes.boxes){
		if(WebBoxes.boxes[i].boxtype){
			if(WebBoxes.boxes[i].id == id){
				WebBoxes.boxes[i] = null
				delete WebBoxes.boxes[i];
			}
		}
	}
	$(id).remove();
	
}
