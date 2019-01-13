<?php
/**
 * Created by PhpStorm.
 * User: mk990
 * Date: 1/12/2019
 * Time: 9:30 PM
 */

namespace MkOrm\Models;


use MkOrm\Configs\Connection;
use MkOrm\Utils\Utils;

class Model
{
    private $db;

    public function __construct()
    {
        $this->db = (new Connection())->connect();
    }

    public function __destruct()
    {
        $this->db = null;
    }

    public function find($id = null)
    {
        $class = strrchr(get_called_class(), "\\");
        $class = str_replace('\\', "", $class);
        $tableName = Utils::deCamelize($class);
        $q = $this->db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($id)) {
            $sql = "SELECT * FROM `$tableName`";
            try {
                $stmt = $this->db->query($sql);
                $results = $stmt->fetchAll(\PDO::FETCH_OBJ);
                $data = [];

                foreach ($results as $result) {
                    $class = new $this();
                    foreach ($tableFields as $tableField) {
                        $setterName = 'set' . ucfirst(Utils::camelize($tableField));
                        $class->$setterName($result->$tableField);
                    }

                    $data[] = $class;
                }
                return $data;
            } catch (\PDOException $e) {
                error_log("\n\n >>>>> PDO ERROR >>>>> \n\n" . var_export($e->getMessage(), true) . "\n\n");
                return false;
            }
        }

        $sql = "SELECT * FROM `$tableName` where id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $id, \PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_OBJ);
            if (!$result) {
                return false;
            }
            foreach ($tableFields as $tableField) {
                $setterName = 'set' . ucfirst(Utils::camelize($tableField));
                $this->$setterName($result->$tableField);
            }

            return $this;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> PDO ERROR >>>>> \n\n" . var_export($e->getMessage(), true) . "\n\n");
            return false;
        }
    }

    public function save()
    {
        $class = strrchr(get_called_class(), "\\");
        $class = str_replace('\\', "", $class);
        $tableName = Utils::deCamelize($class);
        $q = $this->db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(\PDO::FETCH_COLUMN);

        $promoters = '';
        $values = '';
        $updateParameters = '';
        $bindings = [];
        foreach ($tableFields as $tableField) {
            $getterName = 'get' . ucfirst(Utils::camelize($tableField));
            $data = $this->$getterName();
            if (!empty($data)) {
                if ($tableField !== 'updated_at') {
                    $bindings[':' . $tableField] = $data;
                }

                $promoters .= "$tableField ,";
                $values .= ":$tableField ,";
                if ($tableField !== 'id' && $tableField !== 'updated_at') {
                    $updateParameters .= "$tableField = :$tableField ,";
                }
            }
        }
        $promoters = rtrim($promoters, ',');
        $values = rtrim($values, ',');

        //is new
        if (empty($this->getId())) {
            try {
                $sql = "INSERT INTO `$tableName` ( $promoters ) VALUES ( " . $values . " )";
                $stmt = $this->db->prepare($sql);
//                foreach ($bindings as $key => $value) {
//                    $stmt->bindParam($key, $value, \PDO::PARAM_STR);
//                }
                $result = $stmt->execute($bindings);
                if (!$result) {
                    return false;
                }
                return $result;
            } catch (\PDOException $e) {
                error_log("\n\n >>>>> PDO ERROR >>>>> \n\n" . var_export($e->getMessage(), true) . "\n\n");
                return false;
            }

            // update data
        } else {
            $updateParameters = rtrim($updateParameters, ',');

            $sql = "UPDATE `$tableName` SET $updateParameters WHERE id = :id";
            error_log($sql);
            error_log(json_encode($bindings));
            try {
                $stmt = $this->db->prepare($sql);
//                foreach ($bindings as $key => $value) {
//                    error_log($key .' '. $value);
//                    $stmt->bindParam($key, $value, \PDO::PARAM_STR);
//                }
                $result = $stmt->execute($bindings);
                if (!$result) {
                    return false;
                }
                return $result;
            } catch (\PDOException $e) {
                error_log("\n\n >>>>> PDO ERROR >>>>> \n\n" . var_export($e->getMessage(), true) . "\n\n");
                return false;
            }
        }
    }

    public function delete()
    {
        $class = strrchr(get_called_class(), "\\");
        $class = str_replace('\\', "", $class);
        $tableName = Utils::deCamelize($class);

        // if empty id
        $id = $this->getId();
        if (empty($id)) {
            return false;
        }
        $sql = "DELETE FROM `$tableName` WHERE id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(1, $id, \PDO::PARAM_INT);
            $result = $stmt->execute();
            if (!$result) {
                return false;
            }
            return true;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> PDO ERROR >>>>> \n\n" . var_export($e->getMessage(), true) . "\n\n");
            return false;
        }
    }

    public function toArray()
    {
        return $this->processArray(get_object_vars($this));
    }

    private function processArray($array)
    {
        foreach ($array as $key => $value) {
            if (is_object($value) && $value instanceof BaseModel) {
                $array[$key] = $value->toArray();
            }
            if (is_array($value)) {
                $array[$key] = $this->processArray($value);
            }
        }

        // If the property isn't an object or array, leave it untouched
        return $array;
    }

    public function __toString()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function toJSON()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function toJsonApi(array $blackList = [])
    {
        $class = strtolower(get_called_class());
        $class = strrchr($class, "\\");
        $class = str_replace('\\', "", $class);
        $class = rtrim($class, "s");

        /**
         * @var \DateTime $createdAt
         */
        $data = get_object_vars($this);
        $attributes = [];
        foreach ($data as $key => $value) {
            $blackList = array_merge(['id'], $blackList);
            if (!in_array($key, $blackList)) {

                if ($this->$key instanceof \DateTime)
                    $value = $this->$key->format('Y-m-d H:i:s');

                if (!empty($value))
                    $attributes[$this->fromCamelCase($key)] = $value;
            }
        }

        return [
            "data" => [
                "type"       => $class,
                "id"         => $this->getId(),
                "attributes" => $attributes,
                "links"      => [
                    "self" => env('APP_URL') . "/$class/" . $this->getId()
                ]
            ]
        ];
    }
}
