/*/ surcharge envoi requête ajax
Request.prototype.onSendDeveloperBar = Request.prototype.send;
Request.prototype.send = function (pOptions) {
	this.onSendDeveloperBar (pOptions);
	developerBarOnAjaxSend (this, pOptions);
	Copix.currentAjaxDeveloperBar = Copix.create_developerBar ();
}
function developerBarOnAjaxSend (pRequest, pOptions) {
	if (pRequest.options.url.toLowerCase ().indexOf ('developerbar') == -1) {
		//Copix.create_developerBar (false);
	}
}

// surcharge annulation requête ajax
Request.prototype.onCancelDeveloperBar = Request.prototype.cancel;
Request.prototype.cancel = function () {
	this.onCancelDeveloperBar ();
	developerBarOnAjaxCancel (this);
}
function developerBarOnAjaxCancel (pRequest) {

}

// surcharge succès requête ajax
Request.prototype.onSuccessDeveloperBar = Request.prototype.onSuccess;
Request.prototype.onSuccess = function (pText, pXML) {
	this.onSuccessDeveloperBar (pText, pXML);
	developerBarOnAjaxSuccess (this);
}
function developerBarOnAjaxSuccess (pRequest) {
	if (pRequest.options.url.toLowerCase ().indexOf ('developerbar') == -1) {
		//var tempBar = Copix.get_currentAjaxDeveloperBar ();
		//tempBar.setId (pRequest.getHeader ('X-Copix-DeveloperBar-Id'));
		//tempBar.fetch (pRequest);
	}
}

// surcharge erreur lors de la requête ajax
Request.prototype.onFailureDeveloperBar = Request.prototype.onFailure;
Request.prototype.onFailure = function () {
	this.onFailureDeveloperBar ();
	developerBarOnAjaxFailure (this);
}
function developerBarOnAjaxFailure (pRequest) {

}*/

var DeveloperBar = new Class ({
	id: null,
	bar: null,
	exDisplayed: null,
	exVarDisplayed: null,
	varsLoaded: false,
	querysLoaded: false,
	logsLoaded: false,
	errorsLoaded: false,
	isMain: true,
	positioning: 'absolute',

	initialize: function (pIsMain) {
		this.isMain = pIsMain;
	},

	setId: function (pId) {
		this.id = pId;
		this.bar = $ (pId);
		if (this.isMain) {
			this.bar.makeDraggable ({onDrop: this.onDrop.bind (this)});
		}
	},

	fetch: function (pRequest) {
		var bar = new Element ('div');
		bar.id = this.id;
		bar.className = 'developerBar';
		bar.setStyle ('left', 5);
		bar.setStyle ('top', 5);
		document.body.adopt (bar);
		this.bar = $ (this.id);

		if (pRequest.getHeader ('X-Copix-DeveloperBar-Timers-Global')) {
			this._fetchType ('timers', 'Timers', pRequest.getHeader ('X-Copix-DeveloperBar-Timers-Global'));
		}
	},

	_fetchType: function (pType, pName, pCaption) {
		var span = new Element ('span');
		span.className = 'developerBarGroup' + pName;
		span.set ('html', pCaption);
		span.addEvent ('click', function () { Copix.get_developerBar (this.id).show (pType); }.bind (this));
		span.inject (this.bar);

		var div = new Element ('div');
		div.className = 'developerBarContent';
		div.id = this.id + 'content_' + pType;
		document.body.adopt (div);
	},
	
	show: function (pType) {
		var id = this.id + 'content_' + pType;
		var element = $ (id);
		var developerBar = this.id;

		if ($ (this.exDisplayed) != undefined && id != this.exDisplayed) {
			$ (this.exDisplayed).style.display = 'none';
		}
		this.exDisplayed = id;
		displayContent = (element.getStyle ('display') == 'none');
		element.setStyle ('display', (displayContent) ? 'block' : 'none');
		element.setStyle ('left', this.bar.style.left);
		element.setStyle ('top', (this.bar.getCoordinates ().top + this.bar.getSize ().y) + 'px');

		if (pType != 'timers' && pType != 'memory' && !this.isLoaded (pType)) {
			new Request.HTML ({
				url: Copix.getActionURL ('developerbar|default|getValues'),
				update: element,
				evalScripts: true,
				onSuccess: function () {
					Copix.get_developerBar (developerBar).setIsLoaded (pType, true);
				}
			}).post ({idBar: this.id, type: pType});
		}

		if (this.isMain) {
			new Request.HTML ({
				url: Copix.getActionURL ('developerbar|default|onShow')
			}).post ({content: pType, show: displayContent});
		}
	},

	setIsLoaded: function (pType, pLoaded) {
		eval ('this.' + pType + 'Loaded = ' + pLoaded);
	},

	isLoaded: function (pType) {
		return eval ('this.' + pType + 'Loaded');
	},

	showVars: function (pType) {
		if ($ (this.id + 'Vars' + this.exVarDisplayed) != undefined && pType != this.exVarDisplayed) {
			$ (this.id + 'Vars' + this.exVarDisplayed).style.display = 'none';
		}
		this.exVarDisplayed = pType;
		var element = $ (this.id + 'Vars' + pType);
		element.style.display = (element.style.display == 'none') ? '' : 'none';
	},

	setPosition: function (pX, pY) {
		if (this.bar.getCoordinates ().left < 0) {
			pX = 5;
		} else if (this.bar.getCoordinates ().left > (window.getSize ().x - this.bar.getSize ().x)) {
			pX = window.getSize ().x - this.bar.getSize ().x - 5;
		}
		if (this.bar.getCoordinates ().top < 0) {
			pY = 5;
		}
		if (pX != this.bar.getCoordinates ().left || pY != this.bar.getCoordinates ().top) {
			this.bar.set ('styles', {'left': pX, 'top': pY});
		}
	},

	onDrop: function () {
		this.setPosition (this.bar.getCoordinates ().left, this.bar.getCoordinates ().top);
		if (this.isMain) {
			new Request.HTML ({
				url: Copix.getActionURL ('developerbar|default|onDrop')
			}).post ({x: this.bar.getCoordinates ().left, y: this.bar.getCoordinates ().top});
		}
	},

	highlight: function (pType) {
		var element = $ (this.id + 'group_' + pType);
		var myEffect = new Fx.Morph(element, {duration: 200, link: 'chain', transition: Fx.Transitions.Sine.easeOut});
		myEffect.start ({
			'background-color': '#FF0000'
		}).start ({
			'background-color': '#FFFFFF'
		}).start ({
			'background-color': '#FF0000'
		}).start ({
			'background-color': '#FFFFFF'
		});
	},

	setPositioning: function (pPositioning) {
		this.positioning = pPositioning;
		if (this.positioning == 'absolute') {
			this.bar.setStyle ('position', 'absolute');
		} else {
			this.bar.setStyle ('position', 'fixed');
		}
	}
});

CopixClass.implement({
	developerBars: [],

	get_developerBar: function (pId) {
		for (x = 0; x < this.developerBars.length; x++) {
			if (this.developerBars[x].id == pId) {
				return this.developerBars[x];
			}
		}
		throw ('DeveloperBar "' + pId + '" does not exists.');
	},

	get_mainDeveloperBar: function () {
		for (x = 0; x < this.developerBars.length; x++) {
			if (this.developerBars[x].isMain) {
				return this.developerBars[x];
			}
		}
		throw ('Main developerbar is not created.');
	},

	get_currentAjaxDeveloperBar: function () {
		if (this.developerBars.length < 2) {
			throw ('No ajax call for the moment.');
		}
		return this.developerBars[this.developerBars.length - 1];
	},

	create_developerBar: function (pIsMain) {
		return this.developerBars[this.developerBars.length] = new DeveloperBar (pIsMain);
	}
});