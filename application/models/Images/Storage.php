<?php

namespace application\models\Images;
use application\core\Model;
use application\lib\Db;
use application\core\ErrorCodes;
use application\lib\FileName;
use Intervention\Image\ImageManagerStatic as Image;

class Storage extends Model {

    private const UPLOAD_PATH = 'uploads/';

    public static function upload($id, $file) {
        Image::configure(array('driver' => 'imagick'));

        $image = Image::make($file)->fit(300, 300);

        $filename = FileName::random($id, self::UPLOAD_PATH, 'jpg');
        $image->save(self::UPLOAD_PATH.$filename.'.jpg');

        if(file_exists(self::UPLOAD_PATH.$filename.'.jpg')) {
            return [
                'response' => $_SERVER['SERVER_NAME'].'/'.self::UPLOAD_PATH.$filename.'.jpg',
                'http' => 200,
            ];
        } else {
            return [
                'error_code' => ErrorCodes::UNKNOWN_INTERNAL_ERROR,
                'details' => $author,
                'http' => 400,
            ];
        }
    }

}