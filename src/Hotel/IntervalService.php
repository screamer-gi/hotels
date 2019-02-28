<?php

namespace Hotel;

use Common\DbInterface;
use DateInterval;
use DateTimeImmutable;
use Exception;
use LessQL\Row;

class IntervalService
{
    /** @var DbInterface */
    private $db;

    /** @var DateInterval */
    private $dayInterval;

    public function __construct(DbInterface $db)
    {
        $this->db = $db;
        $this->dayInterval = new DateInterval('P1D');
    }

    public function create(Row $interval): bool
    {
        return $this->processIntersections($interval);
    }

    public function update(Row $interval): bool
    {
        return $this->processIntersections($interval);
    }

    protected function processIntersections(Row $interval): bool
    {
        try {
            $this->db->begin();

            $this->deleteInnerIntervals($interval);
            $this->fixIntersections($interval);
            $interval->save($interval);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();

            return false;
        }

        return true;
    }

    private function deleteInnerIntervals(Row $interval): void
    {
        $query = $this->db->intervals()->where('date_start >= :dateStart AND date_end <= :dateEnd', [
            'dateStart' => $interval->date_start,
            'dateEnd' => $interval->date_end,
        ]);

        if ($interval->getId()) {
            $query = $query->whereNot('id', $interval->getId());
        }

        $query->delete();
    }

    private function fixIntersections(Row $interval): void
    {
        $query = $this->db->intervals()->where(
            '(date_start < :dateStart AND date_end > :dateEnd) OR (date_start BETWEEN :dateStart AND :dateEnd) OR (date_end BETWEEN :dateStart AND :dateEnd)',
            ['dateStart' => $interval->date_start, 'dateEnd' => $interval->date_end]);

        if ($interval->getId()) {
            $query = $query->whereNot('id', $interval->getId());
        }

        $outerIntervals = $query->fetchAll();
        $interval = $this->convertDates($interval);

        foreach ($outerIntervals as $outerInterval) {
            if ($outerInterval->getId() == $interval->getId()) {
                continue;
            }

            $convertedInterval = $this->convertDates($outerInterval);
            if ($convertedInterval->date_start < $interval->date_start && $convertedInterval->date_end > $interval->date_end) {
                $closingInterval = $this->db->createRow('intervals', [
                    'date_start' => $interval->date_end->add($this->dayInterval)->format('Y-m-d'),
                    'date_end' => $outerInterval->date_end,
                    'price' => $outerInterval->price,
                ]);
                $outerInterval->date_end = $interval->date_start->sub($this->dayInterval)->format('Y-m-d');
                $outerInterval->save();
                $closingInterval->save();
                continue;
            }

            if ($convertedInterval->date_start < $interval->date_start) {
                $outerInterval->date_end = $interval->date_start->sub($this->dayInterval)->format('Y-m-d');
            } else {
                $outerInterval->date_start = $interval->date_end->add($this->dayInterval)->format('Y-m-d');
            }
            $outerInterval->save();
        }
    }

    private function convertDates(Row $interval): Row
    {
        $newInterval = clone $interval;
        $newInterval->date_start = DateTimeImmutable::createFromFormat('Y-m-d', $interval->date_start);
        $newInterval->date_end = DateTimeImmutable::createFromFormat('Y-m-d', $interval->date_end);

        return $newInterval;
    }
}