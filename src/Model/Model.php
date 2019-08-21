<?php
/**
 * Created by PhpStorm.
 * User: mk990
 * Date: 1/12/2019
 * Time: 9:30 PM
 */

namespace MkOrm\Model;


use MkOrm\Configs\DBConnect;
use PDO;
use PDOException;

class Model
{
    use ModelHelpers;

    public function all($orderBy = 'ASC')
    {
        $db = new DBConnect();
        $tableName = $this->getTableName();
        $tableFields = $this->getTableFields($db);

        $sql = "SELECT * FROM `$tableName` ORDER BY id $orderBy";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $this->dataToModel($results, $tableFields);
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, [], $e);
            return false;
        }
    }

    public function find(array $input = null)
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();
        $tableFields = $this->getTableFields($db);

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
                return $this->dataToModel($results, $tableFields);
            } catch (PDOException $e) {
                if (getenv('DB_DEBUG'))
                    $this->dbErrorLog($sql, $input, $e);
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
                $setterName = 'set' . ucfirst(self::camelize($tableField));
                $this->$setterName($result->$tableField);
            }

            return $this;
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, [], $e);
            return false;
        }
    }

    public function findBy(array $input = [])
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();
        $tableFields = $this->getTableFields($db);

        $str = $this->optionToSqlStr($input)[0];
        $input = $this->optionToSqlStr($input)[1];


        $sql = "SELECT * FROM `$tableName`" . $str;
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $this->dataToModel($results, $tableFields);
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, $input, $e);
            return false;
        }

    }

    public function findLike(array $input = [])
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();
        $tableFields = $this->getTableFields($db);


        $str = $this->optionToSqlStr($input, true)[0];
        $input = $this->optionToSqlStr($input, true)[1];

        $sql = "SELECT * FROM `$tableName` $str";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $this->dataToModel($results, $tableFields);
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, $input, $e);
            return false;
        }

    }

    public function findOneBy(array $input = [])
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();
        $tableFields = $this->getTableFields($db);

        $str = $this->optionToSqlStr($input)[0];
        $input = $this->optionToSqlStr($input)[1];

        $sql = "SELECT * FROM `$tableName` $str LIMIT 1";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$result) {
                return false;
            }
            foreach ($tableFields as $tableField) {
                $setterName = 'set' . ucfirst(self::camelize($tableField));
                $this->$setterName($result->$tableField);
            }

//            error_log(var_export($input), true);
            $this->dbErrorLog($sql, $input);

            return $this;
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, $input, $e);
            return false;
        }

    }

    public function findOneLike(array $input = [])
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_COLUMN);

        $str = $this->optionToSqlStr($input, true)[0];
        $input = $this->optionToSqlStr($input, true)[1];

        $sql = "SELECT * FROM `$tableName` $str LIMIT 1";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$result) {
                return false;
            }
            foreach ($tableFields as $tableField) {
                $setterName = 'set' . ucfirst(self::camelize($tableField));
                $this->$setterName($result->$tableField);
            }
            return $this;
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, $input, $e);
            return false;
        }

    }

    public function count(array $input = [])
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();

        $str = $this->optionToSqlStr($input)[0];
        $input = $this->optionToSqlStr($input)[1];

        $sql = "SELECT COUNT(*) AS cnt FROM `$tableName` $str";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->cnt;
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, $input, $e);
            return false;
        }
    }

    public function sum($column, array $input = [])
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();

        $str = $this->optionToSqlStr($input)[0];
        $input = $this->optionToSqlStr($input)[1];

        $sql = "SELECT SUM($column) AS sum_data FROM `$tableName`" . $str;
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->sum_data;
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, $input, $e);
            return false;
        }
    }

    public function avg($column, array $input = [])
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();

        $str = $this->optionToSqlStr($input)[0];
        $input = $this->optionToSqlStr($input)[1];

        $sql = "SELECT AVG($column) AS avg_data FROM `$tableName` $str";

        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($input);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->avg_data;
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, $input, $e);
            return false;
        }
    }

    public function save()
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();
        $tableFields = $this->getTableFields($db);

        $promoters = '';
        $values = '';
        $updateParameters = '';
        $bindings = [];
        foreach ($tableFields as $tableField) {
            $getterName = 'get' . ucfirst(self::camelize($tableField));
            $data = $this->$getterName();
            if ($data !== null) {
                if ($data === false)
                    $data = 0;

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

        //update date
        if (!empty($this->getId())) {
            $updateParameters = rtrim($updateParameters, ',');

            $sql = "UPDATE `$tableName` SET $updateParameters WHERE id = :id";
            try {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute($bindings);
                if (!$result) {
                    return false;
                }
                return $this->find($this->getId());
            } catch (PDOException $e) {
                if (getenv('DB_DEBUG'))
                    $this->dbErrorLog($sql, $bindings, $e);
                return false;
            }
        }

        // save data
        $sql = "INSERT INTO `$tableName` ( $promoters ) VALUES ( " . $values . " )";
        try {
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($bindings);
            if (!$result) {
                return false;
            }
            return $this->find($db->lastInsertId());
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, $bindings, $e);
            return false;
        }
    }

    public function delete($input = null)
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();

        if ($input == null) {
            $id = $this->getId();
            // if empty id
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
            } catch (PDOException $e) {
                if (getenv('DB_DEBUG'))
                    $this->dbErrorLog($sql, ['?', $id], $e);
                return false;
            }
        }

        if (is_array($input) && !empty($input)) {
            $str = $this->optionToSqlStr($input)[0];
            $input = $this->optionToSqlStr($input)[1];

            $sql = "DELETE FROM `$tableName` $str";
            try {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute($input);
                if (!$result) {
                    return false;
                }
                return true;
            } catch (PDOException $e) {
                if (getenv('DB_DEBUG'))
                    $this->dbErrorLog($sql, $input, $e);
                return false;
            }
        }
        return false;
    }

    public function paginate(int $page = 1, int $limit = 20, $options = [])
    {
        $db = new DBConnect();

        $tableName = $this->getTableName();
        $tableFields = $this->getTableFields($db);

        $count = $this->count($options);

        $str = $this->optionToSqlStr($options)[0];
        $options = $this->optionToSqlStr($options)[1];

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
            $data = $this->dataToModel($results, $tableFields);

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
        } catch (PDOException $e) {
            if (getenv('DB_DEBUG'))
                $this->dbErrorLog($sql, $bind, $e);
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

    public function hasManyPaginate(string $model, int $page = 1, int $limit = 20, array $options = [])
    {
        $tableName = $this->getTableName();

        $name = rtrim($tableName, 's') . '_id';

        $options = array_merge($options, [$name => $this->getId()]);
        return (new $model())->paginate($page, $limit, $options);
    }

    /**
     * @param string $resource
     * @return object
     */
    public function toResource(string $resource): object
    {
        return new $resource($this);
    }

    /**
     * @return string
     */
    private function getTableName(): string
    {
        $class = strrchr(get_called_class(), "\\");
        $class = str_replace('\\', "", $class);
        return self::deCamelize($class);
    }

    /**
     * @param DBConnect $db
     * @return array
     */
    private function getTableFields(DBConnect $db): array
    {
        $tableName = $this->getTableName();
        $statement = $db->prepare("DESCRIBE `$tableName`");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param object $results
     * @param array $tableFields
     * @return array
     */
    private function dataToModel(array $results, array $tableFields): array
    {
        $data = [];
        foreach ($results as $result) {
            $class = new $this();
            foreach ($tableFields as $tableField) {
                $setterName = 'set' . ucfirst(self::camelize($tableField));
                $class->$setterName($result->$tableField);
            }
            $data[] = $class;
        }
        return $data;
    }

    /**
     * @param string $sql
     * @param array $input
     * @param PDOException $exception
     */
    private function dbErrorLog(string $sql, array $input, PDOException $exception = null): void
    {
        error_log("\n\n >>>>> SQL LOG: \n" . $this->pdoSqlDebug($sql, $input) . "\n <<<<< THE END \n\n");
        if (!empty(null))
            error_log("\n\n >>>>> PDO ERROR: \n" . var_export($exception->getMessage(), true) . "\n <<<<< THE END \n\n");
    }

    /**
     * @param array $options
     * @param bool $like
     * @return array
     */
    private function optionToSqlStr(array $options, bool $like = false): array
    {
        if (!empty($options)) {

            $str = ' WHERE ';
            foreach ($options as $key => $value) {

                if (!$like) {
                    $str .= " $key = :$key AND ";
                    $options[":$key"] = $value;
                } else {
                    $str .= " $key like :$key AND ";
                    $input[':' . $key] = "%$value%";
                }

                unset($options[$key]);
            }
            return [rtrim($str, 'AND '), $options];
        }
        return ['', []];
    }
}
