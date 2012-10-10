
CopixClass = new Class({

	// Options par défaut
	options: {
		ajaxSessionId: '',
		module : 'default',
		urlBase : '',
		resourceUrlBase : ''
	},
	
	// Constructeur
	initialize : function(options) {
		// Charge les modifs
		$extend(this.options, options);
		
		// Etend XHR pour envoyer notre identifiant de session AJAX 
		if(this.options.ajaxSessionId) {
			XHR.prototype.options.headers['X-Copix-AJAX-Session-Id'] = this.options.ajaxSessionId;
		}
		
		// Calcule l'URL de l'image de chargement
		this.loadingImage = this.getResourceURL('img/tools/load.gif');
		
		// Surcharge addEvent('scriptLoaded', fn) pour éxecuter immédiatement la fonction
		// s'il n'y a pas de script à charger.
		var copix = this;
		Element.Events.linksloaded = {
			add: function(func){
				if(copix.linksToLoad === null || copix.linksToLoad.length == 0) {
					func.call(this);
				}
			}
		};
	},
	
	//============================================================
	// Gestion du chargement dynamique des liens
	//============================================================
	
	// Liste identifiants de liens à charger
	linksToLoad: null,
	
	// Ajoute un lien à charger	
	addLink : function(params) {		
		var id = params.id;
		
		if($(id) || (this.linksToLoad !== null && this.linksToLoad.contains(id))) {
			return;
		}
		
		var kind = params.kind;		
		var url = params.url;
		delete params.kind;
		delete params.url;
		
		if(kind == 'javascript') {
			if(this.linksToLoad === null) {
				// Supprime les handlers qui pourraient trainer
				window.removeEvents('linksloaded');
				this.linksToLoad = [];
			}
			this.linksToLoad.include(id);
			params.onload = this.onLinkLoaded.bind(this, id);
		}
		
		new Asset[kind](url, params);
	},
	
	// Appelé quand le chargement d'un lien est terminé
	onLinkLoaded : function(id) {
		this.linksToLoad.remove(id);
		if(this.linksToLoad.length == 0) {
			this.linksToLoad = null;
			try {
				window.fireEvent('linksloaded');
			} finally {
				window.removeEvents('linksloaded');
			}
		}
	},
	
	//============================================================
	// Helpers pour les URLS
	//============================================================
	
	// Détermine l'URL d'une action Copix
	getActionURL : function(action, data) {
		var parts = action.split('|');
		var url = this.options.urlBase + 'index.php/' + $pick(parts[0], this.options.module) + '/' + $pick(parts[1], 'default') + '/' + parts[2];
		if(data) {
			url += (url.contains('?') ? '&' : '?') + Object.toQueryString(data);
		}
		return url;
	},
	
	// Détermine l'URL d'une ressource Copix 
	getResourceURL : function(path) {
		var result = path.match(/^(?:([^|]+)\|)?(.*?)$/i);
		return this.options.resourceUrlBase + (result[1] || 'www') + '/' + result[2];
	},

	//============================================================
	// Gestion des sessions AJAX
	//============================================================
	
	sessionKeepalive: function(pingInterval) {
		if(!this.sessionPinger && pingInterval > 0) {
			var ajax = new Ajax(this.getActionURL('generictools|ajax|sessionPing'), {method:'get'});
			this.sessionPinger = ajax.request.periodical(1000*pingInterval, ajax); 
		} else if(this.sessionPinger && pingInterval == 0) {
			$clear(this.sessionPinger);
			delete this.sessionPinger;
		}
	},

	//============================================================
	// Gestion des événements à lancer à la fin de l'initialisation
	//============================================================
	
	// File d'événéments à lancer
	eventQueue: [],
	
	// Mets un événément en attente
	queueEvent: function(element, event, eventArgs) {
		if(!element) {
			throw "Invalid element: "+element;
		}
		var args = [event];
		if(eventArgs) {
			args.concat(eventArgs);
		}
		this.eventQueue.push({element:element,args:args});
	},
	
	// Lance les événéments de la file
	fireQueuedEvents: function() {
		while(this.eventQueue.length > 0) {
			var queuedEvent = this.eventQueue.shift();
			try {
				queuedEvent.element.fireEvent.apply(queuedEvent.element, queuedEvent.args);
			} catch(e) {
				try{
					console.error("Error with queued event '"+queuedEvent.args[0]+"' for "+queuedEvent.element+": "+e);
				} catch(e2) {
					// NOOP
				};
			}
		}
	},
	
	//============================================================
	// Divers
	//============================================================
	
	// Remplace le contenu d'un élément par l'image de chargement
	setLoadingHTML : function(e) {
		e.setStyle('display', '');
		e.setHTML('<img src="'+this.loadingImage+'"/>');
	}
	
	
});

if(MooTools.version == "1.11" && window.ie) {

	Asset.javascript = function(source, properties){
		properties = $merge({
			'onload': Class.empty
		}, properties);
		var script = new Element('script', {'src': source}).addEvent('load', properties.onload);
		script.onreadystatechange = function () {
			if (script.readyState == 'complete' || script.readyState == 'loaded') {
				script.onreadystatechange = null;
				script.fireEvent('load');
			}
		};
		delete properties.onload;
		return script.setProperties(properties).inject(document.head);
	};

}
