<?php

namespace Common;

use LessQL\Result;
use LessQL\Row;

Interface DbInterface
{
    /**
     * Returns a result for intervals table.
     * If $id is given, return the row with that id.
     *
     * @param int|null $id
     *
     * @return Result|Row|null
     */
    public function intervals(int $id = null);

    /**
     * Create a row from given properties.
     * Optionally bind it to the given result.
     *
     * @param string $name
     * @param array $properties
     * @param Result|null $result
     *
     * @return Row
     */
    public function createRow($name, $properties = [], $result = null);

    /**
     * Begin a transaction
     *
     * @return bool
     */
    function begin();

    /**
     * Commit changes of transaction
     *
     * @return bool
     */
    function commit();

    /**
     * Rollback any changes during transaction
     *
     * @return bool
     */
    function rollback();
}