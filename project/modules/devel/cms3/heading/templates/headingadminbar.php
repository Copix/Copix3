<div id="CmsAdmin">
	<table style="width: 100%">
		<tr>
			<td>
				
			</td>
			<td style="text-align: right">
				<?php
				_eTag('button', array('id'=>'cms_mode', 'caption'=>CopixUserPreferences::get('heading|cms_mode') != "advanced" ? "Mode avancé" : "Mode simple"));
				CopixHTMLHeader::addJSDOMReadyCode("$('cms_mode').addEvent('click', function(){Copix.savePreference ('heading|cms_mode', '".(CopixUserPreferences::get('heading|cms_mode') != "advanced" ? "advanced" : "simple")."', false);});");
				?>
			</td>
		</tr>
	</table>
</div>