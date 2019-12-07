<?php

return [

    'users.register' => [
        'controller' => 'users',
        'method' => 'register',
    ],

    'users.login' => [
        'controller' => 'users',
        'method' => 'login',
    ],

    'users.getUser' => [
        'controller' => 'users',
        'method' => 'getUser',
    ],

    'users.getNewTokenPair' => [
        'controller' => 'users',
        'method' => 'getNewTokenPair',
    ],

    'posts.post' => [
        'controller' => 'posts',
        'method' => 'post',
    ],

    'posts.getLastUserPosts' => [
        'controller' => 'posts',
        'method' => 'getLastUserPosts',
    ],
    
    'posts.getPostById' => [
        'controller' => 'posts',
        'method' => 'getPostById',
    ],

    'posts.deletePost' => [
        'controller' => 'posts',
        'method' => 'deletePost',
    ],

    'posts.getUserPostsCount' => [
        'controller' => 'posts',
        'method' => 'getUserPostsCount',
    ],

    'followers.isFollowed' => [
        'controller' => 'followers',
        'method' => 'isFollowed',
    ],

    'followers.follow' => [
        'controller' => 'followers',
        'method' => 'follow',
    ],

    'followers.unfollow' => [
        'controller' => 'followers',
        'method' => 'unfollow',
    ],

    'followers.getUserFollowers' => [
        'controller' => 'followers',
        'method' => 'getUserFollowers',
    ],

    'followers.getUserFriends' => [
        'controller' => 'followers',
        'method' => 'getUserFriends',
    ],

    'followers.getUserFollowing' => [
        'controller' => 'followers',
        'method' => 'getUserFollowing',
    ],

];