/**
 * Animation ajax en cours
 * @return
 */
function ajaxOn() {
	if ($('loading_img')) {		
		$('loading_img').setStyle('display');
	}
	if(typeof(mutexPortal) != 'undefined'){
		mutexPortal.push();
	}
}

/**
 * Animation fin ajax
 * @return
 */
function ajaxOff() {
	if ($('loading_img')) {
		$('loading_img').setStyle('display', 'none');
	}
	if(typeof(mutexPortal) != 'undefined'){
		mutexPortal.pop();
	}
}