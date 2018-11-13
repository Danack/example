<?php

use Phinx\Migration\AbstractMigration;

class Books extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('book');
        $table
            ->addColumn('name', 'string')
            ->addColumn('author', 'string')
            ->addColumn('link', 'string')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
