/*

Vous ne devez pas suprimer cet en-tête
fichier javascript pour créer un menu déroulant automatiquement

nombre de niveaux non limité

Auteur : bieler batiste
Blog : www.magnin-sante.ch/journal

send me a mail : http://www.magnin-sante.ch/journal/?p=mailto.php&amp;m=gbefoAbmufso/psh

*/
    
function initmenu(idMenu){

    var browser = navigator.userAgent;
    browser = browser.toLowerCase();
    browser = browser.split('/');
    var version = browser[1].split('.')

    if(!document.getElementById(idMenu))  return;
    
    var menu = document.getElementById(idMenu);

    var lis = menu.getElementsByTagName('li');
    var uls = menu.getElementsByTagName('ul');
    
    for ( var i=0; i<lis.length; i++){
    
        var ul = lis.item(i).getElementsByTagName('ul');
        
        if ( ul.item(0) ){
            /* for Internet Explorer and Opera6 */
            if ( document.all && browser[0]!='opera' || browser[0]=='opera' && version[0]<7 ){
                lis.item(i).onmouseover = visible;
                lis.item(i).onmouseout = hidden;
                lis.item(i).onkeyup = visible;
            /* for Browser */
            }else if( document.getElementById ){
                lis.item(i).addEventListener("mouseover",visible,true);
                lis.item(i).addEventListener("mouseout",hidden,true);
                lis.item(i).addEventListener("blur",hidden,true);
                lis.item(i).addEventListener("focus",visible,true);
                }
            }
        }
    }
    
function hidden(){
    var ul = this.getElementsByTagName('ul');
    ul.item(0).style.visibility = "hidden";
    }
    
function visible(){
    var ul = this.getElementsByTagName('ul');
    ul.item(0).style.visibility = "visible";
    }
    
    
