<?php
/**
 * DataHelperSet
 */
namespace m35;

class DataHelperSet
{
    const PIPE_FORMAT   = 'format';
    const PIPE_VALIDATE = 'validate';
    const PIPE_FILTER   = 'filter';

    protected static $formatter;
    protected static $validator;
    protected static $filter;

    public static function getValidator()
    {
        if (is_null(self::$validator)) {
            self::$validator = new DataHelperSetValidator();
        }
        return self::$validator;
    }

    public static function getFormatter()
    {
        if (is_null(self::$formatter)) {
            self::$formatter = new DataHelperSetFormatter();
        }
        return self::$formatter;
    }

    public static function getFilter()
    {
        if (is_null(self::$filter)) {
            self::$filter = new DataHelperSetFilter();
        }
        return self::$filter;
    }

    // 输入数据集合，配置集合，根据配置转换数据类型
    public static function formatBundle(array $data, $configs = [])
    {
        foreach ($configs as $field => $config) {
            if (isset($data[$field])) {
                $data[$field] = self::format($data[$field], $config);
            }
        }
        return $data;
    }

    // 根据预期类型转换数据
    public static function format($data, $config = DataHelperSetFormatter::TYPE_STRING)
    {
        $formatter = self::getFormatter();
        return $formatter->format($data, $config);
    }

    public static function extendFormat($type, $callback)
    {
        $formatter = self::getFormatter();
        return $formatter->extend($type, $callback);
    }

    /**
     * @throws exception\DataHelperSetValidateException
     */
    public static function validateBundle(array $data, $configs = [])
    {
        foreach ($configs as $field => $config) {
            if ($config) {
                $value = isset($data[$field]) ? $data[$field] : null;
                self::validate($value, $config, $field);
            }
        }
    }

    /**
     * @throws exception\DataHelperSetValidateException
     */
    public static function validate($data, array $config = [], $field)
    {
        $validator = self::getValidator();
        if (is_array($config[0])) {
            foreach ($config as $singleConfig) {
                self::validate($data, $singleConfig, $field);
            }
        } else {
            if (!is_array($config)) {
                $config = [$config];
            }
            $rule = $config[0];
            if (isset($config[2])) {
                $message = $config[1];
                $params = $config[2];
            } else {
                $message = isset($config[1]) ? $config[1] : null;
                $params = null;
            }
            $validator->validate($data, $rule, $field, $message, $params);
        }
    }

    public static function extendValidate($rule, $message, $callback)
    {
        $validator = self::getValidator();
        return $validator->extend($rule, $message, $callback);
    }

    public static function filterBundle(array $data, $configs = [])
    {
        foreach ($configs as $field => $config) {
            if (isset($data[$field])) {
                $data[$field] = self::filter($config, $data[$field]);
            }
        }
        return $data;
    }
    
    public static function filter($data, $filterType)
    {
        $filter = self::getFilter();
        if (is_array($filterType)) {
            foreach ($filterType as $singleFilterType) {
                if (is_array($singleFilterType)) {
                    $args = array_merge([$data, $singleFilterType[0]], array_slice($singleFilterType, 1));
                    $data = call_user_func_array([$filter, 'filterData'], $args);
                } else {
                    $data = $filter->filterData($data, $singleFilterType);
                }
            }
            return $data;
        } else {
            return call_user_func_array([$filter, 'filterData'], func_get_args());
        }
    }

    public static function extendFilter($filterType, $callback)
    {
        $filter = self::getFilter();
        return $filter->extend($filterType, $callback);
    }

    /**
     * @throws exception\DataHelperSetValidateException
     */
    public static function pipe(array $data, array $configs)
    {
        foreach ($configs as $key => $config) {
            if ($key === 0) {
                $data = self::pipe($data, $config);
            } else {
                switch ($key) {
                    case self::PIPE_FORMAT:
                        $data = self::formatBundle($data, $config);
                        break;
                    case self::PIPE_VALIDATE:
                        self::validateBundle($data, $config);
                        break;
                    case self::PIPE_FILTER:
                        $data = self::filterBundle($data, $config);
                }
            }
        }
        return $data;
    }
}