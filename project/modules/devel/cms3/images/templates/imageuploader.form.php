<table class="CopixVerticalTable">
	<tr>
		<td>
			Nom de l'image
			<br /><input type="text" name="image[<?php echo $fileId; ?>][caption_hei]" value="<?php echo $caption_hei; ?>" class="formField" maxlength="255" />
		</td>
		<td  width="202px" rowspan="2" align="center" style="text-align:center">
			<?php 
			$file_image =  _url('images|upload|ShowImage', array('id_file'=>$fileId));
			list($width,$height) = getimagesize ($file_image); 
			$ratio = $height/$width;
			$height = ($width < 200) ? $height : $height * $ratio;
			$width = ($width < 200) ? $width : 200;		
			?>
			<img id="imageViewer" style="width:<?php echo $width; ?>px;" src="<?php echo $file_image; ?>" title="<?php echo $caption_hei; ?>" />
		</td>
	 </tr>
	 <tr>
		<td>
			Description			
			<br /><textarea style="height:50px;width:100%" name="image[<?php echo $fileId; ?>][description_hei]"></textarea>
		</td>
	</tr>
</table>	