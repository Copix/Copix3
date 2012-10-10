	<form id="uploadform" action="<?php echo _url ('repository|file|validform');?>" enctype="multipart/form-data" method="post">
		<div class="content">
			<fieldset >
				<legend><?php _etag ('i18n', 'repository.form.title');  ?></legend>
				<table style="vertical-align:top;">
					<tr>
						<td>
							<?php _etag ('i18n', 'repository.form.titlefile');  ?>
						</td>
						<td>
							<input name="file_title" id="file_title" type="text" style="width: 200px" />
						</td>
					</tr>
					<tr>
						<td>
							<?php _etag ('i18n', 'repository.form.fileupload');  ?>
						</td>
						<td>
							<input type="file" name="resume_degraded" id="resume_degraded" /> (2 MB max)<br/>
						</td>
					</tr>
					<tr>
						<td>
							<?php _etag ('i18n', 'repository.form.category');  ?>
						</td>
						<td>
						<?php // @todo Utiliser le système générique de categories  ?>
						<select name="file_category" id="file_category" style="width: 200px" />
						<option value="">---</option>
						<option value="1">Categorie 1</option>
						</select>
						</td>
					</tr>
										<tr>
						<td>
							Sous catégorie
						</td>
						<td>
						<?php // @todo Idem précedemment ?>
						<select name="file_subcategory" id="file_subcategory" style="width: 200px" />
						<option value="">---</option>
						<option value="1">Sous catégorie 1</option>
						</select>
						</td>
					</tr>
					<tr>
						<td>
							<?php _etag ('i18n', 'repository.form.comment');?>
						</td>
						<td>
							<textarea name="file_comment" id="file_comment" cols="0" rows="0" style="width: 400px; height: 100px;"></textarea>
						</td>
					</tr>
				</table>
				<br />
				<input type="submit" value="<?php _etag ('i18n', 'repository.form.submit');?>"  id="btnSubmit" />
			</fieldset>
		</div>
	</form>