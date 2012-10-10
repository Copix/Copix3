


<form action="" method="get">
	<div style="zoom:1" >
		Création du tag simple de selectionneur de langue complexe (tester en desactivant le javascript) : <br />
		
		{copixlanguagechooser name='test' }
		<br style="clear: both;" />
		
		<br />
		<br />
		
		Création du tag simple de selectionneur de langue complexe avec divers option : <br />
		
		{copixlanguagechooser name='test2' emptyShow=true emptyValue='no flag' heightSelect=100 arrow=false }
		<br style="clear: both;" />
		
		<br />
		<br />
		Création du tag de select box complexe passage par variable : <br />
		
		{selectcomplex name='test3' options=$ppo->options  alternatives=$ppo->alternatives}{/selectcomplex}
		<br style="clear: both;" />
		
		<br />
		<br />
		Création du tag de select box complexe avec html smarty : <br />
		{selectcomplex name='test4' selected='vert' }
			
			{selectcomplexoption value='rouge' alternative='Rouge' }
				a<div style="width:200px; height:50px; color: red;" >Valeur 1</div>a
				{selectcomplexoptionselectedview}
				<div style="width:50px; height:25px; color: red;" >Rouge</div>
				
			{/selectcomplexoption}
			
			{selectcomplexoption value='vert' alternative='Vert' }
				b<div style="width:200px; height:50px; color: green;" >Valeur 2</div>b
				{selectcomplexoptionselectedview}
				<div style="width:50px; height:25px; color: green;" >Vert</div>
			{/selectcomplexoption}
			
			{selectcomplexoption value='yellow'}
				n<div style="width:200px; height:50px; color: yellow;" >Valeur N</div>n
			{/selectcomplexoption}
			
		{/selectcomplex}
		
		
		<br style="clear: both;" />
		<input type="submit" name="" value="{i18n key='copix:common.buttons.ok' }" />
		
	</div>
</form>


<script type="text/javascript">
	//alert ($('test').value);
	//alert ($('test2').value);
	//alert ($('test3').value);
	//alert ($('test4').value);
</script>