Site = {
	init: function(){
		//for blog, move panel on right colomn
		try{
			$('blog_panel').injectInside($('toright'));
		}catch(e){}
	}
}

window.addEvent('domready',Site.init);
