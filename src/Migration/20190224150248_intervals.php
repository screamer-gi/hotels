<?php

namespace Migration;

use Phinx\Migration\AbstractMigration;

class Intervals extends AbstractMigration
{
    /**
     * Change Method.
     */
    public function change()
    {
        $this->table('intervals')
            ->addColumn('date_start', 'date')
            ->addColumn('date_end', 'date')
            ->addColumn('price', 'float')
            ->addIndex(['date_start'], ['unique' => true])
            ->addIndex(['date_end'], ['unique' => true])
            ->create();
    }
}
