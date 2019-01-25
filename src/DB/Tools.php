<?php
/**
 * Created by PhpStorm.
 * User: mk990
 * Date: 1/25/2019
 * Time: 9:45 PM
 */

namespace MkOrm\DB;


use MkOrm\Configs\Connection;
use PDO;

class Tools
{
    public static function query(string $query, array $data = [])
    {
        $db = (new Connection())->connect();
        $stmt = $db->prepare($query);
        $stmt->execute($data);
        $number_of_rows = $stmt->rowCount();
        if ($number_of_rows > 1) {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        }
        return $result;
    }
}
