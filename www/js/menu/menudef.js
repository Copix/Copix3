if(!document.getElementById)document.getElementById=function(){return false;}
function initmenu(){var browser=navigator.userAgent;browser=browser.toLowerCase();browser=browser.split('/');var version=browser[1].split('.');var menu=document.getElementById('menudef');var lis=menu.getElementsByTagName('li');var uls=menu.getElementsByTagName('ul');
for(var i=0;i<lis.length;i++){var ul=lis.item(i).getElementsByTagName('ul');if(ul.item(0)&&lis.item(i).className!="selected"){
if(document.all||(browser[0]=='opera'&&version[0]<7)){lis.item(i).onmouseover=visible;lis.item(i).onmouseout=hidden;lis.item(i).onkeyup=visible;}else if(document.getElementById){lis.item(i).addEventListener("mouseover",visible,true);lis.item(i).addEventListener("mouseout",hidden,true);lis.item(i).addEventListener("blur",hidden,true);lis.item(i).addEventListener("focus",visible,true);}}}}
function hidden(){var ul=this.getElementsByTagName('ul');ul.item(0).style.visibility="hidden";}
function visible(){var ul=this.getElementsByTagName('ul');ul.item(0).style.visibility="visible";}
initmenu();