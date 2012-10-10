<input type="hidden" name="<?php echo $input ?>" id="<?php echo $input ?>" value="<?php if ($selected !== false) echo $selected->getId () ?>" />
<div style="overflow-x: auto;height: 120px;">
	<?php foreach ($themes as $theme) { ?>
		<div id="<?php echo 'selectTheme' . $theme->getId ();?>" class="theme <?php echo $theme->getId () == $selected->getId () ? "currenttheme" : '';?>">
			<img width="150px" src="<?php echo $theme->getImage () ?>" alt="<?php echo $theme->getName () ?>" title="<?php echo $theme->getName () ?>" />
				<?php				
				$id = $theme->getId ();
				$nameJS = str_replace ("'", '\'', $theme->getName ());
				$idNameJS = $clicker . 'Name';
				$idImageJS = $clicker . 'Image';
				$srcImage = $theme->getImage ();
				$idWindowJS = $clicker . 'Window';
				CopixHTMLHeader::addJSDOMReadyCode ("
				$ ('selectTheme$id').addEvent ('click', function (pEl) {
					$$('.currenttheme').toggleClass('currenttheme');
					$ ('selectTheme$id').addClass('currenttheme');
					var currentTheme = $('$input').value;

					new Request.HTML({
						url: Copix.getActionURL('portal|ajax|updateTheme'),
						update : $('pageContent'),
						onComplete : function(){
							$$('link[type=text/css]').each(function(el){
								if(el.get('href').contains('pageedit.css')){	
									el.dispose();
								}
								if(el.get('href').contains('theme.css')){	
									el.set('href', el.get('href').replace(currentTheme, '$id'));
								}
								if(el.get('href').contains('copix.css')){	
									el.set('href', el.get('href').replace(currentTheme, '$id'));
								}
							});
						}
					}).post({
						'editId' : '"._request("editId")."',
						'theme' : '$id'
					});	
					$ ('$input').value = '$id';
					$ ('$input').fireEvent ('change', {target: $ ('$input')});
				});");
				?>
		</div>
	<?php } ?>
</div>