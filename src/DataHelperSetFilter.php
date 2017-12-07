<?php
/**
 * DataHelperSet
 */
namespace m35;

class DataHelperSetFilter implements DataHelperSetExtendable
{
    protected $extends = [];

    public function filterData($data, $filter)
    {
        $argsWithoutFilter = $this->getArgsWithoutFilter(func_get_args());
        if (is_string($filter) && isset($this->extends[$filter])) {
            $data = call_user_func_array($this->extends[$filter], $argsWithoutFilter);
            return $data;
        }

        if (is_string($filter) && method_exists($this, $filter)) {
            return call_user_func_array([$this, $filter], $argsWithoutFilter);
        }

        if (is_callable($filter)) {
            $data = call_user_func_array($filter, $argsWithoutFilter);
        } elseif (is_int($filter)) {
            $options = func_num_args() > 2 ? func_get_arg(3) : '';
            $data = filter_var($data, $filter, $options);
        }
        return $data;
    }

    public function defaultIf($var, $default, $condition = ['', null])
    {
        if (is_array($condition)) {
            $useDefault = in_array($var, $condition, true);
        } else {
            $useDefault = $var === $condition;
        }
        return $useDefault ? $default : $var;
    }

    public function extend($filter, $callback)
    {
        if (is_callable($callback)) {
            $this->extends[$filter] = $callback;
            return true;
        } else {
            return false;
        }
    }

    public function getArgsWithoutFilter(array $args)
    {
        $newArgs = [$args[0]];
        $appendArgs = array_slice($args, 2);
        if ($appendArgs) {
            $newArgs = array_merge($newArgs, $appendArgs);
        }

        return $newArgs;
    }
}