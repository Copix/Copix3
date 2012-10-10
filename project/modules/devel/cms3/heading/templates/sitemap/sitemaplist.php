<?php _eTag ('beginblock', array ('title' => 'Liste des sitemap', 'isFirst' => true)); ?>
<table class="CopixTable">
	<thead>
		<tr>
			<th>Nom du sitemap</th>
			<th colspan="3"> </th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($ppo->list as $sitemap){?>
		<tr>
			<td>
				<a href="<?php echo _url ('heading|sitemap|editSitemap', array ('linkId' => $sitemap->getSiteMapLink()->getId())) ?>">
					<?php echo $sitemap->getSiteMapLink()->getCaption();?>
				</a> 
			</td>
			<td class="action" style="width: 20px;">
				<a href="<?php echo _url ('heading|sitemap|editSitemap', array ('linkId' => $sitemap->getSiteMapLink()->getId())) ?>">
					<img src="<?php echo _resource ('img/tools/update.png') ?>" alt="Modifier" title="Modifier"/>
				</a>
			</td>
			<td class="action" style="width: 20px;">
				<a href="<?php echo _url ('heading|sitemap|deleteSitemap', array ('id' => $sitemap->getId())) ?>">
					<img src="<?php echo _resource ('img/tools/delete.png') ?>" alt="Supprimer" title="Supprimer"/>
				</a>
			</td>
			<td class="action" style="width: 20px;">
				<a target="sitemap_preview" href="<?php echo _url ('heading|sitemap|getSitemap', array ('id' => $sitemap->getId())) ?>">
					<img src="<?php echo _resource ('heading|img/generalicons/cms_show.png') ?>" alt="Apercu" title="Apercu" />
				</a>
			</td>
		</tr>
		
	<?php }?>
	</tbody>
</table>

<br />
<table style="width: 100%">
	<tr>
		<td style="width: 50%">
			<a href="<?php echo _url('heading|sitemap|editSitemap');?>">
				<img src="<?php echo _resource ('img/tools/add.png') ?>" />
				Ajouter un sitemap
			</a>
		</td>
		<td style="text-align: right"><?php //_eTag ('back', array ('url' => 'admin||')) ?></td>
	</tr>
</table>
<?php _eTag ('endblock'); ?>