
CopixClass.implement({
	registerZone: function(options) {
		var zone = $(options.zoneId);
		var trigger = options.triggerId && $(options.triggerId);
		if(!zone) {
			throw ("invalid zoneId: "+options.zoneId);
		}
		if(options.triggerId && !trigger) {
			throw ("invalid triggerId: "+options.triggerId);
		}

		if(options.instanceId) {
			zone.addEvent('load', function(params) {
				if (!params) {
				   params = {};
				}
				if (zone.innerHTML == '' || params.copixzone_force_refresh) {
					Copix.setLoadingHTML(zone);
					new Ajax($pick(options.url, Copix.getActionURL('generictools|ajax|getZone')), {
						method: 'post',
						update: zone,
						evalScripts : true,
						data: $merge ({'instanceId': options.instanceId}, params),
						onComplete: zone.fireEvent.bind(zone,'complete')
					}).request();
				} else {
					zone.fireEvent('complete');
				}
			});
		} else {
			zone.addEvent('load', zone.fireEvent.bind(zone,'complete'));
		}
		
		zone.addEvent('refresh', function (params) {
			//zone.innerHTML = ''
			if (!params) {
			   params = {};
			}
            params.copixzone_force_refresh=true;
			zone.fireEvent('load', params);
		});
		
		zone.addEvent('display', function() {
			zone.setStyle('display','');
			zone.fireEvent('load');
		});
	
	    zone.addEvent('hide', function() {
	    	zone.setStyle('display','none');
	    });
		
		if(trigger) {
			trigger.addEvent('click', function() {
		    	if(zone.getStyle('display') == 'none') {
		    		zone.fireEvent('display');
		    	} else {
		    		zone.fireEvent('hide');
		    	}
			});
			
			zone.addEvent('display', trigger.fireEvent.bind(trigger,'display'));
			zone.addEvent('hide', trigger.fireEvent.bind(trigger,'hide'));
		}
		
		if(options.onDisplay) {
			zone.addEvent('display', options.onDisplay.pass(zone, trigger));
		}
		if(options.onHide) {
			zone.addEvent('hide', options.onHide.pass(zone, trigger));
		}
		if(options.onComplete) {
			zone.addEvent('complete', options.onComplete.pass(zone, trigger));
		}
	    
	    if(options.auto) {
	    	this.queueEvent(zone, 'display');
	    }
	    
	    return zone;
	}
});
