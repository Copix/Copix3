/*
Class: CopixFormObserver
    Vérifie si un formulaire a changé, si c'est le cas exécute la méthode onChanged passée en paramètre
    
    ATTENTION : La méthode onChange ne sera pas forcément suivie d'un onChanged ou d'un onChangeCanceled
    si l'utilisateur reviens sur ses modifications (saisie de a onChange, puis de ab onChange, supression de b 
    onChange, et enfin retour à écran vide (aucun évènement car le formulaire reviens a l'état non modifié))   

Arguments:
    formId - le formulaire a observer
    options - voir options ci après

Options:
    onChanged - la fonction a exécuter sur changements "terminés" du formulaire 
	checkIntervall - le nombre de millisecondes a attendre entre les vérifications de changemnet
	waitForCycle - le nombre de cycles de vérifications que l'on va attendre après le 
		dernier changement avant de lancer l'évènement (pour attendre durant la frappe au clavier dans les champs 
		de saisie libre par exemple)
	onChangeCanceled - la fonction a eé�cuter sur retour du formulaire a l'état initial (lors de la création de l'objet)
	onChange - la fonction a éxécuter lorsque des changements sont en cours sur le formulaire.
	register : Indique si l'on souhaite que l'objet s'enregistre automatiquement dans l'objet Copix
	registerId : l'identifiant dans le registre Copix que l'on souhaite utiliser. Par défaut cela sera l'identifiant du formulaire a observer.
	   Si deux FormObserver sont crés avec le même identifiant, le dernier construit détruit le premier.
*/
CopixFormObserver = new Class({
    _form: null,

    _lastQueryString: null,
    
    _lastCheckedQueryString : null,
    
    _initialQueryString : null,

    _changed : false,
    
    _changedCycleCount : 0,
    
    _periodicalCheck : null, 

	options: {
		onChanged : null,
		onChangeCanceled : null,
		onChange : null,
		checkIntervall : 100,
		waitForCycle : 10,
		register: true,
		registerId: null
	},

	// Constructeur
	initialize : function (formId, options) {
	   //charge les modifications
	   $extend (this.options, options);

	   if (! (this._form = $(formId))){
	      throw "Form [" + formId + "] does not exists";
	   }
	   
	   this._lastQueryString = this._form.toQueryString ();
	   this._lastCheckedQueryString = this._lastQueryString;
	   this._initialQueryString = this._lastQueryString;
	   
	   if (this.options.register){
	   		var registerId = this.options.registerId || this._form.id;
			Copix.register_observer (registerId, this);
	   }

	   this.start ();
	},
	
	stop : function (){
	   $clear (this._periodicalCheck);
	},
	
	start : function (){
	    this.stop ();
		this._periodicalCheck = this._checkChanges.periodical (this.options.checkIntervall, this);
	},

	toQueryString  : function () {
	   return this._lastQueryString;
	},
	
	hasChanged : function (){
	   return this._lastCheckedQueryString != this._initialQueryString; 
	},
	
	saveState : function (){
	   this._initialQueryString = this._lastCheckedQueryString;
	   if ($type (this.options.onChangeCanceled) == 'function'){
	      this.options.onChangeCanceled ();
	   }				   
	},

	_checkChanges : function (){
		   var currentQueryString = this._form.toQueryString ();
		   //y'a t-il un changement depuis le dernier évènement ?
		   if ((this._changed == false) && currentQueryString != this._lastQueryString){
		   		//on marque la dernière modification de chaine de caractère
		        this._changed = true;
 			    this._lastCheckedQueryString = currentQueryString;
		   }

		   //Si on est en cours de modification, on regarde le nombre de cycles depuis
		   //la dernière modification (pour ne pas lancer l'évènement si le formulaire
		   //est en cours de saisie)
		   if (this._changed){
		   	  //on regarde si on est revenu a la situation initiale
		      if (currentQueryString == this._lastQueryString){
		        this._changed = false;
		        this._changedCycleCount = 0;
		      }else{
		          //Il y a eu des changements depuis le dernier test ?
			      if (this._lastCheckedQueryString != currentQueryString){
			         this._changedCycleCount = 0;
	  	          	 this._lastCheckedQueryString = currentQueryString;
			      }else{
			      	//pas de changements, on comptabilise pour l'évènement changed
			         this._changedCycleCount++;
			      }
			  }
			  
			  //Si le compteur de cycle = 0 alors c'est un "premier changement"
			  //On lance donc l'évènement de changement
			  if (this._changedCycleCount == 0){
				 if ($type (this.options.onChange) == 'function'){
					this.options.onChange ();
				 }
			  }
		   }

		   //On regarde s'il faut lancer l'évènement (nombre de cycle depuis la dernière modification)
		   if (this._changed && (this._changedCycleCount > this.options.waitForCycle)){
				this._lastQueryString = currentQueryString;
				this._lastCheckedQueryString = currentQueryString; 	      

				this._changed = false;
				this._changedCycleCount = 0;

				if ($type (this.options.onChanged) == 'function'){
					this.options.onChanged ();
				}
				
				//On regarde s'il faut lever l'évènement de retour
				//a l'url initiale
				if (! this.hasChanged ()){
				   if ($type (this.options.onChangeCanceled) == 'function'){
				      this.options.onChangeCanceled ();
				   }				   
				}
			}
	}
});

CopixClass.implement({
    _formObservers: new Array(),

	register_observer: function (id, observer) {
		if (this._formObservers[id]) {
			this._formObservers[id].stop ();
			this._formObservers[id] = null;
		}
	    this._formObservers[id] = observer;
	},
	get_observer: function (id) {
		if (this._formObservers[id]) {
		   return this._formObservers[id];
		}
		return false;
	}
});