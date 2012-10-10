/**
 * On surcharge cette mootools qui dans la nouvelle version fait un appel a un DOMParser qui fait planté
 * certains retour Ajax sur certains navigateur
 *
 */
Request.HTML.implement ({processHTML:function(text){
    var match = text.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
    text = (match) ? match[1] : text;

    var container = new Element('div');

    return container.set('html', text);
}
});
