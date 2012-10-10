
Création du tag simple :<br />
<?php _eTag ('spinbutton|spinbutton'); ?>

<br />
<br />

Entier striquement positif pas de 10 maximum de 200 :<br />
<?php _eTag ('spinbutton|spinbutton', array ('min'=>0, 'max'=>200, 'step'=> 10, 'integer'=> true)); ?>