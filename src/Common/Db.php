<?php

namespace Common;

use LessQL\Database;
use LessQL\Result;
use LessQL\Row;

/**
 * @method Result|Row|null intervals()
 */
class Db extends Database implements DbInterface
{
}