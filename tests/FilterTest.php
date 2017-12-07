<?php
/**
 * DataHelperSet
 */
use PHPUnit\Framework\TestCase;
use m35\DataHelperSet;

class FilterTest extends TestCase
{
    public function testFilterTrim()
    {
        $data = '  hello world!';
        $data = DataHelperSet::filter($data, 'trim');
        $this->assertEquals('hello world!', $data);
    }

    public function testFilterSubstr()
    {
        $data = 'hello world';
        $data = DataHelperSet::filter($data, 'substr', 0, 5);
        $this->assertEquals('hello', $data);
    }

    public function testSanitizeFilter()
    {
        $data = '<mai>jianhu@qq.com';
        $data = DataHelperSet::filter($data, FILTER_SANITIZE_EMAIL);
        $this->assertEquals('maijianhu@qq.com', $data);
    }

    public function testCallableFilter()
    {
        $data = 'hello world';
        $data = DataHelperSet::filter($data, function($data) {
            return $data . ' from m35';
        });
        $this->assertEquals('hello world from m35', $data);
    }

    public function testExtend()
    {
        DataHelperSet::extendFilter('append123', function($data) {
            return $data . '123';
        });
        $this->assertEquals('data123', DataHelperSet::filter('data', 'append123'));
    }
}