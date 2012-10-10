(function($) {
	$(document).ready(function(){
		// set headers for ajax
		if(typeof(Copix) == 'object'){
			jQuery.ajaxSetup({
				beforeSend: function(xhr) {
					if(Copix.options.ajaxSessionId){
						xhr.setRequestHeader('X_COPIX_AJAX_SESSION_ID', Copix.options.ajaxSessionId);
					}
					xhr.setRequestHeader('X-COPIX_AJAX_CACHE_TEMPLATE', Copix.options.cacheTemplate);
				}	
				
			});
		}
	});
    $.toQueryString = function(obj) {
    	var params = [];
    	for(var key in obj){
    		var value = obj[key];
    		if(typeof (obj) != 'function'){
    			params.push(key+'='+value);
    		}
    	}
    	return params.join('&');
    };
    
    $.fn.toQueryString = function() {
    	return  $(this).serialize();
    };
})(jQuery);