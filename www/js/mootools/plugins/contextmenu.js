Elements.implement ({
     addContextMenu: function (options) {
        this.div = new Element ('div');
        this.div.addClass ((options.classmenu || 'menu'));

        this.ul = new Element ('ul');
        this.ul.injectInside(this.div);

        options.items.each ( function (el) {
           var li = new Element ('li');
           var a = new Element ('a');

           a.innerHTML = el.caption;
           a.href="javascript:void(null);";
           if (el.href) {
              a.href = el.href;
           }

           if (el.onclick) {
              a.addEvent('click', function (e) {
                  this.display = false;
                  this.div.setStyle ('display', 'none');
                  el.onclick.call (this.current,e);
              }.bind (this));
           }

           a.injectInside (li);
           li.injectInside (this.ul);

        }.bind(this));

        this.div.addEvent ('mouseleave', function () {
            this.over = false;
        }.bind(this));

        this.div.addEvent ('mouseenter', function () {
            this.over = true;
        }.bind(this));

        window.addEvent ('click', function () {
            if (!this.over && this.display) {
                this.display = false;
                this.div.setStyle ('display', 'none');
            }
        }.bind (this))
        
        this.over = false;
        this.display = false;

        this.div.setStyles ({'position':'absolute', 'display':'none', 'background-color':'white', 'border':'1px solid black'});
        this.div.injectInside (document.body);

        this.each (function (element) {
            element.addEvent ('contextmenu', function (e) {
                var e = new Event (e);
                this.display = true;
                this.current = element;
                
                this.div.setStyles({
                        'position':'absolute',
                        'display':'',
                        'top' : e.page.y+'px',
                        'left' : e.page.x+'px',
                        'zIndex':'1001'
                });
                
                
                e.stop();
                return false;
            }.bind(this));
        }.bind (this));
     }
 });

Element.implement ({
     addContextMenu: function (options) {
        new Elements ([this]).addContextMenu (options);
     }
 });
