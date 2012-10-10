CopixClass.implement({
	
	// Liste des jsCalendar2
    arJsCalendar2: new Array(),
    
    register_jsCalendar2: function (id, options) {
		this.arJsCalendar2[id] = Calendar.setup(options);
		
		return this.arJsCalendar2[id];
	}
});
