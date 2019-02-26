<?php

namespace Common;

use LessQL\Database;
use LessQL\Result;
use LessQL\Row;

/**
 * @method Row createRow(string $name, array $properties = [], $result = null)
 * @method bool begin()
 * @method bool commit()
 * @method bool rollback()
 */
class Db extends Database implements DbInterface
{
    /**
     * @param int|null $id
     *
     * @return Result|Row|null
     */
    public function intervals(int $id = null)
    {
        return parent::table('intervals', $id);
    }
}