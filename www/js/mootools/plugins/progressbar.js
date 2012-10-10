/*
Class: ProgressBar
    Creates a slider. Returns the value.

Arguments:
    element - slider container
    options - see Options below

Options:
    mode - either 'horizontal' or 'vertical'. defaults to horizontal.
    steps - the number of steps for your slider.
    color - bar color.
    opacity - bar opacity.
    border - slider border width.

Events:
    onChange - a function to fire when the value changes.
    onComplete - a function to fire when the value changes end.
*/

var ProgressBar = new Class({
    options: {
        steps: 100,
        length: 100,
        statusBar: null
    },

    initialize: function(el, options){
        this.setOptions(options);
        this.value = 0;
        this.el = $(el);
        this.statusBar = null;
        if (this.options.statusBar){
           this.statusBar = document.getElementById (this.options.statusBar);
        }
 		this.bar = new Element('div', {'styles' : {'height' : 20,
                                                   'width' : 1,
                                                   'top' : 0,
                                                   'left' : 0,
                                                   'position' : 'relative',
                                                   'background' : '#ccc'
                                                   }
                                      }).injectInside(this.el);        
    },
    
    set: function(value) {
       this.value = value;
       this._update(value);
    },
    
    step: function(){
       this.set(this.value+1);
    },

    _toStep: function(position){
        return Math.round(position / this.options.steps * this.options.length);
    },

    _update : function(pos){
        this.bar.setStyle('width', this._toStep(pos));
        if (this.statusBar){
           this.statusBar.innerHTML = pos + '/' + this.options.steps + ' (' + Math.round (pos / this.options.steps * 100) +'%)';
        }
    }
});

ProgressBar.implement(new Options);