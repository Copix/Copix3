<?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'MV Testing', 'icon' => _resource ('cms_mvtesting|img/icon_mvtesting.png'))) ?>

<div class="element">
	<div class="elementContent">
		<table class="CopixTable">
			<tr>
				<th>Eléments à visualiser</th>
				<th style="width: 80px">Affichages</th>
			</tr>
			<?php foreach ($mvtesting->elements as $element) { ?>
				<tr <?php _eTag ('trclass', array ('id' => 'mvtesting')) ?>>
					<td>
						<?php if ($element->type_element == MVTestingServices::TYPE_CMS) { ?>
							<img src="<?php echo _resource ('portal|img/icon_page.png') ?>" alt="Page du CMS" title="Page du CMS" />
						<?php } else { ?>
							<img src="<?php echo _resource ('admin|img/icon/module.png') ?>" alt="Module Copix" title="Module Copix" />
						<?php } ?>
						<a href="<?php echo $element->url ?>" target="_blank"><?php echo $element->caption ?></a>
					</td>
					<td style="text-align: right"><?php echo $element->show_element ?> (<?php echo $element->show_percents ?> %)</td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>