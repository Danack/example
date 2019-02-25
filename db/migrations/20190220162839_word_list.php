<?php

use Phinx\Migration\AbstractMigration;

class WordList extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('word_list');
        $table
            ->addColumn('word_forward', 'string')
            ->addColumn('word_reversed', 'string')
            ->addIndex(['word_forward'], ['name' => 'index_word_forward',  ])
            ->addIndex(['word_reversed'], ['name' => 'index_word_reversed',  ])
            ->create();
    }
}
