<?php
class ActionGroupMediaFront extends CopixActionGroup {
 
    /**
	 * Fonction qui ne sert que dans les écrans d'administration, pour les personnes ayant les droits d'écriture sur le média
	 * (en gros utile pour prévisualiser les médias en cours de création, ou au statut brouillon)
	 */
	public function processGetMedia () {

		//on regarde si on dispose bien de notre id
		CopixRequest::assert ('id_media');
		//récupération de l'élément (pour vérifier les droits)
		$headingElementInformationServices = new HeadingElementInformationServices ();
		$element = _ioClass('medias|mediasservices')->getById (_request ('id_media'), 'media');

        if (! HeadingElementCredentials::canWrite ($element->public_id_hei)) {
			return new CopixActionReturn (CopixActionreturn::HTTPCODE, CopixHTTPHeader::get403 ());
		}
		return $this->_getMedia ();
	}

    /**
     * URL front pour la récupération des médias (appelé par heading||)
     * @return <type>
     */
	public function processDefault () {
        // on vérifie que c'est heading|| qui a lancé l'ordre d'affichage des éléments demandés.
		$front = CopixRegistry::instance ()->get ('headingfront');

		if ($front !== _request('public_id')){
			throw new CopixCredentialException ('basic:admin');
		}
        
        //On défini dans la requête le id_media pour pouvoir appeler par la suite _getMedia
    	CopixRequest::assert ('public_id');
        $editedElement = _ioClass('medias|mediasservices')->getByPublicId (_request('public_id'));

        CopixRequest::set ('id_media', $editedElement->id_media);
        //retour du média
        return $this->_getMedia();
	}
    
	/**
	 * Fonction privée qui récupère "en vrai" le média voulu (donné par son id_media)
	 * Les droits sont gérés ailleurs, dans les méthodes "front"
	 */
	private function _getMedia () {
        $content_disposition = _request ('content_disposition' , 'inline');        
        $element  = null;
		$media_id = _request("id_media");
		$element  = _class ('media|mediasservices')->getById ($media_id);
        // C'est l'image alternative que l'on veut et non le média
        if(_request ('image_alternative')) {
            if(!empty($element->image_media)) {
                return _arFile (COPIX_VAR_PATH.MediasServices::MEDIA_PATH.DIRECTORY_SEPARATOR.$element->image_media, array ('content-disposition' => $content_disposition));
            }
            // pas d'image alternative
            else {
                return _arNone();
            }
        }
        else {
            return _arFile (COPIX_VAR_PATH.MediasServices::MEDIA_PATH.DIRECTORY_SEPARATOR.$element->file_media, array ('content-disposition' => $content_disposition));
        }
    }



    /**
	 * Fonction qui ne sert que dans les écrans d'administration, pour les personnes ayant les droits d'écriture sur le média
	 * (utile pour prévisualiser les images alternatives des médias en cours de création, ou au statut brouillon)
	 */
	public function processGetImage (){
		//on regarde si on dispose bien de notre id
		if (!CopixRequest::exists('id_media')) {
			if (CopixConfig::instance ()->getMode () == CopixConfig::PRODUCTION) {
				return new CopixActionReturn (CopixActionreturn::HTTPCODE, CopixHTTPHeader::get404 ());
			}
            else {
				CopixRequest::assert('id_media');
			}
		}

		//récupération de l'élément (pour vérifier les droits)
		$element = _class ('medias|mediasservices')->getById(_request ('id_media'));
		if (! HeadingElementCredentials::canWrite ($element->public_id_hei)){
			return new CopixActionReturn (CopixActionreturn::HTTPCODE, CopixHTTPHeader::get403 ());
		}

        CopixRequest::set ('id_media', $element->id_media);
        CopixRequest::set ('image_alternative', 1);
		return $this->_getMedia ($element);
	}    
}