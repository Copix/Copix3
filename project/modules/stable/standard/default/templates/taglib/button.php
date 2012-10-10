<button class="button" id="<?php echo $id ?>" name="<?php echo $id ?>" type="<?php echo $type ?>"
onmousedown="javascript: $ ('<?php echo $id ?>').addClass ('buttonDown')"
onmouseup="javascript: $ ('<?php echo $id ?>').removeClass ('buttonDown')"
onmouseout="javascript: $ ('<?php echo $id ?>').removeClass ('buttonDown')"
<?php 
if (isset ($extra)) {
	echo ' ' . $extra . ' ';
}
?>
>
	<?php if ($img != null) { ?>
		<img src="<?php echo $img ?>" alt="<?php echo $alt ?>" title="<?php echo $title ?>" />
	<?php } ?>
	<?php echo $caption ?>
</button>