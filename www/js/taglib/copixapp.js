CopixClass.implement({
	registerApp : function(options) {
		var zone = $(options.zoneId);
		if(!zone) {
			throw ("invalid zoneId: "+options.zoneId);
		}

		zone.addEvent('goto', function (url,params) {	
			Copix.setLoadingHTML(zone);
			new Request.HTML({
			    url: url,
				method: 'post',
				update: zone,
				evalScripts : true,
				data: $merge({'instanceId': options.instanceId}, params)
			}).send();
		});

		zone.addEvent('submit',function (el) {
			el = el.getParent();
			while (!el && el.get('tag') != 'form') {
			    el = el.getParent();
			}
			var instanceId = new Element ('input');
			instanceId.setProperty('type','hidden');
			instanceId.setProperty('value',options.instanceId);
			instanceId.setProperty('name','instanceId');
			
			instanceId.injectInside (el);
			el.send();
		});

		this.queueEvent(zone, 'goto', [options.url]);
			
	    return zone;
	}
});