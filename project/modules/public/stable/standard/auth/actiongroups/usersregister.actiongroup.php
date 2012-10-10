<?php
/**
 * @package standard
 * @subpackage auth
 * @author        Gérald Croës
 * @copyright    CopixTeam
 * @link        http://copix.org
 * @license        http://www.gnu.org/licenses/lgpl.html GNU General Lesser  Public Licence, see LICENCE file
 */

/**
 * Opération sur la gestion des utilisateurs
 * @package standard
 * @subpackage auth
 */
class ActionGroupUsersRegister extends CopixActionGroup {
    /**
     * On vérifie que l'on a activé les fonctions de création de compte
     */
    public function beforeAction (){
        if (! CopixConfig::get ('auth|createUser')){
            throw new Exception (_i18n ('auth.notAllowed'));
        }
    }
    
    /**
     * Page de modification d'un utilisateur
     */
    public function processEdit (){
        //création du tableau d'erreur
        $errors = array ();
        if (CopixRequest::get ('loginNotAvailable', '0') == 1){
            $errors[] = _i18n ('auth.error.loginNotAvailable');
        }
        if (CopixRequest::get ('loginEmpty', '0') == 1){
            $errors[] = _i18n ('auth.error.loginEmpty');
        }
        if (CopixRequest::get ('passwordDoNotMatch', '0') == 1){
            $errors[] = _i18n ('auth.error.passwordDoNotMatch');
        }
        if (CopixRequest::get ('passwordEmpty', '0') == 1){
            $errors[] = _i18n ('auth.error.passwordEmpty');
        }
        if (CopixRequest::get ('emailEmpty', '0') == 1){
            $errors[] = _i18n ('auth.error.emailEmpty');
        }
        if (CopixRequest::get ('emailIsBad', '0') == 1){
            $errors[] = _i18n ('auth.error.emailIsBad');
        }
        if (CopixRequest::get ('confirmCodeBad', '0') == 1){
            $errors[] = _i18n ('auth.error.confirmCodeBad');
        }
        
        //Affichage de la page
        $ppo = new CopixPPO ();
        $ppo->TITLE_PAGE =  _i18n ('auth.newUser');
        $ppo->errors = $errors;
        $ppo->createInProcess = "true";
        $ppo->createUser = Copixconfig::get('auth|createUser');
        $ppo->typeConfirm = Copixconfig::get('auth|typeConfirm');
        
        // Cherche les valeurs du formulaire si l'ont est en mode réédition
        if (($idForm = CopixRequest::get ('idForm', '0')) != 0) {
            $user = CopixSession::get ('auth|createForm', $idForm);
            if ($user !== null) {
                CopixSession::destroyNamespace($idForm);
                $ppo->user = $user;
            }
        }
        
        return _arPPO ($ppo, 'user.edit.php');
    }

    /**
     * Validation des modifications apportées sur un utilisateur
     */
    public function processValid (){
        
        $user->login_dbuser = _request ('login_dbuser', '');
        $user->email_dbuser = _request ('email_dbuser');
        
        $errors = array();
        
        //on vérifie si le login n'est pas déja pris
        $sp = CopixDAOFactory::createSearchParams ();
        $sp->addCondition ('login_dbuser', '=', $user->login_dbuser);
        
        if ($user->login_dbuser === '') {
            $errors['loginEmpty'] = 1;
        }
        
        if (count (_ioDAO ('dbuser')->findBy ($sp))){
            $errors['loginNotAvailable'] = 1;
        }
        
        //on vérifie si un mot de passe est donné qu'ils soient bien identiques
        if (CopixRequest::get ('password_dbuser')){
            if (CopixRequest::get ('password_dbuser') != 
                CopixRequest::get ('password_confirmation_dbuser')){
                    $errors['passwordDoNotMatch'] = 1;
            }else{
                $user->password_dbuser = md5 (CopixRequest::get ('password_dbuser'));
            }            
        }else{
            //Comme c'est automatiquement un nouvel utilisateur, il est obligatoire de saisir un nouveau mot de passe.
            $errors['passwordEmpty'] = 1;
        }
        
        if (Copixconfig::get('auth|typeConfirm') == "email"){
            if (!$user->email_dbuser){
                $errors['emailEmpty'] = 1;
            }else{
                try {
                    CopixFormatter::getMail($user->email_dbuser);
                } catch (CopixException $e) {
                    $errors['emailIsBad'] = 1;
                }
            }
        }
        
        if (Copixconfig::get('auth|typeConfirm') == "email"){

            $user->enabled_dbuser = 0;
        }else {
            $user->enabled_dbuser = 1;
        }
        
        //Si le module imageprotect est activé test la protection anti-spam
        if(CopixModule::isEnabled('antispam')) {
            CopixRequest::assert('confirmcode_dbuser');
            CopixRequest::assert('idcode_dbuser');
            $code   = _request('confirmcode_dbuser');
            $idCode = _request('idcode_dbuser');
            
            // Test si le code de ssession est valide
            _classInclude('antispam|imageprotect');
            if (!ImageProtect::getCode($idCode, $code)) {
                $errors['confirmCodeBad'] = 1;
            }
        }
        
        //redirige vers l'éditeur si il y a des erreurs
        if (count($errors) != 0) {
            $errors['idForm'] = uniqid();
            CopixSession::set ('auth|createForm', $user, $errors['idForm']);
            return _arRedirect (_url ('auth|usersregister|edit', $errors));
        }
        
        //sauvegarde de l'utilisateur
        _ioDAO ('dbuser')->insert ($user);
        
        
        return _arRedirect ( _url (''));
    }
}
?>