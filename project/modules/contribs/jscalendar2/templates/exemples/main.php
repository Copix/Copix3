
Création du tag simple :<br />
<?php _eTag ('jscalendar2|jscalendar2'); ?>
<br />
<br />

Création du tag avec id, name, class [...] ainsi que id, class [...] du déclancheur (trigger) :<br />
<?php
_eTag ('jscalendar2|jscalendar2', array (
	
	'id'     =>'idTag1',
	'name'   =>'nameTag1',
	'class'  =>'classTag1',
	'style'  =>'color:red',
	'tabindex'  =>'12',
	'trigger'=>array('id'    => 'idTriggerTag1',
	                 'class' => 'classTriggerTag1',
	                 'style' => 'width:30px; height:30px; margin-bottom:-10px;')
	
));
?>
<br />
<br />

Création avec :<br />
&nbsp;- trigger interne <br />
&nbsp;- champs de saisie bloquer,<br />
&nbsp;- un autre format de date,<br />
&nbsp;- avec un value par default<br />
&nbsp;- sans le numero de semaine<br />
<?php _eTag ('jscalendar2|jscalendar2', array (
	'interne'    =>true,
	'lock'       =>true,
	'dateFormat' =>'%Y  :  %d-%m',
	'value'      =>'2050  :  10-03',
	'weekNumbers'=>false
)); ?>
<br />
<br />




