EyeCandyBar = new Class ({
    _elements: null,
    _arEffects : null,

	options: {
		onChanged : null
	},

	// Constructeur
	initialize : function (collection, options) {
	   this._arEffects = new Array ();

	   //charge les modifications
	   $extend (this.options, options);
	   this._elements = collection;
	   this._elements.each (function (el){
	   		var boundStart = this._startFX.bindWithEvent (el, this);	   
	   		var boundStop = this._stopFX.bindWithEvent (el, this);

			//el.setStyle ('opacity', 0.9);
			el.addEvent('mouseenter', boundStart);
			el.addEvent('mouseleave', boundStop);
	   }, this);
	},

	_startFX : function (evt, toolbar){
		toolbar._resetFX (this, {'duration':50, 'wait': false});
		idElement = this.id;

		//toolbar._arEffects[idElement][0].start (1);
		toolbar._arEffects[idElement][1].start (45);
		toolbar._arEffects[idElement][2].start (45);
		toolbar._arEffects[idElement][3].start (0);	   
	},
	
	_stopFX : function (evt, toolbar){
		toolbar._resetFX (this, {'duration':200, 'wait': false});
		idElement = this.id;	

		//toolbar._arEffects[idElement][0].start (0.9);
		toolbar._arEffects[idElement][1].start (32);
		toolbar._arEffects[idElement][2].start (32);
		toolbar._arEffects[idElement][3].start (4);	   
	},
	
	_resetFX : function (element, params){
	   idElement = element.id;
	   if (! this._arEffects[idElement]){
	      this._arEffects[idElement] = new Array ();
	   }else{
	      //this._arEffects[idElement][0].stop ();
	      this._arEffects[idElement][1].cancel ();
	      this._arEffects[idElement][2].cancel ();
	      this._arEffects[idElement][3].cancel ();
		 }

       //this._arEffects[idElement][0] = new Fx.Style(element,'opacity', params);	   
       this._arEffects[idElement][1] = new Fx.Tween(element, {'property' : 'width', 'duration' : 50});	   
       this._arEffects[idElement][2] = new Fx.Tween(element, {'property' : 'height', 'duration' : 50});	   
       this._arEffects[idElement][3] = new Fx.Tween(element, {'property' : 'padding-top', 'duration' : 50});	
	}
});