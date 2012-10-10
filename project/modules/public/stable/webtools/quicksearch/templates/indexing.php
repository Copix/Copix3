<?php 
_tag ('mootools', array ('plugin'=>'progressbar'));
?>

<div id="statusProgressBar" style="width: 300px;text-align: center">&nbsp;</div> 
<div id="progressBar" style="border: 1px solid #000; width: 300px;"></div>

<script defer="1" language="Javascript">
var linkList;
var position = 0;
var progressBar1;

function doIndex (XMLList){
   var i = 0;
   linkList = XMLList.getElementsByTagName ('link');
   progressBar1 = new ProgressBar ('progressBar', {steps: linkList.length, length: 300, statusBar: 'statusProgressBar'});
   makeCall ();
}

function sleep(millis) {
    var notifier = new EventNotifier();
    setTimeout(notifier, millis);
    notifier.wait();
}

function makeCall (){
   if (position < linkList.length){
      progressBar1.step ();
      new Ajax (linkList[position].getAttribute ('url'), {onComplete: makeCall}).request ();
   }
   position = position+1;
}

new Ajax('<?php echo _url ('admin|prepareIndexAll'); ?>', {
        'onComplete' : function () {
                if (! this.response['xml']){
                   //gestion de l'erreur
                }else{
                   //ok, bien eu une réponse correcte
                   doIndex (this.response['xml']);
                }
        }
}).request ();
</script>
<br />
<a href="<?php echo _url ('admin||'); ?>"><input type="button" value="<?php echo _i18n ('copix:common.buttons.back'); ?>" /></a>