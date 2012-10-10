<?php
echo "Test des classes de génération de template";
require_once ('../classes/copixtemplateproperty.class.php');
require_once ('../classes/copixtemplatesimpleproperty.class.php');
require_once ('../classes/copixtemplatecomboproperty.class.php');
require_once ('../classes/copixtemplateelement.class.php');
require_once ('../classes/copixtemplatecontainer.class.php');
require_once ('../classes/copixtemplatedivcontainer.class.php');

$template = new CopixTemplateContainer ();
$template->addElement ($gauche = new CopixTemplateDivContainer ());
$template->addElement ($droite = new CopixTemplateDivContainer ());
$droite->addElement   ($hautDroite = new CopixTemplateDivContainer ());
$droite->addElement   ($basDroite = new CopixTemplateDivContainer ());

$html = $template->getHtml ();
echo htmlentities ($html);
?>