<?php
/**
 * DataHelperSet
 */
namespace m35;
use m35\exception\DataHelperSetValidateException;

class DataHelperSetValidator implements DataHelperSetExtendable
{
    const REQUIRED = 1;
    const EMAIL    = 2;
    const NUMBER   = 3;

    protected $extendRules    = [];
    protected $extendMessages = [];

    protected $messages = [
        self::REQUIRED => '%field%字段必须',
        self::EMAIL    => '字段%field%不是一个合法的电子邮箱地址',
        self::NUMBER    => '%field%必须是一个数字',
    ];

    /**
     * @throws DataHelperSetValidateException
     */
    public function validate($data, $rule, $field, $message = null, $params = null)
    {
        if (!$this->checkData($data, $rule, $params)) {
            $message = $message ? $message : $this->getErrorMessage($rule);
            $message = $this->convertMessage($message, [
                'field' => $field,
                'data'  => $data,
                'rule'  => $rule,
            ]);

            throw new DataHelperSetValidateException($message);
        }
    }

    public function checkData($data, $rule, $params = [])
    {
        if (isset($this->extendRules[$rule])) {
            return call_user_func($this->extendRules[$rule], $data, $params);
        }

        switch($rule) {
            case self::REQUIRED:
                return $data !== null && $data !== '';
            case self::EMAIL:
                return (bool) filter_var($data, FILTER_VALIDATE_EMAIL);
            case self::NUMBER:
                return is_numeric($data);
        }
        return false;
    }

    protected function getErrorMessage($rule)
    {
        if (isset($this->extendMessages[$rule])) {
            return $this->extendMessages[$rule];
        }
        return isset($this->messages[$rule]) ? $this->messages[$rule] : 'unknown validate error.';
    }

    protected function convertMessage($message, array $change)
    {
        $changes = [];
        foreach ($change as $k => $v) {
            $changes['%' . $k . '%'] = is_scalar($v) ? $v : serialize($v);
        }
        return strtr($message, $changes);
    }

    public function extend($rule, $message, $callback)
    {
        if (is_callable($callback)) {
            $this->extendRules[$rule] = $callback;
            $this->extendMessages[$rule] = $message;
            return true;
        } else {
            return false;
        }
    }
}
