<style>
	div.webBox { padding: 0; border: 0; position: relative; display: block; margin: 0 0 10px 0; z-index: 1; }
	div.handle { padding: 2px; background: black; color: white; display: block; cursor: move; }
	div.content { padding: 5px; background: white; }
	div.webBoxMarker { border: 1px dotted black; margin: 0 0 5px 0; }
	div#webBoxContainer { width: 100%; }
	div#webBoxContainer .webBoxCol { float: left; vertical-align: top; margin: 1px}
</style>
<h2>Page de contenu</h2>
<p>
Ceci est une ébauche du gestionnaire de contenu de page. Voir par la suite comment gérer du contenu autre que du HTML statique.
<br />
Tout est géré en Mootools.
<br />
Ajouter un élément, déplacez le, changer son contenu...
</p>
<input type="button" id="addwb" value="Add"/>
<div id="webBoxContainer">			
			<div class="webBoxCol" style="width: 49%">
			</div>
			<div class="webBoxCol" style="width: 24%">
			</div>
			<div class="webBoxCol" style="width: 24%">
			</div>
</div>
<script type="text/javascript">
		WebBoxes = {
			webBoxMarker: 0,
			webBoxCols: 0,
			draggables:0,
			
			init: function(){
				$('addwb').addEvent('click',function(){
					WebBoxes.openEditor();					
				});
				WebBoxes.initWebBoxes();
				WebBoxes.checkSize();
				window.addEvent('resize',WebBoxes.checkSize);
			},
			
			openEditor: function (){
				var html = 'Title <input type="text" id="mb_title" /><br />';
				html+='<textarea rows="20" cols="50" id="mb_content">';
				html+='</textarea>';
				html+='<br /><input type="button" id="validate" value="OK"/>';
				wb = WebBoxes.addBox();
				wb.getElementsBySelector('.content')[0].setHTML(html);
				$('validate').addEvent('click',function(){
					wb.getElementsBySelector('.handle')[0].setHTML($('mb_title').value);
					wb.getElementsBySelector('.content')[0].setHTML($('mb_content').value);
					WebBoxes.checkSize();
				});
				WebBoxes.checkSize();
			},
			
			addBox: function(){
				var wb = new Element('div').setProperty('class','webBox');						
				new Element('div').setProperty('class','handle').setHTML('Nouveau Contenu, tapez en HTML !').injectInside(wb);
				new Element('div').setProperty('class','content').setHTML('pouet').injectInside(wb);
				try{
					wb.injectBefore($E('.webBoxCol').getChildren()[0]);
				}catch(e){
					wb.injectInside($E('.webBoxCol'));
				}
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
                //var webBoxCols = $('webBoxContainer').getChildren()[0].getChildren().getChildren()[0];
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
				el.makeDraggable({
					'handle': el.getElementsBySelector('.handle')[0],
					'onBeforeStart': function() {
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
 
						// Remove the marking box
						webBoxMarker.injectInside($E('body')).setStyles({'display': 'none'});
						WebBoxes.checkSize();
					},
					'onDrag': function() {
					    var mouseX = this.mouse.now.x; var mouseY = this.mouse.now.y;
 
					     // Work from first column out and top down
					    webBoxTargetCol = webBoxCols[0];
					    webBoxTargetDiv = null;
 
						// X - Which column?
					    webBoxCols.each(function(el, i){
					        if (mouseX > el.getCoordinates().left) webBoxTargetCol = el;
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
						}
						else
						{
							// Or after a webBox
							if ((webBoxTargetDiv != webBoxMarker) && (webBoxTargetDiv != webBoxMarker.getPrevious())) webBoxMarker.injectAfter(webBoxTargetDiv);
						}
						WebBoxes.checkSize();
					}
				});
			}
		}

window.addEvent('domready',WebBoxes.init);
</script>


