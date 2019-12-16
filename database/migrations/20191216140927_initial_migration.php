<?php

use Phinx\Migration\AbstractMigration;

class InitialMigration extends AbstractMigration
{
    public function change()
    {
        $usersTable = $this->table('users');
        $usersTable->addColumn('status', 'integer')
            ->addColumn('firstname', 'string')
            ->addColumn('lastname', 'string')
            ->addColumn('email', 'string')
            ->addColumn('avatar', 'string')
            ->addColumn('password', 'string')
            ->addColumn('refresh_token', 'string')
            ->create();

        $postsTable = $this->table('posts', ['id' => false, 'primary_key' => 'post_id']);
        $postsTable->addColumn('post_id', 'integer')
            ->addColumn('author', 'integer')
            ->addColumn('content', 'string')
            ->addColumn('attachments', 'string')
            ->addColumn('time', 'timestamp')
            ->create();

        $followersTable = $this->table('followers', ['id' => false]);
        $followersTable->addColumn('follower_id', 'integer')
            ->addColumn('following_id', 'integer')
            ->addColumn('is_reversed', 'integer')
            ->create();
    }
}
