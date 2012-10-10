/*
Tooltip.js
Author : Goulven CHAMPENOIS

Description: Replaces element content with a link to a tooltip inside the page.
Usage: new Tooltip( $('elementId'), {'imgPath': 'path/to/an/img.png', 'altText': 'More...', 'rollover': true|false}
Output:
	The content is replaced by a link with class="tooltiplink"
	The tooltip div has class "tooltip" and is inserted at the end of the document (to avoid inheriting styles)
	If the element has no children, the tooltip content is wrapped inside a div with class="container" 
*/


var Tooltip = new Class({
	initialize: function( el, options ){
		this.options = options;
		this.element = $(el);
		this.altText = this.options.altText || '[+]';
		
		if( !this.hasContent() ){
			return;
		}
		this.createTooltip();
		
		// ajoute les déclencheurs
		this.link.addEvent( 'click', this.toggle.bind(this) );
		this.link.addEvent( 'focus', this.show.bind(this) );
		this.link.addEvent( 'blur', this.hide.bind(this) );
		if( this.options.rollover ){
			this.link.addEvent( 'mouseover', this.show.bind(this) );
			this.link.addEvent( 'mouseout', this.hide.bind(this) );
		}
		this.element.unlink = this.unlink;
	},
	
	createTooltip: function(){
		// stocke le contenu original dans un div, réinjecté en fin de document
		this.tooltip = new Element('div', {'class': 'tooltip', 'styles': {'position':'absolute', 'display':'none', 'z-index': 100}});
		this.tooltip.displayed = false;
		this.link = new Element('a', {'href': 'javascript:void(0)', 'class': 'tooltiplink'});
		
		this.tooltip.adopt( new Element('div', {'class': 'container'}).setHTML( this.element.innerHTML ) );
		// Place le tooltip en fin de document, pour qu'il ne soit pas affecté par des styles locaux
		$(document.body).adopt( this.tooltip );

		// Elément qui remplace le contenu original
		if( this.options.imgPath ){
			var img = new Element('img', {'src': this.options.imgPath, 'alt': this.altText});
			img.inject( this.link );
		} else {
			this.link.setText( this.altText );
		}
		
		// On remplace le contenu original par ce lien 
		this.element.empty();
		this.link.inject( this.element );
		
		// Iframes to hide select elements in IE
		if( window.ie ){
			this.iframe = new Element( 'iframe', {'class': 'tooltipHideSelect', 'src': 'https://www.alptis.org/recette/www/commons/js/mootools/plugins/smoothbox.empty.html', 'styles': {'background': '#fff', 'position': 'absolute', 'display': 'none', 'z-index': 99, 'margin': 0, 'padding': 0}} );
			$(document.body).adopt( this.iframe );
		}
	},
	
	toggle: function( e ){
	console.log( this.tooltip.displayed );
		if( this.tooltip.displayed ){
			this.hide( e );
		} else {
			this.show( e );
		}
	},
	
	show: function( e ){
		$$('div.tooltip').setStyles({'display': 'none'});
		if( window.ie ){
			$$('iframe.tooltipHideSelect').setStyles({'display': 'none'});
		}
		this.tooltip.setStyles({'display': 'block','visibility':'hidden'});
		this.tooltip.setStyles({
			'display': 'block',
			'visibility': '',
			'top': this.link.getPosition().y - this.tooltip.getSize().size.y+'px',
			'left': this.link.getPosition().x + this.link.getSize().size.x+'px'
		});
		if( window.ie ){
			this.iframe.setStyles({
				'display': 'block',
				'width': this.tooltip.getStyle('width'),
				'height': this.tooltip.getStyle('height'),
				'top': this.link.getPosition().y - this.tooltip.getSize().size.y+'px',
				'left': this.link.getPosition().x + this.link.getSize().size.x+'px'
			});
		}
		this.tooltip.displayed = true;
	},
	
	hide: function( e ){
		$$('div.tooltip').setStyles({'display': 'none'});
		if( window.ie ){
			$$('iframe.tooltipHideSelect').setStyles({'display': 'none'});
		}
		this.tooltip.displayed = false;
	},
	
	unlink: function(){
		this.tooltip.setStyle('display', 'none');
		this.tooltip = null;
		if( window.ie ){
			this.iframe.setStyle('display', 'none');
			this.iframe = null;
		}
		this.link = null;
	},
	
	hasContent: function(){
		return ( this.element.getText().replace(/\s*/, '').length || this.element.getChildren().length );
	}
});