
//Bout de script pour la compatibilite
var compatibilityList = new Class ({
	get: function (id) {
	 return Copix.get_copixlist(id);
	},
	add: function (id, formid) {
	 params = {formid: formid};
	 Copix.register_copixlist (id, params);
	}
});

var list = new compatibilityList ();

var CopixList = new Class ({
    options: {},
    initialize: function (id, params) {
        
        this.options = new Object();
        $extend (this.options, params);
        this.options.listid = id;
    },
	goto:function (kind, params) {
		if (!params) {
			params = {};
		}
		if (!params['onComplete']) {
		   onComplete = function () {};
		} else {
		   onComplete = params['onComplete'];
		}
		
		//On supprime les paramètres inutile
		$each (['kind','currentForm', 'currentId', 'module', 'action', 'group', 'onComplete'], function (property) {
			if (params[property]) {
				delete(params[property]);
			}
		});
		try {
		   Copix.get_loader().display();
		} catch(e) {}
		
		var ajax = new Ajax (Copix.getActionURL ('generictools|copixlist|goto',{kind:kind, currentForm:this.options.formid,currentList:this.options.listid}),
					{
					method:'post',
					update:this.options.listid,
					data:params,
					onComplete: function () {
					    try {
					        Copix.get_loader().hide();
					    } catch(e) {}
					    onComplete();
					},
					evalScripts:true
					}).request();
		return ajax;
	},
	orderby: function (orderby, params) {
		if (!params) {
			params = {};
		}
		if (!params['onComplete']) {
		   onComplete = function () {};
		} else {
		   onComplete = params['onComplete'];
		}

	
		//On supprime les paramètres inutile
		$each (['kind','currentForm','field', 'currentId', 'module', 'action', 'group', 'onComplete'], function (property) {
			if (params[property]) {
				delete(params[property]);
			}
		});
		
		try {
		   Copix.get_loader().display();
		} catch(e) {}
		var ajax = new Ajax (Copix.getActionURL ('generictools|copixlist|orderby',{field:orderby, currentForm:this.options.formid,currentList:this.options.listid}),
					{
					method:'post',
					update:this.options.listid,
					data:params,
					onComplete: function () {
					    try {
					        Copix.get_loader().hide();
					    } catch(e) {}
					    	onComplete();
						},
					evalScripts:true}).request();
		return ajax;
	},
	find:function (params) {
		if (!params) {
			params = {};
		}
		if (!params['onComplete']) {
		   onComplete = function () {};
		} else {
		   onComplete = params['onComplete'];
		}

		
		//On supprime les paramètres inutile
		$each (['kind','currentForm', 'currentId', 'module', 'action', 'group', 'onComplete'], function (property) {
			if (params[property]) {
				delete(params[property]);
			}
		});
		
		try {
		   Copix.get_loader().display();
		} catch(e) {}
		
		queryParams = Object.toQueryString(params);
		if (queryParams) queryParams += '&';
		data = queryParams+$(this.options.formid).toQueryString();
		var ajax = new Ajax (Copix.getActionURL ('generictools|copixlistfind|find',{currentForm:this.options.formid,currentList:this.options.listid}),
					{
					update:this.options.listid,
					data:data,
					method:'POST',
					onComplete: function () {
					    try {
					        Copix.get_loader().hide();
					    } catch(e) {}
					    	onComplete();
						},
					evalScripts:true
					}).request();
		return ajax;
	}

});


CopixClass.implement({
    ArCopixList: new Array (),
    get_copixlist: function (id) {
        if (!this.ArCopixList[id]) {
            throw "CopixList ["+id+"] n'existe pas";
        }
        return this.ArCopixList[id];
    },
    register_copixlist: function (id, params) {
        this.ArCopixList[id] = new CopixList (id, params);
        return this.ArCopixList[id];
    }
});
