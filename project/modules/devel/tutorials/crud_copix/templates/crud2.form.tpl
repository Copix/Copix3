
{copixform_start form="crud_copix|testform" action='crud_copix|form|test' mode=edit}

test : 
{copixform_field field="test" type="varchar" value="arf"}

<br />
test2 :
{copixform_field field="test2" type="varchar" value="test2"}


<br />
{copixform_end}

<br />

Si test egale exception, une erreur sur lui est généré sur test (si cette erreur est levé les autres ne le sont pas)<br />
Si test2 egale boum, une erreur est declenché sur test et test2<br />
Si test2 egale boumboum plusieurs erreurs sont déclenché sur test et une erreur est declenché sur test2<br /> 