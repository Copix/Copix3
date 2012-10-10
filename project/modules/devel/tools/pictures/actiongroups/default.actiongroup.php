<?php

class ActionGroupDefault extends CopixActionGroup {
    public function processGetImage () {
		return _arFile (_class('pictures|pictures')->getImage(_request('picture_id'), _request('width'), _request('height')));
    }
}
?>