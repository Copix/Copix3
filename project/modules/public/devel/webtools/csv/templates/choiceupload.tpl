{mootools}
<br />
<form action="{copixurl dest="import|selectcolumn"}" method="POST" enctype="multipart/form-data">
	
	<h2>{i18n key='csv.selection'}</h2>
	<br />
	<input type='file' name='fichier_csv' />
	<br />	
	<br />
	<input type="submit" value="{i18n key='csv.save'}" name="save"/>
	<input type="button" value="{i18n key='csv.cancel'}" onclick="javascript:document.location.href='{copixurl dest="import|choosefile"}'"/>
</form>