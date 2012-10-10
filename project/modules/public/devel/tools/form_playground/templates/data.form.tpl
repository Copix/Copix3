


{copixform_start form=$ppo->id dao='forms_playground_multiplepk'}

<h1>Formulaire</h1>

{copixform_field field='caption' caption='Nom' type='varchar'}
<br />
{copixform_field field='number' caption='Pk2' type='varchar'}
<br />
{copixform_field field='description' caption='Description' type='varchar'}
<br />
{copixform_field field='istestordie' caption='Valid' type='varchar' valid='form_playground|validation::testordie'}
<br />
{copixform_end}
<br />
<a href="{copixurl dest='form_playground||'}" >Retour à la liste</a>