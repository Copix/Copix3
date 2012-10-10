<?php

class ActionGroupDefault extends CopixActionGroup {
    
    function processDefault() {
        $tpl = & new CopixTpl ();
        $profil = new CopixLdapProfil ('dc=nodomain', 'localhost', 'cn=admin,dc=nodomain', 'secret' );
        $ldap = new CopixLdapConnection();
        $test = $ldap->connect($profil);
        if ($test == 'true') {
            $tpl->assign('MAIN', "Connexion a LDAP réussi");
        } else {
            $tpl->assign('MAIN', "Connexion échouée");
        }
        return _arDisplay($tpl);
    }
}
?>