
CopixClass.implement({
	registerTabGroup: function(options) {
		var tabGroup = $(options.id);
		if(!tabGroup) {
			throw ("invalid id: "+options.id);
		}
		if(options.defaultTab !== undefined && options.defaultTab > options.tabs.length) {
			throw ("invalid default: "+options.defaultTab);
		}
		
		var unselectTab = function(tabId, tabElement) {
			tabElement.removeClass(options.selectedClass);
			tabGroup.fireEvent('tabUnselected', tabId);
		}
		
		var selectTab = function(tabId, tabElement) {
			if(tabElement.hasClass(options.selectedClass)) {
				return;
			}
			$ES('.'+options.selectedClass, tabGroup).each(function(tabElement){
				tabElement.fireEvent('unselect');
			});
			tabElement.addClass(options.selectedClass);
			tabGroup.fireEvent('tabSelected', tabId);
		};
		
		options.tabs.each(function(tabId, tabIndex) {
			var tabElementId = options.id+'_tab'+tabIndex;
			var tabElement = $(tabElementId);
			if(!tabElement) {
				throw ("tabElement doesn't exist: "+tabElementId);
			}
			tabElement.addEvent('select', selectTab.bind(this, [tabId, tabElement]));
			tabElement.addEvent('unselect', unselectTab.bind(this, [tabId, tabElement]));
			tabElement.addEvent('click', tabElement.fireEvent.bind(tabElement, 'select'));
		});
		
		if(options.onSelect) {
			tabGroup.addEvent('tabSelected', options.onSelect);
		}
		if(options.onUnselect) {
			tabGroup.addEvent('tabUnselected', options.onUnselect);
		}
		
		if($defined(options.defaultTab)) {
			var tab =  $(options.id+'_tab'+options.defaultTab);
			if(!tab) {
				throw "Invalid default tab: "+options.defaultTab; 
			}
			this.queueEvent(tabGroup, ['tabSelected', options.tabs[options.defaultTab]]);
		}
		
		return tabGroup;
	}
});
