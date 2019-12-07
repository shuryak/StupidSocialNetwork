<?php

namespace application\models;
use application\core\Model;
use application\models\Followers\Storage;

class Followers {

    public const NOT_FOLLOWED = Storage::NOT_FOLLOWED;
    public const IS_FOLLOWED = Storage::IS_FOLLOWED;
    public const IS_FRIENDS = Storage::IS_FRIENDS;
    public const IS_REVERSE = Storage::IS_REVERSE;

    public static function isFollowed($follower_id, $following_id) {
        return Storage::isFollowed($follower_id, $following_id);
    }

    public static function follow($follower_id, $following_id) {
        return Storage::follow($follower_id, $following_id);
    }

    public static function unfollow($follower_id, $following_id) {
        return Storage::unfollow($follower_id, $following_id);
    }

    public static function getUserFollowers($follower_id, $offset, $start) {
        return Storage::getUserFollowers($follower_id, $offset, $start);
    }

    public static function getFollowersCount($follower_id) {
        return Storage::getFollowersCount($follower_id);
    }

    public static function getUserFriends($id, $offset, $start) {
        return Storage::getUserFriends($id, $offset, $start);
    }

    public static function getFriendsCount($id) {
        return Storage::getFriendsCount($id);
    }

    public static function getUserFollowing($id, $offset, $start) {
        return Storage::getUserFollowing($id, $offset, $start);
    }

    public static function getFollowingCount($id) {
        return Storage::getFollowingCount($id);
    }

}