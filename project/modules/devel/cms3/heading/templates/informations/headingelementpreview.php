<table class="HeadingElementPreview">
	<tr>
		<td>
			<?php if ($actions->show) { ?>
				<a href="<?php echo $link ?>" target="_blank">
					<img src="<?php echo _resource ('heading|img/actions/show_big.png') ?>" alt="Afficher" title="Afficher" />
					<br />
					Afficher
				</a>
			<?php } ?>
		</td>
		<td>
			<?php if ($actions->publish) { ?>
				<a href="<?php echo $linkPublish ?>">
					<img src="<?php echo _resource ('heading|img/actions/publish_big.png') ?>" alt="Publier" title="Publier" />
					<br />
					Publier
				</a>
			<?php } elseif ($actions->archive) { ?>
				<a href="<?php echo $linkArchive ?>">
					<img src="<?php echo _resource ('heading|img/actions/archive_big.png') ?>" alt="Archiver" title="Archiver" />
					<br />
					Archiver
				</a>
			<?php } ?>
		</td>
		<td>
			<?php if ($actions->planned) { 
				$id = uniqid();
				echo CopixZone::process('heading|scheduler', array('id'=>$id, 'clicker'=>'schedulerClicker'.$id, 'published_date'=>$record->published_date_hei, 'end_published_date'=>$record->end_published_date_hei));
				CopixHTMLHeader::addJSDOMReadyCode ("	
					console.debug('schedulerForm$id');				
					$ ('actionPlanned$id').addEvent ('click', function () { 
						var published_date = $('scheduler_published_date$id').value ? $('scheduler_published_date$id').value + ' ' + $('scheduler_published_hour$id').value + ':' + $('scheduler_published_minute$id').value + ':00' : '';
						var end_published_date = $('scheduler_end_published_date$id').value ? $('scheduler_end_published_date$id').value + ' ' + $('scheduler_end_published_hour$id').value  + ':' + $('scheduler_end_published_minute$id').value  + ':00': '';
						window.location.href='$linkPlan&published_date='+published_date+'&end_published_date='+end_published_date;
					});
				");
				?>
				<a id="schedulerClicker<?php echo $id;?>" href="<?php echo $linkPlan; ?>">
					<img src="<?php echo _resource ('heading|img/actions/planned_big.png') ?>" alt="Planifier" title="Planifier" />
					<br />
					Planifier
				</a>
			<?php } ?>
		</td>
		<td>
			<?php if ($actions->copy) { ?>
				<a href="<?php echo $linkCopy ?>">
					<img src="<?php echo _resource ('heading|img/actions/copy_big.png') ?>" alt="Copier" title="Copier" />
					<br />
					Copier
				</a>
			<?php } ?>
		</td>
		<td>
			<?php if ($actions->cut) { ?>
				<a href="<?php echo $linkCut ?>">
					<img src="<?php echo _resource ('heading|img/actions/cut_big.png') ?>" alt="Couper" title="Couper" />
					<br />
					Couper
				</a>
			<?php } ?>
		</td>
		<td>
			<?php if ($actions->delete) { ?>
				<a href="<?php echo $linkDelete ?>">
					<img src="<?php echo _resource ('heading|img/actions/delete_big.png') ?>" alt="Supprimer" title="Supprimer" />
					<br />
					Supprimer
				</a>
			<?php } ?>
		</td>
	</tr>
</table>

<table class="CopixVerticalTable">
	<?php foreach ($infos as $info) { ?>
		<tr <?php _eTag ('trclass') ?>>
			<th style="width: 90px"><?php echo $info['caption'] ?></th>
			<td><?php echo $info['value'] ?></td>
		</tr>
	<?php } ?>
</table>