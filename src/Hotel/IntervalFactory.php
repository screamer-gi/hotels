<?php

namespace Hotel;

use Common\DbInterface;
use LessQL\Row;

class IntervalFactory
{
    /** @var DbInterface */
    private $db;

    public function __construct(DbInterface $db)
    {
        $this->db = $db;
    }

    public function __invoke(): Row
    {
        return $this->db->createRow('intervals');
    }

    public function create(): Row
    {
        return $this();
    }
}