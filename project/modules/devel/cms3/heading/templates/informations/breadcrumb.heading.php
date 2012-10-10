 <?php echo CopixZone::process ('heading|headingelement/HeadingElementInformationTitle', array ('title' => 'Fil d\'ariane', 'icon' => _resource ('heading|img/togglers/breadcrumb.png'))) ?>

<div class="element">
	<div class="elementContent">
		<table class="CopixVerticalTable">
			<tr>
				<th style="width: 80px">Afficher</th>
				<td>
					<?php
					_eTag ('radiobutton', array (
						'name' => 'breadcrumb_show_heading',
						'values' => array (
							HeadingServices::BREADCRUMB_SHOW => 'Oui', HeadingServices::BREADCRUMB_HIDE => 'Non'
						),
						'selected' => $heading->breadcrumb_show_heading
					))
					?>
				</td>
			</tr>
		</table>
	</div>
</div>