var updateToolBar = function (identifiantPortlet, page_id){
	if($('cancel_link_'+identifiantPortlet)){
		$('cancel_link_'+identifiantPortlet).setStyle('display','inline');
	}
	if($('update_link_'+identifiantPortlet)){
		$('update_link_'+identifiantPortlet).href = Copix.getActionURL('portal|admin|savePortlet', {"id":identifiantPortlet, "editId":page_id});
	}
	if($('update_link_'+identifiantPortlet)){
		$('update_link_'+identifiantPortlet).innerHTML = "<img src='"+Copix.getResourceURL('img/tools/save.png')+"' alt='Enregistrer' />";
	}
}

/**Créé les barres d'outil/deplacement au dessus des portlets en édition */
var createPortletDivMenu = function (portletId, content){
	var divMenu = new Element ('div', {
		/*'class': 'editPortlet', */'id':'divMenu'+portletId
	});
	divMenu.set ('html', content);

	divMenu.setStyle('width', "100%");
	divMenu.setStyle('display', "none");
	divMenu.inject($(portletId), 'top');
	$(portletId).addClass ('draggable');
	
	$(portletId).addEvent('mouseover', function(event){
		$('divMenu'+portletId).addClass('editPortlet');
	});
	$(portletId).addEvent('mouseout', function(event){
		$('divMenu'+portletId).removeClass('editPortlet');
	});
}

/** Creation de la portlet deplaçable */
var createDraggablePortlet = function (portletId, handleId, editId){
	$(handleId).addEvent('mousedown', function(e) {
		if (e.rightClick) return;
		e = new Event(e).stop();
 
 		var indicator = new Element( 'div', {'class':'indicator'});
		indicator.setStyles({'width': $(portletId).getCoordinates().width, 'height': '38px'});
	
 		var indicatorLeft = new Element( 'div');
 		indicatorLeft.setStyles({'width':'16px', 'background': 'url('+Copix.getResourceURL('portal|img/indicator/left.png')+') no-repeat', 'height': '38px'});
 		indicatorLeft.inject(indicator);
		
 		var indicatorLongLeft = new Element( 'div');
 		indicatorLongLeft.setStyles({'width':($(portletId).getCoordinates().width - 134) / 2 +'px','background': 'url('+Copix.getResourceURL('portal|img/indicator/long.png')+') repeat', 'height': '38px'});
 		indicatorLongLeft.inject(indicator);
 		
		var indicatorTitle = new Element( 'div');
		indicatorTitle.setStyles({'width':'102px','background': 'url('+Copix.getResourceURL('portal|img/indicator/title.png')+') no-repeat', 'height': '38px'});
		indicatorTitle.inject(indicator);
		
 		var indicatorLongRight = new Element( 'div');
 		indicatorLongRight.setStyles({'width':($(portletId).getCoordinates().width - 134) / 2 +'px','background': 'url('+Copix.getResourceURL('portal|img/indicator/long.png')+') repeat', 'height': '38px'});
 		indicatorLongRight.inject(indicator);
 		
 		var indicatorRight = new Element( 'div');
 		indicatorRight.setStyles({'width':'16px', 'background': 'url('+Copix.getResourceURL('portal|img/indicator/right.png')+') no-repeat', 'height': '38px'});
 		indicatorRight.inject(indicator);

		var dropElements = $$('.portlet, .ajoutPortlet');

		var clone = $(portletId).setStyle('opacity', 0.8).clone()
			.setStyles($(portletId).getCoordinates()) 
			.setStyles({'position': 'absolute', 'display':'block', 'z-index':3})
			.addEvent('emptydrop', function() {
				$(portletId).remove();
				drop.removeEvents();
			}).inject(document.body);

 		$(portletId).setStyle('opacity', 0.4);
 		
 		var hoveredPortlet = null;
 		var lastHoveredPortlet = null;
		var position = null;
		var rapport = 1 / 3;
		var drag = clone.makeDraggable({
			droppables: dropElements,		
			onStart : function(event){
				if (event.rightClick) return;
				showCMSDiv();
			},
			onDrag: function (el){
				if(hoveredPortlet != null){					
					mouseTop = el.getCoordinates().top - hoveredPortlet.getCoordinates().top;
					if (lastHoveredPortlet != hoveredPortlet){
						rapport = 1 / 2;
					}
					if (mouseTop < hoveredPortlet.getCoordinates().height * rapport){
						position = 'before';
						rapport = 2 / 3;
						indicator.inject( hoveredPortlet, 'before');
					} else {
						position = 'after';
						rapport = 1 / 3;
						indicator.inject( hoveredPortlet, 'after');
					}

					indicator.setStyle('width',  hoveredPortlet.getCoordinates().width);
					indicatorLongRight.setStyle('width',  (hoveredPortlet.getCoordinates().width - 134) / 2 +'px');
					indicatorLongLeft.setStyle('width',  (hoveredPortlet.getCoordinates().width - 134) / 2+'px');
				}
			},
			onDrop: function(el,droppable) { 
				clone.dispose();
			},
			onEnter: function(el,droppable) { 
				if (!$(portletId).hasChild(droppable)){
					lastHoveredPortlet = hoveredPortlet;
					hoveredPortlet = droppable;
					var newWidth = droppable.getCoordinates().width;
					clone.setStyle('width', newWidth);
				}
			}.bind(this),
			onComplete : function (el){					
				$(portletId).inject(indicator, 'before');
				$(portletId).setStyle('opacity', 1);
				$(handleId).setStyle('width', $(portletId).getComputedSize().width);
				indicator.dispose();
				this.detach(); 
				
				if (hoveredPortlet){
					if (hoveredPortlet.hasClass('ajoutPortlet')){
					     new Request ({
					     url: Copix.getActionURL ('portal|admin|move', {id : $(portletId).id, column : hoveredPortlet.id, editId : editId})
							}).send ({method : 'get'});
					  }else{
					     if (hoveredPortlet.id){
					     new Request ({
					     	url: Copix.getActionURL ('portal|admin|move', {id : $(portletId).id, on : hoveredPortlet.id, position : position, editId : editId})
								}).send ({method : 'get'});
					     }
					}
				}
				hideCMSDiv();
			}
		}); 
 
		drag.start(e);
	});	
}

/** Creation de la portlet deplaçable */
var createDraggableElementFromMenu = function (element, editId){
	element.addEvent('mousedown', function(e) {
		if (e.rightClick) return;
		e = new Event(e).stop();
 
 		var indicator = new Element( 'div', {'class':'indicator'});
		indicator.setStyles({'width': element.getCoordinates().width, 'height': '38px'});
	
 		var indicatorLeft = new Element( 'div');
 		indicatorLeft.setStyles({'width':'16px', 'background': 'url('+Copix.getResourceURL('portal|img/indicator/left.png')+') no-repeat', 'height': '38px'});
 		indicatorLeft.inject(indicator);
		
 		var indicatorLongLeft = new Element( 'div');
 		indicatorLongLeft.setStyles({'width':(element.getCoordinates().width - 134) / 2 +'px','background': 'url('+Copix.getResourceURL('portal|img/indicator/long.png')+') repeat', 'height': '38px'});
 		indicatorLongLeft.inject(indicator);
 		
		var indicatorTitle = new Element( 'div');
		indicatorTitle.setStyles({'width':'102px','background': 'url('+Copix.getResourceURL('portal|img/indicator/title.png')+') no-repeat', 'height': '38px'});
		indicatorTitle.inject(indicator);
		
 		var indicatorLongRight = new Element( 'div');
 		indicatorLongRight.setStyles({'width':(element.getCoordinates().width - 134) / 2 +'px','background': 'url('+Copix.getResourceURL('portal|img/indicator/long.png')+') repeat', 'height': '38px'});
 		indicatorLongRight.inject(indicator);
 		
 		var indicatorRight = new Element( 'div');
 		indicatorRight.setStyles({'width':'16px', 'background': 'url('+Copix.getResourceURL('portal|img/indicator/right.png')+') no-repeat', 'height': '38px'});
 		indicatorRight.inject(indicator);

		var dropElements = $$('.portlet, .ajoutPortlet');

		var clone = element.setStyle('opacity', 0.8).clone()
			.setStyles(element.getCoordinates()) 
			.setStyles({'position': 'absolute', 'display':'block', 'z-index':3})
			.addClass('clone')
			.addEvent('emptydrop', function() {
				element.remove();
				drop.removeEvents();
			}).inject(document.body);
	
 		
 		var hoveredPortlet = null;
 		var lastHoveredPortlet = null;
		var position = null;
		var rapport = 1 / 3;
		var drag = clone.makeDraggable({
			droppables: dropElements,		
			onStart : function(event){
				if (event.rightClick) return;
				showCMSDiv();
				clone.removeClass('clone');
				$('closeAddPortletMenu').fireEvent('click');
			},
			onDrag: function (el){
				if(hoveredPortlet != null){					
					mouseTop = el.getCoordinates().top - hoveredPortlet.getCoordinates().top;
					if (lastHoveredPortlet != hoveredPortlet){
						rapport = 1 / 2;
					}
					if (mouseTop < hoveredPortlet.getCoordinates().height * rapport){
						position = 'before';
						rapport = 2 / 3;
						indicator.inject( hoveredPortlet, 'before');
					} else {
						position = 'after';
						rapport = 1 / 3;
						indicator.inject( hoveredPortlet, 'after');
					}

					indicator.setStyle('width',  hoveredPortlet.getCoordinates().width);
					indicatorLongRight.setStyle('width',  (hoveredPortlet.getCoordinates().width - 134) / 2 +'px');
					indicatorLongLeft.setStyle('width',  (hoveredPortlet.getCoordinates().width - 134) / 2+'px');
				}
			},
			onEnter: function(el,droppable) { 
				lastHoveredPortlet = hoveredPortlet;
				hoveredPortlet = droppable;
			}.bind(this),
			onDrop: function(el,droppable) { 
				clone.dispose();
			},
			onComplete : function (el){		
				if (hoveredPortlet){
					if (hoveredPortlet.hasClass('ajoutPortlet')){
						showWaitMessage();
						window.location.href = el.get('rel') + '&position=' + hoveredPortlet.id;
					}else{
						if (hoveredPortlet.id){
							showWaitMessage();
							window.location.href = el.get('rel') + '&position=' + position + "&on=" + hoveredPortlet.id;
						}
					}
				}
				hideCMSDiv();
			}
		}); 
 
		drag.start(e);
	});	
}

var showCMSDiv = function(){
	var div = new Element('div', {'id':'CMSPage_modaldiv', 'class' : 'copixwindow_modaldiv'});
	div.setStyles({'position' :'fixed',
			'left' : 0,
			'top' : 0,
			'width' : '100%',
			'height' : '100%',
			'z-index': '1'			
	});
	$('CMSPage').getParent().adopt(div);
	$('CMSPage').addClass('CMSPageHover');
}

var hideCMSDiv = function(){
	$('CMSPage_modaldiv').dispose();
	$('CMSPage').removeClass('CMSPageHover');
}

var updateText = function(text, portletId, pageId, editor){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('portal|ajax|updateText'),
		evalScripts: true,
		onComplete : function (){
			ajaxOff();
		}
	}).post({'editId' : pageId,
				'portletId' : portletId,
				'text' : text,
				'editor' : editor
				});
}

var updateEditor = function(portletId, pageId, editor){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('portal|ajax|updateEditor'),
		evalScripts: true,
		onComplete : function (){
			ajaxOff();
		}
	}).post({'editId' : pageId,
				'portletId' : portletId,
				'editor' : editor
				});
}

var updateNuage = function(identifiantFormulaire, portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	}
	new Request({
		url : Copix.getActionURL('portal|ajax|updateoptions'),
		evalScripts: true,
        onRequest: function () {
            ajaxOn();
        },
		onComplete: function() {
			ajaxOff();
		},
        onFailure: function (xhr){
            alert(xhr.responseText);
        }
	}).post({
        'editId' : pageId,
        'portletId' : portletId,
        'height' : $('height_' + identifiantFormulaire).value,
        'width' : $('width_' + identifiantFormulaire).value,
        'color' : $('color_' + identifiantFormulaire).value,
        'rollover' : $('rollover_' + identifiantFormulaire).value,
        'bgcolor' : $('bgcolor_' + identifiantFormulaire).value,
        'transparent' : $('transparent_' + identifiantFormulaire).checked ? '1' : '0',
        'speed' : $('speed_' + identifiantFormulaire).value
    });
}

var updateHtmlText = function(identifiantFormulaire, portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('portal|ajax|updateHtmlText'),
		evalScripts: true,
		onComplete : function (){
			ajaxOff();
		}
	}).post({'editId' : pageId,
				'portletId' : portletId,
				'htmltext' : $('htmltext_' + identifiantFormulaire).value
				});
}

var updateMenu = function(identifiantFormulaire, portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('portal|ajax|updateMenu'),
		evalScripts: true,
		onComplete : function (){
			ajaxOff();
		}
	}).post({'editId' : pageId,
				'portletId' : portletId,
				'public_id_hem' : $('portletMenuElementChooser' + identifiantFormulaire).value,
				'level_hem' : $('menu_level_' + identifiantFormulaire).value,
				'depth_hem' : $('menu_depth_' + identifiantFormulaire).value
				});
}

var preview = function (id){
	var request = new Request.HTML({
		url : Copix.getActionURL('portal|ajax|GetTextPreview'),
		evalScripts: true,
		update:'preview'+id
	}).post({'text':$(id).value});
}

function setPolice(beginTag, endTag, id)
{
	var textArea = $(id);
	poscurseur = textArea.scrollTop;
	
	objectValue = textArea.value;

	deb = textArea.selectionStart;
	fin = textArea.selectionEnd;

	objectValueDeb = objectValue.substring( 0 , textArea.selectionStart );
	objectValueFin = objectValue.substring( textArea.selectionEnd , textArea.textLength );
	objectSelected = objectValue.substring( textArea.selectionStart ,textArea.selectionEnd );

	textArea.value = objectValueDeb + beginTag + objectSelected;
	if (endTag){
		textArea.value += endTag ;
	}
	textArea.value += objectValueFin;
	
	textArea.focus();
	textArea.fireEvent('keyup');
	textArea.scrollTop = poscurseur;	
}

var updateAnchor = function(identifiantFormulaire, portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('portal|ajax|updateAnchor'),
		evalScripts: true,
		update: 'erreuranchor_' + identifiantFormulaire,
		onComplete : function (){
			ajaxOff();
		}
	}).post({'editId' : pageId,
				'portletId' : portletId,
				'name' : $('anchorname' + identifiantFormulaire).value
				});
}

var updateDateUpdate = function(identifiantFormulaire, portletId, pageId){
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('portal|ajax|updateDateUpdate'),
		evalScripts: true,
		update: 'div_' + identifiantFormulaire,
		onComplete : function (){
			ajaxOff();
		}
	}).post({'editId' : pageId,
				'portletId' : portletId,
				'template' : $('portletTemplate' + identifiantFormulaire).value
				});
}


// fonctions qui gèresnt le choix du visuel et ses options

function chooseTemplate (button, tpl, caption, options, showSelection,identifiant, inputId, portletId ,urlUpdateOptions, urlGetOptions,editId) {
    
	Copix.get_copixwindow ('templateChooser'+identifiant).close ();
    // Activation de tous les boutons "Choisir ce visuel"
    $('tableTemplateChooser'+identifiant).getElements('input[type=button]').each( function (el){
        el.disabled = false;
        $(el.get('rel')).set('styles', {'font-style':'normal'});
	});
	button.disabled = true;
	$(button.get('rel')).set('styles', {'font-style':'italic'});
	$(inputId).value = tpl;
    $(inputId).set('caption', caption);

	if (showSelection){
		$('captionTemplateChooser'+identifiant).innerHTML = caption;
	}

    if(options){
        // Construction du formulaire d'options
        updateFormOptions(options, identifiant, portletId, urlUpdateOptions, urlGetOptions,editId);
    }

    if($('template_page')) {
        $('template_page').set('html', caption);
    }
	$(inputId).fireEvent('change');
}

// Fonction qui génère le formulaire avec les options du template
function updateFormOptions(options, identifiant, portletId, urlUpdateOptions, urlGetOptions,editId ) {
    var oOptions = eval(options);
    new Request({
        url:        urlGetOptions,
        data:       'portletId='+portletId+'&editId='+editId,
        onComplete: function(responseJSON) {
            var oOptionsValues = JSON.decode(responseJSON);
            var sHTMLContentForm = getOptionsForm(oOptions, oOptionsValues, portletId, identifiant);
            $('optionContent'+identifiant).set('html', sHTMLContentForm);
            // Initialisation des évènements sur le formulaire
            initOptionsForm(identifiant, urlUpdateOptions);
            // le template a des options
            
            // on regarde si on a du texte
            var textData = sHTMLContentForm.replace(/(<[^>]+>)/g, '');
            if($('optionContent'+identifiant)){
	            if(textData != '') {
	                $('optionContent'+identifiant).setStyle('display', 'block');
	                generateAccordionOptions(portletId);
	                sHTMLContentForm.stripScripts(true);
	            }
	            else {
	                $('optionContent'+identifiant).setStyle('display', 'none');
	            }
            }
        }.bind(this)
    }).send();
}

function getOptionsForm(oOptions, oOptionsValues, portletId,identifiant,  notFirst) {
	var sReturn = '';
	// parcour tous les enregistrements
	for ( var key in oOptions ) {
		// n'accepte que les objets
		var currentObj = oOptions[key];
		if($type(currentObj) == 'object') {
			// s'il y a des attributs
			if(currentObj['@attributes']){
				// fais le traitement des attributs
				var attributes = currentObj['@attributes']; 
				if('type' in attributes) {
                    switch(attributes['type']) {
                    	case 'bloc':
                    		sReturn += '<div class="optionBloc"><div class="optionBlocTitle">' + attributes['title'] + '</div><div class="optionBlocContent">'; 
                    		sReturn += getOptionsForm(currentObj, oOptionsValues,portletId, identifiant,true);
                    		sReturn += '</div></div>';
                    		break;
                        case 'span':
                            sReturn += '<span style="font-weidth:bold"';
                            if('class' in attributes) {
                                sReturn += ' class="'+attributes['class']+'"';
                            }
                            sReturn += '>';
                            sReturn += attributes['value'];
                            sReturn += '</span>';
                            sReturn += '<br/>';
                            break;
                        // Exemple
                        // XML : <myOption type="inputtext" id="code_postal" class="myClass" desc="Code Postal : " value="69003" size="5"/>
                        // HTML : <label for="code_postal">Code Postal : </label><input type="text" id="code_postal" name="code_postal" class="myClass" size="5" value="69003" />
                        case 'inputtext':
                            if('desc' in attributes) {
                                sReturn += '<label for="'+attributes['id']+portletId+'">'+attributes['desc']+'</label>';
                            }
                            sReturn += '<input type="text"';
                            if('id' in attributes) {
                                sReturn += ' id="'+attributes['id']+portletId+'"';
                                sReturn += ' name="'+attributes['id']+'"';
                            }
                            if('class' in attributes) {
                                sReturn += ' class="'+attributes['class']+'"';
                            }
                            if('size' in attributes) {
                                sReturn += ' size="'+attributes['size']+'"';
                            }
                            if('value' in attributes || ( ('id' in attributes) && (attributes['id'] in oOptionsValues) )) {
                                sReturn += ' value="';
                                if( ('id' in attributes) && (attributes['id'] in oOptionsValues) ){
                                    sReturn += oOptionsValues[attributes['id']];
                                }
                                else {
                                    sReturn += attributes['value'];
                                }
                                sReturn += '"';
                            }
                            
                            sReturn += ' />';
                            if('suffix' in attributes) {
                                sReturn += attributes['suffix'] + '&nbsp;';
                            }
                            sReturn += '<br/>';
                            break;
                        case 'inputradio':
                            if('desc' in attributes) {
                                sReturn += '<label>'+attributes['desc']+'</label>';
                            }
                            if('enum' in attributes) {
                                var aValues = (attributes['enum']).split(',');
                                // Récupération de la valeur du radiobutton (Valeur enregistrée en base ou valeur par défaut)
                                var sRadioValue = '';
                                if( ('id' in attributes) && (attributes['id'] in oOptionsValues) ) {
                                    sRadioValue = oOptionsValues[attributes['id']];
                                }
                                else if( 'value' in attributes ) {
                                    sRadioValue = attributes['value'];
                                }

                                for ( var key2 = 0; key2 < aValues.length; key2++ ) {
                                    sReturn += '<input type="radio"';
                                    sReturn += ' value="'+aValues[key2]+'"';
                                    if('id' in attributes) {
                                        sReturn += ' id="'+attributes['id']+portletId+aValues[key2]+'" name="'+attributes['id']+'"';
                                    }
                                    if( sRadioValue == aValues[key2] ) {
                                        sReturn += ' checked="checked"';
                                    }
                                    sReturn += '><label';
                                    if('id' in attributes) {
                                        sReturn += ' for="'+attributes['id']+portletId+aValues[key2]+'"';
                                    }
                                    sReturn += '>'+aValues[key2]+'</label>';
                                }
                            }
                            sReturn += '<br/>';
                            break; 
                        // Exemple
                        // XML :
                        // <myOption type="select" id="ma_liste" class="myClass" desc="Ma Liste : " value="option2" enum="option1=Option 1,option2=Option 2" />
                        // HTML :
                        // <label for="code_postal">Ma Liste : </label>
                        // <select id="ma_liste" name="ma_liste" class="myClass">
                        //      <option value="option1">Option 1</option>
                        //      <option value="option2" selected="selected">Option 2</option>
                        // </select>
                        case 'select':
                        	if('desc' in attributes) {
                                sReturn += '<label>'+attributes['desc']+'</label>';
                            }
                        	sReturn += '<select ';
                        	
                        	var valueSelect = '';
                        	if('value' in attributes) {
                                
                                if( ('id' in attributes) && (attributes['id'] in oOptionsValues) ){
                                	valueSelect = oOptionsValues[attributes['id']];
                                }
                                else {
                                	valueSelect = attributes['value'];
                                }
                            }
                        	if('id' in attributes) {
                                sReturn += ' id="'+attributes['id']+portletId+'" name="'+attributes['id']+'"';
                            }
                    		sReturn += '>';
                        	if('enum' in attributes) {
                        		
                        		var aValues = (attributes['enum']).split(',');
                                for ( var key2 = 0; key2 < aValues.length; key2++ ) {
                                	var value2 = aValues[key2];
                                	var pair = value2.split('=');
                                	var optionName = '';
                                	var optionvalue = '';
                                		
                                	if(pair.length > 1){
                                		optionvalue = pair[0];
                                		optionName= pair[1];
                                	}else{
                                		optionName = pair[0];
                                		optionvalue = pair[1];
                                	}
                                	sReturn += '<option value="'+optionvalue+'" ';
                                	if(valueSelect == optionvalue){ 
                                		sReturn += 	'selected="selected"';
                                	}
                                	sReturn += '>'+optionName+'</option>';
                                }
                                    
                                
                        	}
                        	sReturn += '</select>';
                        	break;
                        case 'elementchooser':
                        	if('desc' in attributes) {
                                sReturn += '<label>'+attributes['desc']+'</label>';
                            }
                        	var value = "0";
                        	var idOption = attributes['id'];
                        	
                        	if( ('id' in attributes) && (idOption in oOptionsValues) ){
                                value = oOptionsValues[idOption];
                            } else {
                            	value = attributes['value'];
                            }
                        	
                        	var filter = '';
                        	if(attributes['typeElement']){
	                        	filter = attributes['typeElement'];
                        	}
                        	
                        	var request = new Request({
                        		url : Copix.getActionURL('portal|ajax|elementchooser'),
                        		evalScripts: false,
                        		async: false,
                        		onComplete : function (result){
                        			sReturn += 	this.response.text;	
                        		}
                        	}).get(
                        			{
                        			 'identifiant' :identifiant,
                        			 'id' : idOption, 
                        			 'selected' : value,
                        			 'filter' : filter
                        		});
                        	sReturn += '<br/>';
                        	break;
                    }
                }
			}		
		}
	}
	if(!notFirst){
		sReturn += '<div style="clear:both;"></div>';
	}
	return sReturn;
}

// portlet
function initOptionsForm(identifiant, urlUpdateOptions) {
	var oneElement = null;
	var oneEvent = null;
    $$('#optionsForm'+identifiant+' input, #optionsForm'+identifiant+' select').each( function (el){
        if(el.get('type') == 'text') {
            var sEvent = 'keyup';
        }
        else {
            var sEvent = 'change';
        }
        el.addEvent(sEvent, function(e){
            updateTemplateOptions(identifiant, urlUpdateOptions);
        });
        oneElement = el; 
        oneEvent = sEvent;
    });
    // met à jour avec les valeurs par défaut
    if(oneElement && oneEvent){
    	oneElement.fireEvent(oneEvent);
    }
}


// Fonction qui envoie les options du template en ajax pour les enregistrer en BDD
function updateTemplateOptions(identifiant, urlUpdateOptions) {
	var hashes = $('optionsForm'+identifiant).set('send').toQueryString().split('&');
	//regaede s'il ya des oprions en supprimant portletId et editId  
	var data = new Hash();
	for(var i = 0; i < hashes.length; i++)
    {
        var hash = hashes[i].split('=');
        data.set(hash[0], hash[1]);
    }
	var portletId = data.get('portletId');
	var editId = data.get('editId');
	data.erase('portletId');
	data.erase('editId');
	// pas d'options, on quitte
	if(data.getLength() == 0){
		return;
	}
	// update toolbar
	if(portletId && editId){
		if(typeof updateToolBar == 'function') { 
			if(updateToolBar(portletId, editId)){};
		}
	}
	
	
    $('optionsForm'+identifiant).set('send', {
        url: urlUpdateOptions
    }).send();
}

function generateAccordionOptions(portletId){
	var blocId = 'optionContent' + portletId;
	if($(blocId)){
		if($$('#'+blocId + ' .optionBloc').length > 0){
			 new Accordion($(blocId), '#'+blocId + ' div.optionBlocTitle', '#'+blocId + ' div.optionBlocContent', {
				display : 0,
				opacity: false,
				onActive: function(toggler, element){
					toggler.setStyles({'background-color': '#feff8f', 'color' : '#000'});
				},
				onBackground: function(toggler, element){
					toggler.setStyles({'background-color': '#176f9f', 'color':'#fff'});
				}
			});
		}
	}
}

//fonctions de sauvegarde du formulaire de configuration de menus dans la barre de modifications
function saveConfigurationMenusForm (){
	var formSubmit = new Request.HTML({
		url: Copix.getActionURL('heading|elementinformation|saveMenus'),
		onComplete: function(){
			location.reload(true);
		}
		}).post($('configurationMenusForm')	
	);		
}

var pageSize = function (){
	$('pageContent').setStyle('padding-top', $('pageUpdateHeaderMenu').getComputedSize().totalHeight);
}

var updateZoner = function(identifiantFormulaire, portletId, pageId){
	if(typeof updateToolBar =='function'){
		updateToolBar(portletId, pageId);
	} 
	ajaxOn();
	var request = new Request.HTML({
		url : Copix.getActionURL('portal|ajax|updateZone'),
		evalScripts: true,
		update: 'erreuranchor_' + identifiantFormulaire,
		onComplete : function (){
			ajaxOff();
		}
	}).post({'editId' : pageId,
				'portletId' : portletId,
				'name' : $('anchorname' + identifiantFormulaire).value
				});
}

var showWaitMessage = function(){
	var div = $('waitMessage');
	if (!div){
		var div = new Element('div', {'class' : 'copixwindow CopixLightWindow'});
	
		div.innerHTML = "<img src='"+Copix.getResourceURL('heading|img/loader.gif')+"'/>Chargement";
		document.body.adopt(div);
	}
    var browserWindowSize = window.getSize();
    var elementSize = div.getSize();
    x = (browserWindowSize.x / 2) - (elementSize.x / 2);
    y = (browserWindowSize.y / 2) - (elementSize.y / 2);
	div.setStyles({'top':y, 'left' : x});
}

var hideWaitMessage = function(){
	if ($('waitMessage')){
		$('waitMessage').setStyle('left', '-9999px');
	}
}