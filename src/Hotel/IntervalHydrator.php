<?php

namespace Hotel;

use LessQL\Row;

class IntervalHydrator
{
    public function hydrate(Row $interval, array $data): Row
    {
        $interval->date_start = $data['date_start'];
        $interval->date_end = $data['date_end'];
        $interval->price = $data['price'];

        return $interval;
    }
}