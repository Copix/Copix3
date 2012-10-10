/**
* Class DraggablePortlet v2 Beta2
* Based on Drag.Move
* Author: Goulven Champenois at copix.org
* Usage: new Portlet( element );
* A link handle is added inside the portlet during initialization
* Drag the portlet with the mouse or keyboard.
* The dragged portlet will be dropped before or after the portlet being hovered over, or back where it came from
* Keyboard: focus link to activate, move with arrow keys, hit Enter to confirm, Tab or Esc to cancel
* Keyboard (continued): shift + arrow moves faster, control + arrow moves slower (control supersedes shift)
* options : an object containing
		portletClass: 'portlet', -> elements with this class can receive drops
		draggableClass: 'draggable', -> this class will be added to elements made draggable
		handleText: 'Drag', -> this is the text of the drag handle
		handleClass: 'dragHandle', -> this is the class for the drag handle
		handleContainerSelector: '.editPortlet', -> CSS selector determining where in the portlet the handle will be added. If more than one match, the first one is used
		indicatorClass: 'indicator', -> Class given to the portlet ghost
		placeHolderClass: 'placeHolder', -> Class given to the invisible element used to store the original position
		draggingClass: 'beingDragged', -> Class added to the portlet being dragged
		keyboardMoveLengthHorizontal: 15, -> Horizontal amount to move the element using the keyboard (in pixels)
		keyboardMoveLengthVertical: 30, -> Vertical amount to move the element using the keyboard (in pixels)
		initialNudge: -15 -> Distance used to push the element when starting the drag (horizontal and vertical, in pixels, can be negative). Use to fix positioning issues and make drag start more visible.
		debug: false -> send debug messages to the console
*/


DraggablePortlet = new Class({

	Implements: [Events, Options],
	options: {
		dropzonesClass: '.portlet, .ajoutPortlet',
		draggableClass: 'draggable',
		handleText: 'Drag',
		handleClass: 'dragHandle',
		handleContainerSelector: '.editPortlet',
		indicatorClass: 'indicator',
		placeHolderClass: 'placeHolder',
		draggingClass: 'beingDragged',
		keyboardMoveLengthHorizontal: 1,
		keyboardMoveLengthVertical: 1,
		initialNudge: 5,
		debug: false,
		handleId : null,
		htmlToolBar : null
	},

	initialize: function(portlet, options){
			var params = Array.link(arguments, {'options': Object.type});
			this.setOptions(params.options || {});

			//Initialisation des differents parametres
			this.document = portlet.getDocument();
			this.portlet = portlet;
			this.portlet.addClass(this.options.draggableClass);

			this.placeHolder = null; // Portlet ghost in original position
			this.indicator = null; // Portlet element indicating drop position
			this.portletInfos = null;
			this.dropzones = [];
			this.currentPos = null;
			this.currentTarget = null;

			// Need to have exact reference of functions + args when removing events
			this.bound = {
				start: this.start.bind(this),
				drag: this.handleMove.bind(this),
				handleMove: this.handleMove.bind(this),
				stop: this.stop.bind(this)
			};

			//Ajout de la toolbar	
			var divMenu = new Element ('div', {
				'class': 'editPortlet'
			});

			if (this.options.htmlToolBar){
			   divMenu.set ('html', this.options.htmlToolBar);
			}
			
			if (this.portlet.getElements(this.options.handleContainerSelector)[0]){
				divMenu.inject(this.portlet.getElements(this.options.handleContainerSelector)[0], 'top');
			}else{
				divMenu.inject(this.portlet, 'top');
			}

			//Prise en charge du handle
			if (! this.options.handleId){
				var handle = new Element('a', {
					'href': '#',
					'html': this.options.handleText,
					'class':this.options.handleClass,
					'role': 'grab',
					'aria-grab': 'supported',
					'aria-dropeffect': 'none',
					'styles': {'cursor': 'move'}
				});
				this.handle = handle;				
				this.handle.inject (divMenu, 'top');
			}else{
				this.handle = $(this.options.handleId);
			}
			
			//Ajout des evements au handle
			this.handle.addEvent('click', this.bound.start); // Keyboard event
			this.handle.addEvent('mousedown', this.bound.start); // Mouse event
			this.document.addEvent('unload', this.bound.stop); // Detach and cleanup before exiting
	},

	start: function(event){
		if (event.rightClick) return; // Do nothing if it's a right-click
		if (this.options.debug) console.log( '@start: %s event', event.type);
		
		this.fireEvent('beforeStart', this.portlet);
		this.prepare();
		
		this.document.addEvents({
			mouseup: this.bound.stop,
			mousemove: this.bound.handleMove,
			keydown: this.bound.handleMove,
			keypress: this.bound.handleMove
		});
		if (event.type == 'mousedown'){
			this.currentPos = event.page;
			event.preventDefault();
		} else {
			this.currentPos = {
				'x': this.portletInfos.coordinates.left,
				'y': this.portletInfos.coordinates.top
			};
		}
		this.currentTargetPos = 'before';
		this.prefetchZones();
	},

	handleMove: function(event){
		var left = this.portletInfos.coordinates.left;
		var top = this.portletInfos.coordinates.top;
		if(event.type == 'mousemove'){
			eventHandled = true;
			left = left - this.currentPos.x + event.page.x;
			top = top - this.currentPos.y + event.page.y;
			this.currentPos = event.page;
		} else {
			if (this.options.debug) console.log( '@handleMove received key '+ event.key);
			var eventHandled = false;
			var multiplier = 1;
			if (event.control) multiplier = 0.5;
			if (event.shift) multiplier = 5;
			switch (event.key) {
				case 'left':
					left -= multiplier * this.options.keyboardMoveLengthHorizontal;
					eventHandled = true;
					break;
				case 'right':
					left += multiplier * this.options.keyboardMoveLengthHorizontal;
					eventHandled = true;
					break;
				case 'up':
					top -= multiplier * this.options.keyboardMoveLengthVertical;
					eventHandled = true;
					break;
				case 'down':
					top += multiplier * this.options.keyboardMoveLengthVertical;
					eventHandled = true;
					break;
				case 'esc':
				case 'tab':
					this.currentTarget = this.placeHolder;
					this.stop();
					return;
					break;
				case 'enter':
					eventHandled = true;
					this.stop();
					return;
					break;
			}
			left = Math.round( left );
			top = Math.round( top );
			this.currentPos = {
				'x': left,
				'y': top
			};
		}
		if (eventHandled){
			event.preventDefault();
			this.portlet.setStyles({
				'left': left+'px',
				'top': top+'px'
			});
			this.portletInfos.coordinates.left = left;
			this.portletInfos.coordinates.top = top;
			this.fireEvent('drag', this.portlet);
			this.dragOver();
		}
	},
	
	dragOver: function(){
		var currentTarget = null;
		var zone = null;
		var drag = null;
		for(var i=0; i<this.dropzones.length; i++){
			if(this.dropzones[i]['obj'] == this.portlet) continue; // Skip currently dragged portlet
			zone = this.dropzones[i]['coordinates']; // Iterated portlet coordinates
			drag = this.portletInfos['coordinates']; // Portlet being dragged
			if(
				(drag['top'] > zone['top'] && drag['top'] < zone['top']+zone['height']) && 
				(drag['left'] + (drag['width'] /2) > zone['left'] && drag['left'] + (drag['width'] /2) < zone['left']+zone['width'])
				// AND drag left between dropzone left and right OR drag right between dropzone left and right
				//(drag['right'] < zone['left'] && drag['right'] > zone['right'])
			){
				currentTarget = this.dropzones[i]['obj'];
				break;
			}
		}
		//currentTarget = currentTarget || this.currentTarget;
		if (! currentTarget){
			return;
		}
		
		// Events
		if (this.currentTarget != currentTarget){
			if( this.currentTarget && (this.currentTarget != this.placeHolder)){
				this.fireEvent('dragleave', this.currentTarget);
			}
			if(currentTarget){
				this.fireEvent('dragenter', currentTarget);
			}
			this.currentTarget = currentTarget || this.placeHolder;
			this.move(this.indicator);
		} else {
			if (currentTarget){
				if ( drag['top'] < zone['top'] + (zone['height']/2) ){
					if (this.currentTargetPos != 'before'){
						this.currentTargetPos = 'before';
						this.move(this.indicator);
					}
				} else {
					if (this.currentTargetPos != 'after'){
						this.currentTargetPos = 'after';
						this.move(this.indicator);
					}
				}
			}
		}
		zone = null;
		drag = null;
	},
	
	move: function(obj){
		obj.inject( this.currentTarget, this.currentTargetPos);
		if (this.options.debug) console.log('moved %s %s %s', obj.className, this.currentTargetPos, this.currentTarget.className);
		this.prefetchZones();
	},
	
	prefetchZones: function(){
		// Recalculate dropzones and coordinates
		this.dropzones = [];
		this.document.getElements(this.options.dropzonesClass).each(function(zone){
			this.dropzones.push( {'obj':zone, 'coordinates':zone.getCoordinates2()} );
		}, this);
	},
	
	stop: function(){
		this.document.removeEvent('mouseup', this.bound.stop);
		this.document.removeEvent('mousemove', this.bound.handleMove);
		this.document.removeEvent('keydown', this.bound.handleMove);
		this.document.removeEvent('keypress', this.bound.handleMove);
		this.cleanup();
		this.portlets = [];
	},
	
	prepare: function(){
		// Roles
		this.handle.setProperty('aria-grab', true);
		for(var i=0, j=this.dropzones.length; i<j; i++){
			this.dropzones[i]['obj'].setProperty('aria-dropeffect', 'move');
		};
		
		// Portlet manipulation (cloning and information storing)
		var portlet = {
			'tag': this.portlet.get('tag'),
			'coordinates': this.portlet.getCoordinates2(),
			'style': this.portlet.getProperty('style')
		};
		portlet.coordinates.left += this.options.initialNudge;
		portlet.coordinates.top += this.options.initialNudge;
		this.portletInfos = portlet; // Store these infos for cleanup
		// Original element location (invisible, same tag to ensure HTML validity)
		this.placeHolder = new Element( portlet.tag, {'class':this.options.placeHolderClass, 'styles':{'visibility':'hidden'}} );
		this.placeHolder.inject( this.portlet, 'after');
		this.currentTarget = this.placeHolder;

		// Element indicating drop target (visible, same tag to ensure HTML validity)
		this.indicator = new Element( portlet.tag, {'class':this.options.indicatorClass} );
		this.indicator.setStyles({'width': portlet.coordinates.width, 'height': portlet.coordinates.height});
		this.indicator.inject( this.portlet, 'before');
	
		// Position element so it can be dragged
		this.portlet.setStyles({
			'position': 'absolute',
			'top': portlet.coordinates.top+'px',
			'left': portlet.coordinates.left+'px',
			'width': portlet.coordinates.width+'px',
			'height': portlet.coordinates.height+'px'
		});
		
		// Reposition dragged element outside of any positioning context
		this.portlet.addClass(this.options.draggingClass);
		this.portlet.injectInside(this.document.body, 'bottom');
	},
	
	cleanup: function(){
		this.move( this.portlet );

		if (this.placeHolder){
			this.placeHolder.dispose();
			this.placeHolder = null;
		}
		if (this.indicator){
			this.indicator.dispose();
			this.indicator = null;
		}
		
		this.portlet.removeClass(this.options.draggingClass);
		this.portlet.setProperty( 'style', this.portletInfos.style );
		
		// Roles
		this.handle.setProperty('aria-grab', 'supported');
		for(var i=0; i<this.dropzones.length; i++){
			this.dropzones[i]['obj'].setProperty('aria-dropeffect', 'none');
		};

		// Events
		if (this.currentTarget != this.placeHolder){
			this.fireEvent('dragleave', this.currentTarget);
		}
		this.fireEvent('dragend', [this.portlet, this.currentTargetPos, this.currentTarget]);
		this.currentPos = null;
	}
});

Element.implement({

    getPosition2: function(fn) {
        var left = 0,
            top = 0,
            safari = Browser.Engine.webkit,
            safari2 = Browser.Engine.webkit && Browser.Engine.version == 419,
            mozilla = Browser.Engine.gecko,
            parent = this.parentNode,
            offsetChild = this,
            offsetParent = this.offsetParent,
            doc = this.ownerDocument,
            css = Element.getComputedStyle,
            fixed = css(this, "position") == 'fixed';

        function border(el) {
            add(css(el, 'borderLeftWidth'), css(el, 'borderTopWidth'));
        }

        function add(l, t) {
            left += parseInt(l, 10) || 0;
            top += parseInt(t, 10) || 0;
        }

        if (!(mozilla && this == doc.body) && this.getBoundingClientRect) {
            var box = this.getBoundingClientRect();
            add(box.left + Math.max(doc.documentElement.scrollLeft, doc.body.scrollLeft),
				box.top + Math.max(doc.documentElement.scrollTop, doc.body.scrollTop));
            add(-doc.documentElement.clientLeft, -doc.documentElement.clientTop);
        } else {
            add(this.offsetLeft, this.offsetTop);
            while (offsetParent) {
                add(offsetParent.offsetLeft, offsetParent.offsetTop);
                if (mozilla && !/^t(able|d|h)$/i.test(offsetParent.tagName) || safari && !safari2)
                    border(offsetParent);
                if (!fixed && css(offsetParent, 'position') == 'fixed')
                    fixed = true;
                offsetChild = /^body$/i.test(offsetParent.tagName) ? offsetChild : offsetParent;
                offsetParent = offsetParent.offsetParent;
            }
            while (parent && parent.tagName && !/^body|html$/i.test(parent.tagName)) {
                if (!/^inline|table.*$/i.test(css(parent, 'display'))) add(-parent.scrollLeft, -parent.scrollTop);
                if (mozilla && css(parent, 'overflow') != 'visible') border(parent);
                parent = parent.parentNode;
            }
            if ((safari2 && (fixed || css(offsetChild, 'position') == 'absolute')) ||
				(mozilla && css(offsetChild, 'position') != 'absolute'))
                add(-doc.body.offsetLeft, -doc.body.offsetTop);
            if (fixed)
                add(Math.max(doc.documentElement.scrollLeft, doc.body.scrollLeft),
					Math.max(doc.documentElement.scrollTop, doc.body.scrollTop));
        }
        return { x: left, y: top };
    },
    
	getCoordinates2: function(element){
		var position = this.getPosition2();
		var obj = {
			'width': this.offsetWidth,
			'height': this.offsetHeight,
			'left': position.x,
			'top': position.y
		};
		obj.right = obj.left + obj.width;
		obj.bottom = obj.top + obj.height;
		return obj;
	}
});	