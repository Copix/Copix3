<select size="10" name="tables[]" multiple>
	<?php foreach ($tables as $group => $groupTables) { ?>
		<optgroup label="<?php echo $group ?>">
			<?php foreach ($groupTables as $table) { ?>
				<option value="<?php echo $table ?>" <?php if (in_array ($table, $selected)) echo 'selected="selected"' ?>><?php echo $table ?></option>
			<?php } ?>
		</optgroup>
	<?php } ?>
</select>