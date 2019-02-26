<?php

namespace Hotel;

use Common\DbInterface;
use Exception;
use LessQL\Row;

class IntervalService
{
    /** @var DbInterface */
    private $db;

    public function __construct(DbInterface $db)
    {
        $this->db = $db;
    }

    public function create(Row $interval): bool
    {
        try {
            $this->db->begin();
            $interval->save($interval);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }

        return true;
    }

    public function update(Row $interval): bool
    {
        try {
            $this->db->begin();
            $interval->save($interval);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }

        return true;
    }
}