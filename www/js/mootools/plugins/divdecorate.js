var DivDecorator = {

    skinname: 'default',
    
    create: function (elem,options){
        options = $merge({
            'skin' : 'default'
        },options)
        
        DivDecorator.skinname = options.skin;
        
    	new Asset.css(Copix.getResourceURL('/js/mootools/css/divdecorator/'+options.skin+'/style.css'), {id: 'DivDecorator_skin_'+options.skin});    
        DivDecorator._changeDOM(elem);
    },

    _changeDOM: function (elem){
        var cb = new Element('div',{'class': DivDecorator.skinname}).injectBefore(elem)
        var i1 = new Element('div',{'class': 'i1'}).injectInside(cb)
        var i2 = new Element('div',{'class': 'i2'}).injectInside(i1)
        var i3 = new Element('div',{'class': 'i3'}).injectInside(i2)        

        elem.injectInside(i3)
        if(elem.getStyle('float')){
            cb.setStyle('float', elem.getStyle('float'))
            elem.setStyle('float','');            
        }

        if(elem.getStyle('width').match('%')){
            cb.setStyle('width', elem.getStyle('width'))
            elem.setStyle('width','');
        }
        
        var bt = new Element('div',{'class': 'bt'}).set('html', '<div></div>').injectTop(cb);
        var bb = new Element('div',{'class': 'bb'}).set('html', '<div></div>').injectInside(cb);
    }

}

Element.implement({
    decorate: function (options){
        DivDecorator.create(this,options);
    }
})

Array.implement({
    decorate: function (options){
        this.each(function (elem){
            DivDecorator.create(elem,options);        
        });

    }
})
