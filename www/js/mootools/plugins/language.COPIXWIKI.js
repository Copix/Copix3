if(!language) var language = {};

language.COPIXWIKI = {
  snippets: {
    "code" : ["<code>\n    ","something here","\n</code>"],
    "html" : ["<html>\n    ","something here","\n</html>"],
    "js" : ["<code javascript>\n    ","something here","\n</code>"],
    "php" : ["<code php>\n    ","something here","\n</code>"],
    "date" : {
      command: function(k) {
        var dayNames = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
            monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"],
            dt = new Date(),
            y  = dt.getYear();
        if (y < 1000) y +=1900;
        return {
          //key:"date", optional
          snippet:['',dayNames[dt.getDay()] + ", " + monthNames[dt.getMonth()] + " " + dt.getDate() + ", " + y,' '],
          tab:[dayNames[dt.getDay()] + ", " + monthNames[dt.getMonth()] + " " + dt.getDate() + ", " + y,'']
        };
      }
    },
    "[[" : {
      snippet:['[[','link then press tab','|desc','desc',''],
      tab:['link then press tab','desc','']
    },
    "{{" : {
      snippet:['{{image|size|align','']
    },
    "{" : ["{\n    ","","\n"]
  },
  smartTypingPairs: {
    '"' : '"',
    '(' : ')',
    '{' : '}',
    '[' : ']',
    "<" : ">",
    "`" : "`",
    "'" : {
      scope:{
        "<javascript>":"</javascript>",
        "<code>":"</code>",
        "<html>":"</html>"
      },
      pair:"'"
    }
  },
  
  //ctrl+shift+number
  selections: {
    "0": function(sel) {
    	alert(sel);
  		return ['**',sel,'**'];
  	},
  	"1": function(sel) {
  		return ['//',sel,'//'];
  	},
  	"2": function(sel) {
  		return ['__',sel,'__'];
  	},
  	"3": function(sel) {
  		return ['<code>',sel,'</code>'];
  	},
  	"4": function(sel) {
  		return ['<code javascript>',sel,'</code>'];
  	},
  	"5": function(sel) {
  		return ['<html>',sel,'</html>'];
  	},
  	"6": function(sel) {
  		return ['[[',sel,']]'];
  	},
  	"7": function(sel) {
  		return {
  		  selection: [this.ss(),this.se()],
  		  snippet: ['',sel.toLowerCase(),'']
  		};
  	},
  	"8": function(sel) {
  		return ['',sel.toUpperCase(),''];
  	},
  	"9": function(sel) {
  		var mtoc = /<([^<>]*)>/g;
  		return ['',sel.replace(mtoc,"&lt;$1&gt;"),''];
  	}
  }
};