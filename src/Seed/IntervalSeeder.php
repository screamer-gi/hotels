<?php

namespace Seed;

use Phinx\Seed\AbstractSeed;

class IntervalSeeder extends AbstractSeed
{
    /**
     * Fill table with test data
     */
    public function run()
    {
        $intervals = [
            ['date_start' => '2019-02-01','date_end' => '2019-02-10','price' => '205.5'],
            ['date_start' => '2019-02-11','date_end' => '2019-02-13','price' => '250'],
            ['date_start' => '2019-02-15','date_end' => '2019-02-16','price' => '100'],
            ['date_start' => '2019-02-17','date_end' => '2019-02-20','price' => '111.11'],
            ['date_start' => '2019-02-24','date_end' => '2019-02-26','price' => '120'],
            ['date_start' => '2019-02-27','date_end' => '2019-02-27','price' => '110']
        ];

        $this->table('intervals')
            ->insert($intervals)
            ->save();
    }
}
