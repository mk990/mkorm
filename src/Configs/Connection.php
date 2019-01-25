<?php
/**
 * Created by PhpStorm.
 * User: mk990
 * Date: 1/12/2019
 * Time: 9:30 PM
 */

namespace MkOrm\Configs;

use PDO;

class Connection
{
    public function connect($options = null)
    {
        $dbConnection = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE') . ';charset=' . getenv('DB_CHARSET'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            $options);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
    }
}
