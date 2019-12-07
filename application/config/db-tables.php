<?php

return [
    'CREATE TABLE IF NOT EXISTS users (`id` INTEGER PRIMARY KEY, `status` INTEGER, `firstname` VARCHAR(256), `lastname` VARCHAR(256), `email` VARCHAR(256), `password` VARCHAR(2048), `refresh_token` VARCHAR(2048))',
    'CREATE TABLE IF NOT EXISTS posts (`post_id` INTEGER PRIMARY KEY, `author` INTEGER, `content` TEXT, `attachments` TEXT, `time` INTEGER)',
    'CREATE TABLE IF NOT EXISTS followers (`follower_id` INTEGER, `following_id` INTEGER, `is_reversed` INTEGER)',
];