<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class DatabaseInitialisation extends AbstractMigration
{
    public function change(): void
    {
        $users = $this->table('users');
        $notes = $this->table('notes');
        $users
            ->addColumn('first_name', 'string', ['length' => 40])
            ->addColumn('last_name', 'string', ['length' => 40])
            ->addColumn('email_address', 'string', ['length' => 80])
            ->addIndex(['email_address'])
            ->save();

        $notes
            ->addColumn('note', 'text')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE'])
            ->save();
    }

    public function down(): void
    {
        if ($this->hasTable('users')) {
            $this->table('users')->drop();
        }
        if ($this->hasTable('notes')) {
            $this->table('notes')->drop();
        }
    }
}
