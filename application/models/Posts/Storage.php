<?php

namespace application\models\Posts;
use application\core\Model;
use application\lib\Db;
use application\core\ErrorCodes;

class Storage extends Model {

    const MAX_POSTS_COUNT = 10;

    public static function makePost($author, $content, $attachments) {
        if(\application\models\Users::idExists($author)['response'] == false) {
            return [
                'error_code' => ErrorCodes::EMAIL_IS_NOT_REGISTERED,
                'details' => $author,
                'http' => 400,
            ];
        }

        $sql = 'INSERT INTO posts (author, content, attachments, time) VALUES (:author, :content, :attachments, :time)';
        $replacement = [':author' => $author, ':content' => $content, ':attachments' => $attachments, ':time' => time()];

        $result = Db::queryExecuteResult($sql, $replacement);

        if($result) {
            return [
                'response' => true,
                'http' => 200,
            ];
        }

        return [
            'error_code' => ErrorCodes::UNKNOWN_INTERNAL_ERROR,
            'details' => false,
            'http' => 500,
        ];
    }

    public static function getLastUserPosts($author, $offset, $start) {
        if(\application\models\Users::idExists($author)['response'] == false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $author,
                'http' => 400,
            ];
        }

        $offset = $offset > self::MAX_POSTS_COUNT ? self::MAX_POSTS_COUNT : $offset;

        $sql = 'SELECT * FROM posts WHERE author = :author ORDER BY post_id DESC LIMIT :start, :offset';
        $replacement = [':author' => $author, ':offset' => $offset, ':start' => $start];

        $result =  Db::queryAssoc($sql, $replacement);

        return [
            'response' => $result,
            'http' => 200,
        ];
    }

    public static function getUserPost($id) {
        $sql = 'SELECT * FROM posts WHERE post_id = :post_id';
        $replacement = [':post_id' => $id];

        $result = Db::querySingleAssoc($sql, $replacement);

        if($result) {
            return [
                'response' => $result,
                'http' => 200,
            ];
        }

        return [
            'error_code' => ErrorCodes::POST_DOES_NOT_EXIST,
            'details' => $id,
            'http' => 400,
        ];
    }

    public static function deletePostById($id) {
        $sql = 'UPDATE posts SET author = :author WHERE post_id = :post_id';
        $replacement = [':post_id' => $id, ':author' => 0];

        $result = Db::queryExecuteResult($sql, $replacement);

        if($result) {
            return [
                'response' => true,
                'http' => 200,
            ];
        }

        return [
            'error_code' => ErrorCodes::POST_DOES_NOT_EXIST,
            'details' => $id,
            'http' => 400,
        ];
    }

    public static function getPostsCount($id) {
        $sql = 'SELECT COUNT(*) FROM posts WHERE author = :author';
        $replacement = [':author' => $id];

        $result = Db::queryColumn($sql, $replacement);

        if($result) {
            return [
                'response' => $result,
                'http' => 200,
            ];
        } else {
            return [
                'error_code' => ErrorCodes::POST_DOES_NOT_EXIST,
                'details' => $id,
                'http' => 400,
            ];
        }
    }

}