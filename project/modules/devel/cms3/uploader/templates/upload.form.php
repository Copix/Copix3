<?php
	_tag('mootools');
	CopixHTMLHeader::addJSLink(_resource('uploader|js/swiff.uploader.js'));
	CopixHTMLHeader::addJSLink(_resource('uploader|js/fancyupload2.js'));
	CopixHTMLHeader::addJSLink(_resource('uploader|js/fx.progressbar.js'));
	CopixHTMLHeader::addCSSLink(_resource('uploader|css/style.css'));
?>
<span id='version'></span>
<form action="<?php echo _url('uploader|ajax|upload', array('id_session'=>$id, 'zone'=>$zone)); ?>" method="post" enctype="multipart/form-data" id="form-uploader">
	<fieldset id="fallback" style="display: none">
		<legend>Upload de fichier</legend>
		<p>
			Selectionnez le fichier à envoyer.<br />
		</p>
		<label for="fileupload">
			Fichier :
			<input type="file" name="fileupload" id="fileupload" />
		</label>
	</fieldset>
 
	<div id="status" class="hide">
		<div id="uploadform">
			<table class="CopixVerticalTable">
				<tr>
					<th>Choisissez les fichiers à envoyer :</th>
					<td>
						<input type="button" id="browse" value="Parcourir" />
						<?php if (isset($cancel) && $cancel != null){ ?>
							<input type="button" id="cancel" onclick="location.href='<?php echo _url($cancel); ?>'" value="<?php _etag ('i18n', array('key' => "copix:common.buttons.cancel")); ?>">
						<?php } ?>	
					</td>
				</tr>
				<?php if ($parent_heading_public_id_hei !== false){ ?>
				<tr class="alternate">
					<th>Dossier d'enregistrement : </th>
					<td>
						<?php 
						echo CopixZone::process ('heading|headingelement/headingelementchooser', array('inputElement'=>'uploaderelementchooserheading', 'linkOnHeading'=>true, 'arTypes'=>array('heading'), 'selectedIndex'=>$parent_heading_public_id_hei));
						CopixHTMLHeader::addJSDOMReadyCode("
							$('uploaderelementchooserheading').addEvent('change', function(){
								$('parent_heading_public_id_hei').value = $('uploaderelementchooserheading').value;
							});
						");
						?>
					</td>
				</tr>
				<?php }?>
			</table>			
			<br />
			Une fois le(s) fichier(s) envoyé(s), vous pourrez saisir leurs différentes informations.			
		</div>		
		<br />
		<div>
			<input type="button" style="display:none" id="clear" value="Vider la liste" />
			<input type="button" style="display:none" id="upload" value="Lancer l'envoi de fichier(s)" />
			<br /><br />
		</div>
		<div id="overall-progress" style="display:none">
			<strong class="overall-title">Progression générale</strong><br />
			<img src="<?php echo _resource('uploader|img/bar.gif'); ?>" class="progress overall-progress" />
		</div>
		<div id="current-progress" style="display:none">
			<strong class="current-title">Fichier</strong><br />
			<img src="<?php echo _resource('uploader|img/bar.gif'); ?>" class="progress current-progress" />
		</div>
		<div id="current-text" class="current-text"></div>
	</div>
</form>
<?php if (isset($action) && $action != null){ ?>
<form action="<?php echo _url($action); ?>" id="saveAll" method="post">
	<input type="hidden" name="editId" value="<?php echo _request('editId'); ?>" />
	<input type="hidden" name="id_session" value="<?php echo $id; ?>" />
	<input type="hidden" id="topublish" name="topublish" value="0" />
	<?php if ($parent_heading_public_id_hei !== false){ ?>
		<input type="hidden" name="parent_heading_public_id_hei" id="parent_heading_public_id_hei" value="<?php echo $parent_heading_public_id_hei; ?>" />
	<?php } ?>
	<input type="submit" name="submitForm" id="submitFormtop" value="Enregistrer" style="display:none"/>
	<input type="button" name="publish" id="publishtop" onclick="$('topublish').value=1;$('saveAll').submit();" value="Enregistrer et publier" style="display:none"/>
	<?php if (isset($cancel) && $cancel != null){ ?>
	<input type="button" id="canceltop" style="display:none" onclick="location.href='<?php echo _url($cancel); ?>'" value="<?php _etag ('i18n', array('key' => "copix:common.buttons.cancel")); ?>">
	<?php } ?>
	<ul id="list"></ul>
	<br />
	<input type="submit" name="submitForm" id="submitForm" value="Enregistrer" style="display:none"/>
	<input type="button" name="publish" id="publish" onclick="$('topublish').value=1;$('saveAll').submit();" value="Enregistrer et publier" style="display:none"/>
	<?php if (isset($cancel) && $cancel != null){ ?>
	<input type="button" id="cancel" style="display:none" onclick="location.href='<?php echo _url($cancel); ?>'" value="<?php _etag ('i18n', array('key' => "copix:common.buttons.cancel")); ?>">
	<?php } ?>
</form>

<?php
}
else{
	?>
	<ul id="list"></ul>
	<?php
}

CopixHTMLHeader::addJSDOMReadyCode("
if (Browser.Plugins.Flash.version < 10 ){

var swiffy = new FancyUpload2($('status'), $('list'), {
		'url': $('form-uploader').action,
		'fieldName': 'fileupload',
		'path': '"._resource('uploader|swf/Swiff.Uploader.swf')."',
		'limitFiles' : 100,
		'onLoad': function() {
			$('status').removeClass('hide');
			$('fallback').destroy();
		},
		'onAllComplete' : function(){
			".((isset($action) && $action != null) ? "$('submitForm').setStyle('display','');" : "")."
			".((isset($cancel) && $cancel != null) ? "$('cancel').setStyle('display','');" : "")."
			".((isset($action) && $action != null) ? "$('publish').setStyle('display','');" : "")."
			".((isset($action) && $action != null) ? "$('submitFormtop').setStyle('display','');" : "")."
			".((isset($cancel) && $cancel != null) ? "$('canceltop').setStyle('display','');" : "")."
			".((isset($action) && $action != null) ? "$('publishtop').setStyle('display','');" : "")."
			setTimeout(function(){
				$('overall-progress').fade('out');
				$('current-progress').fade('out');
			}, 1000);
			setTimeout(function(){
				$('overall-progress').setStyle ('display', 'none');
				$('current-progress').setStyle ('display', 'none');
				$('overall-progress').fade('in');
				$('current-progress').fade('in');
			}, 1600);
		},
		'onAllSelect' : function (){
			$('clear').setStyle ('display', '');
			$('upload').setStyle ('display', '');
		},
		'fileRemove' : function(file) {
			var request = new Request.HTML({
				url : Copix.getActionURL('uploader|ajax|removeFile')
			}).post({'file' : file, 'id' : '".$id."'});
			file.element.fade('out').retrieve('tween').chain(Element.destroy.bind(Element, file.element));
			if(this.files.length == 0){
				show ('none');
			}
		}
	});
	
	$('browse').addEvent('click', function() {	
		swiffy.browse(".($authorisedExtensions ? "{'".$extensionsDescription."': '".$authorisedExtensions."'}" : "").");
		return false;
	});
}
else {
	
var swiffy = new FancyUpload2($('status'), $('list'), {
		url: $('form-uploader').action,
		fieldName: 'fileupload',
		path: '"._resource('uploader|swf/Swiff.Uploader.swf')."',
		limitFiles : 100,
		onLoad: function() {
			$('status').removeClass('hide');
			$('fallback').destroy();
		},
		debug: true, // enable logs, uses console.log
		target: 'browse', // the element for the overlay (Flash 10 only)
		onAllComplete : function(){
			".((isset($action) && $action != null) ? "$('submitForm').setStyle('display','');" : "")."
			".((isset($action) && $action != null) ? "$('publish').setStyle('display','');" : "")."
			".((isset($cancel) && $cancel != null) ? "$('cancel').setStyle('display','');" : "")."
			".((isset($action) && $action != null) ? "$('submitFormtop').setStyle('display','');" : "")."
			".((isset($action) && $action != null) ? "$('publishtop').setStyle('display','');" : "")."
			".((isset($cancel) && $cancel != null) ? "$('canceltop').setStyle('display','');" : "")."
			setTimeout(function(){
				$('overall-progress').fade('out');
				$('current-progress').fade('out');
			}, 1000);
			setTimeout(function(){
				$('overall-progress').setStyle ('display', 'none');
				$('current-progress').setStyle ('display', 'none');
				$('overall-progress').fade('in');
				$('current-progress').fade('in');
			}, 1600);
		},
		onAllSelect : function (){
			$('clear').setStyle ('display', '');
			$('upload').setStyle ('display', '');
		},
		fileRemove : function(file) {
			var request = new Request.HTML({
				url : Copix.getActionURL('uploader|ajax|removeFile')
			}).post({'file' : file, 'id' : '".$id."'});
			file.element.fade('out').retrieve('tween').chain(Element.destroy.bind(Element, file.element));
			if(this.files.length == 0){
				show ('none');
			}
		}
	});

	".($authorisedExtensions ? "filter = {'".$extensionsDescription."': '".$authorisedExtensions."'};
		swiffy.options.typeFilter = filter;" : "")."
	
}	
 
	$('clear').addEvent('click', function() {
		swiffy.removeFile();
		show ('none');
		return false;
	});
 
	$('upload').addEvent('click', function() {
		$('overall-progress').setStyle ('display', '');
		$('current-progress').setStyle ('display', '');
		swiffy.upload();
		return false;
	});
");

CopixHTMLHeader::addJSCode("
function show (display){
	$('clear').setStyle ('display', display);
	$('upload').setStyle ('display', display);
	$('overall-progress').setStyle ('display', display);
	$('current-progress').setStyle ('display', display);
	$('publish').setStyle ('display', display);
	".((isset($action) && $action != null) ? "$('submitForm').setStyle ('display', display);" : "")."
	".((isset($cancel) && $cancel != null) ? "$('cancel').setStyle ('display', display);" : "")."
	$('publishtop').setStyle ('display', display);
	".((isset($action) && $action != null) ? "$('submitFormtop').setStyle ('display', display);" : "")."
	".((isset($cancel) && $cancel != null) ? "$('canceltop').setStyle ('display', display);" : "")."
	$('current-text').innerHTML = '';
}
");

?>