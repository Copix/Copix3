<?php _tag('mootools'); ?>
<?php CopixHTMLHeader::addJSLink(_resource ('|js/showhide.js')); ?>
<?php CopixHTMLHeader::addCSSLink (_resource ('|styles/styles.css')); ?>
<?php CopixHTMLHeader::DOMREADY_AUTO ?>
<?php
print '<script language="javascript">';
print 'var ajaxurl = "'._resourcePath('admin|GetConfigurationAjax').'";';
print 'var ajaxnew = "'._resourcePath('admin|GetNewFreeConfigurationAjax').'";';
print '</script>';
?>
<?php if (count ($ppo->arErrors)) { ?>
<div class="errorMessage">
<h1>Erreurs</h1>
<?php echo $ppo->arErrors; ?>
</div>
<?php } ?>

<?php CopixHTMLHeader::addJSLink(_resource ('|js/ajaxcallconfigure.js')); ?>
<?php
if ($ppo->freetest) {
	foreach ($ppo->freetest as $value) { ?>
		<?php
			CopixHTMLHeader::addJSDOMReadyCode(
			'freeTest('.$value->id_test.','.$value->id_tag.');'
			);
	    ?>
	<?php } ?>
<?php } ?>

<div id="main">
<form action="<?php echo CopixUrl::get ('admin|Save') ?>" method="POST"><input
	type="hidden" name="id_test" value="<?php echo $ppo->id_test ?>"> <br />
<table class="CopixTable">
	<thead>
	<tr>
		<th><?php _etag ('copixicon', array ('type'=>"visible")) ?></th>
		<th>
		<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.header')); ?>
		</th>
		<th>
		<div align="right"><?php _etag ('copixicon', array ('type'=>"select")); ?></div>
		</th>
		<th><div align="right"></div></th>
	</tr>
	</thead>
	<tbody class="tag" >
		<?php foreach ($ppo->header as $cle => $element) { ?>
		<tr>
			<td class="key"><?php echo $cle ?> </td>
			<td><?php echo $element ?></td>
			<?php if ($ppo) ?>
			<td align="right"><input type="checkbox" name="header[]"
				value="<?php echo $element ?>"
				<?php
				$find = false;
				foreach ($ppo->headerPreviousValues as $headerValue) { 
					if (strstr($element, $headerValue->value_mark) !== false
						 && $find == false) {
						echo 'checked';
						$find = true;
					}
				}
				?>
				>
				</td> 
		</tr>
		<?php } ?>
	</tbody>
</table>
<br />
<br />
<a href="javascript:freeConfigure(<?php echo count($ppo->body) ?>);" > <?php _etag('i18n', array ('key'=>'copixtest_html.freeConfigure.link')); ?> </a>
<br />
<br />
<table class="CopixTable">
	<thead>
		<tr>
			<th><?php _etag ('copixicon', array ('type'=>"visible")) ?> </th>
			<th> <?php _etag ('i18n', array ('key'=>'copixtest_html.configure.body')); ?> </th>
			<th> <?php _etag ('copixicon', array ('type'=>"select")); ?> </th>
			<th> </th>
		</tr>
	</thead>
	<tbody>
		<?php  foreach ($ppo->body as $cle => $element) { ?>
		<tr class="tag">
			<td class="key"> <?php echo $cle ?> </td>
			<td>
			<?php echo str_repeat ('&nbsp;', count(explode('/', $element->path_tag))-1); ?>
			&lt;<span class="name_tag"><?php echo $element->name_tag.' '; ?> </span>
			<span class="attributes_tag"> <?php foreach (explode(',', $element->attributes_tag) as $value) { echo $value; }
			 ?> </span>&gt;
			 <br />
			 <span class="contains_tag">
			<?php
			if (isset($element->contains)) {
				 for ($i = 0 ;$i <= count(explode('/', $element->path_tag))-1; $i++) {
					echo '&nbsp;&nbsp;';
				}
				echo $element->contains;
			}
			?>
			</span>
			
			</td>
			<td>
			<?php 	foreach ($ppo->bodyPreviousValues as $value) {		
					if ($element->path_tag == $value->path_tag &&
						$element->name_tag == $value->name_tag &&
						$element->attributes_tag == $value->attributes_tag) { 
							 $type = $value->checkType;
							 $check = true;
							 break;
						} else {
							$check = false;
							$type = null;
						}
			}
							?>

				<a href="javascript:call('<?php echo $cle ?>',
				'<?php 
					if (isset($check) && $check == true) {
						echo $type;
					}
				?>',
				'<?php if(isset($element->path_tag)) {echo addslashes($element->path_tag);} ?>',
				'<?php if(isset($element->name_tag)) {echo addslashes($element->name_tag);} ?>',
				'<?php if(isset($element->attributes_tag)) {echo addslashes($element->attributes_tag);} ?>',
				'<?php if (isset($element->contains)) {echo addslashes($element->contains);} ?> ')"> <?php _etag ('copixicon', array('type'=>"update")) ?> </a>
			</td>
			<td>
				<?php
				if (isset($check) && $check == true) {
						_etag ('copixicon', array ('type'=>"valid"));
					}
				?>
			</td>
		</tr>
	</tbody>
<?php } ?>
</table>
<br /> <br />
<div id="config"></div>
<br /> <br />
<div align="center">
<input type="submit" name="envoyer" style="width:150px" value="<?php _etag ('i18n', array ('key'=>'copixtest_html.configure.submit')); ?>"> 
</div>
</form>
<div align="center">
<input type="button" onclick="location.href='<?php echo _resourcePath("admin|cancel") ?>'=" style="width:150px" value="<?php _etag ('i18n',array('key'=>'copixtest_html.edit.cancel')) ?>">
<br /><br />
<input type="submit" onclick="javascript:history.back();" style="width:150px" name="back" style="width:100px" value="Retour">
</div>