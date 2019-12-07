<?php

namespace application\models\Followers;
use application\core\Model;
use application\lib\Db;
use application\core\ErrorCodes;

class Storage extends Model {

    public const NOT_FOLLOWED = 0;
    public const IS_FOLLOWED = 1;
    public const IS_FRIENDS = 2;
    public const IS_REVERSE = 3;

    const MAX_FOLLOWERS_COUNT = 10;
    const MAX_FRIENDS_COUNT = 10;
    const MAX_FOLLOWING_COUNT = 10;

    private const USER_IS_FOLLOWED_IRREVERSIBLY = 0;
    private const USER_IS_FOLLOWED_REVERSIBLY = 1;
    private const USER_IS_NOT_FOLLOWED = 2;
    private const USER_IS_REVERSE_FOLLOWED_REVERSIBLY = 3;
    private const USER_IS_REVERSE_FOLLOWED_IRREVERSIBLY = 4;

    private static function isFollowed_internal($follower_id, $following_id) {
        $sql = 'SELECT is_reversed FROM followers WHERE follower_id = :follower_id AND following_id = :following_id';
        $replacement = [':follower_id' => $follower_id, ':following_id' => $following_id];

        $result = Db::queryColumn($sql, $replacement);

        if(is_numeric($result) && (int)$result === 1) {
            return [
                'response' => self::USER_IS_FOLLOWED_REVERSIBLY,
                'http' => 200,
            ];
        } elseif(is_numeric($result) && (int)$result === 0) {
            return [
                'response' => self::USER_IS_FOLLOWED_IRREVERSIBLY,
                'http' => 200,
            ];
        } else {
            $sql2 = 'SELECT is_reversed FROM followers WHERE follower_id = :follower_id AND following_id = :following_id';
            $replacement2 = [':follower_id' => $following_id, ':following_id' => $follower_id];

            $result2 = Db::queryColumn($sql2, $replacement2);

            if(is_numeric($result2) && (int)$result2 === 1) {
                $response = self::USER_IS_REVERSE_FOLLOWED_REVERSIBLY;
            } elseif(is_numeric($result2) && (int)$result2 === 0) {
                $response = self::USER_IS_REVERSE_FOLLOWED_IRREVERSIBLY;
            } else {
                $response = self::USER_IS_NOT_FOLLOWED;
            }

            return [
                'response' => $response,
                'http' => 200,
            ];
        }
    }

    public static function isFollowed($follower_id, $following_id) {
        if($follower_id == $following_id) {
            return [
                'error_code' => ErrorCodes::IDENTICAL_VALUES,
                'details' => $follower_id,
                'http' => 400,
            ];
        }

        $isFollowed = self::isFollowed_internal($follower_id, $following_id);

        if($isFollowed['response'] === self::USER_IS_FOLLOWED_IRREVERSIBLY) {
            return [
                'response' => self::IS_FOLLOWED,
                'http' => 200,
            ];
        } elseif($isFollowed['response'] === self::USER_IS_FOLLOWED_REVERSIBLY || $isFollowed['response'] === self::USER_IS_REVERSE_FOLLOWED_REVERSIBLY ) {
            return [
                'response' => self::IS_FRIENDS,
                'http' => 200,
            ];
        } elseif($isFollowed['response'] === self::USER_IS_REVERSE_FOLLOWED_IRREVERSIBLY) {
            return [
                'response' => self::IS_REVERSE,
                'http' => 200,
            ];
        } else {
            return [
                'response' => self::NOT_FOLLOWED,
                'http' => 200,
            ];
        }
    }

    public static function follow($follower_id, $following_id) {
        if($follower_id == $following_id) {
            return [
                'error_code' => ErrorCodes::IDENTICAL_VALUES,
                'details' => $follower_id,
                'http' => 400,
            ];
        }

        if(\application\models\Users::idExists($follower_id)['response'] === false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $follower_id,
                'http' => 400,
            ];
        }

        if(\application\models\Users::idExists($following_id)['response'] === false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $following_id,
                'http' => 400,
            ];
        }

        $isFollowed = self::isFollowed_internal($follower_id, $following_id);

        // var_dump($isFollowed);

        if($isFollowed['response'] === self::USER_IS_REVERSE_FOLLOWED_IRREVERSIBLY) {
            $sql = 'UPDATE followers SET is_reversed = :is_reversed WHERE follower_id = :follower_id AND following_id = :following_id';
            $replacement = [':is_reversed' => 1, ':follower_id' => $following_id, ':following_id' => $follower_id];

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
        } elseif($isFollowed['response'] === self::USER_IS_REVERSE_FOLLOWED_REVERSIBLY) {
            return [
                'error_code' => ErrorCodes::USERS_ARE_ALREADY_FRIENDS,
                'details' => [$follower_id, $following_id],
                'http' => 400,
            ];
        } elseif($isFollowed['response'] === self::USER_IS_FOLLOWED_REVERSIBLY) {
            return [
                'error_code' => ErrorCodes::USERS_ARE_ALREADY_FRIENDS,
                'details' => [$follower_id, $following_id],
                'http' => 400,
            ];
        } elseif($isFollowed['response'] === self::USER_IS_FOLLOWED_IRREVERSIBLY) {
            return [
                'error_code' => ErrorCodes::USER_IS_ALREADY_FOLLOWED,
                'details' => [$follower_id, $following_id],
                'http' => 400,
            ];
        } elseif($isFollowed['response'] === self::USER_IS_NOT_FOLLOWED) {
            $sql = 'INSERT INTO followers (follower_id, following_id, is_reversed) VALUES (:follower_id, :following_id, :is_reversed)';
            $replacement = [':follower_id' => $follower_id, ':following_id' => $following_id, ':is_reversed' => 0];

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
    }

    public static function unfollow($follower_id, $following_id) {
        if($follower_id == $following_id) {
            return [
                'error_code' => ErrorCodes::IDENTICAL_VALUES,
                'details' => $follower_id,
                'http' => 400,
            ];
        }

        if(\application\models\Users::idExists($follower_id)['response'] === false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $follower_id,
                'http' => 400,
            ];
        }

        if(\application\models\Users::idExists($following_id)['response'] === false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $following_id,
                'http' => 400,
            ];
        }

        $isFollowed = self::isFollowed_internal($follower_id, $following_id);

        if($isFollowed['response'] === self::USER_IS_FOLLOWED_IRREVERSIBLY) {
            $sql = 'DELETE FROM followers WHERE follower_id = :follower_id AND following_id = :following_id';
            $replacement = [':follower_id' => $follower_id, ':following_id' => $following_id];

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
        } elseif($isFollowed['response'] === self::USER_IS_FOLLOWED_REVERSIBLY) {
            $sql = 'UPDATE followers SET follower_id = :new_follower_id, following_id = :new_following_id, is_reversed = :new_is_reversed WHERE follower_id = :follower_id AND following_id = :following_id';
            $replacement = [':new_follower_id' => $following_id, ':new_following_id' => $follower_id, ':new_is_reversed' => 0, ':follower_id' => $follower_id, ':following_id' => $following_id];

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
        } elseif($isFollowed['response'] === self::USER_IS_REVERSE_FOLLOWED_REVERSIBLY) {
            $sql = 'UPDATE followers SET is_reversed = :new_is_reversed WHERE follower_id = :follower_id AND following_id = :following_id';
            $replacement = [':new_is_reversed' => 0, ':follower_id' => $following_id, ':following_id' => $follower_id];

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

        return [
            'error_code' => ErrorCodes::USER_IS_NOT_FOLLOWED,
            'details' => [$follower_id, $following_id],
            'http' => 500,
        ];
    }

    public static function getUserFollowers($id, $offset, $start) {
        if(\application\models\Users::idExists($id)['response'] == false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $id,
                'http' => 400,
            ];
        }

        $offset = $offset > self::MAX_FOLLOWERS_COUNT ? self::MAX_FOLLOWERS_COUNT : $offset;

        $sql = 'SELECT id, firstname, lastname, email FROM followers INNER JOIN users ON users.id = followers.follower_id WHERE following_id = :follower_id AND is_reversed != 1 LIMIT :start, :offset';
        $replacement = [':follower_id' => $id, ':start' => $start, ':offset' => $offset];

        $result = Db::queryAssoc($sql, $replacement);
        
        return [
            'response' => $result,
            'http' => 200,
        ];
    }

    public static function getFollowersCount($id) {
        if(\application\models\Users::idExists($id)['response'] == false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $id,
                'http' => 400,
            ];
        }

        $sql = 'SELECT COUNT(*) FROM followers INNER JOIN users ON users.id = followers.follower_id WHERE following_id = :follower_id AND is_reversed != 1';
        $replacement = [':follower_id' => $id];

        $result = Db::queryColumn($sql, $replacement);

        if($result) {
            return [
                'response' => $result,
                'http' => 200,
            ];
        } else {
            return [
                'error_code' => ErrorCodes::FOLLOWERS_DOES_NOT_EXISTS,
                'details' => $id,
                'http' => 400,
            ];
        }
    }

    public static function getUserFriends($id, $offset, $start) {
        if(\application\models\Users::idExists($id)['response'] == false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $id,
                'http' => 400,
            ];
        }

        $offset = $offset > self::MAX_FRIENDS_COUNT ? self::MAX_FRIENDS_COUNT : $offset;

        $sql = 'SELECT id, firstname, lastname, email FROM followers
        INNER JOIN users ON (users.id = followers.following_id AND followers.following_id != :id) OR (users.id = followers.follower_id AND followers.follower_id != 1)
        WHERE (following_id = :id OR follower_id = :id) AND is_reversed = 1
        LIMIT :start, :offset';
        $replacement = [':id' => $id, ':start' => $start, ':offset' => $offset];

        $result = Db::queryAssoc($sql, $replacement);

        return [
            'response' => $result,
            'http' => 200,
        ];
    }

    public static function getFriendsCount($id) {
        if(\application\models\Users::idExists($id)['response'] == false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $id,
                'http' => 400,
            ];
        }

        $sql = 'SELECT COUNT(*) FROM followers
        INNER JOIN users ON (users.id = followers.following_id AND followers.following_id != :id) OR (users.id = followers.follower_id AND followers.follower_id != 1)
        WHERE (following_id = :id OR follower_id = :id) AND is_reversed = 1';
        $replacement = [':id' => $id];

        $result = Db::queryColumn($sql, $replacement);

        if($result) {
            return [
                'response' => $result,
                'http' => 200,
            ];
        } else {
            return [
                'error_code' => ErrorCodes::FRIENDS_DOES_NOT_EXISTS,
                'details' => $id,
                'http' => 400,
            ];
        }
    }

    public static function getUserFollowing($id, $offset, $start) {
        if(\application\models\Users::idExists($id)['response'] == false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $id,
                'http' => 400,
            ];
        }

        $offset = $offset > self::MAX_FOLLOWING_COUNT ? self::MAX_FOLLOWING_COUNT : $offset;

        $sql = 'SELECT id, firstname, lastname, email FROM followers
        INNER JOIN users ON users.id = followers.following_id
        WHERE follower_id = :id AND is_reversed != 1
        LIMIT :start, :offset';
        $replacement = [':id' => $id, ':start' => $start, ':offset' => $offset];

        $result = Db::queryAssoc($sql, $replacement);

        return [
            'response' => $result,
            'http' => 200,
        ];
    }

    public static function getFollowingCount($id) {
        if(\application\models\Users::idExists($id)['response'] == false) {
            return [
                'error_code' => ErrorCodes::ID_IS_NOT_REGISTERED,
                'details' => $id,
                'http' => 400,
            ];
        }

        $sql = 'SELECT COUNT(*) FROM followers
        INNER JOIN users ON users.id = followers.following_id
        WHERE follower_id = :id AND is_reversed != 1';
        $replacement = [':id' => $id];

        $result = Db::queryColumn($sql, $replacement);

        if($result) {
            return [
                'response' => $result,
                'http' => 200,
            ];
        } else {
            return [
                'error_code' => ErrorCodes::FOLLOWING_DOES_NOT_EXISTS,
                'details' => $id,
                'http' => 400,
            ];
        }
    }

}