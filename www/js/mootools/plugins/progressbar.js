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
 		this.bar = new Element('div', {'class':'progressBar', 'styles' : {'height' : 20,
                                                   'width' : 1,
                                                   'top' : 0,
                                                   'left' : 0,
                                                   'position' : 'relative',
                                                   'background' : '#ccc'
                                                   }
                                      }).injectInside(this.el);
		this._update (0);
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





/*
Class:    	dwProgress bar
Author:   	David Walsh
Website:    http://davidwalsh.name
Version:  	1.0
Date:     	07/03/2008
Built For:  MooTools 1.2.0

*/


//class is in
var dwProgressBar = new Class({

//implements
Implements: [Options],

//options
options: {
	container: $$('body')[0],
	boxID:'',
	percentageID:'',
	displayID:'',
	startPercentage: 0,
	displayText: false,
	speed:10
},

//initialization
initialize: function(options) {
	//set options
	this.setOptions(options);
	//create elements
	this.createElements();
},

//creates the box and percentage elements
createElements: function() {
	var box = new Element('div', { id:this.options.boxID });
	var perc = new Element('div', { id:this.options.percentageID, 'style':'width:0px;' });
	perc.inject(box);
	box.inject(this.options.container);
	if(this.options.displayText) { 
		var text = new Element('div', { id:this.options.displayID });
		text.inject(this.options.container);
	}
	this.set(this.options.startPercentage);
},

//calculates width in pixels from percentage
calculate: function(percentage) {
	return ($(this.options.boxID).getStyle('width').replace('px','') * (percentage / 100)).toInt();
},

//animates the change in percentage
animate: function(to) {
	$(this.options.percentageID).set('morph', { duration: this.options.speed, link:'cancel' }).morph({width:this.calculate(to.toInt())});
	if(this.options.displayText) { 
		$(this.options.displayID).set('text', to.toInt() + '%'); 
	}
},

//sets the percentage from its current state to desired percentage
set: function(to) {
	this.animate(to);
},

step: function(){
    this.set(this.value+1);
 }

});
