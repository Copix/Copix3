<div id="htmleditor" style="display: block">
	Title <input type="text" id="mb_title" /><br />
<textarea rows="20" cols="50" id="mb_content">
</textarea>
	<br />
	<input type="button" id="validate" value="OK"/>
</div>
<script>
	editAreaLoader.init({
			id : "mb_content"		// textarea id
			,syntax: "<?php echo $lang ?>"			// syntax to be uses for highgliting
			,start_highlight: true		// to display with highlight mode on start-up
	});
</script>