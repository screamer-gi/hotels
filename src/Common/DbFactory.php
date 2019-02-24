<?php

namespace Common;

use PDO;

class DbFactory
{
    public function __invoke(): DbInterface
    {
        $pdo = new PDO('mysql:host=mysql;dbname=hotels', 'hotels', 'hotels', [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']);
        return new Db($pdo);
    }
}