
CopixClass.implement({
	ModalBox: new Abstract({	
		current : null,
		container : null,
		background: null,
		centerLoop: null, 
		ignoreEvent : function(event){event.stop();}.bindWithEvent(),
		createDivs : function() {
			if(!this.container) {
				this.container = new Element('div', {
					'styles': {
						'display': 'none',
						'z-index': 30000,
						'width': '100%',
						'height': '100%'
					}
				});
				this.container.injectInside(document.body);
			}
			if(!this.background) {			
				this.background = new Element('div', {
					'styles': {
						'opacity': 0.5,
						'background-color': 'black',
						'position': window.ie ? 'absolute' : 'fixed',
						'left': 0,
						'right': 0,
						'top': 0,
						'bottom': 0,
						'width': '100%',
						'height': '100%',
						'z-index': 31000
					},
					'events': {
						'click': this.ignoreEvent,
						'keypress': this.ignoreEvent,
						'keydown': this.ignoreEvent,
						'keyup': this.ignoreEvent
					}
				});
				this.background.injectInside(this.container);
			}
		},
		register : function(options) {
			var modalBox = $(options.id);
			var content = $(options.contentId || options.id+'_content');
			
			this.createDivs();
			
			content.addClass('CopixModalBox');
			content.setStyles({
				'position': (window.ie ? 'absolute' : 'fixed'),
				'display': 'none',
				'z-index': 32000,
				'left': 0,
				'top': 0,
				'float': ''
			});
			var contentParent = content.getParent();
	
			var contentFx = content.effects({duration:250, transition: Fx.Transitions.Cubic.easeInOut}); 		
			
			var isOpen = false;
			var first = true;
			var doCenterBox;
			if(window.ie) {
				var windowSize = null;			

				var getWindowSize;

				if(window.getSize().x == 0) {
					getWindowSize = function() {
						return {
							size: {
								x: document.body.clientWidth,
								y: document.body.clientHeight
							},
							scrollSize: {
								x: document.body.scrollWidth,
								y: document.body.scrollHeight
							},
							scroll: {
								x: document.body.scrollLeft,
								y: document.body.scrollTop
							}
						}
					}
				} else {
					getWindowSize = window.getSize.bind(window);
				}

				doCenterBox = function() {
					if(!isOpen) return;
					var dirty = false;
					var newSize = getWindowSize();
					if(!dirty && windowSize) {
						dirty = (
							   (newSize.size.x != windowSize.size.x)
							|| (newSize.size.y != windowSize.size.y)
							|| (newSize.scroll.x != windowSize.scroll.x)
							|| (newSize.scroll.y != windowSize.scroll.y)
							|| (newSize.scrollSize.x != windowSize.scrollSize.x)
							|| (newSize.scrollSize.y != windowSize.scrollSize.y)
						);
					}
					windowSize = newSize;
					if(!first && !dirty) {
						return;
					}
					try {
						var docSize = document.body.getSize();
						this.background.setStyles({
							'width': Math.max(docSize.size.x, windowSize.size.x, windowSize.scrollSize.x),
							'height': Math.max(docSize.size.y, windowSize.size.y, windowSize.scrollSize.y)
						});
						this.background.fixdivUpdate();
						var pos = {
								'left': windowSize.scroll.x + (windowSize.size.x - content.getSize().x) / 2,
								'top':  windowSize.scroll.y + (windowSize.size.y - content.getSize().y) / 2
						};
						if(first) {
							content.setStyles(pos);						
							first = false;
						} else {
							contentFx.start(pos);
						}						
					} catch(e) {
						console.error("centerBox: "+e.message);
					}
				}.bind(this);
	
				modalBox.addEvent('open', function() {
					this.background.fixdivShow();
					this.centerLoop = doCenterBox.periodical(250);
				}.bind(this));
				modalBox.addEvent('close', function() {
					this.background.fixdivHide();;
					$clear(this.centerLoop);
				}.bind(this));			
	
			} else {
				doCenterBox = function() {
					if(!isOpen) return;
					var pos = {
						'left': (window.getWidth()  - content.getSize().x) / 2,
						'top':  (window.getHeight() - content.getSize().y) / 2
					};
					if(first) {
						content.setStyles(pos);						
						first = false;
					} else {
						contentFx.start(pos);
					}
				};
				window.addEvent('resize', doCenterBox.pass());			
			}
			
			content.addEvent('resize', doCenterBox);
			
			modalBox.addEvent('open', function(){
				if(!isOpen && !this.current) {
					content.setStyle('display', '');
					content.injectInside(this.container);
					this.container.setStyle('display', '');
					first = true;
					isOpen = true;
					doCenterBox();
					this.current = modalBox;
				}
			}.bind(this));
			
			modalBox.addEvent('close', function(){
				if(isOpen) {
					this.container.setStyle('display', 'none');				
					content.setStyle('display', 'none')
					content.injectInside(contentParent);
					isOpen = false;
					this.current = null;
				}
			}.bind(this));
			
			if(options.openTriggers) {
				var doOpen = modalBox.fireEvent.bind(modalBox, 'open');
				Array.each(options.openTriggers, function(id){
					$(id).addEvent('click', doOpen);
				});
			}
			
			if(options.closeTriggers) {
				var doClose = modalBox.fireEvent.bind(modalBox, 'close');
				Array.each(options.closeTriggers, function(id){
					$(id).addEvent('click', doClose);
				});
			}
			
		}
	})
});

