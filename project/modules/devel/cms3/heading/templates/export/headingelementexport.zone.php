<?php 
echo _tag("button", array ('img' => 'img/tools/compress.png', 'caption' => 'Exporter les éléments', 'id' => 'exportButton', 'type' => 'button'));
ob_start();
_tag ('mootools', array ('plugin'=>'progressbar'));
?>
<div style="padding: 10px;">
	Vous êtes sur le point d'exporter les éléments recherchés dans un fichier zip.<br />
	La structure des dossiers du CMS sera conservée.
	<br />
	<div style="text-align: center;margin-top:5px;">
		<div id="statusProgressBar">&nbsp;</div> 
		<div id="progressBar"></div>
	</div>
	<br />
	<div style="text-align: center;">
	<?php echo _tag("button", array ('img' => 'img/tools/compress.png', 'caption' => 'Exporter', 'id' => 'exportSubmit', 'type' => 'button'));?>
	<a href="javascript:;" id="cancelExport">Annuler</a>
	<?php
			CopixHTMLHeader::addJSDOMReadyCode ("
				var position = 0, cancel = false;
				$('copixwindowexport').addEvent('close', function(){
					cancelExport();
				});
				$('exportSubmit').addEvent('click', function(){
					position = 0;
					$('statusProgressBar').innerHTML = 'Démarrage de l\'export';
					cancel = false;
					makeCall();
				});	
				$('cancelExport').addEvent('click', function(){
					cancelExport();
			        $('copixwindowexport').fireEvent('close');
				});

				var cancelExport = function(){
					cancel = true;
					new Request.HTML ({
			        	url:'"._url('heading|export|cancelExport')."'
			        }).post ({'key':position});		
				}
				
				var pb = new dwProgressBar({
				    container: $('progressBar'),
				    startPercentage: 0,
				    speed:10,
				    boxID: 'box',
				    percentageID: 'perc',
				    displayID: 'text',
				    displayText: true			    
				  });
				  
				var makeCall = function (){
					if (!cancel && position < ".$nbElements."){
						pb.set(position / $nbElements * 100);
				        new Request.HTML ({
				        	url:'"._url('heading|export|export')."', 
				        	update : $('statusProgressBar'),
				        	onComplete: makeCall
				        }).post ({'key':position});				
					} else if (!cancel) {
						$('statusProgressBar').innerHTML = 'Export terminé';
						pb.set(100);
						position = 0;
						window.location = '"._url('heading|export|getZip', array('v'=>uniqid()))."';
					}
					position++;
				}			  
			");
	?>
	</div>
</div>

<?php 
$content = ob_get_contents();
ob_end_clean();

_eTag('copixwindow', array('id'=>'copixwindowexport', 'fixed' => 1, 'clicker'=>'exportButton', 'title'=>'Export', 'modal'=>true, 'modalclose'=>false), $content);
?>