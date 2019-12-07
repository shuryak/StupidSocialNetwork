<?php

namespace application\models;
use application\core\Model;
use application\models\Posts\Storage;

class Posts {

    public static function makePost($author, $content, $attachments) {
        return Storage::makePost($author, $content, $attachments);
    }

    public static function getLastUserPosts($author, $offset, $start) {
        return Storage::getLastUserPosts($author, $offset, $start);
    }

    public static function getUserPost($id) {
        return Storage::getUserPost($id);
    }

    public static function deletePostById($id) {
        return Storage::deletePostById($id);
    }

    public static function getPostsCount($id) {
        return Storage::getPostsCount($id);
    }

}