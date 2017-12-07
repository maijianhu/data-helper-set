<?php
/**
 * DataHelperSet
 */
namespace m35;

class DataHelperSetFormatter implements DataHelperSetExtendable
{
    const TYPE_STRING  = 1;
    const TYPE_INT     = 2;
    const TYPE_FLOAT   = 3;
    const TYPE_ARRAY   = 4;
    const TYPE_BOOLEAN = 5;
    const TYPE_NULL    = 6;

    protected $extends = [];

    public function format($data, $type = self::TYPE_STRING)
    {
        if (is_string($type) && isset($this->extends[$type])) {
            return call_user_func($this->extends[$type], $data);
        }

        if (is_callable($type)) {
            return call_user_func($type, $data);
        }

        switch ($type) {
            case self::TYPE_STRING:
                return is_scalar($data) ? (string) $data : null;
            case self::TYPE_INT:
                return is_scalar($data) ? (int) $data : 0;
            case self::TYPE_FLOAT:
                return is_scalar($data) ? floatval($data) : 0;
            case self::TYPE_ARRAY:
                return is_array($data) ? $data : (array) $data;
            case self::TYPE_BOOLEAN:
                return filter_var($data, FILTER_VALIDATE_BOOLEAN);
            case self::TYPE_NULL:
                return null;
        }
        return $data;
    }

    public function extend($type, $callback)
    {
        if (is_callable($callback)) {
            $this->extends[$type] = $callback;
            return true;
        } else {
            return false;
        }
    }
}
