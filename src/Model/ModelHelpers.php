<?php


namespace MkOrm\Model;


trait ModelHelpers
{
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

    private function deCamelize($word)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $word));
    }

    private function camelize($word)
    {
        $words = explode('_', $word);
        $words = array_map('ucfirst', $words);
        return ucfirst(str_replace('_', '', implode('_', $words)));
    }



    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function toArray()
    {
        return $this->processArray(get_object_vars($this));
    }

    public function toJSON()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
