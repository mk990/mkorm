<?php
/**
 * Created by PhpStorm.
 * User: mk990
 * Date: 1/12/2019
 * Time: 9:30 PM
 */

namespace MkOrm\Configs;

use PDO;

class DBConnect extends PDO
{
    public function __construct($options = null)
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db = getenv('DB_DATABASE');
        $charset = getenv('DB_CHARSET');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        parent::__construct("mysql:host=$host;port=$port;dbname=$db;charset=$charset;",
            $username,
            $password,
            $options);
        parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
