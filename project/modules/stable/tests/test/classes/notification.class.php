<?php
class Notification {
	
	public static function notifyError ($parameters, $errors) {

		$definition = $parameters->id_test.'::'.$parameters->caption_test;
		$errorString = '';
		foreach ($errors as $value) {
			$errorString = $errorString.$value.'<br>';
		}

		self::notificationTreatement($errorString, $parameters);
	}
	
	public static function notificationTreatement ($errorString, $parameters) {
		$level_test = _dao('testlevel')->get ($parameters->level_test);
		$category_test =  _dao('testcategory')->get ($parameters->category_test);
		$mail = new CopixHTMLEmail ($level_test->email,
									 null,
									 null,
		 							 utf8_decode (
		 							 'Notification d\'erreur(s) sur '.$parameters->caption_test),
		 							 utf8_decode(
									 'Identifiant : '.$parameters->id_test.
									 '<br> Libellé : '.$parameters->caption_test.
									 '<br> Type de test : '.$parameters->type_test.
									 '<br> Catégorie :'.$category_test->caption_ctest.
									 '<br> Criticité :'.$level_test->caption_level.
									 '<br><br>Erreurs : <br>'.$errorString));
		$mail->send();
	}
}