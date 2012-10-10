/*  
 * Enhance definition lists to make definitions clickable
 * Can be used with keyboard : bring focus to a term then hit enter or space
 * DLs with the class 'nohenhance' will not be enhanced
 * If you don't want the first item to open by default, set class 'collapsed' on the DL
 * 
 * CopixTeam - Goulven CHAMPENOIS
 */

window.addEvent('domready', function(){
	$$('dl').each(function(el){
		if (el.hasClass('noenhance')) {return;}
		el.addClass('enhanced');
		el.getElements('dd').each(function(el){
			el.addClass('hide');
		});
		el.addEvent('click', function(e){
			var target = ($type(e) == 'event') ? e.target : e;
			if($(target).get('tag') == 'dt'){
				var els = target.getAllNext(),
					skip = false;
				target.toggleClass('open');
				els.each(function(el){
					if (el.getTag() == 'dt' || skip) {
						skip = true;
						return;
					}
					el.toggleClass('hide');
				});
			}
		});
		if (!el.hasClass('collapsed')) {
			el.fireEvent('click', [el.getElements('dt')[0]]);
		}
		// Making it keyboard accessible
		el.getElements('dt').each(function(el){
			el.set('tabindex', 0); // Allow DT to receive keyboard focus
			el.addEvent('keypress', function(e){
				if(e.key.test(/enter|space/)){
					el.getParent().fireEvent('click', [el]);
					e.stop();
				}
			});
		});
	});
});