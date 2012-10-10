<br />
<div id="schedulerError<?php echo $id; ?>"><br /></div>
<table class="CopixVerticalTable">
	<tr>
		<th>
			<label for="published_date">Date de début de publication</label>
		</th>
		<td>
			<?php 
				echo _tag("calendar2", array('name'=>'scheduler_published_date', 'id'=>"scheduler_published_date$id", 'value'=>isset($published_date) ? $published_date : '')); 
			?>
			&nbsp;&nbsp;
			<label for="scheduler_published_hour<?php echo $id; ?>">Heure</label>
			<select id="scheduler_published_hour<?php echo $id; ?>" name="scheduler_published_hour">
				<?php 
				for ($i=0;$i<24;$i++){
					echo "<option ".(isset($published_hour) && $published_hour == $i ? 'selected="selected"' : '')." value='" . str_pad($i, 2, '0', STR_PAD_LEFT) . "'>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
				}
				?>
			</select>
			<label for="scheduler_published_minute<?php echo $id; ?>">Minutes</label>
			<select id="scheduler_published_minute<?php echo $id; ?>" name="scheduler_published_minute">
				<?php 
				for ($i=0;$i<60;$i++){
					echo "<option ".(isset($published_min) && $published_min == $i ? 'selected="selected"' : '')." value='" . str_pad($i, 2, '0', STR_PAD_LEFT) . "'>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
				}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<th>
			<label for="end_published_date">Date de fin de publication</label>
		</th>
		<td>
			<?php echo _tag("calendar2", array('name'=>'scheduler_end_published_date', 'id'=>"scheduler_end_published_date$id", 'value'=>isset($end_published_date) ? $end_published_date : '')); ?>
			&nbsp;&nbsp;
			<label for="scheduler_end_published_hour<?php echo $id; ?>">Heure</label>
			<select id="scheduler_end_published_hour<?php echo $id; ?>" name="scheduler_end_published_hour">
				<?php 
				for ($i=0;$i<24;$i++){
					echo "<option ".(isset($end_published_hour) && $end_published_hour == $i ? 'selected="selected"' : '')." value='" . str_pad($i, 2, '0', STR_PAD_LEFT) . "'>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
				}
				?>
			</select>
			<label for="scheduler_end_published_minute<?php echo $id; ?>">Minutes</label>
			<select id="scheduler_end_published_minute<?php echo $id; ?>" name="scheduler_end_published_minute">
				<?php 
				for ($i=0;$i<60;$i++){
					echo "<option ".(isset($end_published_min) && $end_published_min == $i ? 'selected="selected"' : '')." value='" . str_pad($i, 2, '0', STR_PAD_LEFT) . "'>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
				}
				?>
			</select>
		</td>
	</tr>
</table>
<br />
<div style="text-align:center">
	<?php _eTag('button', array ('img' => 'heading|img/actions/planned.png', 'caption' => 'Publication différée', 'id' => 'actionPlanned'.$id, 'type' => 'submit', 'extra'=>'disabled="disabled"'));?>
</div>
<?php 
	CopixHTMLHeader::addJSDOMReadyCode("
		".(isset($published_date) || isset($end_published_date) ? "
			$('$clicker').addEvent('click', function(){
				checkSheduleDate('$id');
			});" : '')."
	
		$('actionPlanned$id').addEvent('click', function(){
			$('schedulerzone_copiwindow$id').fireEvent('close');
		});
		
		$('scheduler_published_date$id').addEvent('change', function(){
			checkSheduleDate('$id');
		});
		$('scheduler_end_published_date$id').addEvent('change', function(){
			checkSheduleDate('$id');
		});
		$('scheduler_published_hour$id').addEvent('change', function(){
			checkSheduleDate('$id');
		});
		$('scheduler_end_published_hour$id').addEvent('change', function(){
			checkSheduleDate('$id');
		});
		$('scheduler_published_minute$id').addEvent('change', function(){
			checkSheduleDate('$id');
		});
		$('scheduler_end_published_minute$id').addEvent('change', function(){
			checkSheduleDate('$id');
		});
	");
?>