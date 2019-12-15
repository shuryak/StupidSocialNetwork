<?php

namespace application\models;
use application\core\Model;
use application\models\Images\Storage;

class Images {

    public static function upload($id, $file) {
        return Storage::upload($id, $file);
    }

}