<?php
/**
 * DataHelperSet
 */
use PHPUnit\Framework\TestCase;
use m35\DataHelperSet;
use m35\DataHelperSetFormatter;

class FormatterTest extends TestCase
{
    public function testString()
    {
        $data = 123;
        $data = DataHelperSet::format($data, DataHelperSetFormatter::TYPE_STRING);
        $this->assertInternalType('string', $data);
    }

    public function testInt()
    {
        $data = 'hello world';
        $data = DataHelperSet::format($data, DataHelperSetFormatter::TYPE_INT);
        $this->assertInternalType('int', $data);
    }

    public function testFloat()
    {
        $data = 123;
        $data = DataHelperSet::format($data, DataHelperSetFormatter::TYPE_FLOAT);
        $this->assertInternalType('float', $data);
    }

    public function testArray()
    {
        $data = 'php';
        $data = DataHelperSet::format($data, DataHelperSetFormatter::TYPE_ARRAY);
        $this->assertInternalType('array', $data);
    }

    public function testBoolean()
    {
        $data = 'yes';
        $data = DataHelperSet::format($data, DataHelperSetFormatter::TYPE_BOOLEAN);
        $this->assertInternalType('boolean', $data);
        $this->assertTrue($data);
        $this->assertFalse(DataHelperSet::format('no', DataHelperSetFormatter::TYPE_BOOLEAN));
        $this->assertFalse(DataHelperSet::format(0, DataHelperSetFormatter::TYPE_BOOLEAN));
    }

    public function testNull()
    {
        $data = 'hello world!';
        $this->assertInternalType('null', DataHelperSet::format($data, DataHelperSetFormatter::TYPE_NULL));
    }

    public function testExtend()
    {
        DataHelperSet::extendFormat('nullType', function($data) {
            return null;
        });
        $this->assertInternalType('null', DataHelperSet::format('data', 'nullType'));
    }
}