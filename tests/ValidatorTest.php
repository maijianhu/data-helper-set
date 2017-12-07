<?php
/**
 * Created by PhpStorm.
 * User: maijianhu
 * Date: 2017/12/6
 * Time: 10:40
 */
use PHPUnit\Framework\TestCase;
use m35\DataHelperSet;
use m35\DataHelperSetValidator;

class ValidatorTest extends TestCase
{
    public function testRequired()
    {
        $validator = DataHelperSet::getValidator();

        $this->assertTrue($validator->checkData(0, DataHelperSetValidator::REQUIRED));
        $this->assertTrue($validator->checkData(false, DataHelperSetValidator::REQUIRED));

        $this->assertFalse($validator->checkData(null, DataHelperSetValidator::REQUIRED));
        $this->assertFalse($validator->checkData('', DataHelperSetValidator::REQUIRED));
    }

    public function testEmail()
    {
        $validator = DataHelperSet::getValidator();

        $this->assertTrue($validator->checkData('maijianhu@qq.com', DataHelperSetValidator::EMAIL));

        $this->assertFalse($validator->checkData('maijianhu', DataHelperSetValidator::EMAIL));
    }

    public function testNumber()
    {
        $validator = DataHelperSet::getValidator();

        $this->assertTrue($validator->checkData(123, DataHelperSetValidator::NUMBER));
        $this->assertTrue($validator->checkData('123', DataHelperSetValidator::NUMBER));
        $this->assertTrue($validator->checkData(-100, DataHelperSetValidator::NUMBER));
        $this->assertTrue($validator->checkData(-100.22, DataHelperSetValidator::NUMBER));

        $this->assertFalse($validator->checkData('hello world', DataHelperSetValidator::NUMBER));
    }

    public function testExtend()
    {
        $validator = DataHelperSet::getValidator();
        $validator->extend('extend', '测试扩展', function($data) {
            return (bool) $data;
        });

        $validator->extend('isTwo', '测试param', function($data, $param) {
            return $data + $param === 2;
        });

        $validator->extend('isFive', '测试params', function($data, $params) {
            return $data + $params[0] + $params[1] === 5;
        });

        $this->assertTrue($validator->checkData(true, 'extend'));
        $this->assertFalse($validator->checkData(false, 'extend'));
        $this->assertTrue($validator->checkData(1, 'isTwo', 1));
        $this->assertTrue($validator->checkData(1, 'isFive', [1, 3]));
    }
}