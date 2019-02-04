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
use PDO;

class Model
{
    public function __toString()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    public function all($orderBy = 'ASC')
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);

        $sql = "SELECT * FROM `$tableName` ORDER BY id $orderBy";
        try {
            $stmt = $db->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
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
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, []) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }
    }

    public function find($input = null)
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);

        if (is_array($input)) {
            $input = array_unique($input);
            sort($input);

            $str = "";
            $dataStr = "";
            foreach ($input as $data) {
                $str .= "?,";
                $dataStr .= "$data,";
            }
            $str = rtrim($str, ',');
            $dataStr = rtrim($dataStr, ',');
            $sql = "SELECT * FROM `$tableName` where id IN ($str)";
            try {
                $stmt = $db->prepare($sql);
                $stmt->execute($input);
                $results = $stmt->fetchAll(PDO::FETCH_OBJ);
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
                error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, [$str => $dataStr]) . "\n <<<<< THE END \n\n");
                error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
                return false;
            }
        }

        if (empty($input)) {
            $this->all();
        }

        $sql = "SELECT * FROM `$tableName` where id = ?";

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $input, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            if (!$result) {
                return false;
            }
            foreach ($tableFields as $tableField) {
                $setterName = 'set' . ucfirst(Utils::camelize($tableField));
                $this->$setterName($result->$tableField);
            }

            return $this;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, ['?' => $input]) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }
    }

    public function findBy(array $input = [])
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);


        $str = '';
        if (!empty($input)) {

            $str = ' WHERE ';
            foreach ($input as $key => $value) {

                $str .= " $key = :$key AND ";

                $input[':' . $key] = $value;
                unset($input[$key]);
            }
            $str = rtrim($str, 'AND ');

        }
        $sql = "SELECT * FROM `$tableName`" . $str;
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);

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
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $input) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }

    }

    public function findLike(array $input = [])
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);


        $str = '';
        if (!empty($input)) {

            $str = ' WHERE ';
            foreach ($input as $key => $value) {

                $str .= " $key like :$key AND ";

                $input[':' . $key] = "%$value%";

                unset($input[$key]);
            }
            $str = rtrim($str, 'AND ');

        }
        $sql = "SELECT * FROM `$tableName`" . $str;
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);

            $data = [];
            foreach ($results as $result) {
                $class = new $this();
                foreach ($tableFields as $tableField) {
                    $setterName = 'set' . ucfirst(Utils::camelize($tableField));
                    $class->$setterName($result->$tableField);
                }
                $data[] = $class;
            }
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $input) . "\n <<<<< THE END \n\n");
            return $data;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $input) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }

    }

    public function findOneBy(array $input = [])
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);

        $str = '';
        if (!empty($input)) {

            $str = ' WHERE ';
            foreach ($input as $key => $value) {

                $str .= " $key = :$key AND ";

                $input[':' . $key] = $value;
                unset($input[$key]);
            }
            $str = rtrim($str, 'AND ');

        }
        $sql = "SELECT * FROM `$tableName` $str LIMIT 1";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$result) {
                return false;
            }
            foreach ($tableFields as $tableField) {
                $setterName = 'set' . ucfirst(Utils::camelize($tableField));
                $this->$setterName($result->$tableField);
            }

            return $this;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $input) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }

    }

    public function findOneLike(array $input = [])
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);

        $str = '';
        if (!empty($input)) {

            $str = ' WHERE ';
            foreach ($input as $key => $value) {

                $str .= " $key LIKE :$key AND ";

                $input[':' . $key] = "%$value%";
                unset($input[$key]);
            }
            $str = rtrim($str, 'AND ');

        }
        $sql = "SELECT * FROM `$tableName` $str LIMIT 1";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$result) {
                return false;
            }
            foreach ($tableFields as $tableField) {
                $setterName = 'set' . ucfirst(Utils::camelize($tableField));
                $this->$setterName($result->$tableField);
            }
            return $this;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $input) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }

    }

    public function count(array $input = [])
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $str = '';
        if (!empty($input)) {

            $str = ' WHERE ';
            foreach ($input as $key => $value) {

                $str .= " $key = :$key AND ";

                $input[':' . $key] = $value;
                unset($input[$key]);
            }
            $str = rtrim($str, 'AND ');

        }

        $sql = "SELECT COUNT(*) AS cnt FROM `$tableName`" . $str;
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->cnt;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $input) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }
    }

    public function sum($column, array $input = [])
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $str = '';
        if (!empty($input)) {

            $str = ' WHERE ';
            foreach ($input as $key => $value) {

                $str .= " $key = :$key AND ";

                $input[':' . $key] = $value;
                unset($input[$key]);
            }
            $str = rtrim($str, 'AND ');

        }

        $sql = "SELECT SUM($column) AS sum_data FROM `$tableName`" . $str;
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->sum_data;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $input) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }
    }

    public function avg($column, array $input = [])
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $str = '';
        if (!empty($input)) {

            $str = ' WHERE ';
            foreach ($input as $key => $value) {

                $str .= " $key = :$key AND ";

                $input[':' . $key] = $value;
                unset($input[$key]);
            }
            $str = rtrim($str, 'AND ');

        }

        $sql = "SELECT AVG($column) AS avg_data FROM `$tableName`" . $str;
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->avg_data;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $input) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }
    }

    public function save()
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);

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
                $stmt = $db->prepare($sql);
                $result = $stmt->execute($bindings);
                if (!$result) {
                    return false;
                }
                error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $bindings) . "\n <<<<< THE END \n\n");
                return $this->find($db->lastInsertId());
            } catch (\PDOException $e) {
                error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $bindings) . "\n <<<<< THE END \n\n");
                error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
                return false;
            }

            // update data
        } else {
            $updateParameters = rtrim($updateParameters, ',');

            $sql = "UPDATE `$tableName` SET $updateParameters WHERE id = :id";
            try {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute($bindings);
                if (!$result) {
                    return false;
                }
                return $this->find($this->getId());
            } catch (\PDOException $e) {
                error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $bindings) . "\n <<<<< THE END \n\n");
                error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
                return false;
            }
        }
    }

    public function delete()
    {
        $db = (new Connection())->connect();

        $tableName = $this->getTableName();

        // if empty id
        $id = $this->getId();
        if (empty($id)) {
            return false;
        }
        $sql = "DELETE FROM `$tableName` WHERE id = ?";
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            if (!$result) {
                return false;
            }
            return true;
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, ['?', $id]) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }
    }

    public function paginate(int $page = 1, int $limit = 20, $options = [])
    {
        $db = (new Connection())->connect();
        $tableName = $this->getTableName();
        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);

        $count = $this->count($options);

        $str = '';
        if (!empty($options)) {

            $str = ' WHERE ';
            foreach ($options as $key => $value) {

                $str .= " $key = :$key AND ";

                $options[':' . $key] = $value;
                unset($options[$key]);
            }
            $str = rtrim($str, 'AND ');
        }


        $sql = "SELECT * FROM `$tableName` $str LIMIT :start, :limit";

        if ($page < 1 || !is_numeric($page))
            $page = 1;
        $first = (($page - 1) * $limit);

        $bind = array_merge($options, [":start" => $first, ":limit" => $limit]);
        try {
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt = $db->prepare($sql);
            $stmt->execute($bind);
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            $data = [];
            foreach ($results as $result) {
                $class = new $this();
                foreach ($tableFields as $tableField) {
                    $setterName = 'set' . ucfirst(Utils::camelize($tableField));
                    $class->$setterName($result->$tableField);
                }
                $data[] = $class;
            }
            $lastPage = (int)ceil($count / $limit);
            return
                [
                    'data' => $data,
                    'info' => [
                        'self'         => $page,
                        'first'        => 1,
                        'last'         => $lastPage,
                        'prev'         => $page - 1 < 1 ? null : $page - 1,
                        'next'         => $page + 1 > $lastPage ? null : $page + 1,
                        'current_page' => $page,
                        'from'         => (($page - 1) * $limit) + 1,
                        'last_page'    => $lastPage,
                        "per_page"     => $limit,
                        "to"           => (($page - 1) * $limit) + @count($data),
                        "total"        => $count,
                    ]
                ];
        } catch (\PDOException $e) {
            error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $bind) . "\n <<<<< THE END \n\n");
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($e->getMessage(), true) . "\n <<<<< THE END \n\n");
            return false;
        }

    }

    public function hasOne($model, $options = [])
    {
        $tableName = (new $model())->getTableName();

        $name = 'get' . ucfirst(rtrim($tableName, 's')) . 'Id';

        $options = array_merge($options, ['id' => $this->$name()]);
        return (new $model())->findOneBy($options);
    }

    public function hasMany($model, $options = [])
    {
        $tableName = $this->getTableName();

        $name = rtrim($tableName, 's') . '_id';

        $options = array_merge($options, [$name => $this->getId()]);

        return (new $model())->findBy($options);
    }

    public function hasManyPaginate($model, int $page = 1, int $limit = 20, $options = [])
    {
        $tableName = $this->getTableName();

        $name = rtrim($tableName, 's') . '_id';

        $options = array_merge($options, [$name => $this->getId()]);
        return (new $model())->paginate($page, $limit, $options);
    }

    public function toResource($resource)
    {
        return new $resource($this);
    }


    public function toArray()
    {
        return $this->processArray(get_object_vars($this));
    }

    public function toJSON()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    private function pdoSqlDebug($sql, $placeholders)
    {
        foreach ($placeholders as $k => $v) {
            $sql = str_replace($k, $v, $sql);
        }
        return $sql;
    }

    private function processArray($array)
    {
        foreach ($array as $key => $value) {
            if (is_object($value) && $value instanceof Model) {
                $array[$key] = $value->toArray();
            }
            if (is_array($value)) {
                $array[$key] = $this->processArray($value);
            }
        }

        // If the property isn't an object or array, leave it untouched
        return $array;
    }

    private function getTableName()
    {
        $class = strrchr(get_called_class(), "\\");
        $class = str_replace('\\', "", $class);
        return Utils::deCamelize($class);
    }

}
